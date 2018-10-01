<?php
header('P3P: CP="CAO PSA OUR"');
session_start();
$start_time = microtime(true);
$where = 'index';

include('../config/config.php');
include($c->getDir('model_base').'model.php');

if ($c->getAllowCrossOrigin()){
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
  header('Access-Control-Allow-Methods: GET, POST');
}

// Cargo url
$u = new OUrl($_SERVER['REQUEST_METHOD']);
$u->setCheckUrl($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
$url_result = $u->process();

// Inicializo Utils
$utils = [];

if ($url_result['res']){
  // Si es una llamada de OPTIONS, devuelvo OK directamente
  if ($url_result['params']['method']==='options'){
    header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
    exit();
  }

  // Si hay un filtro de seguridad lo aplico antes del controller
  if (array_key_exists('filter', $url_result)){
    $url_result['params'] = call_user_func($url_result['filter'], $url_result['params']);
    
    // Si el status es error, doy status 403 Forbidden
    if ($url_result['params']['filter']['status']=='error'){
      if (array_key_exists('return', $url_result['params']['filter'])){
        OUrl::goToUrl($url_result['params']['filter']['return']);
      }
      else {
        Base::showErrorPage($url_result, '403');
      }
    }
  }

  if (!array_key_exists('package', $url_result)){
    $module = $c->getDir('controllers').$url_result['module'].'.php';
  }
  else{
    $module = $c->getDir('model_packages').$url_result['package'].'/controllers/'.$url_result['module'].'.php';
    include($c->getDir('model_packages').$url_result['package'].'/config/config.php');
  }

  if (file_exists($module)){
    include($module);
    $controller = new $url_result['module']();
    $controller->loadController($url_result);

    // Cargo utils del usuario
    if ($model = opendir($c->getDir('model_utils'))) {
      while (false !== ($entry = readdir($model))) {
        if ($entry != '.' && $entry != '..') {
          require($c->getDir('model_utils').$entry);
          $util_name = str_ireplace('.php', '', $entry);
          $utils[$util_name] = new $util_name();
          $utils[$util_name]->setController($controller);
        }
      }
      closedir($model);
    }

    if (method_exists($controller, $url_result['action'])){
      call_user_func(array($controller, $url_result['action']), $url_result['params']);
      $controller->getTemplate()->process();
    }
    else{
      Base::showErrorPage($url_result, 'action');
    }
  }
  else{
    Base::showErrorPage($url_result, 'module');
  }
}
else{
  Base::showErrorPage($url_result, '404');
}