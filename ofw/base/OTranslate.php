<?php
class OTranslate{
  private $page = '';
  private $data = null;
  private $translations = [];

  function __construct(){}

  public function setPage($p){
    $this->page = $p;
    $this->loadTranslations();
  }
  public function getPage(){
    return $this->page;
  }

  public function setData($d){
    $this->data = $d;
  }
  public function getData(){
    return $this->data;
  }

  public function setTranslations($t){
    $this->translations = $t;
  }
  public function getTranslations(){
    return $this->translations;
  }

  public function loadTranslations(){
    if (is_null($this->getData())){
      global $c;
      $translations_dir = $c->getDir('app_config').'translations.json';
      $data = json_decode( file_get_contents( $translations_dir ), true );
      $this->setData($data);
    }
    $data = $this->getData();
    if (array_key_exists($this->getPage(),$data['translations'])){
      $this->setTranslations( $data['translations'][$this->getPage()] );
    }
    else{
      $this->setTranslations( [] );
    }
  }
}
