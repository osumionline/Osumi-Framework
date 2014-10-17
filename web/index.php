<?php
  header('P3P: CP="CAO PSA OUR"');
  session_start();
  $start_time = microtime(true);
  $where = 'index';

  include('../config/config.php');
  include($c->getRutaConfig().'gestores.php');

  if ($c->getAllowCrossOrigin()){
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST');
  }

  // Inicio sesion
  $s = new G_Session();

  // Inicio objeto log generico
  $l = new G_Log();

  // Cargo cookies
  $ck = new G_Cookie();
  $ck->loadCookies();

  // Cargo url
  $url = $_SERVER['REQUEST_URI'];

  if ($c->getPaginaCerrada()){
    $url = '/cerrado';
  }

  $u = new G_Url($_SERVER['REQUEST_METHOD']);
  $u->setCheckUrl($url,$_GET,$_POST,$_FILES);
  $res = $u->process();

  if ($res['res']){
    if ($s->getParam('current') != ''){
      $s->addParam('previous', $s->getParam('current'));
    }
    $s->addParam('current', $res['module'].'/'.$res['action']);
    $s->addParam('method', $u->getMethod());

    // Comprobación de login
    if ($res['login']!='dont'){
      $check = Base::checkCookie();
      if ($res['login']=='yes'){
        if (!$check){
          Base::doLogout('Tienes que iniciar sesión antes');
        }
      }
    }

    $t = new G_Template();
    $t->setModule($res['module']);
    $t->setAction($res['action']);
    $t->setLayout( file_get_contents($c->getRutaTemplates().'layout/'.$res['layout'].'.php') );

    $l->setPagina($res['id']);
    $l->setGestor('Generico');

    // Tiene algun mensaje flash?
    if ($s->getParam('flash') != ''){
      $t->setFlash($s->getParam('flash'));
    }

    $func = 'execute'.ucfirst($res['action']);
    $module = $c->getRutaControllers().$res['module'].'.php';

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