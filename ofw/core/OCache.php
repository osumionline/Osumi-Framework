<?php declare(strict_types=1);
/**
 * OCacheContainer - Container for all json cache files
 */
class OCacheContainer {
	private array $list = [];

	/**
	 * Save a cache folder json file in memory to reuse it in case it is needed
	 *
	 * @param string $key Name of the json file in the cache folder
	 *
	 * @param OCache $value OCache object to be stored
	 *
	 * @return void
	 */
	public function set(string $key, OCache $value): void {
		$this->list[$key] = $value;
	}

	/**
	 * Get content of a previously stored json cache file
	 *
	 * @param string $key Name of the json file in the cache folder
	 *
	 * @return ?OCache Stored OCache object or null if not found
	 */
	public function get(string $key): ?OCache {
		return array_key_exists($key, $this->list) ? $this->list[$key] : null;
	}

	/**
	 * Remove a stored cache json file from the container
	 *
	 * @param string $key Name of the json file in the cache folder
	 *
	 * @return void
	 */
	public function remove(string $key): void {
		unset($this->list[$key]);
	}
}

/**
 * OCache - Class to cache data and methods to access/modify/delete it
 */
class OCache {
	private bool   $debug      = false;
	private ?OLog  $l          = null;
	private string $cache      = '';
	private string $cache_file = '';
	private bool   $raw        = false;
	private ?int   $cache_date = null;
	private array  $cache_data = [];
	private string $status     = 'ok';

	/**
	 * On startup the constructor checks if the required file exists, if the content is a parseable json and if hasn't expired (a week)
	 *
	 * @param string $cache Name of the cache file
	 *
	 * @param bool $raw If set to true it doesn't check it's expiration date
	 */
	function __construct(string $cache, bool $raw=false) {
		global $core;
		$this->debug = ($core->config->getLog('level') == 'ALL');
		if ($this->debug) {
			$this->l = new OLog('OCache');
		}

		$this->cache = $cache;
		$this->cache_file = $core->config->getDir('ofw_cache').$cache.'.json';
		if (!file_exists($this->cache_file)) {
			$this->status = 'error_file';
		}

		$data = null;
		if ($this->status=='ok') {
			$data = json_decode(file_get_contents($this->cache_file), true);
			if (!$data) {
				$this->status = 'error_data';
			}
		}

		$this->raw = $raw;
		if ($this->raw) {
			$this->cache_data = $data;
		}
		else {
			if ($this->status=='ok') {
				$this->cache_date = $data['date'];
				$check = mktime();
				if (($this->cache_date+(60*60*24*7))<$check) {
					$this->status = 'error_date';
				}
			}

			if ($this->status=='ok') {
				$this->cache_data = $data['data'];
			}
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
	 * Returns status of the cache (ok / error_date -expired- / error_data -malformed json- / error_file -file not found-)
	 *
	 * @return string Status of the cache
	 */
	public function getStatus(): string {
		return $this->status;
	}

	/**
	 * Set start data for a clean new cache
	 *
	 * @return void
	 */
	public function start(): void {
		$this->status = 'ok';
		$this->cache_date = mktime();
		$this->cache_data = [];
	}

	/**
	 * Get data (a certain key) from the loaded cache
	 *
	 * @param string $key Key of the cached json data to be retrieved
	 *
	 * @return string|int|bool Returns required cache data or false if something failed
	 */
	public function get(string $key) {
		if ($this->status!='ok') {
			return null;
		}

		if (array_key_exists($key, $this->cache_data)) {
			return $this->cache_data[$key];
		}
		else {
			return null;
		}
	}

	/**
	 * Set a key/value pair into the loaded cache
	 *
	 * @param string $key Key of the data to be saved
	 *
	 * @param string|int|bool Data to be saved
	 *
	 * @return void
	 */
	public function set(string $key, $value): void {
		$this->cache_data[$key] = $value;
	}

	/**
	 * Function to save the loaded data back to a file
	 *
	 * @return void
	 */
	public function save(): void {
		$this->cache_date = mktime();
		$data = ['date' => $this->cache_date, 'data' => $this->cache_data];

		$this->log('save - Cache file: '.$this->cache);
		$this->log(var_export($data, true));

		file_put_contents($this->cache_file, json_encode($data));
	}

	/**
	 * Delete a cache file
	 *
	 * @return bool The file got deleted or not
	 */
	public function delete(): bool {
		if ($this->status!='ok') {
			return false;
		}

		if (file_exists($this->cache_file)) {
			$this->log('delete - Cache file: '.$this->cache);
			unlink($this->cache_file);
			return true;
		}

		return false;
	}
}