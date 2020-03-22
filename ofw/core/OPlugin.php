<?php
/**
 * OPlugin - Base class for the plugins with methods to load the required plugin
 */
class OPlugin {
	private $plugins_dir  = null;
	private $name         = null;
	private $version      = null;
	private $description  = null;
	private $file_name    = null;
	private $dependencies = [];

	/**
	 * Start the object with the global configuration
	 *
	 * @param string $name Name of the plugin
	 *
	 * @return void
	 */
	function __construct($name) {
		global $core;
		$this->plugins_dir = $core->config->getDir('ofw_plugins');
		$this->name = $name;
	}

	/**
	 * Set the name of the plugin
	 *
	 * @param string $n Name of the plugin
	 *
	 * @return void
	 */
	public function setName($n) {
		$this->name = $n;
	}

	/**
	 * Get the name of the plugin
	 *
	 * @return string Name of the plugin
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set the version number of the plugin
	 *
	 * @param string $v Version number of the plugin (eg 1.0.3)
	 *
	 * @return void
	 */
	public function setVersion($v) {
		$this->version = $v;
	}

	/**
	 * Get the version number of the plugin
	 *
	 * @return string Version number of the plugin (eg 1.0.3)
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * Set the description of what the plugin does
	 *
	 * @param string $d Description of the plugin
	 *
	 * @return void
	 */
	public function setDescription($d) {
		$this->description = $d;
	}

	/**
	 * Get the description of what the plugin does
	 *
	 * @return string Description of the plugin
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Set file name of the plugin (eg OToken.php)
	 *
	 * @param string $fn Name of the file of the plugin
	 *
	 * @return void
	 */
	public function setFileName($fn) {
		$this->file_name = $fn;
	}

	/**
	 * Get file name of the plugin (eg OToken.php)
	 *
	 * @return string Name of the file of the plugin
	 */
	public function getFileName() {
		return $this->file_name;
	}

	/**
	 * Set the list of dependencies the plugin has (eg other php files)
	 *
	 * @param string[] $d List of files
	 *
	 * @return void
	 */
	public function setDependencies($d) {
		$this->dependencies = $d;
	}

	/**
	 * Get the list of dependencies the plugin has (eg other php files)
	 *
	 * @return string[] List of files
	 */
	public function getDependencies() {
		return $this->dependencies;
	}

	/**
	 * Load the plugins configuration from its json file
	 *
	 * @return string|void Returns a message in case the configuration file is not found or void if everything is ok
	 */
	public function loadConfig() {
		$conf_route = $this->plugins_dir.$this->getName().'/'.$this->getName().'.json';
		if (!file_exists($conf_route)) {
			echo 'ERROR: '.$this->getName().' plugin configuration file not found in '.$conf_route.'.';
			exit;
		}
		$config = json_decode( file_get_contents($conf_route), true);

		$this->setVersion($config['version']);
		$this->setDescription($config['description']);
		$this->setFileName($config['file_name']);
		$this->setDependencies(array_key_exists('dependencies', $config) ? $config['dependencies'] : []);
	}

	/**
	 * Load the files of the plugin so it can be used in the application
	 *
	 * @return string|void Returns a message in case a file is not found or void if everything is ok
	 */
	public function load() {
		$this->loadConfig();

		foreach ($this->getDependencies() as $dep) {
			$dep_route = $this->plugins_dir.$this->getName().'/dependencies/'.$dep;
			if (!file_exists($dep_route)) {
				echo 'ERROR: '.$dep.' dependency file not found in '.$dep_route.'.';
				exit;
			}
			require_once $dep_route;
		}

		$route = $this->plugins_dir.$this->getName().'/'.$this->getFileName();
		if (!file_exists($route)) {
			echo 'ERROR: '.$this->getFileName().' plugin file not found in '.$route.'.';
			exit;
		}
		require_once $route;
	}
}