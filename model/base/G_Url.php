<?php
class G_Url{
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
  
    $l = new G_Log();
    $this->setLog($l);
    $this->getLog()->setSection($where);
    $this->getLog()->setModel('G_Url');
    
    $this->setMethod( $method );
        
    $u = json_decode(file_get_contents($c->getConfigDir().'urls.json'));
    $this->setUrls($u);
    
    $this->setRoutingDir( $c->getModelDir().'routing/' );
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
  
  public function setCheckUrl($cu,$g=null,$p=null,$f=null){
    global $c;
    // Comprobación de url carpeta
    if ($c->getFolderUrl()!=''){
      $cu = str_ireplace($c->getFolderUrl(), '', $cu);
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
            'login' => 'dont',
            'res' => false
           );
    
    // Incluyo routing de Symfony
    require_once($this->getRoutingDir().'sfRoute.class.php');
    
    while (!$enc && $i<count($u->urls)){
      $route = new sfRoute($u->urls[$i]->url);
      $chk = $route->matchesUrl($this->getCheckUrl());

      // Si hay resultado devuelvo valores del urls.json mas parametros devueltos por la ruta
      if ($chk !== false){
        $enc = true;
        $ret['id'] = $u->urls[$i]->id;
        $ret['module'] = $u->urls[$i]->module;
        $ret['action'] = $u->urls[$i]->action;
        $ret['res'] = true;
        
        if (isset($u->urls[$i]->layout)){
          $ret['layout'] = $u->urls[$i]->layout;
        }
        
        if (isset($u->urls[$i]->login)){
          $ret['login'] = $u->urls[$i]->login;
        }
        
        $ret['params'] = $chk;
        
        $ret['params']['url_params'] = $this->getUrlParams();
      }

      $i++;
    }
    
    $this->setRetData($ret);
    return $ret;
  }
  
  public static function generateUrl($id,$params=array(),$absolute=null){
    // Cargo las urls, al ser un metodo estatico no va a pasar por el constructor
    global $c;
    $u = json_decode(file_get_contents($c->getRutaConfig().'urls.json'));

    $enc = false;
    $i   = 0;
    $url = '';
    
    while (!$enc && $i<count($u->urls)){
      if ($u->urls[$i]->id == $id){
        $url = $u->urls[$i]->url;
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
      $base = $c->getBaseUrl();
      $base = substr($base,0,strlen($base)-1);
      
      $url = $base.$url;
    }
    
    return $url;
  }
}