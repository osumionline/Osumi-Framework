<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Core;

use \PDO;
use \ReflectionParameter;
use \ReflectionClass;
use OsumiFramework\OFW\DB\ODBContainer;
use OsumiFramework\OFW\Cache\OCacheContainer;
use OsumiFramework\OFW\Web\OSession;
use OsumiFramework\OFW\Web\ORequest;
use OsumiFramework\OFW\Routing\OUrl;
use OsumiFramework\OFW\Tools\OTools;
use OsumiFramework\OFW\Log\OLog;

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
		require $this->config->getDir('ofw_vendor').'core/odto.interface.php';
		require $this->config->getDir('ofw_vendor').'core/ocomponent.class.php';
		require $this->config->getDir('ofw_vendor').'core/oservice.class.php';
		require $this->config->getDir('ofw_vendor').'core/oplugin.class.php';
		require $this->config->getDir('ofw_vendor').'core/otask.class.php';
		require $this->config->getDir('ofw_vendor').'core/otranslate.class.php';
		require $this->config->getDir('ofw_vendor').'routing/omodule.class.php';
		require $this->config->getDir('ofw_vendor').'routing/omoduleaction.class.php';
		require $this->config->getDir('ofw_vendor').'routing/oaction.class.php';
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

		// Database model classes
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
			if (array_key_exists('filters', $url_result) && count($url_result['filters']) > 0) {
				$filter_check =  true;
				$filter_return = null;
				foreach ($url_result['filters'] as $filter_name => $value) {
					// Check if the filter's file exist as it is loaded per request
					$filter_route = $this->config->getDir('app_filter').$filter_name.'.filter.php';
					if (file_exists($filter_route)) {
						require_once $filter_route;

						$value = call_user_func(
							"\\OsumiFramework\\App\\Filter\\".$filter_name."Filter",
							$url_result['params'],
							$url_result['headers']
						);

						// If status is not 'ok', filter checks have failed
						if ($value['status'] !== 'ok') {
							$filter_check = false;
							if (is_null($filter_return) && array_key_exists('return', $value)) {
								$filter_return = $value['return'];
							}
							break;
						}

						// Store the result value
						$url_result['filters'][$filter_name] = $value;
					}
					else {
						OTools::showErrorPage($url_result, '403');
					}
				}

				// If filter checks didn't pass
				if (!$filter_check) {
					// If return value has been set in any of the filters, go there, otherwise go to error page
					if (!is_null($filter_return)) {
						OUrl::goToUrl($filter_return);
					}
					else {
						OTools::showErrorPage($url_result, '403');
					}
				}
			}

			// If there are any "utils" classes required to be loaded, load before the controller
			if (array_key_exists('utils', $url_result) && !is_null($url_result['utils'])) {
				$utils = explode(',', $url_result['utils']);
				foreach ($utils as $util) {
					$util_file = $this->config->getDir('app_utils').$util.'.php';
					if (file_exists($util_file)) {
						require_once $util_file;
					}
				}
			}

			$module_path = $this->config->getDir('app_module').$url_result['module'].'/'.$url_result['module'].'.module.php';

			if (file_exists($module_path)) {
				require_once $module_path;
				$module_name = "\\OsumiFramework\\App\\Module\\".$url_result['module'].'Module';
				$module = new $module_name;
				$module_attributes = OTools::getClassAttributes($module);

				if (in_array($url_result['action'], $module_attributes->getActions())) {
					$action_path = $this->config->getDir('app_module').$url_result['module'].'/actions/'.$url_result['action'].'/'.$url_result['action'].'.action.php';
					if (file_exists($action_path)) {
						require_once $action_path;

						$this->eagerLoader($action_path);

						$action_name = "\\OsumiFramework\\App\\Module\\Action\\".$url_result['action'].'Action';

						$action = new $action_name;
						$action_attributes = OTools::getClassAttributes($action);
						$reflection_param = new ReflectionParameter([$action_name, 'run'], 0);
						$reflection_param_type = $reflection_param->getType()->getName();
						$req = new ORequest($url_result);
						if (str_starts_with($reflection_param_type, 'OsumiFramework\App\DTO')) {
							$param = new $reflection_param_type;
							$param->load($req);
						}
						else {
							$param = $req;
						}

						$action->loadAction($url_result, $action_attributes);
						call_user_func([$action, 'run'], $param);
						echo $action->getTemplate()->process();
					}
					else {
						OTools::showErrorPage($url_result, 'action');
					}
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

	/**
	 * Load an action's required DTO and components
	 */
	public function eagerLoader(string $path): void {
		$action_content = file_get_contents($path);

		// Check for DTOs
		$dto = $this->getContentDTO($action_content);
		if (!is_null($dto)) {
			require_once $this->config->getDir('app_dto').$dto.'.dto.php';
		}

		// Check for components
		$components = $this->getContentComponents($action_content);
		foreach ($components as $component) {
			$this->loadComponent($component);
		}
	}

	/**
	 * Get the name of the DTO used in an action. If there is no DTO returns null.
	 *
	 * @param string $content Content of the action's file
	 *
	 * @return string Name of the DTO file used in the action or null if there is no DTO.
	 */
	public function getContentDTO(string $content): ?string {
		preg_match("/^use OsumiFramework\\\App\\\DTO\\\(.*?);$/m", $content, $match);
		if (!is_null($match) && count($match) > 1) {
			return OTools::toSnakeCase(str_ireplace("DTO", "", $match[1]));
		}
		return null;
	}

	/**
	 * Get the name of the components used in an action. If there are no components returns an empty list.
	 *
	 * @param string $content Content of the action's file
	 *
	 * @return array List of component names
	 */
	public function getContentComponents(string $content): array {
		$pattern = "/^use OsumiFramework\\\App\\\Component\\\(.*?);$/m";
		$result = preg_match_all($pattern, $content, $matches);
		$ret = [];

		if  (!is_null($matches) && count($matches) > 1) {
			for ($i = 0; $i < count($matches[1]); $i++) {
				$component = $matches[1][$i];
				$component_parts = explode('\\', $component);
				if (count($component_parts) > 1) {
					$name = array_pop($component_parts);
					array_push($component_parts, str_ireplace("Component", "", $name));
				}
				for ($j = 0; $j < count($component_parts); $j++) {
					$component_parts[$j] = OTools::toSnakeCase($component_parts[$j]);
				}
				array_push($ret, implode('/', $component_parts));
			}
		}

		return $ret;
	}

	/**
	 * Load a component and it's dependencies
	 *
	 * @param string $component Path/name of the component
	 *
	 * @return void
	 */
	public function loadComponent(string $component): void {
		$file = $component;

		// Check if component is in a sub-folder
		if (stripos($component, '/') !== false) {
			$data = explode('/', $component);
			$file = array_pop($data);
		}

		$component_path = $this->config->getDir('app_component').$component.'/'.$file.'.component.php';
		$template_path = $this->config->getDir('app_component').$component.'/'.$file.'.template.php';
		if (file_exists($component_path)) {
			require_once $component_path;
		}

		$subcomponents = [];
		$component_content = file_get_contents($component_path);
		$subcomponents = array_merge($subcomponents, $this->getContentComponents($component_content));
		$template_content = file_get_contents($template_path);
		$subcomponents = array_merge($subcomponents, $this->getContentComponents($template_content));

		foreach ($subcomponents as $sub) {
			$this->loadComponent($sub);
		}
	}

	/**
	 * Custom error handler, shows an error page and the error's stack trace
	 *
	 * @param Throwable $ex Given error
	 *
	 * @return void
	 */
	public function errorHandler(\Throwable $ex): void {
		$log = new OLog(get_class($this));
		$params = ['message' => OTools::getMessage('ERROR_500_LABEL')];
		if ($this->config->getEnvironment()!='prod') {
			$params['message'] = "<strong>Error:</strong> \"".$ex->getMessage()."\"\n<strong>File:</strong> \"".$ex->getFile()."\" (Line: ".$ex->getLine().")\n\n<strong>Trace:</strong> \n";
			foreach ($ex->getTrace() as $trace) {
				if (array_key_exists('file', $trace)) {
					$params['message'] .= "  <strong>File:</strong> \"".$trace['file']." (Line: ".$trace['line'].")\"\n";
				}
				if (array_key_exists('class', $trace)) {
					$params['message'] .= "  <strong>Class:</strong> \"".$trace['class']."\"\n";
				}
				if (array_key_exists('function', $trace)) {
					$params['message'] .= "  <strong>Function:</strong> \"".$trace['function']."\"\n\n";
				}
			}
		}
		$log->error( str_ireplace('</strong>', '', str_ireplace('<strong>', '', $params['message'])) );
		OTools::showErrorPage($params, '500');
	}
}
