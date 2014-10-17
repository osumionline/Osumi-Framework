<?php
class G_Config{
  private $modo_debug = false;

  private $ruta_base                = '';
  private $ruta_config              = '';
  private $ruta_gestores            = '';
  private $ruta_gestores_app        = '';
  private $ruta_gestores_base       = '';
  private $ruta_logs                = '';
  private $ruta_debug_log           = '';
  private $ruta_tasks               = '';
  private $ruta_web                 = '';
  private $ruta_controllers         = '';
  private $ruta_templates           = '';
  private $ruta_photos              = '';

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
  function setModoDebug($md){
    $this->modo_debug = $md;
  }

  function getModoDebug(){
    return $this->modo_debug;
  }

  // Rutas
  function setRutaBase($rb){
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

  function getRutaBase(){
    return $this->ruta_base;
  }

  function setRutaConfig($rc){
    $this->ruta_config = $rc;
  }

  function getRutaConfig(){
    return $this->ruta_config;
  }

  function setRutaGestores($rg){
    $this->ruta_gestores = $rg;
  }

  function getRutaGestores(){
    return $this->ruta_gestores;
  }

  function setRutaGestoresApp($rga){
    $this->ruta_gestores_app = $rga;
  }

  function getRutaGestoresApp(){
    return $this->ruta_gestores_app;
  }

  function setRutaGestoresBase($rgb){
    $this->ruta_gestores_base = $rgb;
  }

  function getRutaGestoresBase(){
    return $this->ruta_gestores_base;
  }

  function setRutaLogs($rl){
    $this->ruta_logs = $rl;
  }

  function getRutaLogs(){
    return $this->ruta_logs;
  }

  function setRutaDebugLog($rdl){
    $this->ruta_debug_log = $rdl;
  }

  function getRutaDebugLog(){
    return $this->ruta_debug_log;
  }

  function setRutaTasks($rt){
    $this->ruta_tasks = $rt;
  }

  function getRutaTasks(){
    return $this->ruta_tasks;
  }

  function setRutaWeb($rw){
    $this->ruta_web = $rw;
  }

  function getRutaWeb(){
    return $this->ruta_web;
  }

  function setRutaControllers($rc){
    $this->ruta_controllers = $rc;
  }

  function getRutaControllers(){
    return $this->ruta_controllers;
  }

  function setRutaTemplates($rt){
    $this->ruta_templates = $rt;
  }

  function getRutaTemplates(){
    return $this->ruta_templates;
  }

  function setRutaPhotos($rp){
    $this->ruta_photos = $rp;
  }

  function getRutaPhotos(){
    return $this->ruta_photos;
  }

  // Base de datos
  function setDbUser($du){
    $this->db_user = $du;
  }

  function getDbUser(){
    return $this->db_user;
  }

  function setDbPass($dp){
    $this->db_pass = $dp;
  }

  function getDbPass(){
    return $this->db_pass;
  }

  function setDbHost($dh){
    $this->db_host = $dh;
  }

  function getDbHost(){
    return $this->db_host;
  }

  function setDbName($dn){
    $this->db_name = $dn;
  }

  function getDbName(){
    return $this->db_name;
  }

  // Urls
  function setUrlBase($ub){
    $this->url_base = $ub;
    $this->setUrlApi($ub.$this->getUrlCarpeta().'api/');
  }

  function getUrlBase(){
    return $this->url_base;
  }

  function setUrlCarpeta($uc){
    $this->url_carpeta = $uc;
  }

  function getUrlCarpeta(){
    return $this->url_carpeta;
  }

  function setUrlApi($ua){
    $this->url_api = $ua;
  }

  function getUrlApi(){
    return $this->url_api;
  }

  // Extras
  function setPaginaCerrada($pc){
    $this->pagina_cerrada = $pc;
  }

  function getPaginaCerrada(){
    return $this->pagina_cerrada;
  }

  function setImageTypes($it){
    $this->image_types = $it;
  }

  function getImageTypes(){
    return $this->image_types;
  }

  // Cookies
  function setCookiePrefix($cp){
    $this->cookie_prefix = $cp;
  }

  function getCookiePrefix(){
    return $this->cookie_prefix;
  }

  function setCookieUrl($cu){
    $this->cookie_url = $cu;
  }

  function getCookieUrl(){
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

  function setAdminEmail($ae){
    $this->admin_email = $ae;
  }

  function getAdminEmail(){
    return $this->admin_email;
  }

  function setMailingFrom($mf){
    $this->mailing_from = $mf;
  }

  function getMailingFrom(){
    return $this->mailing_from;
  }

  function setLang($l){
    $this->lang= $l;
  }

  function getLang(){
    return $this->lang;
  }
}