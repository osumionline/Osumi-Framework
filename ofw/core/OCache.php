<?php
/**
 * OCacheContainer - Container for all json cache files
 */
class OCacheContainer {
	private $list = [];

	/**
	 * Save a cache folder json file in memory to reuse it in case it is needed
	 *
	 * @param string $key Name of the json file in the cache folder
	 *
	 * @param string $value Content of the json file
	 *
	 * @return void
	 */
	public function set($key, $value) {
		$this->list[$key] = $value;
	}

	/**
	 * Get content of a previously stored json cache file
	 *
	 * @param string $key Name of the json file in the cache folder
	 *
	 * @return string|boolean Content of the json file or false if the key is not found
	 */
	public function get($key) {
		return array_key_exists($key, $this->list) ? $this->list[$key] : false;
	}

	/**
	 * Remove a stored cache json file from the container
	 *
	 * @param string $key Name of the json file in the cache folder
	 *
	 * @return void
	 */
	public function remove($key) {
		unset($this->list[$key]);
	}
}

/**
 * OCache - Class to cache data and methods to access/modify/delete it
 */
class OCache {
	private $debug      = false;
	private $l          = null;
	private $cache      = null;
	private $cache_file = null;
	private $raw        = false;
	private $cache_date = null;
	private $cache_data = null;
	private $status     = 'ok';

	/**
	 * On startup the constructor checks if the required file exists, if the content is a parseable json and if hasn't expired (a week)
	 *
	 * @param string $cache Name of the cache file
	 *
	 * @param boolean $raw If set to true it doesn't check it's expiration date
	 *
	 * @return void
	 */
	function __construct($cache, $raw=false) {
		global $core;
		$this->debug = ($core->config->getLog('level') == 'ALL');
		if ($this->debug) {
			$this->l = new OLog();
		}

		$this->cache = $cache;
		$this->cache_file = $core->config->getDir('app_cache').$cache.'.json';
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
	private function log($str) {
		if ($this->debug) {
			$this->l->debug($str);
		}
	}

	/**
	 * Returns status of the cache (ok / error_date -expired- / error_data -malformed json- / error_file -file not found-)
	 *
	 * @return string Status of the cache
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Set start data for a clean new cache
	 *
	 * @return void
	 */
	public function start() {
		$this->status = 'ok';
		$this->cache_date = mktime();
		$this->cache_data = [];
	}

	/**
	 * Get data (a certain key) from the loaded cache
	 *
	 * @param string $key Key of the cached json data to be retrieved
	 *
	 * @return string|integer|boolean Returns required cache data or false if something failed
	 */
	public function get($key) {
		if ($this->status!='ok') {
			return false;
		}

		if (array_key_exists($key, $this->cache_data)) {
			return $this->cache_data[$key];
		}
		else {
			return false;
		}
	}

	/**
	 * Set a key/value pair into the loaded cache
	 *
	 * @param string $key Key of the data to be saved
	 *
	 * @param string|integer|boolean Data to be saved
	 *
	 * @return void
	 */
	public function set($key, $value) {
		$this->cache_data[$key] = $value;
	}

	/**
	 * Function to save the loaded data back to a file
	 *
	 * @return void
	 */
	public function save() {
		$this->cache_date = mktime();
		$data = ['date' => $this->cache_date, 'data' => $this->cache_data];

		$this->log('[OCache] - save: '.$this->cache);
		$this->log(var_export($data, true));

		file_put_contents($this->cache_file, json_encode($data));
	}

	/**
	 * Delete a cache file
	 *
	 * @return boolean The file got deleted or not
	 */
	public function delete() {
		if ($this->status!='ok') {
			return false;
		}

		if (file_exists($this->cache_file)) {
			$this->log('[OCache] - delete: '.$this->cache);
			unlink($this->cache_file);
			return true;
		}

		return false;
	}
}