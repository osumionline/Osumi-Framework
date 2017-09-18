<?php
session_start();
$start_time = microtime(true);
$where = 'task_ofw';

require(dirname(__FILE__).'/../config/config.php');
require($c->getDir('config').'model.php');

if (!array_key_exists(1, $argv)){
  echo "\nTienes que indicar una opción.\n\n";
  echo "  Opciones:\n";
  echo "  ·  generate-model: Función para generar el script con el que crear la base de datos a partir del modelo.\n";
  echo "  ·  update-urls: Función para crear nuevos controladores y acciones a partir del archivo de urls.\n";
  echo "\nPor ejemplo: php task/ofw.php generate-model\n\n";
  exit();
}

$option = $argv[1];

switch($option){
  case 'generate-model':{
    Base::generateModel();
  }
  break;
  case 'update-urls':{
    Base::updateUrls();
  }
  break;
}