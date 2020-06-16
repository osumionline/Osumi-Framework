<?php declare(strict_types=1);
/**
 * Task to manage plugins (list available / install / remove)
 */
class pluginsTask extends OTask {
	public function __toString() {
		return $this->getColors()->getColoredString('plugins', 'light_green').': '.OTools::getMessage('TASK_PLUGINS');
	}

	private array  $plugins_file = [];
	private string $repo_url     = 'https://raw.githubusercontent.com/igorosabel/Osumi-Plugins/';

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
		$path = $this->getConfig()->getDir('ofw_template').'plugins/availablePlugins.php';
		$plugins = $this->getPluginsFile();
		$params = [
			'colors' => $this->getColors(),
			'list' => []
		];

		foreach ($plugins['plugins'] as $plugin) {
			array_push($params['list'], $plugin);
		}

		echo OTools::getPartial($path, $params);
	}

	/**
	 * Install a new plugin
	 *
	 * @param array Command line parameters, last parameter is the name of the plugin to install
	 *
	 * @return void Echoes messages returned while installing a new plugin
	 */
	public function installPlugin(array $params): void {
		$path = $this->getConfig()->getDir('ofw_template').'plugins/installPlugin.php';
		$values = [
			'colors' => $this->getColors(),
			'error' => 0,
			'error_path' => '',
			'plugin_path' => '',
			'plugin_name' => '',
			'plugin_file' => '',
			'deps' => []
		];

		if (count($params)<2) {
			$values['error'] = 1;
			echo OTools::getPartial($path, $values);
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
			$values['error'] = 2;
			echo OTools::getPartial($path, $values);
			exit;
		}

		$plugin = new OPlugin($params[1]);
		$plugins_file = $this->getConfig()->getDir('app_config').'plugins.json';
		if (file_exists($plugins_file)) {
			$plugins_list = json_decode( file_get_contents($plugins_file), true );
		}
		else {
			$plugins_list = ['plugins'=>[]];
		}

		array_push($plugins_list['plugins'], $params[1]);

		$new_plugin_route = $this->getConfig()->getDir('ofw_plugins').$plugin->getName();
		if (file_exists($new_plugin_route)) {
			$values['error'] = 3;
			$values['error_path'] = $new_plugin_route;
			echo OTools::getPartial($path, $values);
			exit;
		}

		// Create plugins folder
		mkdir($new_plugin_route);
		$values['plugin_path'] = $new_plugin_route;

		// Get plugins data
		$plugin_repo = $this->repo_url.'master/'.$plugin->getName().'/'.$plugin->getName().'.json';
		$plugin_config_file = file_get_contents($plugin_repo);
		$repo_data = json_decode( $plugin_config_file, true);
		file_put_contents($new_plugin_route.'/'.$plugin->getName().'.json', $plugin_config_file);
		$values['plugin_name'] = $plugin->getName();

		// Plugin file
		$plugin_file = file_get_contents($this->repo_url.'master/'.$plugin->getName().'/'.$repo_data['file_name']);
		file_put_contents($new_plugin_route.'/'.$repo_data['file_name'], $plugin_file);
		$values['plugin_file'] = $repo_data['file_name'];

		// Dependencies
		if (array_key_exists('dependencies', $repo_data)) {
			mkdir($new_plugin_route.'/dependencies');
			foreach ($repo_data['dependencies'] as $dep) {
				$dep_file = file_get_contents($this->repo_url.'master/'.$plugin->getName().'/'.$dep);
				file_put_contents($new_plugin_route.'/dependencies/'.$dep, $dep_file);
				array_push($values['deps'], $dep);
			}
		}

		// Plugins configuration file
		file_put_contents($plugins_file, json_encode($plugins_list));
		echo OTools::getPartial($path, $values);
	}

	/**
	 * List installed plugins
	 *
	 * @return void Echoes messages returned while checking installed plugins
	 */
	public function installedPlugins(): void {
		$path = $this->getConfig()->getDir('ofw_template').'plugins/installedPlugins.php';
		$values = [
			'colors' => $this->getColors(),
			'plugins' => []
		];

		if (count($this->getConfig()->getPlugins())>0) {
			foreach ($this->getConfig()->getPlugins() as $p) {
				$plugin = new OPlugin($p);
				$plugin->loadConfig();
				array_push($values['plugins'], $plugin);
			}
		}
		echo OTools::getPartial($path, $values);
	}

	/**
	 * Remove an installed plugin
	 *
	 * @param array Command line parameters, last parameter is the name of the plugin to remove
	 *
	 * @return void Echoes messages returned while removing a plugin
	 */
	public function removePlugin(array $params): void {
		$path = $this->getConfig()->getDir('ofw_template').'plugins/removePlugin.php';
		$values = [
			'colors' => $this->getColors(),
			'error' => 0,
			'plugin_path' => '',
			'plugin_name' => '',
			'plugin_file_name' => '',
			'dep_path' => '',
			'deps' => [],
			'plugins_file' => '',
			'num_plugins' => 0
		];

		if (count($params)<2){
			$values['error'] = 1;
			echo OTools::getPartial($path, $values);
			exit;
		}
		$found = null;
		foreach ($this->getConfig()->getPlugins() as $p) {
			if ($p==$params[1]) {
				$found = $p;
				break;
			}
		}
		if (is_null($found)) {
			$values['error'] = 2;
			echo OTools::getPartial($path, $values);
			exit;
		}

		$plugin = new OPlugin($params[1]);
		$plugin->loadConfig();

		$plugins_file = $this->getConfig()->getDir('app_config').'plugins.json';
		$values['plugins_file'] = $plugins_file;
		$values['plugin_name'] = $plugin->getName();
		$values['plugin_file_name'] = $plugin->getFileName();
		$plugins_list = json_decode( file_get_contents($plugins_file), true );

		$plugin_index = array_search($plugin->getName(), $plugins_list['plugins']);
		array_splice($plugins_list['plugins'], $plugin_index, 1);

		$plugin_route = $this->getConfig()->getDir('ofw_plugins').$plugin->getName();
		$values['plugin_path'] = $plugin_route;
		if (!file_exists($plugin_route)) {
			$values['error'] = 3;
			echo OTools::getPartial($path, $values);
			exit;
		}

		unlink($plugin_route.'/'.$plugin->getName().'.json');
		unlink($plugin_route.'/'.$plugin->getFileName());

		if (count($plugin->getDependencies())>0) {
			$dep_path = $plugin_route.'/dependencies';
			$values['dep_path']= $dep_path;
			foreach ($plugin->getDependencies() as $dep) {
				$dep_route = $dep_path.'/'.$dep;
				unlink($dep_route);
				array_push($values['deps'], $dep_route);
			}

			rmdir($dep_path);
		}

		rmdir($plugin_route);
		$values['num_plugins'] = count($plugins_list['plugins']);

		if ($values['num_plugins']>0) {
			file_put_contents($plugins_file, json_encode($plugins_list));
		}
		else {
			unlink($plugins_file);
		}

		echo OTools::getPartial($path, $values);
	}

	/**
	 * Check for updates
	 *
	 * @return void Echoes messages returned while checking updates
	 */
	public function updateCheck(): void {
		$path = $this->getConfig()->getDir('ofw_template').'plugins/updateCheck.php';
		$values = [
			'colors' => $this->getColors(),
			'error' => 0,
			'plugins' => [],
			'updates' => false
		];

		if (count($this->getConfig()->getPlugins())==0) {
			$values['error'] = 1;
			echo OTools::getPartial($path, $values);
			exit;
		}

		$updates = false;

		foreach ($this->getConfig()->getPlugins() as $p) {
			$plugin = new OPlugin($p);
			$plugin->loadConfig();
			$plugin_update = [
				'plugin' => $plugin,
				'update' => false,
				'repo_version' => ''
			];

			$repo_check = json_decode( file_get_contents($this->repo_url.'master/'.$plugin->getName().'/'.$plugin->getName().'.json'), true );
			$plugin_update['repo_version'] = $repo_check['version'];

			if (version_compare($plugin->getVersion(), $repo_check['version'])==-1) {
				$plugin_update['update'] = true;
				$values['updates'] = true;
			}
			array_push($values['plugins'], $plugin_update);
		}

		echo OTools::getPartial($path, $values);
	}

	/**
	 * Perform the update
	 *
	 * @return void Echoes messages returned while performing updates
	 */
	public function update(): void {
		$path = $this->getConfig()->getDir('ofw_template').'plugins/update.php';
		$values = [
			'colors' => $this->getColors(),
			'error' => 0,
			'plugins' => []
		];
		if (count($this->getConfig()->getPlugins())==0) {
			$values['error'] = 1;
			echo OTools::getPartial($path, $values);
			exit;
		}

		foreach ($this->getConfig()->getPlugins() as $p) {
			$plugin = new OPlugin($p);
			$plugin->loadConfig();
			$deletes = [];
			$backups = [];
			$updates = [];
			$plugin_update = [
				'plugin' => $plugin,
				'repo_version' => '',
				'update' => false,
				'update_message' => '',
				'deletes' => [],
				'files' => []
			];

			$repo_version_file = file_get_contents($this->repo_url.'master/'.$plugin->getName().'/'.$plugin->getName().'.json');
			$repo_check = json_decode( $repo_version_file, true );
			$plugin_update['repo_version'] = $repo_check['version'];

			if (version_compare($plugin->getVersion(), $repo_check['version'])==-1) {
				$plugin_update['update'] = true;
				$update = $repo_check['updates'][$repo_check['version']];
				$plugin_update['update_message'] = $update['message'];

				if (array_key_exists('deletes', $update)) {
					foreach ($update['deletes'] as $delete) {
						$delete_file = $this->getConfig()->getDir('ofw_plugins').$plugin->getName().'/'.$delete;
						$plugin_update_delete = [
							'delete' => $delete,
							'file' => $delete_file,
							'error' => false
						];

						if (file_exists($delete_file)) {
							array_push($deletes, $delete_file);
						}
						else {
							$plugin_update_delete['error'] = true;
							array_push($plugin_update['deletes'], $plugin_update_delete);
							array_push($values['plugins'], $plugin_update);
							echo OTools::getPartial($path, $values);
							exit;
						}
					}
				}

				foreach ($update['files'] as $file) {
					$file_url = $this->repo_url.'master/'.$plugin->getName().'/'.$file;
					$plugin_update_file = [
						'url' => $file_url,
						'exists' => false
					];

					$file_content = file_get_contents($file_url);

					$local_file = $this->getConfig()->getDir('ofw_plugins').$plugin->getName().'/'.$file;
					if (file_exists($local_file)) {
						$plugin_update_file['exists'] = true;
						$backup_file = $local_file.'_backup';
						rename($local_file, $backup_file);
						array_push($backups, ['new_file'=>$local_file, 'backup'=>$backup_file]);
					}
					file_put_contents($local_file, $file_content);
					array_push($plugin_update['files'], $plugin_update_file);
				}

				foreach ($deletes as $delete) {
					unlink($delete);
				}
				foreach ($backups as $backup) {
					unlink($backup['backup']);
				}

				file_put_contents($this->getConfig()->getDir('ofw_plugins').$plugin->getName().'/'.$plugin->getName().'.json', $repo_version_file);
			}

			array_push($values['plugins'], $plugin_update);
		}

		echo OTools::getPartial($path, $values);
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
				$path = $this->getConfig()->getDir('ofw_template').'plugins/plugins.php';
				$values = [
					'colors' => $this->getColors()
				];
				echo OTools::getPartial($path, $values);
			}
		}
	}
}