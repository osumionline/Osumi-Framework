<?php
/**
 * OUrl - Class with methods to check required URL, get its data, generate new URLs or redirect the user to a new one
 */
class OUrl {
	private $config      = null;
	private $urls        = null;
	private $check_url   = '';
	private $routing_dir = '';
	private $url_params  = [];
	private $method      = '';

	/**
	 * Loads user defined urls, used method to access and URL and path to the routing library
	 *
	 * @param string $method Method used to access the URL (get / post / delete)
	 *
	 * @return void
	 */
	function __construct($method) {
		global $core;
		$this->config = $core->config;
		$this->method = $method;
		$this->urls = $this->loadUrls();

		// Routing lib dir
		$this->routing_dir = $this->config->getDir('ofw_lib').'routing/';
	}

	/**
	 * Loads URLS from the flattened-cached file. If it doesn't exist it generates it.
	 *
	 * @return array Array of configured URLs
	 */
	public function loadUrls() {
		$urls_cache_file = $this->config->getDir('app_cache').'urls.cache.json';
		if (!file_exists($urls_cache_file)){
			OTools::updateUrls(true);
		}

		// App urls
		if (is_null($this->config->getUrlList())) {
			$this->config->setUrlList(json_decode(file_get_contents($urls_cache_file), true));
		}
		return $this->config->getUrlList();
	}

	/**
	 * Sets URL to be checked and loads all passed parameters (get / post / files / document body)
	 *
	 * @param string $check_url URL to be checked
	 *
	 * @param array $get Array of parameters passed by GET method
	 *
	 * @param array $post Array of parameters passed by POST method
	 *
	 * @param array $files Array of files submitted by a form (multipart/form-data)
	 *
	 * @return void
	 */
	public function setCheckUrl($check_url, $get=null, $post=null, $files=null) {
		$this->check_url = $check_url;
		$check_params = stripos($check_url, '?');
		if ($check_params !== false) {
			$check_url = substr($check_url, 0, $check_params);
		}
		if (!is_null($get)) {
			foreach ($get as $key => $value) {
				$this->url_params[$key] = $value;
			}
		}
		if (!is_null($post)) {
			foreach ($post as $key => $value) {
				$this->url_params[$key] = $value;
			}
		}
		if (!is_null($files)) {
			foreach ($files as $key => $value) {
				$this->url_params[$key] = $value;
			}
		}
		$input = json_decode(file_get_contents('php://input'), true);
		if (!is_null($input)) {
			foreach ($input as $key => $value) {
				$this->url_params[$key] = $value;
			}
		}
	}

	/**
	 * Process the given URL checking it against user defined URLs and get its configuration information if found
	 *
	 * @param string $url URL to be checked
	 *
	 * @return array Array of configuration information
	 */
	public function process($url=null) {
		if (!is_null($url)) {
			$this->check_url = $url;
		}

		$found = false;
		$i     = 0;
		$ret   = [
			'id'      => '',
			'module'  => '',
			'action'  => '',
			'type'    => 'html',
			'params'  => [],
			'headers' => getallheaders(),
			'method'  => strtolower($this->method),
			'layout'  => 'default',
			'res'     => false
		];

		// Include Symfony routing
		require_once($this->routing_dir.'sfRoute.class.php');
		while (!$found && $i<count($this->urls)) {
			$route = new sfRoute($this->urls[$i]['url']);
			$chk = $route->matchesUrl($this->check_url);

			// If there is a match, return urls.json values plus the parameters in the route
			if ($chk !== false) {
				$found         = true;
				$ret['id']     = $this->urls[$i]['id'];
				$ret['module'] = $this->urls[$i]['module'];
				$ret['action'] = $this->urls[$i]['action'];
				$ret['res']    = true;

				if (array_key_exists('type', $this->urls[$i])) {
					$ret['type'] = $this->urls[$i]['type'];
				}
				if (array_key_exists('layout', $this->urls[$i])) {
					$ret['layout'] = $this->urls[$i]['layout'];
				}
				if (array_key_exists('filter', $this->urls[$i])) {
					$ret['filter'] = $this->urls[$i]['filter'];
				}

				$ret['params'] = $chk;

				foreach ($this->url_params as $key => $value) {
					$ret['params'][$key] = $value;
				}
			}

			$i++;
		}
		return $ret;
	}

	/**
	 * Static method to generate a URL for a user configured URL
	 *
	 * @param string $id Id of the URL in the urls.json file
	 *
	 * @param array $params Array of parameters to build the URL in case of a dynamic URL (eg /user/:id/:slug -> /user/1/igorosabel)
	 *
	 * @param boolean $absolute If true returns an absolute URL and if false returns a partial URL
	 *
	 * @return string Generated URL with given parameters
	 */
	public static function generateUrl($id, $params=[], $absolute=null) {
		// Load URLs, as it's a static method it won't go through the constructor
		global $core;
		$urls = self::loadUrls();

		$found = false;
		$i   = 0;
		$url = '';

		while (!$found && $i<count($urls)) {
			if ($urls[$i]['id'] == $id) {
				$url = $urls[$i]['url'];
				$found = true;
			}
			$i++;
		}

		if (!$found) {
			$url = '';
		}
		else {
			foreach ($params as $key => $value) {
				$url = str_replace(':'.$key, $value, $url);
			}
		}

		if ($absolute === true) {
			$base = $core->config->getUrl('base');
			$base = substr($base, 0, strlen($base)-1);

			$url = $base.$url;
		}

		return $url;
	}

	/**
	 * Static method to redirect the user to a new URL using a 301 redirect
	 *
	 * @param string $url URL where the user will be redirected
	 *
	 * @return void
	 */
	public static function goToUrl($url) {
		header('Location:'.$url);
		exit;
	}
}