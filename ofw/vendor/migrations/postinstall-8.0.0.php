<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Migrations;

use OsumiFramework\OFW\Core\OConfig;
use OsumiFramework\OFW\Tools\OColors;

class OPostInstall {
	private ?OColors $colors = null;
	private ?OConfig $config = null;
	private array    $messages = [
		'es' => [
			'TITLE'                          => "\nPOST INSTALL 8.0.0\n\n",
			'UPDATING_COMPONENTS'            => "  Actualizando componentes...\n",
			'COMPONENTS_UPDATED'             => "  Componentes actualizados.\n",
			'UPDATING_FILTERS'               => "  Actualizando filtros...\n",
			'FILTERS_UPDATED'                => "  Filtros actualizados.\n",
			'UPDATING_LAYOUTS'               => "  Actualizando layouts...\n",
			'LAYOUTS_UPDATED'                => "  Layouts actualizados.\n",
			'UPDATING_MODELS'                => "  Actualizando modelos...\n",
			'MODELS_UPDATED'                 => "  Modelos actualizados.\n",
			'UPDATING_MODULES'               => "  Actualizando módulos...\n",
			'MODULE_PROCESSING'              => "    Procesando módulo \"%s\"...\n",
			'MODULE_FILE_RENAMED'            => "    Archivo de módulo renombrado: \"%s\" -> \"%s\"\n",
			'MODULE_CREATE_ACTIONS_FOLDER'   => "    Creada carpeta para acciones del módulo: \"%s\"\n",
			'MODULE_SERVICES_FOUND'          => "    Servicios encontrados en el módulo: \"%s\"\n",
			'MODULE_ACTION_PROCESSING'       => "    Procesando acción \"%s\"...\n",
			'MODULE_ACTION_CREATE_FOLDER'    => "      Creada carpeta para la acción: \"%s\"\n",
			'MODULE_ACTION_CREATE_FILE'      => "      Creado archivo en blanco para la acción: \"%s\"\n",
			'MODULE_ACTION_MOVE_TEMPLATE'    => "      Movido archivo de plantilla: \"%s\" -> \"%s\"\n",
			'MODULE_TEMPLATE_FOLDER_DELETED' => "    Borrada carpeta de plantillas: \"%s\"\n",
			'MODULE_WARNING'                 => "    Módulo procesado.\n    ATENCIÓN: El contenido de las acciones debe ser copiado a mano.\n\n",
			'MODULES_UPDATED'                => "  Módulos actualizados.\n",
			'UPDATING_SERVICES'              => "  Actualizando servicios...\n",
			'SERVICES_UPDATED'               => "  Servicios actualizados.\n",
			'UPDATING_TASKS'                 => "  Actualizando tareas...\n",
			'TASKS_UPDATED'                  => "  Tareas actualizadas.\n",
			'URL_CACHE_DELETE'               => "  Archivo cache de URLs borrado: \"%s\"\n",
			'END_TITLE'                      => "\nPOST INSTALL 8.0.0 finalizado.\n\n"
		],
		'en' => [
			'TITLE'                          => "\nPOST INSTALL 8.0.0\n\n",
			'UPDATING_COMPONENTS'            => "  Updating components...\n",
			'COMPONENTS_UPDATED'             => "  Components updated.\n",
			'UPDATING_FILTERS'               => "  Updating filters...\n",
			'FILTERS_UPDATED'                => "  Filters updated.\n",
			'UPDATING_LAYOUTS'               => "  Updating layouts...\n",
			'LAYOUTS_UPDATED'                => "  Layouts updated.\n",
			'UPDATING_MODELS'                => "  Updating models...\n",
			'MODELS_UPDATED'                 => "  Models updated.\n",
			'UPDATING_MODULES'               => "  Updating modules...\n",
			'MODULE_PROCESSING'              => "    Processing module \"%s\"...\n",
			'MODULE_FILE_RENAMED'            => "    Module file renamed: \"%s\" -> \"%s\"\n",
			'MODULE_CREATE_ACTIONS_FOLDER'   => "    Created folder for module's actions: \"%s\"\n",
			'MODULE_SERVICES_FOUND'          => "    Services found on the module: \"%s\"\n",
			'MODULE_ACTION_PROCESSING'       => "    Processing action \"%s\"...\n",
			'MODULE_ACTION_CREATE_FOLDER'    => "      Created folder for the action: \"%s\"\n",
			'MODULE_ACTION_CREATE_FILE'      => "      Created blank file for the action: \"%s\"\n",
			'MODULE_ACTION_MOVE_TEMPLATE'    => "      Template file moved: \"%s\" -> \"%s\"\n",
			'MODULE_TEMPLATE_FOLDER_DELETED' => "    Template folder deleted: \"%s\"\n",
			'MODULE_WARNING'                 => "    Module processed.\n    WARNING: The content of the actions must be copied manually.\n\n",
			'MODULES_UPDATED'                => "  Modules updated.\n",
			'UPDATING_SERVICES'              => "  Updating services...\n",
			'SERVICES_UPDATED'               => "  Services updated.\n",
			'UPDATING_TASKS'                 => "  Updating tasks...\n",
			'TASKS_UPDATED'                  => "  Tasks updated.\n",
			'URL_CACHE_DELETE'               => "  URL cache file deleted: \"%s\"\n",
			'END_TITLE'                      => "\nPOST INSTALL 8.0.0 finished.\n\n"
		],
		'eu' => [
			'TITLE'                          => "\nPOST INSTALL 8.0.0\n\n",
			'UPDATING_COMPONENTS'            => "  Eguneratzen osagaiak...\n",
			'COMPONENTS_UPDATED'             => "  Osagaiak eguneratu dira.\n",
			'UPDATING_FILTERS'               => "  Eguneratzen iragazkiak...\n",
			'FILTERS_UPDATED'                => "  Iragazkiak eguneratu dira.\n",
			'UPDATING_LAYOUTS'               => "  Eguneratzen layout-ak...\n",
			'LAYOUTS_UPDATED'                => "  Layout-ak eguneratu dira.\n",
			'UPDATING_MODELS'                => "  Eguneratzen modeloak...\n",
			'MODELS_UPDATED'                 => "  Modeloak eguneratu dira.\n",
			'UPDATING_MODULES'               => "  Eguneratzen moduluak...\n",
			'MODULE_PROCESSING'              => "    \"%s\" modulua prozesatzen...\n",
			'MODULE_FILE_RENAMED'            => "    Izena aldatuta modulu fitxategiari: \"%s\" -> \"%s\"\n",
			'MODULE_CREATE_ACTIONS_FOLDER'   => "    Sortuta karpeta moduluaren ekintzentzat: \"%s\"\n",
			'MODULE_SERVICES_FOUND'          => "    Moduluan aurkitutako zerbitzuak: \"%s\"\n",
			'MODULE_ACTION_PROCESSING'       => "    \"%s\" ekintza prozesatzen...\n",
			'MODULE_ACTION_CREATE_FOLDER'    => "      Sortuta karpeta ekintzarentzat: \"%s\"\n",
			'MODULE_ACTION_CREATE_FILE'      => "      Sortuta fitxategia hutsik ekintzarentzat: \"%s\"\n",
			'MODULE_ACTION_MOVE_TEMPLATE'    => "      Txantiloia mugituta: \"%s\" -> \"%s\"\n",
			'MODULE_TEMPLATE_FOLDER_DELETED' => "    Ezabatuta txantiloien karpeta: \"%s\"\n",
			'MODULE_WARNING'                 => "    Modulua prozesatu da.\n    OHARRA: Ekintzen edukia eskuz kopiatu behar da.\n\n",
			'MODULES_UPDATED'                => "  Moduluak eguneratu dira.\n",
			'UPDATING_SERVICES'              => "  Eguneratzen zerbitzuak...\n",
			'SERVICES_UPDATED'               => "  Zerbitzuak eguneratu dira.\n",
			'UPDATING_TASKS'                 => "  Eguneratzen atazak...\n",
			'TASKS_UPDATED'                  => "  Atazak eguneratu dira.\n",
			'URL_CACHE_DELETE'               => "  URLen cache-fitxategia ezabatu da: \"%s\"\n",
			'END_TITLE'                      => "\nPOST INSTALL 8.0.0 bukatu du.\n\n"
		]
	];

	/**
	 * Store global configuration locally
	 */
	public function __construct() {
		global $core;
		$this->config = $core->config;
		$this->colors = new OColors();
	}

	/**
	 * Convert underscore notation (snake case) to camel case (eg id_user -> idUser)
	 *
	 * @param string $string Text string to convert
	 *
	 * @param bool $capitalizeFirstCharacter Should first letter be capitalized or not, defaults to no
	 *
	 * @return string Converted text string
	 */
	private function underscoresToCamelCase(string $string, bool $capitalizeFirstCharacter = false): string {
		$str = str_replace('_', '', ucwords($string, '_'));

		if (!$capitalizeFirstCharacter) {
			$str = lcfirst($str);
		}

		return $str;
	}

	/**
	 * Search the given folder looking for components and update them to the new OComponent structure
	 */
	public function searchAndUpdateComponents(string $path): void {
		if ($folder = opendir($path)) {
			while (false !== ($file = readdir($folder))) {
				if ($file != '.' && $file != '..') {
					// It is a component folder if there is a file named like the folder with the '.php' extension on it
					if (is_dir($path.$file)) {
						if (file_exists($path.$file.'/'.$file.'.php')) {
							rename($path.$file.'/'.$file.'.php', $path.$file.'/'.$file.'.template.php');

							$component_content = "<"."?php declare(strict_types=1);\n\n";
							$component_content .= "namespace OsumiFramework\App\Component;\n\n";
							$component_content .= "use OsumiFramework\OFW\Core\OComponent;\n\n";
							$component_content .= "class ".$this->underscoresToCamelCase($file, true)."Component extends OComponent {}\n";
							$component_path = $path.$file.'/'.$file.'.component.php';

							file_put_contents($component_path, $component_content);
						}
						else {
							$this->searchAndUpdateComponents($path.$file.'/');
						}
					}
				}
			}
		}
	}

	/**
	 * Runs the v8.0.0 update post-installation tasks
	 *
	 * @return string
	 */
	public function run(): string {
		$ret = '';
		// Start
		$ret .= $this->messages[$this->config->getLang()]['TITLE'];

		// Update components to the new OComponent
		$ret .= $this->messages[$this->config->getLang()]['UPDATING_COMPONENTS'];

		$this->searchAndUpdateComponents($this->config->getDir('app_component'));

		$ret .= $this->messages[$this->config->getLang()]['COMPONENTS_UPDATED'];

		// Update the filters to the new naming convention
		$ret .= $this->messages[$this->config->getLang()]['UPDATING_FILTERS'];

		$filters_path = $this->config->getDir('app_filter');
		if (file_exists($filters_path)) {
			if ($folder = opendir($filters_path)) {
				while (false !== ($file = readdir($folder))) {
					if ($file != '.' && $file != '..' && stripos($file, '.filter.') === false) {
						$name_data = explode('.', str_replace('Filter', '', $file));
						$ext = array_pop($name_data);
						array_push($name_data, 'filter');
						array_push($name_data, $ext);
						$new_name = implode('.', $name_data);

						rename($filters_path.$file, $filters_path.$new_name);
					}
				}
				closedir($folder);
			}
		}

		$ret .= $this->messages[$this->config->getLang()]['FILTERS_UPDATED'];

		// Update the layouts to the new naming convention
		$ret .= $this->messages[$this->config->getLang()]['UPDATING_LAYOUTS'];

		$layout_path = $this->config->getDir('app_layout');
		if (file_exists($layout_path)) {
			if ($folder = opendir($layout_path)) {
				while (false !== ($file = readdir($folder))) {
					if ($file != '.' && $file != '..' && stripos($file, '.layout.') === false) {
						$name_data = explode('.', $file);
						$ext = array_pop($name_data);
						array_push($name_data, 'layout');
						array_push($name_data, $ext);
						rename($layout_path.$file, $layout_path.implode('.', $name_data));
					}
				}
				closedir($folder);
			}
		}

		$ret .= $this->messages[$this->config->getLang()]['LAYOUTS_UPDATED'];

		// Update the models to the new naming convention, and remove $table_name
		$ret .= $this->messages[$this->config->getLang()]['UPDATING_MODELS'];

		$model_path = $this->config->getDir('app_model');
		if (file_exists($model_path)) {
			if ($folder = opendir($model_path)) {
				while (false !== ($file = readdir($folder))) {
					if ($file != '.' && $file != '..' && stripos($file, '.model.') === false) {
						$model_content = file_get_contents($model_path.$file);
						preg_match('/\$table_name = \'(.*?)\';/i', $model_content, $matches);

						$table_name = $matches[1];
						$model_content = preg_replace('/^(\s+)\$table_name(\s+)=(\s+)\'(.*?)\';$/mi', '', $model_content);
						$model_content = str_ireplace('parent::load($table_name, $model);', 'parent::load($model);', $model_content);
						$model_content = str_ireplace("function __construct() {\n\n", "function __construct() {\n", $model_content);
						file_put_contents($model_path.$file, $model_content);

						rename($model_path.$file, $model_path.$table_name.'.model.php');
					}
				}
				closedir($folder);
			}
		}

		$ret .= $this->messages[$this->config->getLang()]['MODELS_UPDATED'];

		// Update the modules to the new structure system
		$ret .= $this->messages[$this->config->getLang()]['UPDATING_MODULES'];

		$module_path = $this->config->getDir('app_module');
		if (file_exists($module_path)) {
			if ($folder = opendir($module_path)) {
				// Every folder on app_module is a module
				while (false !== ($file = readdir($folder))) {
					if ($file != '.' && $file != '..') {
						$ret .= sprintf($this->messages[$this->config->getLang()]['MODULE_PROCESSING'],
							$this->colors->getColoredString($file, 'light_green')
						);
						$old_module_path = $module_path.$file."/".$file.".php";
						$new_module_path = $module_path.$file."/".$file.".module.php";

						// Rename module to the new convention
						rename($old_module_path, $new_module_path);

						$ret .= sprintf($this->messages[$this->config->getLang()]['MODULE_FILE_RENAMED'],
							$this->colors->getColoredString($module_path.$file.'/'.$file.'.php', 'light_green'),
							$this->colors->getColoredString($module_path.$file.'/'.$file.'.module.php', 'light_green'),
						);

						// Create the new actions folder
						$actions_folder = $module_path.$file.'/actions';
						mkdir($actions_folder);

						$ret .= sprintf($this->messages[$this->config->getLang()]['MODULE_CREATE_ACTIONS_FOLDER'],
							$this->colors->getColoredString($actions_folder, 'light_green')
						);

						$module_template_folder = $module_path.$file.'/template';

						// Read module files content
						$module_content = file_get_contents($new_module_path);

						// Search all actions
						preg_match_all('/function (.*?)\(/', $module_content, $action_matches);
						$action_list = [];
						for ($i=0; $i<count($action_matches[1]); $i++) {
							if (trim($action_matches[1][$i]) != '__construct') {
								array_push($action_list, trim($action_matches[1][$i]));
							}
						}

						// Update module class name
						$module_content = str_ireplace("class ".$file." extends OModule", "class ".$file."Module", $module_content);
						$module_content_array = explode("\n", $module_content);
						$module_content_array_result = [];
						for ($i=0; $i<count($module_content_array); $i++) {
							if (!preg_match('/^use (.*?);$/', $module_content_array[$i])) {
								array_push($module_content_array_result, $module_content_array[$i]);
							}
						}
						$module_content = implode("\n", $module_content_array_result);
						$module_content = str_ireplace("namespace OsumiFramework\App\Module;\n\n\n", "namespace OsumiFramework\App\Module;\n\n", $module_content);

						if (stripos($module_content, ";\n\n#[ORoute(") !== false) {
							$module_content = str_ireplace(
								";\n\n#[ORoute(\n",
								";\n\nuse OsumiFramework\OFW\Routing\OModule;\n\n#[OModule(\n\tactions: '".implode(', ', $action_list)."',\n",
								$module_content
							);
						}
						else {
							$module_content = str_ireplace(
								"class ".$file."Module",
								"use OsumiFramework\OFW\Routing\OModule;\n\n#[OModule(\n\tactions: '".implode(', ', $action_list)."'\n)]\nclass ".$file."Module",
								$module_content
							);
						}
						file_put_contents($new_module_path, $module_content);

						// Search all services used in the module
						preg_match('/private \?(.*?)Service/i', $module_content, $service_matches);
						$service_list = [];
						for ($i=1; $i<count($service_matches); $i++) {
							array_push($service_list, $service_matches[$i]);
						}

						if (count($service_list) > 0) {
							$ret .= sprintf($this->messages[$this->config->getLang()]['MODULE_SERVICES_FOUND'],
								$this->colors->getColoredString(implode(', ', $service_list), 'light_green')
							);
						}

						// For every action
						foreach ($action_list as $action_name) {
							$ret .= sprintf($this->messages[$this->config->getLang()]['MODULE_ACTION_PROCESSING'],
								$this->colors->getColoredString($action_name, 'light_green')
							);

							// Create actions folder
							mkdir($actions_folder.'/'.$action_name);

							$ret .= sprintf($this->messages[$this->config->getLang()]['MODULE_ACTION_CREATE_FOLDER'],
								$this->colors->getColoredString($actions_folder.'/'.$action_name, 'light_green')
							);

							// Get actions parameter and type
							preg_match('/function '.$action_name.'\((.*?)\): void/', $module_content, $function_data);
							$data_parameter = $function_data[1];
							if (stripos($data_parameter, 'ORequest') === false) {
								$data = explode(' ', $data_parameter);
								$data_namespace = "use OsumiFramework\App\DTO\\".$data[0].";\n\n";
								preg_match('/\* @param '.$data[0].' (.*?)$/m', $module_content, $data_info_match);
								$data_info = "	 * @param ".$data[0]." ".$data_info_match[1]."\n";
							}
							else {
								$data_namespace = "use OsumiFramework\OFW\Web\ORequest;\n\n";
								$data_info = "	 * @param ORequest $"."req Request object with method, headers, parameters and filters used\n";
							}

							$action_file = $actions_folder.'/'.$action_name.'/'.$action_name.'.action.php';

							// Create blank action file
							$action_content = "<"."?php declare(strict_types=1);\n\n";
							$action_content .= "namespace OsumiFramework\App\Module\Action;\n\n";
							$action_content .= "use OsumiFramework\OFW\Routing\OModuleAction;\n";
							$action_content .= "use OsumiFramework\OFW\Routing\OAction;\n";
							$action_content .= $data_namespace;
							$action_content .= "#[OModuleAction(\n";
							$action_content .= "	url: '/".$action_name."'";
							if (count($service_list) > 0) {
								$action_content .= ",\n	services: '".implode(', ', $service_list)."'";
							}
							$action_content .= "\n)]\n";
							$action_content .= "class ".$action_name."Action extends OAction {\n";
							$action_content .= "	/"."**\n";
							$action_content .= "	 * Function description\n";
							$action_content .= "	 *\n";
							$action_content .= $data_info;
							$action_content .= "	 * @return void\n";
							$action_content .= "	 *"."/\n";
							$action_content .= "	public function run(".$data_parameter."):void {\n";
							$action_content .= "		\n";
							$action_content .= "	}\n";
							$action_content .= "}";

							file_put_contents($action_file, $action_content);

							$ret .= sprintf($this->messages[$this->config->getLang()]['MODULE_ACTION_CREATE_FILE'],
								$this->colors->getColoredString($action_file, 'light_green')
							);

							// Get actions template files extension
							$template_files = glob($module_template_folder.'/'.$action_name.'.*');

							if (count($template_files) > 0) {
								$template_data = explode('.', $template_files[0]);
								$template_ext = array_pop($template_data);

								// Move template file to actions folder with the new naming convention
								$old_template_path = $module_template_folder.'/'.$action_name.'.'.$template_ext;
								$action_template = $actions_folder.'/'.$action_name.'/'.$action_name.'.action.'.$template_ext;

								rename($old_template_path, $action_template);

								$ret .= sprintf($this->messages[$this->config->getLang()]['MODULE_ACTION_MOVE_TEMPLATE'],
									$this->colors->getColoredString($old_template_path, 'light_green'),
									$this->colors->getColoredString($action_template, 'light_green')
								);
							}
						}

						// After all templates have been moved template folder will be empty so delete it
						if (file_exists($module_template_folder)) {
							rmdir($module_template_folder);

							$ret .= sprintf($this->messages[$this->config->getLang()]['MODULE_TEMPLATE_FOLDER_DELETED'],
								$this->colors->getColoredString($module_template_folder, 'light_green')
							);
						}

						$ret .= sprintf($this->messages[$this->config->getLang()]['MODULE_WARNING'],
							$this->colors->getColoredString($file, 'light_green')
						);
					}
				}
			}
		}

		$ret .= $this->messages[$this->config->getLang()]['MODULES_UPDATED'];

		// Update the services to the new naming convention
		$ret .= $this->messages[$this->config->getLang()]['UPDATING_SERVICES'];

		$service_path = $this->config->getDir('app_service');
		if (file_exists($service_path)) {
			if ($folder = opendir($service_path)) {
				while (false !== ($file = readdir($folder))) {
					if ($file != '.' && $file != '..' && stripos($file, '.service.') === false) {
						$name_data = explode('.', $file);
						$ext = array_pop($name_data);
						array_push($name_data, 'service');
						array_push($name_data, $ext);
						rename($service_path.$file, $service_path.implode('.', $name_data));
					}
				}
				closedir($folder);
			}
		}

		$ret .= $this->messages[$this->config->getLang()]['SERVICES_UPDATED'];

		// Update the tasks to the new naming convention
		$ret .= $this->messages[$this->config->getLang()]['UPDATING_TASKS'];

		$task_path = $this->config->getDir('app_task');
		if (file_exists($task_path)) {
			if ($folder = opendir($task_path)) {
				while (false !== ($file = readdir($folder))) {
					if ($file != '.' && $file != '..') {
						$name_data = explode('.', $file);
						$ext = array_pop($name_data);
						array_push($name_data, 'task');
						array_push($name_data, $ext);
						rename($task_path.$file, $task_path.implode('.', $name_data));
					}
				}
				closedir($folder);
			}
		}

		$ret .= $this->messages[$this->config->getLang()]['TASKS_UPDATED'];

		// Delete the URL cache file
		$url_cache_file = $this->config->getDir('ofw_cache').'urls.cache.json';
		if (file_exists($url_cache_file)) {
			unlink($url_cache_file);
			$ret .= sprintf($this->messages[$this->config->getLang()]['URL_CACHE_DELETE'],
				$this->colors->getColoredString($url_cache_file, 'light_green')
			);
		}

		// End
		$ret .= $this->messages[$this->config->getLang()]['END_TITLE'];

		return $ret;
	}
}
