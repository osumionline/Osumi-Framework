<?php
class G_Config{
  private $modo_debug         = false;
  private $allow_cross_origin = false;

  private $default_modules = array();

  private $ruta_base          = '';
  private $ruta_config        = '';
  private $ruta_gestores      = '';
  private $ruta_gestores_app  = '';
  private $ruta_gestores_base = '';
  private $ruta_logs          = '';
  private $ruta_debug_log     = '';
  private $ruta_tasks         = '';
  private $ruta_web           = '';
  private $ruta_controllers   = '';
  private $ruta_templates     = '';
  private $ruta_photos        = '';

  private $db_user = '';
  private $db_pass = '';
  private $db_host = '';
  private $db_name = '';

  private $url_base    = '';
  private $url_carpeta = '';
  private $url_api     = '';

  private $pagina_cerrada = false;
  private $image_types           = array();

  private $cookie_prefix = '';
  private $cookie_url    = '';

  private $css_list              = array();
  private $ext_css_list          = array();
  private $js_list               = array();
  private $ext_js_list           = array();
  private $default_title         = '';
  private $default_idi           = 1;
  private $default_location_lat  = '';
  private $default_location_long = '';
  private $admin_email           = '';
  private $mailing_from          = '';
  private $lang                  = '';

  function __construct(){}

  // Modo debug
  public function setModoDebug($md){
    $this->modo_debug = $md;
  }

  public function getModoDebug(){
    return $this->modo_debug;
  }

  // Permitir Cross-Origin
  public function setAllowCrossOrigin($aco){
    $this->allow_cross_origin = $aco;
  }

  public function getAllowCrossOrigin(){
    return $this->allow_cross_origin;
  }

  // Módulos por defecto
  public function setDefaultModules($dm){
    $this->default_modules = $dm;
  }

  public function getDefaultModules(){
    return $this->default_modules;
  }

  public function loadDefaultModules(){
    $ruta_base_json = $this->getRutaConfig().'base.json';
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

  // Rutas
  public function setRutaBase($rb){
    $this->ruta_base = $rb;
    $this->setRutaConfig($rb."config/");
    $this->setRutaGestores($rb."gestores/");
    $this->setRutaGestoresApp($rb."gestores/app/");
    $this->setRutaGestoresBase($rb."gestores/base/");
    $this->setRutaLogs($rb."log/");
    $this->setRutaDebugLog($rb."log/debug.log");
    $this->setRutaTasks($rb."task/");
    $this->setRutaWeb($rb."web/");
    $this->setRutaControllers($rb."controllers/");
    $this->setRutaTemplates($rb."templates/");
    $this->setRutaPhotos($rb."web/photos/");
  }

  public function getRutaBase(){
    return $this->ruta_base;
  }

  public function setRutaConfig($rc){
    $this->ruta_config = $rc;
  }

  public function getRutaConfig(){
    return $this->ruta_config;
  }

  public function setRutaGestores($rg){
    $this->ruta_gestores = $rg;
  }

  public function getRutaGestores(){
    return $this->ruta_gestores;
  }

  public function setRutaGestoresApp($rga){
    $this->ruta_gestores_app = $rga;
  }

  public function getRutaGestoresApp(){
    return $this->ruta_gestores_app;
  }

  public function setRutaGestoresBase($rgb){
    $this->ruta_gestores_base = $rgb;
  }

  public function getRutaGestoresBase(){
    return $this->ruta_gestores_base;
  }

  public function setRutaLogs($rl){
    $this->ruta_logs = $rl;
  }

  public function getRutaLogs(){
    return $this->ruta_logs;
  }

  public function setRutaDebugLog($rdl){
    $this->ruta_debug_log = $rdl;
  }

  public function getRutaDebugLog(){
    return $this->ruta_debug_log;
  }

  public function setRutaTasks($rt){
    $this->ruta_tasks = $rt;
  }

  public function getRutaTasks(){
    return $this->ruta_tasks;
  }

  public function setRutaWeb($rw){
    $this->ruta_web = $rw;
  }

  public function getRutaWeb(){
    return $this->ruta_web;
  }

  public function setRutaControllers($rc){
    $this->ruta_controllers = $rc;
  }

  public function getRutaControllers(){
    return $this->ruta_controllers;
  }

  public function setRutaTemplates($rt){
    $this->ruta_templates = $rt;
  }

  public function getRutaTemplates(){
    return $this->ruta_templates;
  }

  public function setRutaPhotos($rp){
    $this->ruta_photos = $rp;
  }

  public function getRutaPhotos(){
    return $this->ruta_photos;
  }

  // Base de datos
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
  public function setUrlBase($ub){
    $this->url_base = $ub;
    $this->setUrlApi($ub.$this->getUrlCarpeta().'api/');
  }

  public function getUrlBase(){
    return $this->url_base;
  }

  public function setUrlCarpeta($uc){
    $this->url_carpeta = $uc;
  }

  public function getUrlCarpeta(){
    return $this->url_carpeta;
  }

  public function setUrlApi($ua){
    $this->url_api = $ua;
  }

  public function getUrlApi(){
    return $this->url_api;
  }

  // Extras
  public function setPaginaCerrada($pc){
    $this->pagina_cerrada = $pc;
  }

  public function getPaginaCerrada(){
    return $this->pagina_cerrada;
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

  public function setDefaultIdi($di){
    $this->default_idi = $di;
  }

  public function getDefaultIdi(){
    return $this->default_idi;
  }

  public function setDefaultLocationLat($dll){
    $this->default_location_lat = $dll;
  }

  public function getDefaultLocationLat(){
    return $this->default_location_lat;
  }

  public function setDefaultLocationLong($dll){
    $this->default_location_long = $dll;
  }

  public function getDefaultLocationLong(){
    return $this->default_location_long;
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