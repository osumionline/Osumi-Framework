<?php
class updateUrlsTask{
  public function __toString(){
    return "updateUrls: Función para crear nuevos controladores y acciones a partir del archivo de urls.";
  }

  public function run(){
    Base::updateUrls();
  }
}
