<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Core;

use \PDO;
use OsumiFramework\OFW\DB\ODBContainer;
use OsumiFramework\OFW\Cache\OCacheContainer;
use OsumiFramework\OFW\Web\OSession;
use OsumiFramework\OFW\Web\ORequest;
use OsumiFramework\OFW\Routing\OUrl;
use OsumiFramework\OFW\Tools\OTools;

/**
 * OCore - Base class for the framework with methods to load required files and start the application
 */
class OCore {
	public ?ODBContainer    $dbContainer = null;
	public ?OCacheContainer $cacheContainer = null;
	public ?OConfig         $config = null;
	public ?OSession        $session = null;
	public ?OTranslate      $translate = null;
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
		$basedir = str_ireplace('ofw/vendor/core', '', $basedir);

		require $basedir.'ofw/vendor/core/oconfig.class.php';
		$this->config = new OConfig($basedir);

		// Check locale file
		$locale_file = $this->config->getDir('ofw_locale').$this->config->getLang().'.po';
		if (!file_exists($locale_file)){
			echo "ERROR: locale file ".$this->config->getLang()." not found.";
			exit;
		}

		// Core
		require $this->config->getDir('ofw_vendor').'log/olog.class.php';
		require $this->config->getDir('ofw_vendor').'cache/ocache.container.class.php';
		require $this->config->getDir('ofw_vendor').'cache/ocache.class.php';
		require $this->config->getDir('ofw_vendor').'core/omodule.class.php';
		require $this->config->getDir('ofw_vendor').'core/oservice.class.php';
		require $this->config->getDir('ofw_vendor').'core/oplugin.class.php';
		require $this->config->getDir('ofw_vendor').'core/otask.class.php';
		require $this->config->getDir('ofw_vendor').'core/otranslate.class.php';
		require $this->config->getDir('ofw_vendor').'routing/oroute.class.php';
		require $this->config->getDir('ofw_vendor').'routing/oroutecheck.class.php';
		require $this->config->getDir('ofw_vendor').'routing/ourl.class.php';
		require $this->config->getDir('ofw_vendor').'tools/oform.class.php';
		require $this->config->getDir('ofw_vendor').'tools/otools.class.php';

		// Due to a circular dependancy, check name of the log file after core loading
		if (is_null($this->config->getLog('name'))) {
			$this->config->setLog('name', OTools::slugify($this->config->getName()));
		}

		// Load framework translations
		$this->translate = new OTranslate();
		$this->translate->load($this->config->getDir('ofw_locale').$this->config->getLang().'.po');

		// If there is a DB connection configured, check drivers and load required classes
		if ($this->config->getDB('user')!=='' || $this->config->getDB('pass')!=='' || $this->config->getDB('host')!=='' || $this->config->getDB('name')!=='') {
			$pdo_drivers = PDO::getAvailableDrivers();
			if (!in_array($this->config->getDB('driver'), $pdo_drivers)) {
				echo "ERROR: El sistema no dispone del driver ".$this->config->getDB('driver')." solicitado para realizar la conexión a la base de datos.\n";
				exit;
			}
			require $this->config->getDir('ofw_vendor').'db/odb.container.class.php';
			require $this->config->getDir('ofw_vendor').'db/odb.class.php';
			require $this->config->getDir('ofw_vendor').'db/omodel.class.php';
			$this->dbContainer = new ODBContainer();
		}

		if (!$from_cli) {
			require $this->config->getDir('ofw_vendor').'core/otemplate.class.php';
			require $this->config->getDir('ofw_vendor').'web/osession.class.php';
			require $this->config->getDir('ofw_vendor').'web/ocookie.class.php';
			require $this->config->getDir('ofw_vendor').'web/orequest.class.php';

			$this->session  = new OSession();
		}
		else {
			require $this->config->getDir('ofw_vendor').'tools/ocolors.class.php';
			require $this->config->getDir('ofw_vendor').'core/oupdate.class.php';
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

		if (!$from_cli) {
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
			if (array_key_exists('filter', $url_result) && !is_null($url_result['filter'])) {
				$url_result[$url_result['filter']] = call_user_func(
					"\\OsumiFramework\\App\\Filter\\".$url_result['filter'],
					$url_result['params'],
					$url_result['headers']
				);

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

			$module_path = $this->config->getDir('app_module').'/'.$url_result['module'].'/'.$url_result['module'].'.php';

			if (file_exists($module_path)) {
				require_once $module_path;
				$module_name = "\\OsumiFramework\\App\\Module\\".$url_result['module'];
				$module = new $module_name;

				if (method_exists($module, $url_result['action'])) {
					$module->loadModule($url_result);
					call_user_func(array($module, $url_result['action']), new ORequest($url_result));
					echo $module->getTemplate()->process();
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