<?php
class OSession{
  private $debug_mode = false;
	private $l = null;
  private $params = [];

  function __construct(){
    global $c, $where;
    $this->setDebugMode($c->getDebugMode());

    $l = new OLog();
    $this->setLog($l);
    $this->getLog()->setSection($where);
    $this->getLog()->setModel('OSession');

    if (isset($_SESSION['params'])){
      $this->setParams(unserialize($_SESSION['params']));
    }
    else{
      $this->setParams([]);
    }
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
    $this->setParams([]);
  }
}
