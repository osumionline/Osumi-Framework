<?php declare(strict_types=1);
/**
 * OTask - Base class for the task classes
 */
class OTask {
	protected ?OColors $colors = null;
	protected ?OConfig $config = null;
	protected ?OLog    $log    = null;

	/**
	 * Load global configuration and logger to use in the service
	 *
	 * @return void
	 */
	public final function loadTask(): void {
		global $core;

		if (class_exists('OColors')) {
			$this->colors = new OColors();
		}
		$this->config = $core->config;
		$this->log    = new OLog(get_class($this));
	}

	/**
	 * Get the colors object used to colorize messages in the CLI tasks
	 *
	 * @return ?OColors Message colorizer object
	 */
	public final function getColors(): ?OColors {
		return $this->colors;
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
}