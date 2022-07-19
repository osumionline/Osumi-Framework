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
			'TITLE'            => "\nPOST INSTALL 8.1.1\n\n",
			'UPDATING_MODELS'  => "  Actualizando modelos...\n",
			'UPDATED_MODEL'    => "    Modelo \"%s\" actualizado.\n",
			'MODELS_UPDATED'   => "  Modelos actualizados.\n",
			'UPDATING_SERVICES'=> "  Actualizando servicios...\n",
			'UPDATED_SERVICE'  => "    Servicio \"%s\" actualizado.\n",
			'SERVICES_UPDATED' => "  Servicios actualizados.\n",
			'UPDATING_TASKS'   => "  Actualizando tareas...\n",
			'UPDATED_TASK'     => "    Tarea \"%s\" actualizada.\n",
			'TASKS_UPDATED'    => "  Tareas actualizados.\n",
			'URL_CACHE_DELETE' => "  Archivo cache de URLs borrado: \"%s\"\n",
			'END_TITLE'        => "\nPOST INSTALL 8.1.1 finalizado.\n\n"
		],
		'en' => [
			'TITLE'             => "\nPOST INSTALL 8.1.1\n\n",
			'UPDATING_MODELS'   => "  Updating models...\n",
			'UPDATED_MODEL'     => "    Model \"%s\" updated.",
			'MODELS_UPDATED'    => "  Models updated.\n",
			'UPDATING_SERVICES' => "  Updating services...\n",
			'UPDATED_SERVICE'   => "    Service \"%s\" updated.",
			'SERVICES_UPDATED'  => "  Services updated.\n",
			'UPDATING_TASKS'    => "  Updating tasks...\n",
			'UPDATED_TASK'      => "    Task \"%s\" updated.",
			'TASKS_UPDATED'     => "  Tasks updated.\n",
			'URL_CACHE_DELETE'  => "  URL cache file deleted: \"%s\"\n",
			'END_TITLE'         => "\nPOST INSTALL 8.1.1 finished.\n\n"
		],
		'eu' => [
			'TITLE'             => "\nPOST INSTALL 8.1.1\n\n",
			'UPDATING_MODELS'   => "  Eguneratzen modeloak...\n",
			'UPDATED_MODEL'     => "    \"%s\" modeloa eguneratu da.",
			'MODELS_UPDATED'    => "  Modeloak eguneratu dira.\n",
			'UPDATING_SERVICES' => "  Eguneratzen zerbitzuak...\n",
			'UPDATED_SERVICE'   => "    \"%s\" zerbitzua eguneratu da.",
			'SERVICES_UPDATED'  => "  Zerbitzuak eguneratu dira.\n",
			'UPDATING_TASKS'    => "  Eguneratzen atazak...\n",
			'UPDATED_TASK'      => "    \"%s\" ataza eguneratu da.",
			'TASKS_UPDATED'     => "  Atazak eguneratu dira.\n",
			'URL_CACHE_DELETE'  => "  URLen cache-fitxategia ezabatu da: \"%s\"\n",
			'END_TITLE'         => "\nPOST INSTALL 8.1.1 bukatu du.\n\n"
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
	 * Function to update the models and remove all "loadService" lines
	 *
	 * @return string Result messages returned on every model updated
	 */
	private function updateModels(): string {
		$ret = '';

		if (file_exists($this->config->getDir('app_model'))) {
			if ($model = opendir($this->config->getDir('app_model'))) {
				while (false !== ($entry = readdir($model))) {
					if ($entry != '.' && $entry != '..') {
						$model_path = $this->config->getDir('app_model').$entry;
						$model_content = file_get_contents($model_path);

						$pattern = "/^\s+?OTools::loadService\((.*?)\);\n/m";
						$replace = '';
						$model_content = preg_replace($pattern, $replace, $model_content);

						if (strpos($model_content, "OTools::") === false) {
							$model_content = str_ireplace("use OsumiFramework\OFW\Tools\OTools;\n", "", $model_content);
						}

						file_put_contents($model_path, $model_content);

						$ret .= sprintf($this->messages[$this->config->getLang()]['UPDATED_MODEL'],
							$this->colors->getColoredString($model_path, 'light_green')
						);
					}
				}
				closedir($model);
			}
		}

		return $ret;
	}

	/**
	 * Function to update the services and remove all "loadService" and "loadComponent" lines
	 *
	 * @return string Result messages returned on every service updated
	 */
	private function updateServices(): string {
		$ret = '';

		if (file_exists($this->config->getDir('app_service'))) {
			if ($service = opendir($this->config->getDir('app_service'))) {
				while (false !== ($entry = readdir($service))) {
					if ($entry != '.' && $entry != '..') {
						$service_path = $this->config->getDir('app_service').$entry;
						$service_content = file_get_contents($service_path);

						$pattern = "/^\s+?OTools::loadService\((.*?)\);\n/m";
						$replace = '';
						$service_content = preg_replace($pattern, $replace, $service_content);

						$pattern = "/^\s+?OTools::loadComponent\((.*?)\);\n/m";
						$service_content = preg_replace($pattern, $replace, $service_content);

						if (strpos($service_content, "OTools::") === false) {
							$service_content = str_ireplace("use OsumiFramework\OFW\Tools\OTools;\n", "", $service_content);
						}

						file_put_contents($service_path, $service_content);

						$ret .= sprintf($this->messages[$this->config->getLang()]['UPDATED_SERVICE'],
							$this->colors->getColoredString($service_path, 'light_green')
						);
					}
				}
				closedir($service);
			}
		}

		return $ret;
	}

	/**
	 * Function to update the tasks and remove all "loadService" and "loadComponent" lines
	 *
	 * @return string Result messages returned on every task updated
	 */
	private function updateTasks(): string {
		$ret = '';

		if (file_exists($this->config->getDir('app_task'))) {
			if ($task = opendir($this->config->getDir('app_task'))) {
				while (false !== ($entry = readdir($task))) {
					if ($entry != '.' && $entry != '..') {
						$task_path = $this->config->getDir('app_task').$entry;
						$task_content = file_get_contents($task_path);

						$pattern = "/^\s+?OTools::loadService\((.*?)\);\n/m";
						$replace = '';
						$task_content = preg_replace($pattern, $replace, $task_content);

						$pattern = "/^\s+?OTools::loadComponent\((.*?)\);\n/m";
						$task_content = preg_replace($pattern, $replace, $task_content);

						if (strpos($task_content, "OTools::") === false) {
							$task_content = str_ireplace("use OsumiFramework\OFW\Tools\OTools;\n", "", $task_content);
						}

						file_put_contents($task_path, $task_content);

						$ret .= sprintf($this->messages[$this->config->getLang()]['UPDATED_SERVICE'],
							$this->colors->getColoredString($task_path, 'light_green')
						);
					}
				}
				closedir($task);
			}
		}

		return $ret;
	}

	/**
	 * Runs the v8.1.1 update post-installation tasks
	 *
	 * @return string
	 */
	public function run(): string {
		$ret = '';

		// Start
		$ret .= $this->messages[$this->config->getLang()]['TITLE'];

		// Update models

		$ret .= $this->messages[$this->config->getLang()]['UPDATING_MODELS'];

		$ret .= $this->updateModels();

		$ret .= $this->messages[$this->config->getLang()]['MODELS_UPDATED'];

		// Update services

		$ret .= $this->messages[$this->config->getLang()]['UPDATING_SERVICES'];

		$ret .= $this->updateServices();

		$ret .= $this->messages[$this->config->getLang()]['SERVICES_UPDATED'];
		
		// Update tasks

		$ret .= $this->messages[$this->config->getLang()]['UPDATING_TASKS'];

		$ret .= $this->updateTasks();

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
