<?php
class generateModelTask{
  public function __toString(){
    return "generateModel: Función para generar el script con el que crear la base de datos a partir del modelo.";
  }

  public function run(){
    Base::generateModel();
  }
}
