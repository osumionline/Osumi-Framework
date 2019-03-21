<?php
session_start();
$start_time = microtime(true);
$where = 'task_ofw';

require dirname(__FILE__).'/ofw/base/start.php' ;
// Include OColors class to make colores messages available in CLI
require $c->getDir('ofw_base').'OColors.php';

$task_list = [];
$colors = new OColors();

// OFW Tasks
if ($model = opendir($c->getDir('ofw_task'))) {
  while (false !== ($entry = readdir($model))) {
    if ($entry != "." && $entry != "..") {
      array_push($task_list, str_ireplace(".php", "", $entry));
    }
  }
  closedir($model);
}

// App Tasks
if ($model = opendir($c->getDir('app_task'))) {
  while (false !== ($entry = readdir($model))) {
    if ($entry != "." && $entry != "..") {
      require($c->getDir('app_task').$entry);
      array_push($task_list, str_ireplace(".php", "", $entry));
    }
  }
  closedir($model);
}

function taskOptions($task_list){
  $ret = "";
  $ret .= "  Opciones:\n";
  asort($task_list);
  foreach ($task_list as $task){
    $task_name = $task."Task";
    $tsk = new $task_name();
    $ret .= "  ·  ".$tsk."\n";
  }
  $ret .= "\nPor ejemplo: php ofw.php ".$task_list[0]."\n\n";
  return $ret;
}
if (!array_key_exists(1, $argv)){
  echo "\n  ".$colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
  echo "Tienes que indicar una opción.\n\n";
  echo taskOptions($task_list);
  exit();
}

$option = $argv[1];
if (!in_array($option, $task_list)){
  echo "\nLa opción \"".$option."\" no es correcta.\n\n";
  echo taskOptions($task_list);
  exit();
}

array_shift($argv);
array_shift($argv);

$task_name = $option."Task";
$tsk = new $task_name();
$tsk->run($argv);
