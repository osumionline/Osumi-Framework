<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Task;

use OsumiFramework\OFW\Core\OTask;
use OsumiFramework\OFW\Tools\OTools;

/**
 * Cleans all non framework data, to be used on new installations
 */
class resetTask extends OTask {
	public function __toString() {
		return $this->getColors()->getColoredString('reset', 'light_green').': '.OTools::getMessage('TASK_RESET');
	}

	private function rrmdir(string $dir): bool {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			if (is_dir($dir.'/'.$file)) {
				$this->rrmdir($dir.'/'.$file);
			}
			else {
				unlink($dir.'/'.$file);
			}
		}
		return rmdir($dir);
	}

	private function countDown(): void {
		for ($i=15; $i>=0; $i--) {
			echo "  ";
			if ($i<4) {
				echo $this->getColors()->getColoredString(strval($i), 'red');
			}
			else {
				echo $i;
			}
			echo "\n";
			sleep(1);
		}
	}

	private function cleanData(): void {
		// Components
		if ($model = opendir($this->config->getDir('app_component'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..') {
					$this->rrmdir($this->config->getDir('app_component').$entry);
				}
			}
			closedir($model);
		}

		// Config
		if ($model = opendir($this->config->getDir('app_config'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..') {
					unlink($this->config->getDir('app_config').$entry);
				}
			}
			closedir($model);
		}

		// DTO
		if ($model = opendir($this->config->getDir('app_dto'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..') {
					unlink($this->config->getDir('app_dto').$entry);
				}
			}
			closedir($model);
		}

		// Filters
		if ($model = opendir($this->config->getDir('app_filter'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..') {
					unlink($this->config->getDir('app_filter').$entry);
				}
			}
			closedir($model);
		}

		// Layout
		if ($model = opendir($this->config->getDir('app_layout'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..') {
					unlink($this->config->getDir('app_layout').$entry);
				}
			}
			closedir($model);
		}

		// Model
		if ($model = opendir($this->config->getDir('app_model'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..') {
					unlink($this->config->getDir('app_model').$entry);
				}
			}
			closedir($model);
		}

		// Module
		if ($model = opendir($this->config->getDir('app_module'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..') {
					$this->rrmdir($this->config->getDir('app_module').$entry);
				}
			}
			closedir($model);
		}

		// Service
		if ($model = opendir($this->config->getDir('app_service'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..') {
					unlink($this->config->getDir('app_service').$entry);
				}
			}
			closedir($model);
		}

		// Task
		if ($model = opendir($this->config->getDir('app_task'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..') {
					unlink($this->config->getDir('app_task').$entry);
				}
			}
			closedir($model);
		}

		// Cache
		if ($model = opendir($this->config->getDir('ofw_cache'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..' && $entry != '.gitignore') {
					unlink($this->config->getDir('ofw_cache').$entry);
				}
			}
			closedir($model);
		}

		// Export
		if ($model = opendir($this->config->getDir('ofw_export'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..' && $entry != '.gitignore') {
					unlink($this->config->getDir('ofw_export').$entry);
				}
			}
			closedir($model);
		}

		// Generate default config.json
		$default_config_json = "{\n";
		$default_config_json .= "	\"name\": \"Osumi Framework\"\n";
		$default_config_json .= "}";
		$config_file = $this->config->getDir('app_config').'config.json';
		file_put_contents($config_file, $default_config_json);

		// Generate default layout
		$default_layout = "<!DOCTYPE html>\n";
		$default_layout .= "<html>\n";
		$default_layout .= "	<head>\n";
		$default_layout .= "		<meta charset=\"utf-8\">\n";
		$default_layout .= "		<meta name=\"viewport\" content=\"width=device-width\">\n";
		$default_layout .= "		<meta name=\"description\" content=\"\">\n";
		$default_layout .= "		<title>{{title}}</title>\n";
		$default_layout .= "		<link type=\"image/x-icon\" href=\"/favicon.png\" rel=\"icon\">\n";
		$default_layout .= "		<link type=\"image/x-icon\" href=\"/favicon.png\" rel=\"shortcut icon\">\n";
		$default_layout .= "		{{css}}\n";
		$default_layout .= "		{{js}}\n";
		$default_layout .= "	</head>\n";
		$default_layout .= "	<body>\n";
		$default_layout .= "		{{body}}\n";
		$default_layout .= "	</body>\n";
		$default_layout .= "</html>";
		$layout_file = $this->config->getDir('app_layout').'default.php';
		file_put_contents($layout_file, $default_layout);
	}

	/**
	 * Run the task
	 *
	 * @return void 
	 */
	public function run(array $options = []): void {
		$cache_file = $this->config->getDir('ofw_cache').'reset.json';
		$reset_key = '';
		$reset_date = 0;

		if (file_exists($cache_file)) {
			$reset_data = json_decode(file_get_contents($cache_file), true);
			if (!is_null($reset_data)) {
				$reset_key  = $reset_data['key'];
				$reset_date = $reset_data['date'];
			}
			unlink($cache_file);
		}

		if (count($options) == 0) {
			echo "\n  ".$this->getColors()->getColoredString(OTools::getMessage('TASK_RESET_WARNING'), 'red')."\n\n";
			echo "  ".OTools::getMessage('TASK_RESET_CONTINUE')."\n\n";
			echo "  ".OTools::getMessage('TASK_RESET_TIME_TO_CANCEL')."\n\n";

			$this->countDown();

			$data = [
				'key' => substr(hash('sha512', strval(time())), 0, 12),
				'date' => time() + (60 * 15)
			];
			file_put_contents($cache_file, json_encode($data));

			echo "\n  ".OTools::getMessage('TASK_RESET_RESET_KEY_CREATED')."\n\n";
			echo "    php ofw.php reset ".$data['key']."\n\n";
		}
		else {
			if ($options[0] === 'silent') {
				$this->cleanData();
			}
			else {
				if (
					$options[0] === $reset_key &&
					$reset_date > time()
				) {
					$this->cleanData();
					echo "\n  ".OTools::getMessage('TASK_RESET_DATA_ERASED')."\n\n";
				}
				else {
					echo "\n  ".$this->getColors()->getColoredString(OTools::getMessage('TASK_RESET_ERROR'), 'red')."\n\n";
					echo "  ".OTools::getMessage('TASK_RESET_GET_NEW_KEY')."\n\n";
					echo "    php ofw.php reset\n\n";
				}
			}
		}
	}
}