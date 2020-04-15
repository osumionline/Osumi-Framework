<?php declare(strict_types=1);
/**
 * OSession - Class with methods to get/set information into the users session
 */
class OSession {
	private bool  $debug  = false;
	private ?OLog $l      = null;
	private array $params = [];

	/**
	 * Load on startup the session information
	 */
	function __construct() {
		global $core;
		$this->debug = ($core->config->getLog('level') == 'ALL');
		if ($this->debug) {
			$this->l = new OLog('OSession');
		}

		if (isset($_SESSION['params'])) {
			$this->params = unserialize($_SESSION['params']);
		}
	}

	/**
	 * Logs internal information of the class
	 *
	 * @param string $str String to be logged
	 *
	 * @return void
	 */
	private function log(string $str): void {
		if ($this->debug) {
			$this->l->debug($str);
		}
	}

	/**
	 * Save given parameter list into memory and into session
	 *
	 * @param array $p Array of key / value pairs
	 *
	 * @return void
	 */
	public function setParams(array $p): void {
		$this->log('setParams - Params:');
		$this->log(var_export($p, true));
		$this->params = $p;
		$_SESSION['params'] = serialize($p);
	}

	/**
	 * Get parameter list
	 *
	 * @return array Array of key / value pairs
	 */
	public function getParams(): array {
		return $this->params;
	}

	/**
	 * Adds a new key / value parameter into memory and into session
	 *
	 * @param string $key Key code of the parameter
	 *
	 * @param string|int|float|bool $value Value of the parameter
	 *
	 * @return void
	 */
	public function addParam(string $key, $value): void {
		$this->params[$key] = $value;
		$this->setParams($this->params);
	}

	/**
	 * Get a parameter from the previously loaded list
	 *
	 * @param string $key Key code of the parameter
	 *
	 * @return string|int|float|bool|void Value of the parameter or null if not found
	 */
	public function getParam(string $key) {
		if (array_key_exists($key, $this->params)) {
			return $this->params[$key];
		}
		else {
			return null;
		}
	}

	/**
	 * Removes a parameter from the list and the users session
	 *
	 * @param string $key Key code of the parameter
	 *
	 * @return void
	 */
	public function removeParam(string $key): void {
		unset($this->params[$key]);
		$this->setParams($this->params);
	}

	/**
	 * Removes all parameters from users session and resets the list
	 *
	 * @return void
	 */
	public function cleanSession(): void {
		unset($_SESSION['params']);
		$this->setParams([]);
	}
}