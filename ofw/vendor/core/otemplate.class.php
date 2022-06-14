<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Core;

use OsumiFramework\OFW\Log\Olog;
use OsumiFramework\OFW\Tools\OTools;
use OsumiFramework\Plugins\Translate\OTranslate;

/**
 * OTemplate - Class used by the controllers to show the required template and its data
 */
class OTemplate {
	private string      $environment   = '';
	private bool        $debug         = false;
	private ?OLog       $l             = null;
	private string      $component_dir = '';
	private string      $layout_dir    = '';
	private string      $modules_dir   = '';
	private ?string     $template      = null;
	private string      $action        = '';
	private string      $module        = '';
	private string      $type          = 'html';
	private string      $layout        = '';
	private array       $params        = [];
	private array       $css_list      = [];
	private array       $ext_css_list  = [];
	private array       $js_list       = [];
	private array       $ext_js_list   = [];
	private string      $title         = '';
	private bool        $json          = false;
	private string      $lang          = '';
	private ?OTranslate $translator    = null;
	private array       $return_types  = [
		'html' => 'text/html',
		'json' => 'application/json',
		'xml'  => 'text/xml'
	];

	/**
	 * Load on startup applications configuration and check if there are translations
	 */
	function __construct() {
		global $core;
		$this->environment = $core->config->getEnvironment();
		$this->debug = ($core->config->getLog('level') == 'ALL');
		if ($this->debug) {
			$this->l = new OLog('OTemplate');
		}

		$this->component_dir = $core->config->getDir('app_component');
		$this->layout_dir = $core->config->getDir('app_layout');
		$this->modules_dir = $core->config->getDir('app_module');
		$this->title = $core->config->getDefaultTitle();

		if ($core->config->getPlugin('translate')) {
			$this->lang = $core->config->getLang();
			$this->translator = new OTranslate();
		}
	}

	/**
	 * Logs internal information of the class
	 *
	 * @param string $str String to be logged
	 *
	 * @return void
	 */
	private function log(string $str): void {
		if ($this->debug) {
			$this->l->debug($str);
		}
	}

	/**
	 * Set the module that is being executed
	 *
	 * @param string $m Name of the module
	 *
	 * @return void
	 */
	public function setModule(string $m): void {
		$this->module = $m;
	}

	/**
	 * Set the action of the module to get its template
	 *
	 * @param string $a Name of the action
	 *
	 * @return void
	 */
	public function setAction(string $a): void {
		$this->action = $a;
	}

	/**
	 * Set the return content-type (html / xml / json)
	 *
	 * @param string $t Content-type to return (html / xml / json)
	 *
	 * @return void
	 */
	public function setType(string $t): void {
		$this->type = $t;
	}

	/**
	 * Set the content of the layout of a requested page or call
	 *
	 * @param string $l Content of the layout or false if there is no layout
	 *
	 * @return void
	 */
	public function setLayout(string $l=null): void {
		if (is_null($l)) {
			$l = '';
		}
		$this->layout = $l;
	}

	/**
	 * Read a layout files content and set it to the current template
	 *
	 * @param string $layout Name of the layout file to be loaded
	 *
	 * @return void
	 */
	public function loadLayout(string $layout=null): void {
		if (is_null($layout)) {
			$layout = 'default';
		}
		$this->setLayout( file_get_contents($this->layout_dir.$layout.'.layout.php') );
	}

	/**
	 * Set array of CSS files to be used in the template
	 *
	 * @param array $cl Array of CSS file names to be included
	 *
	 * @return void
	 */
	public function setCssList(array $cl): void {
		$list = [];
		foreach ($cl as $item) {
			array_push($list, ['file' => $item, 'inline' => false]);
		}
		$this->css_list = $list;
	}

	/**
	 * Set array of external CSS file URLs to be used in the application (eg in a CDN)
	 *
	 * @param array $ecl Array of external CSS file URLs to be included
	 *
	 * @return void
	 */
	public function setExtCssList(array $ecl): void {
		$this->ext_css_list = $ecl;
	}

	/**
	 * Set array of JS files to be used in the application
	 *
	 * @param array $jl Array of JS file names to be included
	 *
	 * @return void
	 */
	public function setJsList(array $jl): void {
		$list = [];
		foreach ($jl as $item) {
			array_push($list, ['file' => $item, 'inline' => false]);
		}
		$this->js_list = $list;
	}

	/**
	 * Set array of external JS file URLs to be used in the application (eg in a CDN)
	 *
	 * @param array $ejl Array of external JS file URLs to be included
	 *
	 * @return void
	 */
	public function setExtJsList(array $ejl): void {
		$this->ext_js_list = $ejl;
	}

	/**
	 * Set value of the title of the page (<title> tag)
	 *
	 * @param string $t Title of the page (<title> tag)
	 *
	 * @return void
	 */
	public function setTitle(string $t): void {
		$this->title = $t;
	}

	/**
	 * Set code language to be used on translations (eg "es", "en", "eu"...)
	 *
	 * @param string $l Code language for translations (eg "es", "en", "eu"...)
	 *
	 * @return void
	 */
	public function setLang(string $l): void {
		$this->lang = $l;
	}

	/**
	 * Add a parameter to be used in the template (eg {{title}} -> "Osumi")
	 *
	 * @param string $key Key value in the template that will get substituted (eg {{title}})
	 *
	 * @param string|object $value Value to be substituted
	 *
	 * @param string|int $extra Optional information about the value ('nourlencode' in json files, cut strings if too long...)
	 *
	 * @return void
	 */
	public function add(string $key, $value, $extra=null): void {
		$temp = ['name' => $key, 'value' => strval($value)];
		if (is_object($value) && str_starts_with(get_class($value), 'OsumiFramework\App\Component')) {
			if (property_exists($value, 'css')) {
				foreach ($value->css as $item) {
					$css_path = $value->getPath().$item.'.css';
					if (file_exists($css_path)) {
						$this->addCss($css_path, true);
					}
				}
			}
			if (property_exists($value, 'js')) {
				foreach ($value->js as $item) {
					$js_path = $value->getPath().$item.'.js';
					if (file_exists($js_path)) {
						$this->addJs($js_path, true);
					}
				}
			}
			if (!$value->getUrlEncode()) {
				$extra = 'nourlencode';
			}
		}
		if (!is_null($extra)) {
			$temp['extra'] = $extra;
		}
		array_push($this->params, $temp);
	}

	/**
	 * Adds a single item to the array of CSS files to be included in the template
	 *
	 * @param string $item Name of a CSS file to be included
	 *
	 * @param bool $inline Set if CSS file will be linked or embedded on the resulting HTML
	 *
	 * @return void
	 */
	public function addCss(string $item, bool $inline=false): void {
		$key = array_search($item, array_column($this->css_list, 'file'));
		if ($key===false) {
			array_push($this->css_list, ['file' => $item, 'inline' => $inline]);
		}
	}

	/**
	 * Adds a single item to the array of external CSS file URLs to be included in the template
	 *
	 * @param string $item Name of a CSS file URL to be included
	 *
	 * @return void
	 */
	public function addExtCss(string $item): void {
		array_push($this->ext_css_list, $item);
	}

	/**
	 * Adds a single item to the array of JS files to be included in the template
	 *
	 * @param string $item Name of a JS file to be included
	 *
	 * @param bool $inline Set if JS file will be linked or embedded on the resulting HTML
	 *
	 * @return void
	 */
	public function addJs(string $item, bool $inline=false): void {
		$key = array_search($item, array_column($this->js_list, 'file'));
		if ($key===false) {
			array_push($this->js_list,  ['file' => $item, 'inline' => $inline]);
		}
	}

	/**
	 * Adds a single item to the array of external JS file URLs to be included in the template
	 *
	 * @param string $item Name of a JS file URL to be included
	 *
	 * @return void
	 */
	public function addExtJs(string $item): void {
		array_push($this->ext_js_list, $item);
	}

	/**
	 * Add a php file that can have its own logic into a substitution key on the template
	 *
	 * @param string $where Key value in the template that will get substituted (eg {{users}})
	 *
	 * @param string $name Name of the component file that will be loaded
	 *
	 * @param array $values Array of information that will be loaded into the component
	 *
	 * @return void
	 */
	public function addComponent(string $where, string $name, array $values=[]): void {
		$component_name = $name;
		if (stripos($component_name, '/')!==false) {
			$component_name = array_pop(explode('/', $component_name));
		}

		$component_config_file = $this->component_dir.$name.'/config.json';
		if (file_exists($component_config_file)) {
			$component_config = json_decode(file_get_contents($component_config_file), true);
			if (array_key_exists('css', $component_config)) {
				foreach ($component_config['css'] as $css) {
					$this->addCss($this->component_dir.$name.'/'.$css.'.css', true);
				}
			}
			if (array_key_exists('js', $component_config)) {
				foreach ($component_config['js'] as $js) {
					$this->addJs($this->component_dir.$name.'/'.$js.'.js', true);
				}
			}
		}

		$component_file = $this->component_dir.$name.'/'.$component_name.'.php';
		$output = OTools::getPartial($component_file, $values);

		if (is_null($output)) {
			$output = 'ERROR: File '.$name.' not found';
		}
		$this->add($where, $output, array_key_exists('extra', $values) ? $values['extra'] : null);
	}

	/**
	 * Add a model object's JSON representation into a substitution key on the template
	 *
	 * @param string $where Key value in the template that will get substituted (eg {{users}})
	 *
	 * @param any $obj Model object
	 *
	 * @param array $exclude List of fields to be excluded
	 *
	 * @param array $empty List of fields to be returned empty
	 *
	 * @return void
	 */
	public function addModelComponent(string $where, $obj, array $exclude=[], array $empty=[]): void {
		$this->add($where, OTools::getModelComponent($obj, $exclude, $empty), 'nourlencode');
	}

	/**
	 * Add a list of model object's JSON representations into a substitution key on the template
	 *
	 * @param string $where Key value in the template that will get substituted (eg {{users}})
	 *
	 * @param array $list Model object list
	 *
	 * @param array $exclude List of fields to be excluded
	 *
	 * @param array $empty List of fields to be returned empty
	 *
	 * @return void
	 */
	public function addModelComponentList(string $where, array $list, array $exclude=[], array $empty=[]): void {
		$result = '[';
		$result_list = [];

		foreach ($list as $i => $item) {
			array_push($result_list, OTools::getModelComponent($item, $exclude, $empty));
		}
		$result .= implode(',', $result_list);
		$result .= ']';

		$this->add($where, $result, 'nourlencode');
	}

	/**
	 * Loads all the information (css, js, given parameters, translations) into the module/actions template
	 *
	 * @return string Returns the processed template with all the information
	 */
	public function process(): string {
		global $core;
		$this->log('process - Type: '.$this->type);
		$this->template = file_get_contents($this->modules_dir.$this->module.'/actions/'.$this->action.'/'.$this->action.'.action.'.$this->type);
		foreach ($core->config->getCssList() as $css) {
			$this->addCss($css);
		}
		$this->ext_css_list = array_merge($this->ext_css_list, $core->config->getExtCssList());
		foreach ($core->config->getJsList() as $js) {
			$this->addJs($js);
		}
		$this->ext_js_list = array_merge($this->ext_js_list, $core->config->getExtJsList());

		$layout   = $this->layout;
		$str_body = $this->template;

		// If type is html, add 'title', 'css' and 'js'
		if ($this->type==='html') {
			// Add html lang, if present
			if (stripos($layout, '<html lang="') !== false && !empty($this->lang)) {
				$layout = preg_replace('/<html lang="(.*?)"/i', '<html lang="'.$this->lang.'"', $layout);
			}

			// Add title
			$layout = preg_replace('/<title>(.*?)<\/title>/i', '<title>'.$this->title.'</title>', $layout);

			// Add css
			$str_css = '';
			$this->log('process - CSS: '.count($this->css_list));

			foreach ($this->css_list as $css_item) {
				if (!$css_item['inline']) {
					$str_css .= '<link rel="stylesheet" media="screen" type="text/css" href="/css/'.$css_item['file'].'.css">'."\n";
				}
				else {
					$str_css .= '<style type="text/css">'.file_get_contents($css_item['file']).'</style>'."\n";
				}
			}

			// Add external css
			$this->log('process - Ext CSS: '.count($this->ext_css_list));

			foreach ($this->ext_css_list as $ext_css_item) {
				$str_css .= '<link rel="stylesheet" media="screen" type="text/css" href="'.$ext_css_item.'">'."\n";
			}

			$layout = str_replace(['</head>'], $str_css.'</head>', $layout);

			// Add js
			$str_js = '';
			$this->log('process - JS: '.count($this->js_list));

			foreach ($this->js_list as $js_item) {
				if (!$js_item['inline']) {
					$str_js .= '<script src="/js/'.$js_item['file'].'.js"></script>'."\n";
				}
				else {
					$str_js .= '<script>'.file_get_contents($js_item['file']).'</script>'."\n";
				}
			}

			// Add external js
			$this->log('process - Ext JS: '.count($this->ext_js_list));

			foreach ($this->ext_js_list as $ext_js_item) {
				$str_js .= '<script src="'.$ext_js_item.'"></script>'."\n";
			}

			$layout = str_replace(['</head>'], $str_js.'</head>', $layout);
		}

		// Add parameters to the body
		$this->log('process - Params:');
		$this->log(var_export($this->params, true));

		foreach ($this->params as $param) {
			$sub_value = ($this->type!=='html') ? urlencode(strval($param['value'])) : $param['value'];
			if (isset($param['extra']) && $param['extra'] === 'nourlencode') {
				$sub_value = $param['value'];
			}

			$str_body = str_replace(['{{'.$param['name'].'}}'], $sub_value, $str_body);
			$layout = str_replace(['{{'.$param['name'].'}}'], $sub_value, $layout);
		}

		// Add body to the layout
		if ($this->type==='html') {
			$layout = str_replace(['{{body}}'], $str_body, $layout);
		}
		else {
			$layout = $str_body;
		}

		// Add translations
		if (!is_null($this->translator) && $this->translator->getPage()!='') {
			// Add page specific translations
			$trads = $this->translator->getTranslations();
			foreach ($trads as $trad=>$obj) {
				$layout = str_replace(['{{trans_'.$trad.'}}'], $obj[$this->lang], $layout);
			}
			// Add global translations
			$this->translator->setPage('general');
			$trads = $this->translator->getTranslations();
			foreach ($trads as $trad=>$obj) {
				$layout = str_replace(['{{trans_general_'.$trad.'}}'], $obj[$this->lang], $layout);
			}
		}

		// If type is not html is most likely it's and API call so tell the browsers not to cache it
		if ($this->type!=='html') {
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		}
		// If the request is a JSON and we are in production environment, encode and decode it to send minified
		if ($this->environment=='prod' && $this->type=='json') {
			$layout = json_encode(json_decode($layout));
		}

		header('Content-type: '.$this->return_types[$this->type]);
		header('X-Powered-By: Osumi Framework '.OTools::getVersion());

		return $layout;
	}
}
