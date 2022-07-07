<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Routing;

use OsumiFramework\OFW\Routing\OModuleAction;
use OsumiFramework\OFW\Core\OConfig;
use OsumiFramework\OFW\Core\OTemplate;
use OsumiFramework\OFW\DB\ODB;
use OsumiFramework\OFW\Log\OLog;
use OsumiFramework\OFW\Web\OSession;
use OsumiFramework\OFW\Web\OCookie;
use OsumiFramework\OFW\Cache\OCacheContainer;
use OsumiFramework\OFW\Tools\OTools;

/**
 * OAction - Base class for the module actions providing access to the framework configuration, database, template, logs, session or cookies
 */
class OAction {
	protected ?OModuleAction   $attributes = null;
	protected ?OConfig         $config     = null;
	protected ?ODB             $db         = null;
	protected ?OTemplate       $template   = null;
	protected ?OLog            $log        = null;
	protected ?OSession        $session    = null;
	protected ?OCookie         $cookie     = null;
	protected ?OCacheContainer $cacheContainer = null;

	/**
	 * Load matched URL configuration value into the module
	 *
	 * @param array $url_result Configuration array as in urls.json
	 *
	 * @param OModuleAction $attributes Action attributes
	 *
	 * @return void
	 */
	public final function loadAction(array $url_result, OModuleAction $attributes): void {
		global $core;

		$this->attributes = $attributes;
		$this->config     = $core->config;
		$this->session    = $core->session;
		$this->cacheContainer = $core->cacheContainer;
		if (!is_null($core->dbContainer)) {
			$this->db = new ODB();
		}
		$this->template = new OTemplate();
		$this->log      = new OLog(get_class($this));
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

		// Load action's required services
		foreach ($this->attributes->getServices() as $item) {
			OTools::loadService($item);
			$service_name = "\\OsumiFramework\\App\\Service\\".$item.'Service';
			$service = new $service_name;
			$service->loadService();
			$this->{$item.'_service'} = $service;
		}

		// Load action's CSS and JS files
		foreach ($this->attributes->getInlineCss() as $item) {
			$css_file = $this->config->getDir('app_module').$url_result['module'].'/actions/'.$url_result['action'].'/'.$item.'.css';
			$this->template->addCss($css_file, true);
		}

		foreach ($this->attributes->getCss() as $item) {
			$this->template->addCss($item);
		}

		foreach ($this->attributes->getInlineJs() as $item) {
			$js_file = $this->config->getDir('app_module').$url_result['module'].'/actions/'.$url_result['action'].'/'.$item.'.js';
			$this->template->addJs($js_file, true);
		}

		foreach ($this->attributes->getJs() as $item) {
			$this->template->addJs($item);
		}
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

	/**
	 * Get access to the cache container
	 *
	 * @return OCacheContainer Cache container class object
	 */
	public final function getCacheContainer(): OCacheContainer {
		return $this->cacheContainer;
	}
}
