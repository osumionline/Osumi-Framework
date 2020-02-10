<?php
class OCacheContainer{
	private $list = [];

	public function set($key, $value){
		$this->list[$key] = $value;
	}

	public function get($key){
		return array_key_exists($key, $this->list) ? $this->list[$key] : false;
	}

	public function remove($key){
		unset($this->list[$key]);
	}
}