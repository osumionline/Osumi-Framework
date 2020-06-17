<?php declare(strict_types=1);
class OPostInstall {
	private ?OColors $colors = null;
	private ?OConfig $config = null;
	private ?string  $controller_path = null;
	private ?string  $module_path = null;
	private ?string  $template_path = null;
	private ?array   $urls = null;
	private array    $messages = [
		'es' => [
					'TITLE'                             => "\n\nPOST INSTALL 5.8.0\n\n",
					'NEW_MODULES_FOLDER'                => "  Nueva carpeta para módulos: \"%s\".\n",
					'DELETE_CONTROLLER_FOLDER'          => "  Carpeta controllers borrada.\n",
					'MODULES_UPDATING'                  => "  Actualizando módulos...\n",
					'CONTROLLER_UPDATED'                => "    Controller actualizado para que use OModule.\n",
					'NEW_MODULE_FOLDER'                 => "    Nueva carpeta para el módulo: \"%s\".\n",
					'NEW_MODULE_TEMPLATE_FOLDER'        => "    Nueva carpeta para templates en el módulo: \"%s\".\n",
					'MOVE_MODULE_CONTROLLER'            => "    Controller movido a carpeta de módulo: \"%s\" -> \"%s\".\n",
					'MOVE_TEMPLATE_FILE'                => "    Archivo de template movido a carpeta de módulo: \"%s\" -> \"%s\".\n",
					'DELETE_CONTROLLER_TEMPLATE_FOLDER' => "    Carpeta de template antigua borrada: \"%s\".\n\n",
					'END_TITLE'                         => "\n\nPOST INSTALL 5.8.0 finalizado.\n\n"
				],
		'en' => [
					'TITLE'                             => "\n\nPOST INSTALL 5.8.0\n\n",
					'NEW_MODULES_FOLDER'                => "  New modules folder: \"%s\".\n",
					'DELETE_CONTROLLER_FOLDER'          => "  Controllers folder deleted.\n",
					'MODULES_UPDATING'                  => "  Updating modules...\n",
					'CONTROLLER_UPDATED'                => "    Controller updated to use OModule.\n",
					'NEW_MODULE_FOLDER'                 => "    New module folder: \"%s\".\n",
					'NEW_MODULE_TEMPLATE_FOLDER'        => "    New folder for templates in the module: \"%s\".\n",
					'MOVE_MODULE_CONTROLLER'            => "    Controller moved to module folder: \"%s\" -> \"%s\".\n",
					'MOVE_TEMPLATE_FILE'                => "    Template file moved to module folder: \"%s\" -> \"%s\".\n",
					'DELETE_CONTROLLER_TEMPLATE_FOLDER' => "    Old template folder deleted: \"%s\".\n\n",
					'END_TITLE'                         => "\n\nPOST INSTALL 5.8.0 finished.\n\n"
				]
	];

	/**
	 * Store global configuration locally
	 */
	public function __construct() {
		global $core;
		$this->colors = new OColors();
		$this->config = $core->config;
		$this->controller_path = $this->config->getDir('app').'controller';
		$this->module_path = $this->config->getDir('app').'module';
		$this->template_path = $this->config->getDir('app_template');

		$this->urls = json_decode( file_get_contents( $this->config->getDir('app_cache').'urls.cache.json' ), true );
	}

	private function getType(string $module, string $action): string {
		$type = 'html';

		foreach ($this->urls as $url) {
			if ($url['module']==$module && $url['action']==$action) {
				if (array_key_exists('type', $url)) {
					$type = $url['type'];
				}
				break;
			}
		}

		return $type;
	}

	/**
	 * Updates a controller and moves required files to new folders
	 *
	 * @param string $controller Name of the controller file to be updated
	 *
	 * @return string Information about the process
	 */
	private function updateController(string $controller): string {
		$ret = '';
		$path = $this->controller_path.'/'.$controller.'.php';

		// Actualizo para que use OModule en vez de OController
		$content = file_get_contents($path);
		$content = str_ireplace(' extends OController {', ' extends OModule {', $content);
		file_put_contents($path, $content);

		$ret .= sprintf($this->messages[$this->config->getLang()]['CONTROLLER_UPDATED'],
			$this->colors->getColoredString($controller, 'light_green')
		);

		// Creo carpeta app/module/controller
		$new_module_path = $this->module_path.'/'.$controller;
		$ret .= sprintf($this->messages[$this->config->getLang()]['NEW_MODULE_FOLDER'],
			$this->colors->getColoredString($new_module_path, 'light_green')
		);
		mkdir($new_module_path);

		// Creo carpeta app/module/controller/template
		$new_module_template_path = $new_module_path.'/template';
		$ret .= sprintf($this->messages[$this->config->getLang()]['NEW_MODULE_TEMPLATE_FOLDER'],
			$this->colors->getColoredString($new_module_template_path, 'light_green')
		);
		mkdir($new_module_template_path);

		// Muevo app/controller/controller.php a app/module/controller/controller.php
		$ret .= sprintf($this->messages[$this->config->getLang()]['MOVE_MODULE_CONTROLLER'],
			$this->colors->getColoredString($path, $new_module_path.'/'.$controller.'.php', 'light_green')
		);
		rename($path, $new_module_path.'/'.$controller.'.php');

		// Muevo cada archivo de template que tuviese en app/template
		$controller_template_path = $this->template_path.$controller;
		if (file_exists($controller_template_path)) {
			if ($model = opendir($controller_template_path)) {
				while (false !== ($entry = readdir($model))) {
					if ($entry != '.' && $entry != '..') {
						$action = str_ireplace('.php', '', $entry);
						$type = $this->getType($controller, $action);
						$ret .= sprintf($this->messages[$this->config->getLang()]['MOVE_TEMPLATE_FILE'],
							$this->colors->getColoredString($controller_template_path.'/'.$entry, 'light_green'),
							$this->colors->getColoredString($new_module_template_path.'/'.$action.'.'.$type, 'light_green')
						);
						rename($controller_template_path.'/'.$entry, $new_module_template_path.'/'.$action.'.'.$type);
					}
				}
				closedir($model);
			}
			$ret .= sprintf($this->messages[$this->config->getLang()]['DELETE_CONTROLLER_TEMPLATE_FOLDER'],
				$this->colors->getColoredString($controller_template_path, 'light_green')
			);
			rmdir($controller_template_path);
		}

		return $ret;
	}

	/**
	 * Runs the v5.8.0 update post-installation tasks
	 *
	 * @return string
	 */
	public function run(): string {
		$ret = '';
		$ret .= $this->messages[$this->config->getLang()]['TITLE'];

		$ret .= sprintf($this->messages[$this->config->getLang()]['NEW_MODULES_FOLDER'],
			$this->colors->getColoredString($this->module_path, 'light_green')
		);
		mkdir($this->module_path);

		$ret .= $this->messages[$this->config->getLang()]['MODULES_UPDATING'];
		if (file_exists($this->controller_path)) {
			if ($model = opendir($this->controller_path)) {
				while (false !== ($entry = readdir($model))) {
					if ($entry != '.' && $entry != '..') {
						$controller = str_ireplace('.php', '', $entry);
						$ret .= $this->updateController($controller);
					}
				}
				closedir($model);
			}
		}

		$ret .= $this->messages[$this->config->getLang()]['DELETE_CONTROLLER_FOLDER'];
		rmdir($this->controller_path);

		$ret .= $this->messages[$this->config->getLang()]['END_TITLE'];

		return $ret;
	}
}