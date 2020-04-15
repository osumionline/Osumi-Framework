<?php declare(strict_types=1);
/**
 * OCookie - Class with methods to create/modify/delete cookies on clients
 */
class OCookie {
	private bool     $debug       = false;
	private ?Olog    $l           = null;
	private ?OConfig $config      = null;
	private array    $cookie_list = [];

	/**
	 * Set up a logger for internal operations and get applications configuration (shortcut to $core->config)
	 */
	function __construct() {
		global $core;
		$this->debug = ($core->config->getLog('level') == 'ALL');
		if ($this->debug) {
			$this->l = new OLog('OCookie');
		}
		$this->config = $core->config;
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
	 * Set array of values stored in cookies
	 *
	 * @param string[] $l Array of values stored in cookies
	 *
	 * @return void
	 */
	public function setCookieList(array $l): void {
		$this->cookie_list = $l;
	}

	/**
	 * Get array of values stored in cookies
	 *
	 * @return string[] Array of values stored in cookies
	 */
	public function getCookieList(): array {
		return $this->cookie_list;
	}

	/**
	 * Add a new cookie to the user and store it in the list
	 *
	 * @param string $key Key code for the cookie value
	 *
	 * @param string $value Value to be stored in the users cookies
	 *
	 * @return void
	 */
	public function add(string $key, string $value): void {
		$this->cookie_list[$key] = $value;
		setcookie ($this->config->getCookiePrefix().'['.$key.']', $value, time() + (3600*24*31), '/', $this->config->getCookieUrl());
	}

	/**
	 * Get a cookies value from the previously loaded list
	 *
	 * @param string $key Key code for the cookie value
	 *
	 * @return string Value of the key in the users cookies
	 */
	public function get(string $key): ?string {
		return array_key_exists($key, $this->cookie_list) ? $this->cookie_list[$key] : null;
	}

	/**
	 * Load users cookies into the loaded list
	 *
	 * @return void
	 */
	public function load(): void {
		$this->cookie_list = [];

		if (isset($_COOKIE[$this->config->getCookiePrefix()])) {
			foreach ($_COOKIE[$this->config->getCookiePrefix()] as $key => $value) {
				$key = htmlspecialchars($key);
				$value = htmlspecialchars($value);

				$this->cookie_list[$key] = $value;
			}
		}

		$this->log('load - Cookie list:');
		$this->log(var_export($this->cookie_list, true));
	}

	/**
	 * Store all the values in the list into the users cookies
	 *
	 * @return void
	 */
	public function save(): void {
		$this->log('save - Cookie list:');
		$this->log(var_export($this->cookie_list, true));

		foreach ($this->cookie_list as $key => $value) {
			setcookie ($this->config->getCookiePrefix().'['.$key.']', $value, time() + (3600*24*31), '/', $this->config->getCookieUrl());
		}
	}

	/**
	 * Delete all user cookies
	 *
	 * @return void
	 */
	public function clean(): void {
		$this->log('clean - Cookies removed');

		foreach ($this->cookie_list as $key => $value){
			setcookie ($this->config->getCookiePrefix().'['.$key.']', $value, 1, '/', $this->config->getCookieUrl());
		}
		$this->cookie_list = [];
	}
}