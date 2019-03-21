<?php
class updateUrlsTask{
  public function __toString(){
    return $this->colors->getColoredString("updateUrls", "light_green").": Función para crear nuevos controladores y acciones a partir del archivo de urls.";
  }

  private $colors = null;

  function __construct(){
    $this->colors = new OColors();
  }

  public function run(){
    Base::updateUrls();
  }
}
