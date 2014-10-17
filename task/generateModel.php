<?php
  session_start();
  $start_time = microtime(true);
  $where = 'task_generate';
  
  include(dirname(__FILE__).'/../config/config.php');
  include($c->getRutaConfig().'gestores.php');
  
  echo "Modelo\n\n";
  
  $t = new G_Tabla();
  echo $t->generate();
  
  echo "\n\n";