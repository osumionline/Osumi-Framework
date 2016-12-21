<?php
session_start();
$start_time = microtime(true);
$where = 'task_ftp';

require(dirname(__FILE__).'/../config/config.php');
require($c->getDir('config').'model.php');

$f = new OFTP('host','user','pass');
$f->autoDisconnect(false);

$subir = $f->put('/local/prueba.txt','/remote/prueba.txt');

if ($subir){
  echo "El archivo se ha subido correctamente\n";
}
else{
  echo "Error al subir el archivo\n";
}

$bajar = $f->get('/remote/prueba2.txt','/local/prueba2.txt');

if ($bajar){
  echo "El archivo se ha bajado correctamente\n";
}
else{
  echo "Error al bajar el archivo\n";
}

$borrar = $f->delete('/remote/prueba.txt');

if ($borrar){
  echo "El archivo se ha borrado correctamente\n";
}
else{
  echo "Error al borrar el archivo\n";
}

$crear = $f->mkdir('/remote/prueba');

if ($crear){
  echo "La carpeta se ha creado correctamente\n";
}
else{
  echo "Error al crear la carpeta\n";
}

$f->disconnect();