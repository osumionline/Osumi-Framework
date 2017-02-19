<?php
class OConfig{
  private $debug_mode         = false;
  private $allow_cross_origin = false;

  private $default_modules = array();
  private $packages        = array();

  private $dirs = array();
  private $db = array(
    'user' => '',
    'pass' => '',
    'host' => '',
    'name' => ''
  );
  private $urls = array(
    'base'   => '',
    'folder' => '',
    'api'    => ''
  );

  private $backend = array(
    'user' => '',
    'pass' => ''
  );

  private $closed      = false;

  private $cookie_prefix = '';
  private $cookie_url    = '';

  private $url_list      = null;
  
  private $error_pages  = array(
    '403' => null,
    '404' => null,
    '500' => null
  );

  private $css_list              = array();
  private $ext_css_list          = array();
  private $js_list               = array();
  private $ext_js_list           = array();
  private $default_title         = '';
  private $default_lang          = 'es';
  private $admin_email           = '';
  private $mailing_from          = '';
  private $lang                  = '';
  private $image_types           = array();

  private $extras = array();

  function __construct(){}

  // Debug mode
  function setDebugMode($dm){
    $this->debug_mode = $dm;
  }
  function getDebugMode(){
    return $this->debug_mode;
  }

  // Allow Cross-Origin
  public function setAllowCrossOrigin($aco){
    $this->allow_cross_origin = $aco;
  }
  public function getAllowCrossOrigin(){
    return $this->allow_cross_origin;
  }

  // Default modules
  public function setDefaultModules($dm){
    $this->default_modules = $dm;
  }
  public function getDefaultModules(){
    return $this->default_modules;
  }

  public function loadDefaultModules(){
    $ruta_base_json = $this->getDir('config').'base.json';
    if (file_exists($ruta_base_json)){
      $base_json = json_decode( file_get_contents($ruta_base_json), true );
      $this->setDefaultModules($base_json);
    }
  }

  public function getDefaultModule($m){
    $base_modules = $this->getDefaultModules();
    if (array_key_exists($m,$base_modules['base_modules']) && $base_modules['base_modules'][$m]===true){
      return true;
    }
    else{
      return false;
    }
  }

  // Packages
  public function setPackages($p){
    $this->packages = $p;
  }
  public function getPackages(){
    return $this->packages;
  }

  public function loadPackages(){
    $ruta_base_json = $this->getDir('config').'packages.json';
    if (file_exists($ruta_base_json)){
      $base_json = json_decode( file_get_contents($ruta_base_json), true );
      $this->setPackages($base_json);
    }
  }

  public function getPackage($p){
    $packages = $this->getPackages();
    if (array_key_exists($p,$packages['packages']) && $packages['packages'][$p]===true){
      return true;
    }
    else{
      return false;
    }
  }

  // Dirs
  function setDir($dir,$value){
    $this->dirs[$dir] = $value;
  }
  function getDir($dir){
    return array_key_exists($dir, $this->dirs) ? $this->dirs[$dir] : null;
  }

  function setBaseDir($bd){
    $this->setDir('base',           $bd);
    $this->setDir('cache',          $bd.'cache/');
    $this->setDir('config',         $bd.'config/');
    $this->setDir('controllers',    $bd.'controllers/');
    $this->setDir('model',          $bd.'model/');
    $this->setDir('model_app',      $bd.'model/app/');
    $this->setDir('model_base',     $bd.'model/base/');
    $this->setDir('model_lib',      $bd.'model/lib/');
    $this->setDir('model_packages', $bd.'model/packages/');
    $this->setDir('model_static',   $bd.'model/static/');
    $this->setDir('model_filters',  $bd.'model/filters/');
    $this->setDir('logs',           $bd.'logs/');
    $this->setDir('debug_log',      $bd.'logs/debug.log');
    $this->setDir('task',           $bd.'task/');
    $this->setDir('sql',            $bd.'sql/');
    $this->setDir('templates',      $bd.'templates/');
    $this->setDir('tmp',            $bd.'tmp/');
    $this->setDir('web',            $bd.'web/');
    $this->setDir('img',            $bd.'web/img/');
    $this->setDir('thumb',          $bd.'web/img/thumb');
  }

  // Data base
  public function setDB($key,$value){
    $this->db[$key] = $value;
  }
  public function getDB($key){
    return array_key_exists($key, $this->db) ? $this->db[$key] : null;
  }

  // Urls
  function setUrl($key,$url){
    $this->urls[$key] = $url;
  }
  public function getUrl($key){
    return array_key_exists($key, $this->urls) ? $this->urls[$key] : null;
  }

  function setBaseUrl($bu){
    $this->setUrl('base', $bu);
    $this->setUrl('api',  $bu.$this->getUrl('folder').'api/');
  }

  // Backend
  function setBackend($key,$value){
    $this->backend[$key] = $value;
  }

  function getBackend($key){
    return array_key_exists($key, $this->backend) ? $this->backend[$key] : null;
  }

  // Extras
  function setClosed($c){
    $this->closed = $c;
  }
  function getClosed(){
    return $this->closed;
  }

  public function setImageTypes($it){
    $this->image_types = $it;
  }
  public function getImageTypes(){
    return $this->image_types;
  }

  // Cookies
  public function setCookiePrefix($cp){
    $this->cookie_prefix = $cp;
  }
  public function getCookiePrefix(){
    return $this->cookie_prefix;
  }

  public function setCookieUrl($cu){
    $this->cookie_url = $cu;
  }
  public function getCookieUrl(){
    return $this->cookie_url;
  }

  // Url cache
  public function setUrlList($u){
    $this->url_list = $u;
  }

  public function getUrlList(){
    return $this->url_list;
  }
  
  // Error pages
  public function setErrorPage($num,$url){
    $this->error_pages[$num] = $url;
  }
  
  public function getErrorPage($num){
    if (array_key_exists($num, $this->error_pages)){
      return $this->error_pages[$num];
    }
    return null;
  }

  // Templates
  public function setCssList($cl){
    $this->css_list = $cl;
  }
  public function getCssList(){
    return $this->css_list;
  }

  public function addCssList($item){
    $css_list = $this->getCssList();
    array_push($css_list,$item);
    $this->setCssList($css_list);
  }

  public function setExtCssList($ecl){
    $this->ext_css_list = $ecl;
  }
  public function getExtCssList(){
    return $this->ext_css_list;
  }

  public function addExtCssList($item){
    $css_list = $this->getExtCssList();
    array_push($css_list,$item);
    $this->setExtCssList($css_list);
  }

  public function setJsList($jl){
    $this->js_list = $jl;
  }
  public function getJsList(){
    return $this->js_list;
  }

  public function addJsList($item){
    $js_list = $this->getJsList();
    array_push($js_list,$item);
    $this->setJsList($js_list);
  }

  public function setExtJsList($ejl){
    $this->ext_js_list = $ejl;
  }
  public function getExtJsList(){
    return $this->ext_js_list;
  }

  public function addExtJsList($item){
    $js_list = $this->getExtJsList();
    array_push($js_list,$item);
    $this->setExtJsList($js_list);
  }

  public function setDefaultTitle($dt){
    $this->default_title = $dt;
  }
  public function getDefaultTitle(){
    return $this->default_title;
  }

  public function setDefaultLang($dl){
    $this->default_lang = $dl;
  }
  public function getDefaultLang(){
    return $this->default_lang;
  }

  public function setAdminEmail($ae){
    $this->admin_email = $ae;
  }
  public function getAdminEmail(){
    return $this->admin_email;
  }

  public function setMailingFrom($mf){
    $this->mailing_from = $mf;
  }
  public function getMailingFrom(){
    return $this->mailing_from;
  }

  public function setLang($l){
    $this->lang= $l;
  }
  public function getLang(){
    return $this->lang;
  }

  // Extras
  function setExtra($key,$value){
    $this->extras[$key] = $value;
  }
  function getExtra($key){
    return array_key_exists($key, $this->extras) ? $this->extras[$key] : null;
  }
}