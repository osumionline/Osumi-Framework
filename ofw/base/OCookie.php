<?php
class OCookie {
	private $config      = null;
	private $cookie_list = [];

	function __construct() {
		global $c;
		$this->config = $c;
	}

	function setCookieList($l) {
		$this->cookie_list = $l;
	}

	function getCookieList() {
		return $this->cookie_list;
	}

	function addCookieToList($k, $v) {
		$list = $this->getCookieList();
		$list[$k] = $v;

		setcookie ($this->config->getCookiePrefix().'['.$k.']', $v, time() + (3600*24*31), '/', $this->config->getCookieUrl());

		$this->setCookieList($list);
	}

	function getCookie($k) {
		$list = $this->getCookieList();
		if (array_key_exists($k, $list)){
			return $list[$k];
		}
		else{
			return false;
		}
	}

	function loadCookies() {
		$list = [];

		if (isset($_COOKIE[$this->config->getCookiePrefix()])) {
			foreach ($_COOKIE[$this->config->getCookiePrefix()] as $name => $value) {
				$name = htmlspecialchars($name);
				$value = htmlspecialchars($value);

				$list[$name] = $value;
			}
		}

		$this->setCookieList($list);
	}

	function saveCookies() {
		$list = $this->getCookieList();

		foreach ($list as $key => $value){
			setcookie ($this->config->getCookiePrefix().'['.$key.']', $value, time() + (3600*24*31), '/', $this->config->getCookieUrl());
		}
	}

	function cleanCookies(){
		$list = $this->getCookieList();

		foreach ($list as $key => $value){
			setcookie ($this->config->getCookiePrefix().'['.$key.']', $value, 1, '/', $this->config->getCookieUrl());
		}

		$this->setCookieList([]);
	}
}