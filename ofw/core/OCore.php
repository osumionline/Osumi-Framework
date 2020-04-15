<?php declare(strict_types=1);
/**
 * OCore - Base class for the framework with methods to load required files and start the application
 */
class OCore {
	// Field types
	const PK       = 1;
	const PK_STR   = 10;
	const CREATED  = 2;
	const UPDATED  = 3;
	const NUM      = 4;
	const TEXT     = 5;
	const DATE     = 6;
	const BOOL     = 7;
	const LONGTEXT = 8;
	const FLOAT    = 9;

	const DEFAULT_MODEL = [
		self::PK       => ['default'=>null,  'original'=>null,  'value'=>null,  'incr'=>true,  'size'=>11, 'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>''],
		self::PK_STR   => ['default'=>null,  'original'=>null,  'value'=>null,  'incr'=>false, 'size'=>50, 'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>''],
		self::CREATED  => ['default'=>null,  'original'=>null,  'value'=>null,  'incr'=>false, 'size'=>0,  'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>''],
		self::UPDATED  => ['default'=>null,  'original'=>null,  'value'=>null,  'incr'=>false, 'size'=>0,  'nullable'=>true,  'comment'=>'', 'ref'=>'', 'by'=>''],
		self::NUM      => ['default'=>0,     'original'=>0,     'value'=>0,     'incr'=>false, 'size'=>11, 'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>''],
		self::TEXT     => ['default'=>'',    'original'=>'',    'value'=>'',    'incr'=>false, 'size'=>50, 'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>''],
		self::DATE     => ['default'=>null,  'original'=>null,  'value'=>'',    'incr'=>false, 'size'=>0,  'nullable'=>true,  'comment'=>'', 'ref'=>'', 'by'=>''],
		self::BOOL     => ['default'=>false, 'original'=>false, 'value'=>false, 'incr'=>false, 'size'=>1,  'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>''],
		self::LONGTEXT => ['default'=>'',    'original'=>'',    'value'=>'',    'incr'=>false, 'size'=>0,  'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>''],
		self::FLOAT    => ['default'=>0,     'original'=>0,     'value'=>0,     'incr'=>false, 'size'=>0,  'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>'']
	];

	public ?ODBContainer    $dbContainer = null;
	public ?OCacheContainer $cacheContainer = null;
	public ?OConfig         $config = null;
	public ?array           $locale = null;
	public ?float           $start_time = null;

	/**
	 * Get the start time in milliseconds to use in benchmarks
	 */
	public function __construct() {
		$this->start_time = microtime(true);
	}

	/**
	 * Include required files for the framework and start up some components like configuration, cache container or database connection container
	 *
	 * @param bool $from_cli Marks if the core is being loaded for use in web application or CLI application
	 *
	 * @return void
	 */
	public function load(bool $from_cli=false): void {
		session_start();
		date_default_timezone_set('Europe/Madrid');

		$basedir = realpath(dirname(__FILE__));
		$basedir = str_ireplace('ofw/core','',$basedir);

		require $basedir.'ofw/core/OConfig.php';
		$this->config = new OConfig($basedir);

		// Check locale file
		$locale_file = $this->config->getDir('ofw_locale').$this->config->getLang().'.php';
		if (!file_exists($locale_file)){
			echo "ERROR: locale file ".$this->config->getLang()." not found.";
			exit;
		}

		// Core
		require $this->config->getDir('ofw_core').'OModel.php';
		require $this->config->getDir('ofw_core').'OController.php';
		require $this->config->getDir('ofw_core').'OService.php';
		require $this->config->getDir('ofw_core').'ODB.php';
		require $this->config->getDir('ofw_core').'OLog.php';
		require $this->config->getDir('ofw_core').'OUrl.php';
		require $this->config->getDir('ofw_core').'OCache.php';
		require $this->config->getDir('ofw_core').'OForm.php';
		require $this->config->getDir('ofw_core').'OPlugin.php';
		require $this->config->getDir('ofw_core').'OTools.php';

		if (!$from_cli) {
			require $this->config->getDir('ofw_core').'OTemplate.php';
			require $this->config->getDir('ofw_core').'OSession.php';
			require $this->config->getDir('ofw_core').'OCookie.php';
		}
		else {
			require $this->config->getDir('ofw_core').'OColors.php';
			require $this->config->getDir('ofw_core').'OUpdate.php';
		}

		// Plugins
		foreach ($this->config->getPlugins() as $p) {
			$plugin = new OPlugin($p);
			$plugin->load();
		}

		// OFW Tasks
		if ($model = opendir($this->config->getDir('ofw_task'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..') {
					require $this->config->getDir('ofw_task').$entry;
				}
			}
			closedir($model);
		}

		// Libs
		$lib_list = $this->config->getLibs();
		foreach ($lib_list as $lib) {
			$lib_file = $this->config->getDir('ofw_lib').$lib.'.php';
			if (file_exists($lib_file)) {
				require $this->config->getDir('ofw_lib').$lib.'.php';
			}
			else {
				echo "ERROR: Lib file \"".$lib_file."\" not found.\n";
				exit;
			}
		}

		if (!$from_cli) {
			// User services
			if (file_exists($this->config->getDir('app_service'))) {
				if ($model = opendir($this->config->getDir('app_service'))) {
					while (false !== ($entry = readdir($model))) {
						if ($entry != '.' && $entry != '..') {
							require $this->config->getDir('app_service').$entry;
						}
					}
					closedir($model);
				}
			}

			// Filters
			if (file_exists($this->config->getDir('app_filter'))) {
				if ($model = opendir($this->config->getDir('app_filter'))) {
					while (false !== ($entry = readdir($model))) {
						if ($entry != '.' && $entry != '..') {
							require $this->config->getDir('app_filter').$entry;
						}
					}
					closedir($model);
				}
			}
		}

		// App
		if (file_exists($this->config->getDir('app_model'))) {
			if ($model = opendir($this->config->getDir('app_model'))) {
				while (false !== ($entry = readdir($model))) {
					if ($entry != '.' && $entry != '..') {
						require $this->config->getDir('app_model').$entry;
					}
				}
				closedir($model);
			}
		}

		// If there is a DB connection configured, check drivers
		if ($this->config->getDB('user')!=='' || $this->config->getDB('pass')!=='' || $this->config->getDB('host')!=='' || $this->config->getDB('name')!=='') {
			$pdo_drivers = PDO::getAvailableDrivers();
			if (!in_array($this->config->getDB('driver'), $pdo_drivers)) {
				echo "ERROR: El sistema no dispone del driver ".$this->config->getDB('driver')." solicitado para realizar la conexión a la base de datos.\n";
				exit;
			}
			$this->dbContainer = new ODBContainer();
		}

		// Set up an empty cache container
		$this->cacheContainer = new OCacheContainer();
	}

	/**
	 * Start up the application checking the accessed URL, load matched URL or give the appropiate error
	 *
	 * @return void
	 */
	public function run(): void {
		if ($this->config->getAllowCrossOrigin()) {
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
			header('Access-Control-Allow-Methods: GET, POST');
		}

		// Load current URL
		$u = new OUrl($_SERVER['REQUEST_METHOD']);
		$u->setCheckUrl($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
		$url_result = $u->process();

		if ($url_result['res']) {
			// If the call method is OPTIONS, just return OK right away
			if ($url_result['method']==='options'){
				header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
				exit;
			}

			// If there is a filter defined, apply it before the controller
			if (array_key_exists('filter', $url_result)) {
				$url_result[$url_result['filter']] = call_user_func($url_result['filter'], $url_result['params'], $url_result['headers']);

				// If status is 'error', return 403 Forbidden
				if ($url_result[$url_result['filter']]['status']=='error') {
					if (array_key_exists('return', $url_result[$url_result['filter']])) {
						OUrl::goToUrl($url_result[$url_result['filter']]['return']);
					}
					else {
						OTools::showErrorPage($url_result, '403');
					}
				}
			}

			$module = $this->config->getDir('app_controller').$url_result['module'].'.php';

			if (file_exists($module)) {
				require_once $module;
				$controller = new $url_result['module']();

				if (method_exists($controller, $url_result['action'])) {
					$controller->loadController($url_result);
					call_user_func(array($controller, $url_result['action']), OTools::getControllerParams($url_result));
					echo $controller->getTemplate()->process();
				}
				else {
					OTools::showErrorPage($url_result, 'action');
				}
			}
			else {
				OTools::showErrorPage($url_result, 'module');
			}
		}
		else {
			OTools::showErrorPage($url_result, '404');
		}

		if (!is_null($this->dbContainer)) {
			$this->dbContainer->closeAllConnections();
		}
	}
}