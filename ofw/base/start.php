<?php
  /* Datos generales */
  date_default_timezone_set('Europe/Madrid');

  $basedir = realpath(dirname(__FILE__));
  $basedir = str_ireplace('ofw/base','',$basedir);

  require($basedir.'ofw/base/OConfig.php');
  $c = new OConfig($basedir);

  // Base
  require($c->getDir('ofw_base').'OBase.php');
  require($c->getDir('ofw_base').'OController.php');
  require($c->getDir('ofw_base').'OService.php');
  require($c->getDir('ofw_base').'ODB.php');
  require($c->getDir('ofw_base').'ODBp.php');
  require($c->getDir('ofw_base').'OLog.php');
  require($c->getDir('ofw_base').'OUrl.php');
  require($c->getDir('ofw_base').'OTemplate.php');
  require($c->getDir('ofw_base').'OSession.php');
  require($c->getDir('ofw_base').'OCookie.php');
  require($c->getDir('ofw_base').'OCache.php');
  require($c->getDir('ofw_base').'OForm.php');
  require($c->getDir('ofw_base').'OToken.php');

  // Optionals
  if ($c->getDefaultModule('browser')){
    require($c->getDir('ofw_base').'OBrowser.php');
  }
  if ($c->getDefaultModule('email')){
    require($c->getDir('ofw_base').'OEmail.php');
  }
  if ($c->getDefaultModule('email_smtp')){
    require($c->getDir('ofw_lib').'email/Exception.php');
    require($c->getDir('ofw_lib').'email/PHPMailer.php');
    require($c->getDir('ofw_lib').'email/SMTP.php');
  }
  if ($c->getDefaultModule('ftp')){
    require($c->getDir('ofw_base').'OFTP.php');
  }
  if ($c->getDefaultModule('image')){
    require($c->getDir('ofw_lib').'image/SimpleImage.php');
    require($c->getDir('ofw_base').'OImage.php');
  }
  if ($c->getDefaultModule('pdf')){
    require($c->getDir('ofw_lib').'pdf/tcpdf.php');
    require($c->getDir('ofw_base').'OPDF.php');
  }
  if ($c->getDefaultModule('translate')){
    require($c->getDir('ofw_base').'OTranslate.php');
  }

  // Base functions
  require($c->getDir('ofw_base').'base.php');

  // OFW Tasks
  if ($model = opendir($c->getDir('ofw_task'))) {
    while (false !== ($entry = readdir($model))) {
      if ($entry != "." && $entry != "..") {
        require($c->getDir('ofw_task').$entry);
      }
    }
    closedir($model);
  }

  // Libs
  $lib_list = $c->getLibs();
  foreach ($lib_list as $lib){
    require($c->getDir('ofw_lib').$lib.'.php');
  }

  // User services
  if ($model = opendir($c->getDir('app_service'))) {
    while (false !== ($entry = readdir($model))) {
      if ($entry != '.' && $entry != '..') {
        require($c->getDir('app_service').$entry);
      }
    }
    closedir($model);
  }

  // Filters
  if ($model = opendir($c->getDir('app_filter'))) {
    while (false !== ($entry = readdir($model))) {
      if ($entry != "." && $entry != "..") {
        require($c->getDir('app_filter').$entry);
      }
    }
    closedir($model);
  }

  // App
  if ($model = opendir($c->getDir('app_model'))) {
    while (false !== ($entry = readdir($model))) {
      if ($entry != "." && $entry != "..") {
        require($c->getDir('app_model').$entry);
      }
    }
    closedir($model);
  }
