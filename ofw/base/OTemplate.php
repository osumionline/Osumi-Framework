<?php
class OTemplate {
	private $debug_mode    = false;
	private $l             = null;
	private $templates_dir = '';
	private $template      = null;
	private $action        = '';
	private $module        = '';
	private $type          = 'html';
	private $layout        = '';
	private $params        = [];
	private $folder_url    = '';
	private $css_list      = [];
	private $ext_css_list  = [];
	private $mq_css_list   = [];
	private $js_list       = [];
	private $ext_js_list   = [];
	private $title         = '';
	private $json          = false;
	private $lang          = '';
	private $translator    = null;
	private $package       = null;

	function __construct() {
		global $c, $where;
		$this->setDebugMode($c->getDebugMode());

		$l = new OLog();
		$this->setLog($l);
		$this->getLog()->setSection($where);
		$this->getLog()->setModel('OTemplate');

		$this->setTemplatesDir( $c->getDir('app_template') );
		$this->setTitle( $c->getDefaultTitle() );
		$this->setFolderUrl( $c->getUrl('folder') );

		$this->setLang( $c->getLang() );

		if ($c->getPlugin('translate')){
			$tr = new OTranslate();
			$this->setTranslator($tr);
		}
	}

	public function setDebugMode($dm) {
		$this->debug_mode = $dm;
	}

	public function getDebugMode() {
		return $this->debug_mode;
	}

	public function setLog($l) {
		$this->l = $l;
	}

	public function getLog() {
		return $this->l;
	}

	function setTemplatesDir($td) {
		$this->templates_dir = $td;
	}

	function getTemplatesDir() {
		return $this->templates_dir;
	}

	public function setTemplate($t) {
		$this->template = $t;
	}

	public function getTemplate() {
		return $this->template;
	}

	public function setAction($a) {
		$this->action = $a;
	}

	public function getAction() {
		return $this->action;
	}

	public function setModule($m) {
		$this->module = $m;
	}

	public function getModule() {
		return $this->module;
	}

	public function setType($t) {
		$this->type = $t;
	}

	public function getType() {
		return $this->type;
	}

	public function setParams($p) {
		$this->params = $p;
	}

	public function getParams() {
		return $this->params;
	}

	public function setFolderUrl($fu) {
		$this->folder_url = $fu;
	}

	public function getFolderUrl() {
		return $this->folder_url;
	}

	public function setLayout($l) {
		if ($l === false){
			$l = '';
		}
		$this->layout = $l;
	}

	public function getLayout() {
		return $this->layout;
	}

	public function loadLayout($layout) {
		$this->setLayout( file_get_contents($this->getTemplatesDir().'layout/'.$layout.'.php') );
	}

	public function setCssList($cl) {
		$this->css_list = $cl;
	}

	public function getCssList() {
		return $this->css_list;
	}

	public function setExtCssList($ecl) {
		$this->ext_css_list = $ecl;
	}

	public function getExtCssList() {
		return $this->ext_css_list;
	}

	public function setMQCssList($ecl) {
		$this->mq_css_list = $ecl;
	}

	public function getMQCssList() {
		return $this->mq_css_list;
	}

	public function setJsList($jl) {
		$this->js_list = $jl;
	}

	public function getJsList() {
		return $this->js_list;
	}

	public function setExtJsList($ejl) {
		$this->ext_js_list = $ejl;
	}

	public function getExtJsList() {
		return $this->ext_js_list;
	}

	public function setTitle($t) {
		$this->title = $t;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setLang($l) {
		$this->lang = $l;
	}

	public function getLang() {
		return $this->lang;
	}

	public function setTranslator($t) {
		$this->translator = $t;
	}

	public function getTranslator() {
		return $this->translator;
	}

	public function setPackage($p) {
		global $c;
		$this->package = $p;
		$this->setTemplatesDir($c->getDir('ofw_packages').$p.'/templates/');
	}

	public function getPackage() {
		return $this->package;
	}

	public function add($key, $value, $extra=null) {
		$params = $this->getParams();
		$temp = ['name' => $key, 'value' => $value];
		if (!is_null($extra)){
			$temp['extra'] = $extra;
		}
		array_push($params, $temp);

		$this->setParams($params);
	}

	public function addCss($new_css) {
		$css = $this->getCssList();
		array_push($css, $new_css);

		$this->setCssList($css);
	}

	public function addExtCss($new_ext_css) {
		$ext_css = $this->getExtCssList();
		array_push($ext_css, $new_ext_css);

		$this->setExtCssList($ext_css);
	}

	public function addMQCss($mq,$new_mq_css) {
		$mq_css = $this->getMQCssList();
		$mq_css[$mq] = $new_mq_css;

		$this->setMQCssList($mq_css);
	}

	public function addJs($new_js) {
		$js = $this->getJsList();
		array_push($js, $new_js);

		$this->setJsList($js);
	}

	public function addExtJs($new_ext_js) {
		$ext_js = $this->getExtJsList();
		array_push($ext_js, $new_ext_js);

		$this->setExtJsList($ext_js);
	}

	public function addPartial($where, $name, $values=[]) {
		$partial_file = $this->getTemplatesDir().'partials/'.$name.'.php';
		if (file_exists($partial_file)){
			ob_start();
			include($partial_file);
			$output = ob_get_contents();
			ob_end_clean();
		}
		else{
			$output = 'ERROR: No existe el archivo '.$name;
		}
		if (!array_key_exists('extra',$values)){
			$this->add($where,$output);
		}
		else{
			$this->add($where,$output,$values['extra']);
		}
	}

	public function readPartial($name, $values) {
		$filename = $this->getTemplatesDir().'partials/'.$name.'.php';
		if (!file_exists($filename)){
			return '';
		}
		ob_start();
		include($filename);
		$output = ob_get_contents();
		ob_end_clean();

		foreach ($values as $key => $value){
			if (!is_object($value) && !is_array($value)){
				$output = str_replace(['{{'.$key.'}}'], $value, $output);
			}
		}

		return $output;
	}

	public function process() {
		global $c;
		$template = $this->getTemplatesDir().$this->getModule().'/'.$this->getAction().'.php';

		$this->setTemplate(file_get_contents($template));

		$this->setExtCssList( array_merge($this->getExtCssList(),$c->getExtCssList()) );
		$this->setCssList( array_merge($this->getCssList(),$c->getCssList()) );
		$this->setJsList( array_merge($this->getJsList(),$c->getJsList()) );
		$this->setExtJsList( array_merge($this->getExtJsList(),$c->getExtJsList()) );

		$str      = $this->getLayout();
		$p        = $this->getParams();
		$css      = $this->getCssList();
		$ext_css  = $this->getExtCssList();
		$mq_css   = $this->getMQCssList();
		$js       = $this->getJsList();
		$ext_js   = $this->getExtJsList();
		$title    = $this->getTitle();
		$str_body = $this->getTemplate();

		// Si no es JSON, por defecto, añado titulo, css y js
		if ($this->getType()==='html'){
			// Añado titulo a la pagina
			$str = str_replace(['{{title}}'], $title, $str);

			// Añado css
			$str_css = '';
			$css_base = '/'.$this->getFolderUrl().'css/';
			if (!is_null($this->getPackage())){
				$css_base = '/'.$this->getFolderUrl().'pkg/'.$this->getPackage().'/css/';
			}
			foreach ($css as $css_item){
				$css_data = [];
				if (stripos($css_item, '#')){
					$css_data = explode('#', $css_item);
					$css_item = array_shift($css_data);
				}
				$str_css .= '<link rel="stylesheet" media="screen" type="text/css" href="'.$css_base.$css_item.'.css" ';
				foreach ($css_data as $css_data_item){
					$css_extra_data = explode('=', $css_data_item);
					$str_css .= $css_extra_data[0].'="'.$css_extra_data[1].'" ';
				}
				$str_css .= ' />'."\n";
			}

			// Añado css externos
			$str_ext_css = '';
			foreach ($ext_css as $ext_css_item){
				$str_ext_css .= '<link rel="stylesheet" media="screen" type="text/css" href="'.$ext_css_item.'" />'."\n";
			}

			// Añado ambos css
			$str_css .= $str_ext_css;

			// Añado css con media querys
			$str_mq_css = '';
			foreach ($mq_css as $mq => $css_item){
				$str_mq_css .= '<link rel="stylesheet" media="'.$mq.'" type="text/css" href="/'.$this->getFolderUrl().'css/'.$css_item.'.css" />'."\n";
			}

			// Añado al css
			$str_css .= $str_mq_css;

			$str = str_replace(['{{css}}'], $str_css, $str);

			// Añado js
			$str_js = '';
			$js_base = '/'.$this->getFolderUrl().'js/';
			if (!is_null($this->getPackage())){
				$js_base = '/'.$this->getFolderUrl().'pkg/'.$this->getPackage().'/js/';
			}
			foreach ($js as $js_item){
				$str_js .= '<script type="text/javascript" src="'.$js_base.$js_item.'.js"></script>'."\n";
			}

			// Añado js externos
			$str_ext_js = '';
			foreach ($ext_js as $ext_js_item){
				$str_ext_js .= '<script type="text/javascript" src="'.$ext_js_item.'"></script>'."\n";
			}

			// Uno ambos js
			$str_js .= $str_ext_js;

			$str = str_replace(['{{js}}'], $str_js, $str);
		}

		// Añado parametros al cuerpo
		foreach ($p as $param){
			$sub_value = ($this->getType()!=='html')?urlencode($param['value']):$param['value'];
			if (isset($param['extra']) && $param['extra'] === 'nourlencode'){
				$sub_value = $param['value'];
			}

			$str_body = str_replace(['{{'.$param['name'].'}}'], $sub_value, $str_body);
			$str = str_replace(['{{'.$param['name'].'}}'], $sub_value, $str);
		}

		// Añado carpeta imágenes
		if (stripos($str_body, '{{img}}')){
			$str_body = str_replace(['{{img}}'], '/'.$c->getUrl('folder').'img/', $str_body);
			$str_body = str_replace(['{{img_url}}'], '/'.$c->getUrl('folder').'img/', $str_body);
		}

		// Añado cuerpo al layout
		if ($this->getType()==='html'){
			$str = str_replace(['{{body}}'], $str_body, $str);
		}
		else{
			$str = $str_body;
		}

		// Añado traducciones
		if ($c->getPlugin('translate') && $this->getTranslator()->getPage()!=''){
			// Añado traducciones específicas de la página
			$trads = $this->getTranslator()->getTranslations();
			foreach ($trads as $trad=>$obj){
				$str = str_replace(['{{trans_'.$trad.'}}'], $obj[$this->getLang()], $str);
			}
			// Añado traducciones generales
			$this->getTranslator()->setPage('general');
			$trads = $this->getTranslator()->getTranslations();
			foreach ($trads as $trad=>$obj){
				$str = str_replace(['{{trans_general_'.$trad.'}}'], $obj[$this->getLang()], $str);
			}
		}

		if ($this->getType()!=='html'){
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		}
		header('Content-type: text/'.$this->getType());

		echo $str;
	}
}