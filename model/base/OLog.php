<?php
class OLog{
  private $section  = '';
  private $model    = '';
  private $function = '';
  private $log_dir  = '';
  
  function __construct(){
    global $c;
    $this->setLogDir($c->getDir('debug_log'));
  }
  
  public function setSection($s){
    $this->section = $s;
  }
  public function getSection(){
    return $this->section;
  }
  
  public function setModel($m){
    $this->model = $m;
  }
  public function getModel(){
    return $this->model;
  }
  
  public function setFunction($f){
    $this->function = $f;
  }
  public function getFunction(){
    return $this->function;
  }
  
  public function setLogDir($ld){
    $this->log_dir = $ld;
  }
  public function getLogDir(){
    return $this->log_dir;
  }
  
  public function putLog($data){
    $str = "[".date("Y-m-d H:i:s",time())."] - ";
    if ($this->getSection() != ''){
      $str .= "[S: ".$this->getSection()."] - ";
    }
    if ($this->getModel() != ''){
      $str .= "[M: ".$this->getModel()."] - ";
    }
    if ($this->getFunction() != ''){
      $str .= "[F: ".$this->getFunction()."] - ";
    }
    $str .= $data."\n";
    if ( file_put_contents($this->getLogDir(),$str,FILE_APPEND) === false){
      return false;
    }
    else{
      return true;
    }
	}
}