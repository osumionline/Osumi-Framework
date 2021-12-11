<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Routing;

use OsumiFramework\OFW\Core\OConfig;
use OsumiFramework\OFW\Tools\OTools;

/**
 * OUrl - Class with methods to check required URL, get its data, generate new URLs or redirect the user to a new one
 */
class OUrl {
	private ?OConfig $config      = null;
	private ?array   $urls        = null;
	private string   $check_url   = '';
	private array    $url_params  = [];
	private string   $method      = '';

	/**
	 * Loads user defined urls, used method to access and URL and path to the routing library
	 *
	 * @param string $method Method used to access the URL (get / post / delete)
	 */
	function __construct(string $method) {
		global $core;
		$this->config = $core->config;
		$this->method = $method;
		$this->urls   = $this->loadUrls();
	}

	/**
	 * Loads URLS from the flattened-cached file. If it doesn't exist it generates it.
	 *
	 * @return array Array of configured URLs
	 */
	public static function loadUrls(): array {
		global $core;
		$urls_cache_file = $core->cacheContainer->getItem('urls');

		// If it doesn't exist, generate it
		if (!$urls_cache_file->isHit() || $urls_cache_file->get()===null){
			OTools::updateUrls(true);
			$urls_cache_file->reload();
		}

		// App urls
		if (is_null($core->config->getUrlList())) {
			$core->config->setUrlList(json_decode($urls_cache_file->get(), true));
		}
		return $core->config->getUrlList();
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
	public function setCheckUrl(string $check_url, array $get=null, array $post=null, array $files=null): void {
		$this->check_url = $check_url;
		$check_params = stripos($check_url, '?');
		if ($check_params !== false) {
			$this->check_url = substr($check_url, 0, $check_params);
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
	public function process(string $url=null): array {
		if (!is_null($url)) {
			$this->check_url = $url;
		}

		$found = false;
		$i     = 0;
		$ret   = [
			'module'  => '',
			'action'  => '',
			'type'    => 'html',
			'params'  => [],
			'headers' => getallheaders(),
			'method'  => strtolower($this->method),
			'layout'  => 'default',
			'res'     => false
		];

		while (!$found && $i<count($this->urls)) {
			$route = new ORouteCheck($this->urls[$i]['url']);
			$chk = $route->matchesUrl($this->check_url);

			// If there is a match, return urls.json values plus the parameters in the route
			if (!is_null($chk)) {
				$found         = true;
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
	 * @param string $module Module of the action
	 *
	 * @param string $action Action whose url has to be generated
	 *
	 * @param array $params Array of parameters to build the URL in case of a dynamic URL (eg /user/:id/:slug -> /user/1/igorosabel)
	 *
	 * @param bool $absolute If true returns an absolute URL and if false returns a partial URL
	 *
	 * @return string Generated URL with given parameters
	 */
	public static function generateUrl(string $module, string $action, array $params=[], bool $absolute=false): string {
		// Load URLs, as it's a static method it won't go through the constructor
		global $core;
		$urls = self::loadUrls();

		$found = false;
		$i   = 0;
		$url = '';

		while (!$found && $i<count($urls)) {
			if ($urls[$i]['module']==$module && $urls[$i]['action']==$action) {
				$url = $urls[$i]['url'];
				$found = true;
			}
			$i++;
		}

		if ($found) {
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
	public static function goToUrl(string $url): void {
		header('Location:'.$url);
		exit;
	}
}