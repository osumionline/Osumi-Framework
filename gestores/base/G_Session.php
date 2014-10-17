<?php
class G_Session{
  private $modo_debug = false;
	private $l = null;
  private $params = array();
  
  function G_Session(){
    global $c, $where;
    $this->setModoDebug($c->getModoDebug());
    
    $l = new G_Log();
    $this->setLog($l);
    $this->getLog()->setPagina($where);
    $this->getLog()->setGestor('G_Session');
    
    if (isset($_SESSION['params'])){
      $this->setParams(unserialize($_SESSION['params']));
    }
    else{
      $this->setParams(array());
    }
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
	
	public function setParams($p){
    $this->params = $p;
    
    $_SESSION['params'] = serialize($p);
	}
	
	public function getParams(){
    return $this->params;
	}
	
	public function addParam($key,$value){
    $params = $this->getParams();
    $params[$key] = $value;
    
    $this->setParams($params);
  }
  
  public function getParam($key){
    $params = $this->getParams();
    if (array_key_exists($key, $params)){
      return $params[$key];
    }
    else{
      return false;
    }
  }
  
  public function removeParam($key){
    $params = $this->getParams();
    unset($params[$key]);
    $this->setParams($params);
  }
  
  public function cleanSession(){
    unset($_SESSION['params']);
    
    $this->setParams(array());
  }
}