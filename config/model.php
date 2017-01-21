<?php
  // Base
  require($c->getDir('model_base').'OBase.php');
  require($c->getDir('model_base').'ODB.php');
  require($c->getDir('model_base').'ODBp.php');
  require($c->getDir('model_base').'OLog.php');
  require($c->getDir('model_base').'OUrl.php');
  require($c->getDir('model_base').'OTemplate.php');
  require($c->getDir('model_base').'OSession.php');
  require($c->getDir('model_base').'OCookie.php');
  
  // Opcionales
  if ($c->getDefaultModule('browser')){
    require($c->getDir('model_base').'OBrowser.php');
  }
  if ($c->getDefaultModule('email')){
    require($c->getDir('model_base').'OEmail.php');
  }
  if ($c->getDefaultModule('email_smtp')){
    require($c->getDir('model_lib').'email/PHPMailerAutoload.php');
  }
  if ($c->getDefaultModule('ftp')){
    require($c->getDir('model_base').'OFTP.php');
  }
  if ($c->getDefaultModule('image')){
    require($c->getDir('model_lib').'image/SimpleImage.php');
    require($c->getDir('model_base').'OImage.php');
  }
  if ($c->getDefaultModule('pdf')){
    require($c->getDir('model_lib').'pdf/tcpdf.php');
    require($c->getDir('model_base').'OPDF.php');
  }
  if ($c->getDefaultModule('translate')){
    require($c->getDir('model_base').'OTranslate.php');
  }
  
  // Funciones base
  require($c->getDir('model_base').'base.php');
  
  // App
  if ($model = opendir($c->getDir('model_app'))) {
    while (false !== ($entry = readdir($model))) {
      if ($entry != "." && $entry != "..") {
        require($c->getDir('model_app').$entry);
      }
    }
    closedir($model);
  }
  
  // Static
  if ($model = opendir($c->getDir('model_static'))) {
    while (false !== ($entry = readdir($model))) {
      if ($entry != "." && $entry != "..") {
        require($c->getDir('model_static').$entry);
      }
    }
    closedir($model);
  }
  
  // Filters
  if ($model = opendir($c->getDir('model_filters'))) {
    while (false !== ($entry = readdir($model))) {
      if ($entry != "." && $entry != "..") {
        require($c->getDir('model_filters').$entry);
      }
    }
    closedir($model);
  }