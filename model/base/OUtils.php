<?php
  class OUtils{
    protected $controller = null;
    
    public final function setController($controller){
      $this->controller = $controller;
    }
    
    public final function getController(){
      return $this->controller;
    }
  }