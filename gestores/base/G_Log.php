<?php
class G_Log{
  private $pagina   = '';
  private $gestor   = '';
  private $funcion  = '';
  private $ruta_log = '';
  
  function __construct(){
    global $c;
    $this->setRutaLog($c->getRutaDebugLog());
  }
  
  public function setPagina($p){
    $this->pagina = $p;
  }
  
  public function getPagina(){
    return $this->pagina;
  }
  
  public function setGestor($g){
    $this->gestor = $g;
  }
  
  public function getGestor(){
    return $this->gestor;
  }
  
  public function setFuncion($f){
    $this->funcion = $f;
  }
  
  public function getFuncion(){
    return $this->funcion;
  }
  
  public function setRutaLog($rl){
    $this->ruta_log = $rl;
  }
  
  public function getRutaLog(){
    return $this->ruta_log;
  }
  
  public function putLog($datos){
    $cad = "[".date("Y-m-d H:i:s",time())."] - ";
    if ($this->getPagina() != ''){
      $cad .= "[P: ".$this->getPagina()."] - ";
    }
    if ($this->getGestor() != ''){
      $cad .= "[G: ".$this->getGestor()."] - ";
    }
    if ($this->getFuncion() != ''){
      $cad .= "[F: ".$this->getFuncion()."] - ";
    }
    $cad .= $datos."\n";
    if ( file_put_contents($this->getRutaLog(),$cad,FILE_APPEND) === false){
      return false;
    }
    else{
      return true;
    }
	}
}