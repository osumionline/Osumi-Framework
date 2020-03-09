<?php
class OConfig {
	private $debug_mode         = false;
	private $allow_cross_origin = true;

	private $plugins  = [];
	private $packages = [];

	private $dirs = [];
	private $db = [
		'driver'  => 'mysql',
		'user'    => '',
		'pass'    => '',
		'host'    => '',
		'name'    => '',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_unicode_ci'
	];
	private $urls = [
		'base'   => '',
		'folder' => '',
		'api'    => ''
	];

	private $backend = [
		'user' => '',
		'pass' => ''
	];

	private $smtp = [
		'host'   => '',
		'port'   => null,
		'secure' => 'tls',
		'user'   => '',
		'pass'   => ''
	];

	private $closed = false;

	private $cookie_prefix = '';
	private $cookie_url    = '';

	private $url_list = null;

	private $error_pages  = [
		'403' => null,
		'404' => null,
		'500' => null
	];

	private $css_list      = [];
	private $ext_css_list  = [];
	private $js_list       = [];
	private $ext_js_list   = [];
	private $default_title = '';
	private $default_lang  = 'es';
	private $admin_email   = '';
	private $mailing_from  = '';
	private $lang          = '';
	private $image_types   = [];

	private $libs = [];

	private $extras = [];

	function __construct($bd) {
		$this->setBaseDir($bd);
		$json_file = $this->getDir('app_config').'config.json';
		if (!file_exists($json_file)){
			echo "ERROR: config.json file not found.\n";
			exit;
		}
		$config = json_decode( file_get_contents($json_file), true );
		if (!$config){
			echo "ERROR: config.json file is malformed.\n";
			exit;
		}
		$this->loadConfig($config);
		if (array_key_exists('environment', $config)){
			$json_env_file = $this->getDir('app_config').'config.'.$config['environment'].'.json';
			if (!file_exists($json_env_file)){
				echo "ERROR: config.".$config['environment'].".json file not found.\n";
				exit;
			}
			$config_env = json_decode( file_get_contents($json_env_file), true );
			if (!$config_env){
				echo "ERROR: config.".$config['environment'].".json file is malformed.\n";
				exit;
			}
			$this->loadConfig($config_env);
		}
		$plugins_file = $this->getDir('app_config').'plugins.json';
		if (file_exists($plugins_file)){
			$plugins = json_decode( file_get_contents($plugins_file), true );
			if (array_key_exists('plugins', $plugins) && is_array($plugins['plugins'])){
				$this->setPlugins($plugins['plugins']);
			}
		}
	}

	function loadConfig($config) {
		if (array_key_exists('packages', $config)){
			$this->setPackages($config['packages']);
		}
		if (array_key_exists('db', $config)){
			$db_fields = ['driver', 'host', 'user', 'pass', 'name', 'charset', 'collate'];
			foreach ($db_fields as $db_field){
				if (array_key_exists($db_field, $config['db'])){
					$this->setDB($db_field, $config['db'][$db_field]);
				}
			}
		}
		if (array_key_exists('cookies', $config)){
			if (array_key_exists('prefix', $config['cookies'])){
				$this->setCookiePrefix($config['cookies']['prefix']);
			}
			if (array_key_exists('url', $config['cookies'])){
				$this->setCookieUrl($config['cookies']['url']);
			}
		}
		if (array_key_exists('debug_mode', $config) && $config['debug_mode']===true){
			$this->setDebugMode(true);
		}
		if (array_key_exists('base_url', $config)){
			$this->setBaseUrl($config['base_url']);
		}
		if (array_key_exists('admin_email', $config)){
			$this->setAdminEmail($config['admin_email']);
		}
		if (array_key_exists('default_title', $config)){
			$this->setDefaultTitle($config['default_title']);
		}
		if (array_key_exists('lang', $config)){
			$this->setLang($config['lang']);
		}
		if (array_key_exists('smtp', $config)){
			$smtp_fields = ['host', 'port', 'secure', 'user', 'pass'];
			foreach ($smtp_fields as $smtp_field){
				if (array_key_exists($smtp_field, $config['smtp'])){
					$this->setSMTP($smtp_field, $config['smtp'][$smtp_field]);
				}
			}
		}
		if (array_key_exists('error_pages', $config)){
			$error_fields = ['404', '403', '500'];
			foreach ($error_fields as $error_field){
				if (array_key_exists($error_field, $config['error_pages'])){
					$this->setErrorPage($error_field, $config['error_pages'][$error_field]);
				}
			}
		}
		if (array_key_exists('css', $config)){
			$this->setCssList($config['css']);
		}
		if (array_key_exists('ext_css', $config)){
			$this->setExtCssList($config['ext_css']);
		}
		if (array_key_exists('js', $config)){
			$this->setJsList($config['js']);
		}
		if (array_key_exists('ext_js', $config)){
			$this->setExtJsList($config['ext_js']);
		}
		if (array_key_exists('extra', $config)){
			foreach ($config['extra'] as $key => $value){
				$this->setExtra($key, $value);
			}
		}
		if (array_key_exists('dir', $config)){
			$dir_list = $this->getDir();
			$dir_from = [];
			$dir_to = [];
			foreach ($dir_list as $key => $value){
				array_push($dir_from, '{{'.$key.'}}');
				array_push($dir_to, $value);
			}
			foreach ($config['dir'] as $key => $value){
				$this->setDir($key, str_ireplace($dir_from, $dir_to, $value));
			}
		}
		if (array_key_exists('libs', $config)){
			$this->setLibs($config['libs']);
		}
	}

	// Debug mode
	function setDebugMode($dm) {
		$this->debug_mode = $dm;
	}
	function getDebugMode() {
		return $this->debug_mode;
	}

	// Allow Cross-Origin
	public function setAllowCrossOrigin($aco) {
		$this->allow_cross_origin = $aco;
	}
	public function getAllowCrossOrigin() {
		return $this->allow_cross_origin;
	}

	// Default modules
	public function setPlugins($p) {
		$this->plugins = $p;
	}
	public function getPlugins() {
		return $this->plugins;
	}

	public function getPlugin($p) {
		return in_array($p, $this->plugins);
	}

	// Packages
	public function setPackages($p) {
		$this->packages = $p;
	}
	public function getPackages() {
		return $this->packages;
	}

	public function getPackage($p) {
		$packages = $this->getPackages();
		if (array_key_exists($p,$packages) && $packages[$p]===true){
			return true;
		}
		else{
			return false;
		}
	}

	// Dirs
	function setDir($dir,$value) {
		$this->dirs[$dir] = $value;
	}
	function getDir($dir=null) {
		if (is_null($dir)){
			return $this->dirs;
		}
		return array_key_exists($dir, $this->dirs) ? $this->dirs[$dir] : null;
	}

	function setBaseDir($bd) {
		$this->setDir('base',           $bd);
		$this->setDir('app',            $bd."app/");
		$this->setDir('app_cache',      $bd.'app/cache/');
		$this->setDir('app_config',     $bd.'app/config/');
		$this->setDir('app_controller', $bd.'app/controller/');
		$this->setDir('app_filter',     $bd.'app/filter/');
		$this->setDir('app_model',      $bd.'app/model/');
		$this->setDir('app_service',    $bd.'app/service/');
		$this->setDir('app_template',   $bd.'app/template/');
		$this->setDir('app_task',       $bd.'app/task/');
		$this->setDir('ofw_base',       $bd.'ofw/base/');
		$this->setDir('ofw_lib',        $bd.'ofw/lib/');
		$this->setDir('ofw_plugins',    $bd.'ofw/plugins/');
		$this->setDir('ofw_packages',   $bd.'ofw/packages/');
		$this->setDir('ofw_task',       $bd.'ofw/task/');
		$this->setDir('ofw_export',     $bd.'ofw/export/');
		$this->setDir('ofw_tmp',        $bd.'ofw/tmp/');
		$this->setDir('logs',           $bd.'logs/');
		$this->setDir('debug_log',      $bd.'logs/debug.log');
		$this->setDir('web',            $bd.'web/');
	}

	// Data base
	public function setDB($key, $value) {
		$this->db[$key] = $value;
	}
	public function getDB($key) {
		return array_key_exists($key, $this->db) ? $this->db[$key] : null;
	}

	// Urls
	function setUrl($key, $url) {
		$this->urls[$key] = $url;
	}
	public function getUrl($key) {
		return array_key_exists($key, $this->urls) ? $this->urls[$key] : null;
	}

	function setBaseUrl($bu) {
		$this->setUrl('base', $bu);
		$this->setUrl('api',  $bu.$this->getUrl('folder').'api/');
	}

	// Backend
	function setBackend($key, $value) {
		$this->backend[$key] = $value;
	}
	function getBackend($key) {
		return array_key_exists($key, $this->backend) ? $this->backend[$key] : null;
	}

	// SMTP
	function setSMTP($key, $value) {
		$this->smtp[$key] = $value;
	}
	function getSMTP($key=null) {
		if (is_null($key)){
			return $this->smtp;
		}
		return array_key_exists($key, $this->smtp) ? $this->smtp[$key] : null;
	}

	// Extras
	function setClosed($c) {
		$this->closed = $c;
	}
	function getClosed() {
		return $this->closed;
	}

	public function setImageTypes($it) {
		$this->image_types = $it;
	}
	public function getImageTypes() {
		return $this->image_types;
	}

	// Cookies
	public function setCookiePrefix($cp) {
		$this->cookie_prefix = $cp;
	}
	public function getCookiePrefix() {
		return $this->cookie_prefix;
	}

	public function setCookieUrl($cu) {
		$this->cookie_url = $cu;
	}
	public function getCookieUrl() {
		return $this->cookie_url;
	}

	// Url cache
	public function setUrlList($u) {
		$this->url_list = $u;
	}
	public function getUrlList() {
		return $this->url_list;
	}

	// Error pages
	public function setErrorPage($num, $url) {
		$this->error_pages[$num] = $url;
	}
	public function getErrorPage($num) {
		if (array_key_exists($num, $this->error_pages)){
			return $this->error_pages[$num];
		}
		return null;
	}

	// Templates
	public function setCssList($cl) {
		$this->css_list = $cl;
	}
	public function getCssList() {
		return $this->css_list;
	}

	public function addCssList($item) {
		array_push($this->css_list, $item);
	}

	public function setExtCssList($ecl) {
		$this->ext_css_list = $ecl;
	}
	public function getExtCssList() {
		return $this->ext_css_list;
	}

	public function addExtCssList($item) {
		array_push($this->ext_css_list, $item);
	}

	public function setJsList($jl) {
		$this->js_list = $jl;
	}
	public function getJsList() {
		return $this->js_list;
	}

	public function addJsList($item) {
		array_push($this->js_list, $item);
	}

	public function setExtJsList($ejl) {
		$this->ext_js_list = $ejl;
	}
	public function getExtJsList() {
		return $this->ext_js_list;
	}

	public function addExtJsList($item) {
		array_push($this->ext_js_list, $item);
	}

	public function setDefaultTitle($dt) {
		$this->default_title = $dt;
	}
	public function getDefaultTitle() {
		return $this->default_title;
	}

	public function setDefaultLang($dl) {
		$this->default_lang = $dl;
	}
	public function getDefaultLang() {
		return $this->default_lang;
	}

	public function setAdminEmail($ae) {
		$this->admin_email = $ae;
	}
	public function getAdminEmail() {
		return $this->admin_email;
	}

	public function setMailingFrom($mf) {
		$this->mailing_from = $mf;
	}
	public function getMailingFrom() {
		return $this->mailing_from;
	}

	public function setLang($l) {
		$this->lang= $l;
	}
	public function getLang() {
		return $this->lang;
	}

	// Libs
	public function setLibs($l) {
		$this->libs = $l;
	}
	public function getLibs() {
		return $this->libs;
	}

	public function addLib($item) {
		array_push($this->libs, $item);
	}

	// Extras
	function setExtra($key, $value) {
		$this->extras[$key] = $value;
	}
	function getExtra($key) {
		return array_key_exists($key, $this->extras) ? $this->extras[$key] : null;
	}
}