<?php
  class G_Template{
    private $modo_debug     = false;
    private $l = null;
    private $ruta_templates = '';
    private $template       = null;
    private $action         = '';
    private $module         = '';
    private $layout         = '';
    private $params         = array();
    private $url_carpeta    = '';
    private $css_list       = array();
    private $ext_css_list   = array();
    private $mq_css_list    = array();
    private $js_list        = array();
    private $ext_js_list    = array();
    private $title          = '';
    private $json           = false;
    private $flash          = '';
    private $lang           = '';
    private $translator     = null;

    function G_Template(){
      global $c, $where;
      $this->setModoDebug($c->getModoDebug());

      $l = new G_Log();
      $this->setLog($l);
      $this->getLog()->setPagina($where);
      $this->getLog()->setGestor('G_Template');

      $this->setRutaTemplates( $c->getRutaTemplates() );
      $this->setCssList( $c->getCssList() );
      $this->setJsList( $c->getJsList() );
      $this->setExtJsList( $c->getExtJsList() );
      $this->setTitle( $c->getDefaultTitle() );
      $this->setUrlCarpeta( $c->getUrlCarpeta() );

      $this->setLang( $c->getLang() );

      $tr = new G_Translate();
      $this->setTranslator($tr);
    }

    public function setModoDebug($md){
      $this->modo_debug = $md;
    }

    public function getModoDebug(){
      return $this->modo_debug;
    }

    public function setLog($l){
      $this->l = $l;
    }

    public function getLog(){
      return $this->l;
    }

    function setRutaTemplates($rt){
      $this->ruta_templates = $rt;
    }

    function getRutaTemplates(){
      return $this->ruta_templates;
    }

    public function setTemplate($t){
      $this->template = $t;
    }

    public function getTemplate(){
      return $this->template;
    }

    public function setAction($a){
      $this->action = $a;
    }

    public function getAction(){
      return $this->action;
    }

    public function setModule($m){
      $this->module = $m;
    }

    public function getModule(){
      return $this->module;
    }

    public function setParams($p){
      $this->params = $p;
    }

    public function getParams(){
      return $this->params;
    }

    public function setUrlCarpeta($uc){
      $this->url_carpeta = $uc;
    }

    public function getUrlCarpeta(){
      return $this->url_carpeta;
    }

    public function setLayout($l){
      if ($l === false){
        $l = '';
      }

      $this->layout = $l;
    }

    public function getLayout(){
      return $this->layout;
    }

    public function loadLayout($layout){
      $this->setLayout( file_get_contents($this->getRutaTemplates().'layout/'.$layout.'.php') );
    }

    public function setCssList($cl){
      $this->css_list = $cl;
    }

    public function getCssList(){
      return $this->css_list;
    }

    public function setExtCssList($ecl){
      $this->ext_css_list = $ecl;
    }

    public function getExtCssList(){
      return $this->ext_css_list;
    }

    public function setMQCssList($ecl){
      $this->mq_css_list = $ecl;
    }

    public function getMQCssList(){
      return $this->mq_css_list;
    }

    public function setJsList($jl){
      $this->js_list = $jl;
    }

    public function getJsList(){
      return $this->js_list;
    }

    public function setExtJsList($ejl){
      $this->ext_js_list = $ejl;
    }

    public function getExtJsList(){
      return $this->ext_js_list;
    }

    public function setTitle($t){
      $this->title = $t;
    }

    public function getTitle(){
      return $this->title;
    }

    public function setJson($j){
      $this->json = $j;
    }

    public function getJson(){
      return $this->json;
    }

    public function setFlash($f){
      $this->flash = $f;
    }

    public function getFlash(){
      return $this->flash;
    }

    public function setLang($l){
      $this->lang = $l;
    }

    public function getLang(){
      return $this->lang;
    }

    public function setTranslator($t){
      $this->translator = $t;
    }

    public function getTranslator(){
      return $this->translator;
    }

    public function add($key,$value,$extra=null){
      $params = $this->getParams();
      $temp = array('name' => $key, 'value' => $value);
      if (!is_null($extra)){
        $temp['extra'] = $extra;
      }
      array_push($params, $temp);

      $this->setParams($params);
    }

    public function addCss($new_css){
      $css = $this->getCssList();
      array_push($css, $new_css);

      $this->setCssList($css);
    }

    public function addExtCss($new_ext_css){
      $ext_css = $this->getExtCssList();
      array_push($ext_css, $new_ext_css);

      $this->setExtCssList($ext_css);
    }

    public function addMQCss($mq,$new_mq_css){
      $mq_css = $this->getMQCssList();
      $mq_css[$mq] = $new_mq_css;

      $this->setMQCssList($mq_css);
    }

    public function addJs($new_js){
      $js = $this->getJsList();
      array_push($js, $new_js);

      $this->setJsList($js);
    }

    public function addExtJs($new_ext_js){
      $ext_js = $this->getExtJsList();
      array_push($ext_js, $new_ext_js);

      $this->setExtJsList($ext_js);
    }

    public function addPartial($where, $name, $values=array()){
      ob_start();
      include($this->getRutaTemplates().'partials/'.$name.'.php');
      $output = ob_get_contents();
      ob_end_clean();
      if (!array_key_exists('extra',$values)){
        $this->add($where,$output);
      }
      else{
        $this->add($where,$output,$values['extra']);
      }
    }
    
    public function readPartial($name, $values){
      $filename = $this->getRutaTemplates().'partials/'.$name.'.php';
      if (!file_exists($filename)){
        return '';
      }
      ob_start();
      include($filename);
      $output = ob_get_contents();
      ob_end_clean();

      foreach ($values as $key => $value){
        if (!is_object($value) && !is_array($value)){
          $output = str_replace(array('{{'.strtoupper($key).'}}'), $value, $output);
        }
      }

      return $output;
    }

    public function process(){
      global $c;
      $template = $this->getRutaTemplates().$this->getModule().'/'.$this->getAction().'.php';
      $this->setTemplate(file_get_contents($template));

      $cad      = $this->getLayout();
      $p        = $this->getParams();
      $css      = $this->getCssList();
      $ext_css  = $this->getExtCssList();
      $mq_css   = $this->getMQCssList();
      $js       = $this->getJsList();
      $ext_js   = $this->getExtJsList();
      $title    = $this->getTitle();
      $cad_body = $this->getTemplate();

      // Si no es JSON, por defecto, añado titulo, css y js
      if (!$this->getJson()){
        // Añado titulo a la pagina
        $cad = str_replace(array('{{TITLE}}'), $title, $cad);
  
        // Añado css
        $cad_css = '';
        foreach ($css as $css_item){
          $css_data = array();
          if (stripos($css_item, '#')){
            $css_data = explode('#', $css_item);
            $css_item = array_shift($css_data);
          }
          $cad_css .= '<link rel="stylesheet" media="screen" type="text/css" href="/'.$this->getUrlCarpeta().'css/'.$css_item.'.css" ';
          foreach ($css_data as $css_data_item){
            $css_extra_data = explode('=', $css_data_item);
            $cad_css .= $css_extra_data[0].'="'.$css_extra_data[1].'" ';
          }
          $cad_css .= ' />'."\n";
        }

        // Añado css externos
        $cad_ext_css = '';
        foreach ($ext_css as $ext_css_item){
          $cad_ext_css .= '<link rel="stylesheet" media="screen" type="text/css" href="'.$ext_css_item.'" />'."\n";
        }

        // Añado ambos css
        $cad_css .= $cad_ext_css;

        // Añado css con media querys
        $cad_mq_css = '';
        foreach ($mq_css as $mq => $css_item){
          $cad_mq_css .= '<link rel="stylesheet" media="'.$mq.'" type="text/css" href="/'.$this->getUrlCarpeta().'css/'.$css_item.'.css" />'."\n";
        }

        // Añado al css
        $cad_css .= $cad_mq_css;

        $cad = str_replace(array('{{CSS}}'), $cad_css, $cad);

        // Añado js
        $cad_js = '';
        foreach ($js as $js_item){
          $cad_js .= '<script type="text/javascript" src="/'.$this->getUrlCarpeta().'js/'.$js_item.'.js"></script>'."\n";
        }

        // Añado js externos
        $cad_ext_js = '';
        foreach ($ext_js as $ext_js_item){
          $cad_ext_js .= '<script type="text/javascript" src="'.$ext_js_item.'"></script>'."\n";
        }

        // Uno ambos js
        $cad_js .= $cad_ext_js;

        $cad = str_replace(array('{{JS}}'), $cad_js, $cad);

        // Tiene mensaje flash?
        if ($this->getFlash() != ''){
          $cad_flash = $this->readPartial('common/flash',array('flash' => $this->getFlash()));
          $cad = str_replace(array('{{FLASH}}'), $cad_flash, $cad);

          global $s;
          $s->addParam('flash','');
        }
        else{
          $cad = str_replace(array('{{FLASH}}'), '', $cad);
        }
      }

      // Añado parametros al cuerpo
      foreach ($p as $param){
        $sub_value = ($this->getJson())?urlencode($param['value']):$param['value'];
        if (isset($param['extra']) && $param['extra'] == 'nourlencode'){
          $sub_value = $param['value'];
        }

        $cad_body = str_replace(array('{{'.strtoupper($param['name']).'}}'), $sub_value, $cad_body);
        $cad = str_replace(array('{{'.strtoupper($param['name']).'}}'), $sub_value, $cad);
      }

      // Añado carpeta imágenes
      if (stripos($cad_body, '{{IMG}}')){
        $cad_body = str_replace(array('{{IMG}}'), '/'.$c->getUrlCarpeta().'img/', $cad_body);
        $cad_body = str_replace(array('{{IMG_URL}}'), '/'.$c->getUrlCarpeta().'img/', $cad_body);
      }

      // Añado cuerpo al layout
      if (!$this->getJson()){
        $cad = str_replace(array('{{BODY}}'), $cad_body, $cad);
      }
      else{
        $cad = $cad_body;
      }

      // Añado traducciones
      if ($c->getDefaultModule('translate')){
        if ($this->getTranslator()->getPag()!=''){
          // Añado traducciones específicas de la página
          $trads = $this->getTranslator()->getTranslations();
          foreach ($trads as $trad=>$obj){
            $cad = str_replace(array('{{TRANS_'.strtoupper($trad).'}}'), $obj[$this->getLang()], $cad);
          }
          // Añado traducciones generales
          $this->getTranslator()->setPag('general');
          $trads = $this->getTranslator()->getTranslations();
          foreach ($trads as $trad=>$obj){
            $cad = str_replace(array('{{TRANS_GENERAL_'.strtoupper($trad).'}}'), $obj[$this->getLang()], $cad);
          }
        }
      }

      if ($this->getJson()){
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
      }
      else{
        header('Content-type: text/html');
      }

      echo $cad;
    }
  }