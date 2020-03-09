<?php
class OCache {
	private $cache_file = null;
	private $raw        = false;
	private $cache_date = null;
	private $cache_data = null;
	private $status     = 'ok';

	function __construct($cache, $raw=false) {
		global $c;
		$this->cache_file = $c->getDir('app_cache').$cache.'.json';
		if (!file_exists($this->cache_file)){
			$this->status = 'error_file';
		}

		$data = null;
		if ($this->status=='ok'){
			$data = json_decode(file_get_contents($this->cache_file), true);
			if (!$data){
				$this->status = 'error_data';
			}
		}

		$this->raw = $raw;
		if ($this->raw){
			$this->cache_data = $data;
		}
		else{
			if ($this->status=='ok'){
				$this->cache_date = $data['date'];
				$check = mktime();
				if (($this->cache_date+(60*60*24*7))<$check){
					$this->status = 'error_date';
				}
			}

			if ($this->status=='ok'){
				$this->cache_data = $data['data'];
			}
		}
	}

	public function getStatus() {
		return $this->status;
	}

	public function start() {
		$this->status = 'ok';
		$this->cache_date = mktime();
		$this->cache_data = [];
	}

	public function get($key) {
		if ($this->status!='ok'){
			return false;
		}

		if (array_key_exists($key, $this->cache_data)){
			return $this->cache_data[$key];
		}
		else{
			return false;
		}
	}

	public function set($key, $value) {
		$this->cache_data[$key] = $value;
	}

	public function save() {
		$this->cache_date = mktime();
		$data = ['date' => $this->cache_date, 'data' => $this->cache_data];

		file_put_contents($this->cache_file, json_encode($data));
	}

	public function delete() {
		if ($this->status!='ok'){
			return false;
		}

		if (file_exists($this->cache_file)){
			unlink($this->cache_file);
			return true;
		}

		return false;
	}
}