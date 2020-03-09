<?php
class OController {
	protected $config   = null;
	protected $db       = null;
	protected $template = null;
	protected $log      = null;
	protected $session  = null;
	protected $cookie   = null;

	public final function loadController($url_result) {
		global $c;

		$this->config   = $c;
		$this->db       = new ODB();
		$this->template = new OTemplate();
		$this->log      = new OLog();
		$this->session  = new OSession();
		$this->cookie   = new OCookie();

		// Módulo/acción actual y anterior
		if ($this->session->getParam('current') != ''){
			$this->session->addParam('previous', $this->session->getParam('current'));
		}
		$this->session->addParam('current', $url_result['module'].'/'.$url_result['action']);

		// Cargo módulo, acción y layout en el template
		$this->template->setModule($url_result['module']);
		$this->template->setAction($url_result['action']);
		$this->template->setType($url_result['type']);
		$this->template->loadLayout($url_result['layout']);
		if (array_key_exists('package', $url_result)){
			$this->template->setPackage($url_result['package']);
		}

		// Inicializo el log
		$this->log->setSection($url_result['id']);
		$this->log->setModel($url_result['action']);
	}

	public final function setConfig($config) {
		$this->config = $config;
	}

	public final function getConfig() {
		return $this->config;
	}

	public final function setDB($db) {
		$this->db = $db;
	}

	public final function getDB() {
		return $this->db;
	}

	public final function setTemplate($template) {
		$this->template = $template;
	}

	public final function getTemplate() {
		return $this->template;
	}

	public final function setLog($log) {
		$this->log = $log;
	}

	public final function getLog() {
		return $this->log;
	}

	public final function setSession($session) {
		$this->session = $session;
	}

	public final function getSession() {
		return $this->session;
	}

	public final function setCookie($cookie) {
		$this->cookie = $cookie;
	}

	public final function getCookie() {
		return $this->cookie;
	}
}