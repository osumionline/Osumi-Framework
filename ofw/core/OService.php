<?php
/**
 * OService - Base class for the service classes
 */
class OService {
	protected $config = null;
	protected $log    = null;

	/**
	 * Load global configuration and logger to use in the service
	 *
	 * @return void
	 */
	public final function loadService() {
		global $core;

		$this->config = $core->config;
		$this->log    = new OLog(get_class($this));
	}
}