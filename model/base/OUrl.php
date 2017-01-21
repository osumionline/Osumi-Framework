<?php
class OUrl{
  private $debug_mode  = false;
  private $l           = null;
  private $urls        = null;
  private $check_url   = '';
  private $routing_dir = '';
  private $url_params  = array();
  private $method      = '';
  private $ret_data    = array();

  function __construct($method){
    global $c, $where;
    $this->setDebugMode($c->getDebugMode());

    $l = new OLog();
    $this->setLog($l);
    $this->getLog()->setSection($where);
    $this->getLog()->setModel('OUrl');

    $this->setMethod( $method );

    $this->setUrls($this->loadUrls());

    // Routing lib dir
    $this->setRoutingDir( $c->getDir('model_lib').'routing/' );
  }

  public function setDebugMode($dm){
    $this->debug_mode = $dm;
  }
  public function getDebugMode(){
    return $this->debug_mode;
  }

  public function setLog($l){
    $this->l = $l;
  }
  public function getLog(){
    return $this->l;
  }

  public function setUrls($u){
    $this->urls = $u;
  }
  public function getUrls(){
    return $this->urls;
  }

  public function loadUrls(){
    global $c;
    $ret = array();

    // App urls
    if (is_null($c->getUrlList())){
      $u = json_decode(file_get_contents($c->getDir('config').'urls.json'),true);
      $ret = $u;
    }
    else{
      $ret = $c->getUrlList();
    }
    // Package urls
    if (count($c->getPackages())>0){
      $packages = $c->getPackages();
      foreach($packages['packages'] as $p){
        $package_urls = json_decode(file_get_contents($c->getDir('model_packages').$p['name'].'/config/urls.json'),true);
        $ret['urls']  = array_merge($ret['urls'],$package_urls['urls']);
      }
    }

    return $ret;
  }

  public function setCheckUrl($cu,$g=null,$p=null,$f=null){
    global $c;
    // Comprobación de url carpeta
    if ($c->getUrl('folder')!=''){
      $cu = str_ireplace($c->getUrl('folder'), '', $cu);
    }

    $check_params = stripos($cu,'?');
    if ($check_params !== false){
      $cu = substr($cu, 0, $check_params);
    }
    if (!is_null($g)){
      foreach ($g as $key => $value){
        $this->addUrlParam($key,$value);
      }
    }
    if (!is_null($p)){
      foreach ($p as $key => $value){
        $this->addUrlParam($key,$value);
      }
    }
    if (!is_null($f)){
      foreach ($f as $key => $value){
        $this->addUrlParam($key,$value);
      }
    }
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_null($input)){
      foreach ($input as $key => $value){
        $this->addUrlParam($key,$value);
      }
    }
    $this->check_url = $cu;
  }
  public function getCheckUrl(){
    return $this->check_url;
  }

  public function setRoutingDir($rd){
    $this->routing_dir = $rd;
  }
  public function getRoutingDir(){
    return $this->routing_dir;
  }

  public function setUrlParams($up){
    $this->url_params = $up;
  }
  public function getUrlParams(){
    return $this->url_params;
  }

  public function addUrlParam($key,$value){
    $params = $this->getUrlParams();
    $params[$key] = $value;

    $this->setUrlParams($params);
  }

  public function setMethod($m){
    $this->method = $m;
  }
  public function getMethod(){
    return $this->method;
  }

  public function setRetData($rd){
    $this->ret_data = $rd;
  }
  public function getRetData(){
    return $this->ret_data;
  }

  public function process($url=null){
    if (!is_null($url)){
      $this->setCheckUrl($url);
    }

    $enc = false;
    $i = 0;
    $u = $this->getUrls();
    $ret = array(
      'id' => '',
      'module' => '',
      'action' => '',
      'params' => array(),
      'layout' => 'default',
      'res' => false
    );

    // Incluyo routing de Symfony
    require_once($this->getRoutingDir().'sfRoute.class.php');

    while (!$enc && $i<count($u['urls'])){
      $route = new sfRoute($u['urls'][$i]['url']);
      $chk = $route->matchesUrl($this->getCheckUrl());

      // Si hay resultado devuelvo valores del urls.json mas parametros devueltos por la ruta
      if ($chk !== false){
        $enc = true;
        $ret['id'] = $u['urls'][$i]['id'];
        $ret['module'] = $u['urls'][$i]['module'];
        $ret['action'] = $u['urls'][$i]['action'];
        $ret['res'] = true;

        if (array_key_exists('package', $u['urls'][$i])){
          $ret['package'] = $u['urls'][$i]['package'];
        }

        if (array_key_exists('layout', $u['urls'][$i])){
          $ret['layout'] = $u['urls'][$i]['layout'];
        }

        if (array_key_exists('filter', $u['urls'][$i])){
          $ret['filter'] = $u['urls'][$i]['filter'];
        }

        $ret['params'] = $chk;

        $ret['params']['url_params'] = $this->getUrlParams();
        $ret['params']['headers'] = getallheaders();
      }

      $i++;
    }

    $this->setRetData($ret);
    return $ret;
  }

  public static function generateUrl($id,$params=array(),$absolute=null){
    // Cargo las urls, al ser un metodo estatico no va a pasar por el constructor
    global $c;
    $u = self::loadUrls();

    $enc = false;
    $i   = 0;
    $url = '';

    while (!$enc && $i<count($u['urls'])){
      if ($u['urls'][$i]['id'] == $id){
        $url = $u['urls'][$i]['url'];
        $enc = true;
      }
      $i++;
    }

    if (!$enc){
      $url = '';
    }
    else{
      foreach ($params as $key => $value){
        $url = str_replace(':'.$key, $value, $url);
      }
    }

    if (!is_null($absolute) && $absolute === true){
      $base = $c->getUrl('base');
      $base = substr($base,0,strlen($base)-1);

      $url = $base.$url;
    }

    return $url;
  }
}