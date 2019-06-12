<?php
class backupAllTask{
  public function __toString(){
    return $this->colors->getColoredString("backupAll", "light_green").": Función para crear una copia de seguridad de TODA la aplicación (base de datos más archivos).";
  }

  private $colors = null;

  function __construct(){
    $this->colors = new OColors();
  }

  public function run(){
    echo "\n";
    echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";

    Base::runOFWTask('backupDB', true);
    Base::runOFWTask('composer', true);
    
    echo "\n  ".$this->colors->getColoredString("Copia de seguridad completada.", "light_green")."\n\n";
  }
}