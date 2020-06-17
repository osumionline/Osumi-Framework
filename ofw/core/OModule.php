<?php declare(strict_types=1);
/**
 * OModule - Base class for the module classes providing access to the framework configuration, database, template, logs, session or cookies
 */
class OModule {
	protected ?OConfig   $config   = null;
	protected ?ODB       $db       = null;
	protected ?OTemplate $template = null;
	protected ?OLog      $log      = null;
	protected ?OSession  $session  = null;
	protected ?OCookie   $cookie   = null;

	/**
	 * Load matched URL configuration value into the module
	 *
	 * @param array $url_result Configuration array as in urls.json
	 *
	 * @return void
	 */
	public final function loadModule(array $url_result): void {
		global $core;

		$this->config   = $core->config;
		$this->db       = new ODB();
		$this->template = new OTemplate();
		$this->log      = new OLog(get_class($this));
		$this->session  = new OSession();
		$this->cookie   = new OCookie();

		// Current and previous module
		if ($this->session->getParam('current') != '') {
			$this->session->addParam('previous', $this->session->getParam('current'));
		}
		$this->session->addParam('current', $url_result['module'].'/'.$url_result['action']);

		// Load module, action and layout into the template
		$this->template->setModule($url_result['module']);
		$this->template->setAction($url_result['action']);
		$this->template->setType($url_result['type']);
		$this->template->loadLayout($url_result['layout']);
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
	 * Get a preloaded object to access the database
	 *
	 * @return ODB Database access object
	 */
	public final function getDB(): ODB {
		return $this->db;
	}

	/**
	 * Get access to the module's template via a template configuration class object
	 *
	 * @return OTemplate Template configuration class object
	 */
	public final function getTemplate(): OTemplate {
		return $this->template;
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
	 * Get access to the users session information
	 *
	 * @return OSession Session configuration class object
	 */
	public final function getSession(): OSession {
		return $this->session;
	}

	/**
	 * Get access to the users cookies
	 *
	 * @return OCookie Cookie configuration class object
	 */
	public final function getCookie(): OCookie {
		return $this->cookie;
	}
}