<?php
class G_Config{
  private $debug_mode         = false;
  private $allow_cross_origin = false;

  private $default_modules = array();

  private $base_dir         = '';
  private $cache_dir        = '';
  private $config_dir       = '';
  private $controllers_dir  = '';
  private $model_dir        = '';
  private $model_dir_app    = '';
  private $model_dir_base   = '';
  private $model_dir_static = '';
  private $logs_dir         = '';
  private $debug_log_dir    = '';
  private $tasks_dir        = '';
  private $sql_dir          = '';
  private $templates_dir    = '';
  private $tmp_dir          = '';
  private $web_dir          = '';
  private $img_dir          = '';
  private $thumb_dir        = '';

  private $db_user = '';
  private $db_pass = '';
  private $db_host = '';
  private $db_name = '';

  private $base_url   = '';
  private $folder_url = '';
  private $api_url    = '';

  private $closed      = false;

  private $cookie_prefix = '';
  private $cookie_url    = '';

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
    $ruta_base_json = $this->getConfigDir().'base.json';
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

  // Dirs
  function setBaseDir($bd){
    $this->base_dir = $bd;
    $this->setCacheDir(       $bd.'cache/');
    $this->setConfigDir(      $bd.'config/');
    $this->setControllersDir( $bd.'controllers/');
    $this->setModelDir(       $bd.'model/');
    $this->setModelDirApp(    $bd.'model/app/');
    $this->setModelDirBase(   $bd.'model/base/');
    $this->setModelDirStatic( $bd.'model/static/');
    $this->setLogsDir(        $bd.'logs/');
    $this->setDebugLogDir(    $bd.'logs/debug.log');
    $this->setTasksDir(       $bd.'task/');
    $this->setSQLDir(         $bd.'sql/');
    $this->setTemplatesDir(   $bd.'templates/');
    $this->setTmpDir(         $bd.'tmp/');
    $this->setWebDir(         $bd.'web/');
    $this->setImgDir(         $bd.'web/img/');
    $this->setThumbDir(       $bd.'web/img/thumb');
  }
  
  function getBaseDir(){
    return $this->base_dir;
  }

  function setCacheDir($cd){
    $this->cache_dir = $cd;
  }
  function getCacheDir(){
    return $this->cache_dir;
  }
  
  function setConfigDir($cd){
    $this->config_dir = $cd;
  }
  function getConfigDir(){
    return $this->config_dir;
  }

  function setControllersDir($cd){
    $this->controllers_dir = $cd;
  }
  function getControllersDir(){
    return $this->controllers_dir;
  }
  
  function setModelDir($md){
    $this->model_dir = $md;
  }
  function getModelDir(){
    return $this->model_dir;
  }
  
  function setModelDirApp($mda){
    $this->model_dir_app = $mda;
  }
  function getModelDirApp(){
    return $this->model_dir_app;
  }
  
  function setModelDirBase($mdb){
    $this->model_dir_base = $mdb;
  }
  function getModelDirBase(){
    return $this->model_dir_base;
  }
  
  function setModelDirStatic($mds){
    $this->model_dir_static = $mds;
  }
  function getModelDirStatic(){
    return $this->model_dir_static;
  }
  
  function setLogsDir($ld){
    $this->logs_dir = $ld;
  }
  function getLogsDir(){
    return $this->logs_dir;
  }
  
  function setDebugLogDir($dld){
    $this->debug_log_dir = $dld;
  }
  function getDebugLogDir(){
    return $this->debug_log_dir;
  }
  
  function setTasksDir($td){
    $this->tasks_dir = $td;
  }
  function getTasksDir(){
    return $this->tasks_dir;
  }

  function setSQLDir($sd){
    $this->sql_dir = $sd;
  }
  function getSQLDir(){
    return $this->sql_dir;
  }

  function setTemplatesDir($td){
    $this->templates_dir = $td;
  }
  function getTemplatesDir(){
    return $this->templates_dir;
  }

  function setTmpDir($td){
    $this->tmp_dir = $td;
  }
  function getTmpDir(){
    return $this->tmp_dir;
  }
  
  function setWebDir($wd){
    $this->web_dir = $wd;
  }
  function getWebDir(){
    return $this->web_dir;
  }

  function setImgDir($id){
    $this->img_dir = $id;
  }
  function getImgDir(){
    return $this->img_dir;
  }

  function setThumbDir($td){
    $this->thumb_dir = $td;
  }
  function getThumbDir(){
    return $this->thumb_dir;
  }

  // Data base
  public function setDbUser($du){
    $this->db_user = $du;
  }
  public function getDbUser(){
    return $this->db_user;
  }

  public function setDbPass($dp){
    $this->db_pass = $dp;
  }
  public function getDbPass(){
    return $this->db_pass;
  }

  public function setDbHost($dh){
    $this->db_host = $dh;
  }
  public function getDbHost(){
    return $this->db_host;
  }

  public function setDbName($dn){
    $this->db_name = $dn;
  }
  public function getDbName(){
    return $this->db_name;
  }

  // Urls
  function setBaseUrl($bu){
    $this->base_url = $bu;
    $this->setApiUrl($bu.$this->getFolderUrl().'api/');
  }
  function getBaseUrl(){
    return $this->base_url;
  }
  
  function setFolderUrl($fu){
    $this->folder_url = $fu;
  }
  function getFolderUrl(){
    return $this->folder_url;
  }
    
  function setApiUrl($au){
    $this->api_url = $au;
  }
  function getApiUrl(){
    return $this->api_url;
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
}