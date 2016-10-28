<?php
header('P3P: CP="CAO PSA OUR"');
session_start();
$start_time = microtime(true);
$where = 'index';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

include('../config/config.php');
include($c->getDir('config').'model.php');

// Inicio sesion
$s = new OSession();

// Inicio objeto log generico
$l = new OLog();

// Cargo cookies
$ck = new OCookie();
$ck->loadCookies();

// Cargo url
$url = ((!empty($_SERVER['HTTPS'])) ? "https://":"http://").$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

$u = new OUrl($_SERVER['REQUEST_METHOD']);
$u->setCheckUrl($_SERVER['REQUEST_URI'],$_GET,$_POST,$_FILES);
$res = $u->process();

if ($res['res']){
  if ($s->getParam('current') != ''){
    $s->addParam('previous', $s->getParam('current'));
  }
  $s->addParam('current', $res['module'].'/'.$res['action']);
  $s->addParam('method', $u->getMethod());

  // Comprobación de login
  if (array_key_exists('login',$res) && $res['login']!='dont'){
    $check = Base::checkCookie();
    if ($res['login']=='yes'){
      if (!$check){
        Base::doLogout('Tienes que iniciar sesión antes');
      }
    }
  }

  $t = new OTemplate();
  $t->setModule($res['module']);
  $t->setAction($res['action']);

  $l->setSection($res['id']);
  $l->setModel('Generico');

  // Tiene algun mensaje flash?
  if ($s->getParam('flash') != ''){
    $t->setFlash($s->getParam('flash'));
  }

  $func = 'execute'.ucfirst($res['action']);
  if (!array_key_exists('package', $res)){
    $module = $c->getDir('controllers').$res['module'].'.php';
  }
  else{
    $t->setPackage($res['package']);
    $module = $c->getDir('model_packages').$res['package'].'/controllers/'.$res['module'].'.php';
    include($c->getDir('model_packages').$res['package'].'/config/config.php');
  }
  $t->loadLayout($res['layout']);
  if (file_exists($module)){
    include($module);

    if (function_exists($func)){
      call_user_func($func, $res['params'], $t);
    }
    else{
      Base::showErrorPage($res,'action');
    }
  }
  else{
    Base::showErrorPage($res,'module');
  }
}
else{
  Base::showErrorPage($res,'404');
}
