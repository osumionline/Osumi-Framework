<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Migrations;

use OsumiFramework\OFW\Core\OConfig;
use OsumiFramework\OFW\Tools\OColors;
use OsumiFramework\OFW\Tools\OTools;

class OPostInstall {
	private ?OColors $colors = null;
	private ?OConfig $config = null;
	private array    $messages = [
		'es' => [
			'TITLE'               => "\nPOST INSTALL 8.1.0\n\n",
			'UPDATING_COMPONENTS' => "  Actualizando componentes...\n",
			'COMPONENTS_UPDATED'  => "  Componentes actualizados.\n",
			'UPDATING_MODULES'    => "  Actualizando módulos...\n",
			'MODULES_UPDATED'     => "  Módulos actualizados.\n",
			'UPDATING_DTOS'       => "  Actualizando DTOs...\n",
			'DTOS_UPDATED'        => "  DTOs actualizados.\n",
      'URL_CACHE_DELETE'    => "  Archivo cache de URLs borrado: \"%s\"\n",
			'END_TITLE'           => "\nPOST INSTALL 8.1.0 finalizado.\n\n"
		],
		'en' => [
			'TITLE'               => "\nPOST INSTALL 8.1.0\n\n",
			'UPDATING_COMPONENTS' => "  Updating components...\n",
			'COMPONENTS_UPDATED'  => "  Components updated.\n",
			'UPDATING_MODULES'    => "  Updating modules...\n",
			'MODULES_UPDATED'     => "  Modules updated.\n",
			'UPDATING_DTOS'       => "  Updating DTOs...\n",
			'DTOS_UPDATED'        => "  DTOs updated.\n",
      'URL_CACHE_DELETE'    => "  URL cache file deleted: \"%s\"\n",
			'END_TITLE'           => "\nPOST INSTALL 8.1.0 finished.\n\n"
		],
		'eu' => [
			'TITLE'               => "\nPOST INSTALL 8.1.0\n\n",
			'UPDATING_COMPONENTS' => "  Eguneratzen konponenteak...\n",
			'COMPONENTS_UPDATED'  => "  Konponenteak eguneratu dira.\n",
			'UPDATING_MODULES'    => "  Eguneratzen moduluak...\n",
			'MODULES_UPDATED'     => "  Moduluak eguneratu dira.\n",
			'UPDATING_DTOS'       => "  Eguneratzen DTOak...\n",
			'DTOS_UPDATED'        => "  DTOak eguneratu dira.\n",
      'URL_CACHE_DELETE'    => "  URLen cache-fitxategia ezabatu da: \"%s\"\n",
			'END_TITLE'           => "\nPOST INSTALL 8.1.0 bukatu du.\n\n"
		]
	];

	private array $replaces = [];

	/**
	 * Store global configuration locally
	 */
	public function __construct() {
		global $core;
		$this->config = $core->config;
		$this->colors = new OColors();
	}

	/**
	 * Update components and save update history to update modules after this job
	 *
	 * @param string $path Path from where to look upon searching for components
	 *
	 * @return void
	 */
	private function updateComponents(string $path): void {
		if ($folder = opendir($path)) {
			// Recorrer path
			while (false !== ($file = readdir($folder))) {
				if ($file != '.' && $file != '..') {
					$check_path = $path.$file.'/'.$file.'.component.php'; // ejemplo: /var/www/vhosts/osumi.es/dev.osumi.es/app/component/home/photo_list/photo_list.php
					// Si existe file/file.component.php es un componente
					if (file_exists($check_path)) {
						$partial_path = str_ireplace($this->config->getDir('app_component'), '', $check_path); // Quito el principio, ejemplo: home/photo_list/photo_list.php
						$partial_path = str_ireplace('/'.$file.'/'.$file.'.component.php', '', $partial_path); // Quito el final, ejemplo: home
						// Exploto por / y a cada pieza underscoresToCamelCase, vuelvo a juntar piezas con \
						$partial_path_parts = explode('/', $partial_path);
						for ($i = 0; $i < count($partial_path_parts); $i++) {
							$partial_path_parts[$i] = OTools::underscoresToCamelCase($partial_path_parts[$i], true);
						}
						$partial_path = implode('\\', $partial_path_parts);
						// Leo archivo ruta_completa
						$component_content = file_get_contents($check_path);
						$component_content = str_ireplace(
							'namespace OsumiFramework\\App\\Component;',
							'namespace OsumiFramework\\App\\Component\\' . $partial_path . ';',
							$component_content
						);
						// Busco depends
						$depends_pattern = '/\n\spublic array \\$depends = \[(.*?)];\n/m';
						$result = preg_match($depends_pattern, $component_content, $depends_match);
						if ($result === 1) {
							$replace = '/\n\spublic array \\$depends = \['.str_ireplace('/', '\/', $depends_match[1]).'];\n/m';
							$component_content = preg_replace($replace, '', $component_content);
						}
						file_put_contents($check_path, $component_content);
						// Obtengo nombre de la clase
						preg_match("/^class (.*?) extends OComponent/m", $component_content, $name_match);
						// Guardo reemplazo
						$this->replaces['use OsumiFramework\\App\\Component\\'.$name_match[1].';'] = 'use OsumiFramework\\App\\Component\\'.$partial_path.'\\'.$name_match[1].';';
					}
					else {
						$this->updateComponents($path . $file . '/');
					}
				}
			}
		}
	}

	/**
	 * Update component templates
	 *
	 * @param string $path Path from where to look upon searching for components
	 *
	 * @return void
	 */
	private function updateComponentTemplates(string $path): void {
		if ($folder = opendir($path)) {
			// Recorrer path
			while (false !== ($file = readdir($folder))) {
				if ($file != '.' && $file != '..') {
					$check_path = $path.$file.'/'.$file.'.template.php';
					// Si existe file/file.component.php es un componente
					if (file_exists($check_path)) {
						$template_content = file_get_contents($check_path);
						foreach ($this->replaces as $old => $new) {
							$template_content = str_ireplace($old, $new, $template_content);
						}
						file_put_contents($check_path, $template_content);
					}
					else {
						$this->updateComponentTemplates($path . $file . '/');
					}
				}
			}
		}
	}

	/**
	 * Function to update all modules: update components "use" lines and remove "components" section from OModuleAction declaration
	 *
	 * @return void
	 */
	private function updateModules(): void {
		if ($folder = opendir($this->config->getDir('app_module'))) {
			// Recorrer módulos
			while (false !== ($module = readdir($folder))) {
				if ($module != '.' && $module != '..') {
					$actions_path = $this->config->getDir('app_module').$module.'/actions/';
					if ($actions_folder = opendir($actions_path)) {
						// Recorrer acciones
						while (false !== ($action = readdir($actions_folder))) {
							if ($action != '.' && $action != '..') {
								$action_path = $actions_path.$action.'/'.$action.'.action.php';
								$action_content = file_get_contents($action_path);
								$result = preg_match("/,\n\scomponents: \[(.*?)]/m", $action_content, $component_match);

								// Si la acción usa componentes
								if ($result === 1) {
									// Renombro a la nueva estructura de carpetas/namespaces
									foreach ($this->replaces as $old => $new) {
										$action_content = str_ireplace($old, $new, $action_content);
									}
									// Quito la línea de "components" en OModuleAction
									$replace = "/,\n\scomponents: \[".str_ireplace("/", "\/", $component_match[1])."]/m";
									$action_content = preg_replace($replace, "", $action_content);
									file_put_contents($action_path, $action_content);
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Function to update all DTO file names to the new convention: userDTO.php -> user.dto.php
	 *
	 * @return void
	 */
	private function updateDTOs(): void {
		$list = [];
		if ($folder = opendir($this->config->getDir('app_dto'))) {
			// Recorrer DTOs
			while (false !== ($dto = readdir($folder))) {
				if ($dto != '.' && $dto != '..') {
					array_push($list, $dto);
				}
			}
		}

		foreach ($list as $item) {
			$new_name = str_ireplace("DTO.php", ".dto.php", $item);
			$new_name_parts = explode('.', $new_name);
			$new_name_parts[0] = OTools::toSnakeCase($new_name_parts[0]);
			$new_name = implode('.', $new_name_parts);
			rename($this->config->getDir('app_dto').$item, $this->config->getDir('app_dto').$new_name);
		}
	}

	/**
	 * Runs the v8.1.0 update post-installation tasks
	 *
	 * @return string
	 */
	public function run(): string {
		$ret = '';
		// Start
		$ret .= $this->messages[$this->config->getLang()]['TITLE'];

		// Update components
		$ret .= $this->messages[$this->config->getLang()]['UPDATING_COMPONENTS'];

		$this->updateComponents($this->config->getDir('app_component'));
		$this->updateComponentTemplates($this->config->getDir('app_component'));

		$ret .= $this->messages[$this->config->getLang()]['COMPONENTS_UPDATED'];

		// Update modules

		$ret .= $this->messages[$this->config->getLang()]['UPDATING_MODULES'];

		$this->updateModules();

		$ret .= $this->messages[$this->config->getLang()]['MODULES_UPDATED'];

		// Update DTOs

		$ret .= $this->messages[$this->config->getLang()]['UPDATING_DTOS'];

		$this->updateDTOs();

		$ret .= $this->messages[$this->config->getLang()]['DTOS_UPDATED'];

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
