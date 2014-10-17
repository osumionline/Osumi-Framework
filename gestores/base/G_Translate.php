<?php
  class G_Translate{
    private $pag = '';
    private $data = null;
    private $translations = array();

    function __construct(){}

    public function setPag($p){
      $this->pag = $p;
      $this->loadTranslations();
    }

    public function getPag(){
      return $this->pag;
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
        $ruta_translations = $c->getRutaConfig().'translations.json';
        $data = json_decode( file_get_contents( $ruta_translations ), true );
        $this->setData($data);
      }
      $data = $this->getData();
      if (array_key_exists($this->getPag(),$data['translations'])){
        $this->setTranslations( $data['translations'][$this->getPag()] );
      }
      else{
        $this->setTranslations( array() );
      }
    }
  }