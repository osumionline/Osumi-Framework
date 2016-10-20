<?php
session_start();
$start_time = microtime(true);
$where = 'task_generateModel';

require(dirname(__FILE__).'/../config/config.php');
require($c->getDir('config').'model.php');

echo "Modelo\n\n";
$sql = "";
$models = Base::getModelList();

foreach ($models as $model) {
  $sql .= $model->generate()."\n\n";
}

echo $sql;

$sql_file = $c->getDir('sql').'model.sql';
if (file_exists($sql_file)){
  unlink($sql_file);
}

file_put_contents($sql_file,$sql);