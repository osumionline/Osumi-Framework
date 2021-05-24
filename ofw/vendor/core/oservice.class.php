<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Core;

use OsumiFramework\OFW\Log\OLog;
use OsumiFramework\OFW\Cache\OCacheContainer;

/**
 * OService - Base class for the service classes
 */
class OService {
	protected ?OConfig $config = null;
	protected ?OLog    $log    = null;
	protected ?OCacheContainer $cacheContainer = null;

	/**
	 * Load global configuration and logger to use in the service
	 *
	 * @return void
	 */
	public final function loadService(): void {
		global $core;

		$this->config = $core->config;
		$this->log    = new OLog(get_class($this));
		$this->cacheContainer = $core->cacheContainer;
	}

	/**
	 * Get the application configuration (shortcut to $core->config)
	 *
	 * @return OConfig Configuration class object
	 */
	public final function getConfig(): OConfig {
		return $this->config;
	}

	/**
	 * Get object to log information into the debug log
	 *
	 * @return OLog Information logger object
	 */
	public final function getLog(): OLog {
		return $this->log;
	}

	/**
	 * Get access to the cache container
	 *
	 * @return OCacheContainer Cache container class object
	 */
	public final function getCacheContainer(): OCacheContainer {
		return $this->cacheContainer;
	}
}