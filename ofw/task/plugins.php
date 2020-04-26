<?php declare(strict_types=1);
/**
 * Task to manage plugins (list available / install / remove)
 */
class pluginsTask {
	/**
	 * Returns description of the task
	 *
	 * @return string Description of the task
	 */
	public function __toString() {
		return $this->colors->getColoredString("plugins", "light_green").": ".OTools::getMessage('TASK_PLUGINS');
	}

	private ?OConfig $config       = null;
	private ?OColors $colors       = null;
	private array    $plugins_file = [];
	private string   $repo_url     = 'https://raw.githubusercontent.com/igorosabel/Osumi-Plugins/';

	/**
	 * Loads class used to colorize messages and global configuration
	 */
	function __construct() {
		global $core;
		$this->config = $core->config;
		$this->colors = new OColors();
	}

	/**
	 * Get available plugins list file from repository
	 *
	 * @return array Available plugin list array
	 */
	private function getPluginsFile(): array {
		if (empty($this->plugins_file)){
			$this->plugins_file = json_decode( file_get_contents($this->repo_url.'master/plugins.json'), true );
		}
		return $this->plugins_file;
	}

	/**
	 * List available plugins
	 *
	 * @return void Echoes information about available plugins
	 */
	public function availablePlugins(): void {
		$plugins = $this->getPluginsFile();
		echo OTools::getMessage('TASK_PLUGINS_AVAILABLE_TITLE');

		foreach ($plugins['plugins'] as $plugin) {
			echo "  · ".$this->colors->getColoredString($plugin['name'], "light_green")." (".$plugin['version']."): ".$plugin['description']."\n";
		}

		echo "\n\n";
		echo OTools::getMessage('TASK_PLUGINS_AVAILABLE_INSTALL');
		echo "      ".$this->colors->getColoredString("php ofw.php plugins install (".OTools::getMessage('TASK_PLUGINS_AVAILABLE_NAME').")", "light_green")."\n\n";
		echo OTools::getMessage('TASK_PLUGINS_AVAILABLE_LIST');
		echo "      ".$this->colors->getColoredString("php ofw.php plugins list", "light_green")."\n\n";
		echo OTools::getMessage('TASK_PLUGINS_AVAILABLE_DELETE');
		echo "      ".$this->colors->getColoredString("php ofw.php plugins remove (".OTools::getMessage('TASK_PLUGINS_AVAILABLE_NAME').")", "light_green")."\n\n";
	}

	/**
	 * Install a new plugin
	 *
	 * @param array Command line parameters, last parameter is the name of the plugin to install
	 *
	 * @return void Echoes messages returned while installing a new plugin
	 */
	public function installPlugin(array $params): void {
		if (count($params)<2) {
			echo "  ".$this->colors->getColoredString("ERROR", "red").": ".OTools::getMessage('TASK_PLUGINS_INSTALL_ERROR')."\n\n";
			echo "      ".$this->colors->getColoredString("php ofw.php plugins install email", "light_green")."\n\n\n";
			exit;
		}

		$plugins = $this->getPluginsFile();
		$found = null;
		foreach ($plugins['plugins'] as $p) {
			if ($p['name']==$params[1]) {
				$found = $p;
				break;
			}
		}
		if (is_null($found)) {
			echo "  ".$this->colors->getColoredString("ERROR", "red").": ".OTools::getMessage('TASK_PLUGINS_INSTALL_NOT_AVAILABLE')."\n\n";
			echo OTools::getMessage('TASK_PLUGINS_INSTALL_CHECK_LIST');
			echo "      ".$this->colors->getColoredString("php ofw.php plugins", "light_green")."\n\n\n";
			exit;
		}

		$plugin = new OPlugin($params[1]);
		$plugins_file = $this->config->getDir('app_config').'plugins.json';
		if (file_exists($plugins_file)) {
			$plugins_list = json_decode( file_get_contents($plugins_file), true );
		}
		else {
			$plugins_list = ['plugins'=>[]];
		}

		array_push($plugins_list['plugins'], $params[1]);

		$new_plugin_route = $this->config->getDir('ofw_plugins').$plugin->getName();
		if (file_exists($new_plugin_route)) {
			echo "  ".$this->colors->getColoredString("ERROR", "red").": ".OTools::getMessage('TASK_PLUGINS_INSTALL_FOLDER_EXISTS', [$new_plugin_route])."\n\n";
			exit;
		}

		// Create plugins folder
		mkdir($new_plugin_route);
		echo OTools::getMessage('TASK_PLUGINS_INSTALL_CREATE_FOLDER', [
			$new_plugin_route
		]);

		// Get plugins data
		$plugin_repo = $this->repo_url.'master/'.$plugin->getName().'/'.$plugin->getName().'.json';
		$plugin_config_file = file_get_contents($plugin_repo);
		$repo_data = json_decode( $plugin_config_file, true);
		file_put_contents($new_plugin_route.'/'.$plugin->getName().'.json', $plugin_config_file);
		echo OTools::getMessage('TASK_PLUGINS_INSTALL_CREATE_CONFIG', [
			$new_plugin_route, $plugin->getName()
		]);

		// Plugin file
		$plugin_file = file_get_contents($this->repo_url.'master/'.$plugin->getName().'/'.$repo_data['file_name']);
		file_put_contents($new_plugin_route.'/'.$repo_data['file_name'], $plugin_file);
		echo OTools::getMessage('TASK_PLUGINS_INSTALL_CREATE_FILE', [
			$new_plugin_route, $repo_data['file_name']
		]);

		// Dependencies
		if (array_key_exists('dependencies', $repo_data)) {
			echo OTools::getMessage('TASK_PLUGINS_INSTALL_DOWNLOAD_DEPS');
			mkdir($new_plugin_route.'/dependencies');
			foreach ($repo_data['dependencies'] as $dep) {
				$dep_file = file_get_contents($this->repo_url.'master/'.$plugin->getName().'/'.$dep);
				file_put_contents($new_plugin_route.'/dependencies/'.$dep, $dep_file);
				echo OTools::getMessage('TASK_PLUGINS_INSTALL_NEW_DEP', [
					$new_plugin_route, $dep
				]);
			}
		}

		// Plugins configuration file
		file_put_contents($plugins_file, json_encode($plugins_list));
		echo OTools::getMessage('TASK_PLUGINS_INSTALL_UPDATED');
		echo OTools::getMessage('TASK_PLUGINS_INSTALL_DONE');
	}

	/**
	 * List installed plugins
	 *
	 * @return void Echoes messages returned while checking installed plugins
	 */
	public function installedPlugins(): void {
		echo OTools::getMessage('TASK_PLUGINS_INSTALLED');
		if (count($this->config->getPlugins())>0) {
			foreach ($this->config->getPlugins() as $p) {
				$plugin = new OPlugin($p);
				$plugin->loadConfig();
				echo "  · ".$this->colors->getColoredString($plugin->getName(), "light_green")." (".$plugin->getVersion()."): ".$plugin->getDescription()."\n";
			}
			echo "\n";
		}
		else {
			echo OTools::getMessage('TASK_PLUGINS_INSTALLED_NONE');
		}
	}

	/**
	 * Remove an installed plugin
	 *
	 * @param array Command line parameters, last parameter is the name of the plugin to remove
	 *
	 * @return void Echoes messages returned while removing a plugin
	 */
	public function removePlugin(array $params): void {
		if (count($params)<2){
			echo "  ".$this->colors->getColoredString("ERROR", "red").": ".OTools::getMessage('TASK_PLUGINS_REMOVE_ERROR')."\n\n";
			echo "      ".$this->colors->getColoredString("php ofw.php plugins remove email", "light_green")."\n\n\n";
			exit;
		}
		$found = null;
		foreach ($this->config->getPlugins() as $p) {
			if ($p==$params[1]) {
				$found = $p;
				break;
			}
		}
		if (is_null($found)) {
			echo "  ".$this->colors->getColoredString("ERROR", "red").": ".OTools::getMessage('TASK_PLUGINS_REMOVE_NOT_INSTALLED')."\n\n";
			echo OTools::getMessage('TASK_PLUGINS_REMOVE_CHECK_LIST');
			echo "      ".$this->colors->getColoredString("php ofw.php plugins list", "light_green")."\n\n\n";
			exit;
		}

		$plugin = new OPlugin($params[1]);
		$plugin->loadConfig();

		$plugins_file = $this->config->getDir('app_config').'plugins.json';
		$plugins_list = json_decode( file_get_contents($plugins_file), true );

		$plugin_index = array_search($plugin->getName(), $plugins_list['plugins']);
		array_splice($plugins_list['plugins'], $plugin_index, 1);

		$plugin_route = $this->config->getDir('ofw_plugins').$plugin->getName();
		if (!file_exists($plugin_route)) {
			echo "  ".$this->colors->getColoredString("ERROR", "red").": ".OTools::getMessage('TASK_PLUGINS_REMOVE_FOLDER_NOT_FOUND', [$plugin_route])."\n\n";
			exit;
		}

		unlink($plugin_route.'/'.$plugin->getName().'.json');
		echo OTools::getMessage('TASK_PLUGINS_REMOVE_CONF_REMOVED', [
			$plugin_route, $plugin->getName()
		]);
		unlink($plugin_route.'/'.$plugin->getFileName());
		echo OTools::getMessage('TASK_PLUGINS_REMOVE_PLUGIN_REMOVED', [
			$plugin_route, $plugin->getFileName()
		]);

		if (count($plugin->getDependencies())>0) {
			echo OTools::getMessage('TASK_PLUGINS_REMOVE_REMOVING_DEPS');
			foreach ($plugin->getDependencies() as $dep) {
				$dep_route = $plugin_route.'/dependencies/'.$dep;
				unlink($dep_route);
				echo OTools::getMessage('TASK_PLUGINS_REMOVE_DEP_REMOVED', [$dep_route]);
			}
			rmdir($plugin_route.'/dependencies');
			echo OTools::getMessage('TASK_PLUGINS_REMOVE_DEP_FOLDER_REMOVED', [$plugin_route]);
		}

		rmdir($plugin_route);
		echo OTools::getMessage('TASK_PLUGINS_REMOVE_FOLDER_REMOVED', [$plugin_route]);

		if (count($plugins_list['plugins'])>0) {
			file_put_contents($plugins_file, json_encode($plugins_list));
			echo OTools::getMessage('TASK_PLUGINS_REMOVE_LIST_UPDATED');
		}
		else {
			unlink($plugins_file);
			echo OTools::getMessage('TASK_PLUGINS_REMOVE_PLUGINS_REMOVED', [$plugins_file]);
		}

		echo OTools::getMessage('TASK_PLUGINS_REMOVE_DONE');
	}

	/**
	 * Check for updates
	 *
	 * @return void Echoes messages returned while checking updates
	 */
	public function updateCheck(): void {
		if (count($this->config->getPlugins())==0) {
			echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECK_NO_PLUGINS');
			exit;
		}

		echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECK_CHECKING');
		$updates = false;

		foreach ($this->config->getPlugins() as $p) {
			$plugin = new OPlugin($p);
			$plugin->loadConfig();

			echo "  · ".$this->colors->getColoredString($plugin->getName(), "light_green")."\n";
			echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECK_VERSION', [$plugin->getVersion()]);

			$repo_check = json_decode( file_get_contents($this->repo_url.'master/'.$plugin->getName().'/'.$plugin->getName().'.json'), true );
			echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECK_CURRENT_VERSION', [$repo_check['version']]);
			if (version_compare($plugin->getVersion(), $repo_check['version'])==-1) {
				echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECK_AVAILABLE');
				$updates = true;
			}
			echo "\n";
		}

		if ($updates) {
			echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECK_UPDATE');
			echo "    ".$this->colors->getColoredString("php ofw.php plugins update", "light_green")."\n\n\n";
		}
	}

	/**
	 * Perform the update
	 *
	 * @return void Echoes messages returned while performing updates
	 */
	public function update(): void {
		if (count($this->config->getPlugins())==0) {
			echo OTools::getMessage('TASK_PLUGINS_UPDATE_NO_PLUGINS');
			exit;
		}

		echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECKING');

		foreach ($this->config->getPlugins() as $p) {
			$plugin = new OPlugin($p);
			$plugin->loadConfig();
			$deletes = [];
			$backups = [];
			$updates = [];

			echo "  · ".$this->colors->getColoredString($plugin->getName(), "light_green")."\n";
			echo OTools::getMessage('TASK_PLUGINS_UPDATE_INSTALLED_VERSION', [$plugin->getVersion()]);

			$repo_version_file = file_get_contents($this->repo_url.'master/'.$plugin->getName().'/'.$plugin->getName().'.json');
			$repo_check = json_decode( $repo_version_file, true );
			echo OTools::getMessage('TASK_PLUGINS_UPDATE_CURRENT_VERSION', [$repo_check['version']]);
			if (version_compare($plugin->getVersion(), $repo_check['version'])==-1) {
				echo OTools::getMessage('TASK_PLUGINS_UPDATE_UPDATING');
				$update = $repo_check['updates'][$repo_check['version']];
				echo "      ".$update['message']."\n";
				if (array_key_exists('deletes', $update)) {
					foreach ($update['deletes'] as $delete) {
						$delete_file = $this->config->getDir('ofw_plugins').$plugin->getName().'/'.$delete;
						if (file_exists($delete_file)) {
							echo OTools::getMessage('TASK_PLUGINS_UPDATE_TO_BE_DELETED', [$delete]);
							array_push($deletes, $delete_file);
						}
						else {
							echo "    ".$this->colors->getColoredString("ERROR", "red").": ".OTools::getMessage('TASK_PLUGINS_UPDATE_FILE_NOT_FOUND', [$delete_file])."\n\n\n";
							exit;
						}
					}
				}

				foreach ($update['files'] as $file) {
					$file_url = $this->repo_url.'master/'.$plugin->getName().'/'.$file;
					echo OTools::getMessage('TASK_PLUGINS_UPDATE_DOWNLOADING', [$file_url]);
					$file_content = file_get_contents($file_url);

					$local_file = $this->config->getDir('ofw_plugins').$plugin->getName().'/'.$file;
					if (file_exists($local_file)) {
						echo OTools::getMessage('TASK_PLUGINS_UPDATE_FILE_EXISTS');
						$backup_file = $local_file.'_backup';
						rename($local_file, $backup_file);
						array_push($backups, ['new_file'=>$local_file, 'backup'=>$backup_file]);
						echo OTools::getMessage('TASK_PLUGINS_UPDATE_FILE_UPDATED');
					}
					else {
						echo OTools::getMessage('TASK_PLUGINS_UPDATE_NEW_FILE');
					}
					file_put_contents($local_file, $file_content);
				}

				foreach ($deletes as $delete) {
					unlink($delete);
				}
				foreach ($backups as $backup) {
					unlink($backup['backup']);
				}

				echo OTools::getMessage('TASK_PLUGINS_UPDATE_VERSION_UPDATED');
				file_put_contents($this->config->getDir('ofw_plugins').$plugin->getName().'/'.$plugin->getName().'.json', $repo_version_file);

				echo OTools::getMessage('TASK_PLUGINS_UPDATE_DONE');
			}
		}
	}

	/**
	 * Run the task
	 *
	 * @param array Command line parameters: option and plugin name
	 *
	 * @return void Echoes messages generated while installing / updating / deleting plugins
	 */
	public function run(array $params): void {
		$option = (count($params)>0) ? $params[0] : 'none';
		$this->getPluginsFile();

		echo "\n";
		echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";

		switch ($option) {
			case 'none': {
				$this->availablePlugins();
			}
			break;
			case 'install': {
				$this->installPlugin($params);
			}
			break;
			case 'list': {
				$this->installedPlugins();
			}
			break;
			case 'remove': {
				$this->removePlugin($params);
			}
			break;
			case 'updateCheck': {
				$this->updateCheck();
			}
			break;
			case 'update': {
				$this->update();
			}
			break;
			default: {
				echo "  ".$this->colors->getColoredString("ERROR", "red").": ".OTools::getMessage('TASK_PLUGINS_DEFAULT_NOT_VALID')."\n\n";
				echo OTools::getMessage('TASK_PLUGINS_DEFAULT_AVAILABLE_OPTIONS');
				echo "  · ".$this->colors->getColoredString("list", "light_green").": ".OTools::getMessage('TASK_PLUGINS_DEFAULT_LIST')."\n";
				echo "  · ".$this->colors->getColoredString("install", "light_green").": ".OTools::getMessage('TASK_PLUGINS_DEFAULT_INSTALL')."\n";
				echo "  · ".$this->colors->getColoredString("remove", "light_green").": ".OTools::getMessage('TASK_PLUGINS_DEFAULT_REMOVE')."\n\n";
				echo OTools::getMessage('TASK_PLUGINS_DEFAULT_NO_OPTION');
			}
		}
	}
}