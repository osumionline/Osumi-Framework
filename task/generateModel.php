<?php
  session_start();
  $start_time = microtime(true);
  $where = 'task_generateModel';
  
  require(dirname(__FILE__).'/../config/config.php');
  require($c->getDir('config').'model.php');
  
  echo "Modelo\n\n";
  $sql = "";

  if ($model = opendir($c->getDir('model_app'))) {
    while (false !== ($entry = readdir($model))) {
      if ($entry != "." && $entry != "..") {
        $table = str_ireplace('.php','',$entry);
        eval('$sql .= (new '.$table.'())->generate();');
        $sql .= "\n\n";
      }
    }
    closedir($model);
  }

  echo $sql;

  $sql_file = $c->getDir('sql').'model.sql';
  if (file_exists($sql_file)){
    unlink($sql_file);
  }

  file_put_contents($sql_file,$sql);