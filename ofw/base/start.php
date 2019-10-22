<?php
  /* Datos generales */
  date_default_timezone_set('Europe/Madrid');

  $basedir = realpath(dirname(__FILE__));
  $basedir = str_ireplace('ofw/base','',$basedir);

  require $basedir.'ofw/base/OConfig.php';
  $c = new OConfig($basedir);

  // Base
  require $c->getDir('ofw_base').'OBase.php';
  require $c->getDir('ofw_base').'OController.php';
  require $c->getDir('ofw_base').'OService.php';
  require $c->getDir('ofw_base').'ODB.php';
  require $c->getDir('ofw_base').'ODBContainer.php';
  require $c->getDir('ofw_base').'OLog.php';
  require $c->getDir('ofw_base').'OUrl.php';
  require $c->getDir('ofw_base').'OTemplate.php';
  require $c->getDir('ofw_base').'OSession.php';
  require $c->getDir('ofw_base').'OCookie.php';
  require $c->getDir('ofw_base').'OCache.php';
  require $c->getDir('ofw_base').'OForm.php';
  require $c->getDir('ofw_base').'OToken.php';
  require $c->getDir('ofw_base').'OColors.php';

  // Optionals
  if ($c->getDefaultModule('browser')){
    require $c->getDir('ofw_base').'OBrowser.php';
  }
  if ($c->getDefaultModule('email')){
    require $c->getDir('ofw_base').'OEmail.php';
  }
  if ($c->getDefaultModule('email_smtp')){
    require $c->getDir('ofw_lib').'email/Exception.php';
    require $c->getDir('ofw_lib').'email/PHPMailer.php';
    require $c->getDir('ofw_lib').'email/SMTP.php';
  }
  if ($c->getDefaultModule('ftp')){
    require $c->getDir('ofw_base').'OFTP.php';
  }
  if ($c->getDefaultModule('image')){
    require $c->getDir('ofw_base').'OImage.php';
  }
  if ($c->getDefaultModule('pdf')){
    require $c->getDir('ofw_lib').'pdf/tcpdf.php';
    require $c->getDir('ofw_base').'OPDF.php';
  }
  if ($c->getDefaultModule('translate')){
    require $c->getDir('ofw_base').'OTranslate.php';
  }
  if ($c->getDefaultModule('crypt')){
    require $c->getDir('ofw_base').'OCrypt.php';
  }
  if ($c->getDefaultModule('file')){
    require $c->getDir('ofw_base').'OFile.php';
  }

  // Base functions
  require $c->getDir('ofw_base').'base.php';

  // OFW Tasks
  if ($model = opendir($c->getDir('ofw_task'))) {
    while (false !== ($entry = readdir($model))) {
      if ($entry != "." && $entry != "..") {
        require $c->getDir('ofw_task').$entry;
      }
    }
    closedir($model);
  }

  // Libs
  $lib_list = $c->getLibs();
  foreach ($lib_list as $lib){
    $lib_file = $c->getDir('ofw_lib').$lib.'.php';
    if (file_exists($lib_file)){
      require $c->getDir('ofw_lib').$lib.'.php';
    }
    else{
      echo "ERROR: Lib file \"".$lib_file."\" not found.\n";
      exit();
    }
  }

  // User services
  if (file_exists($c->getDir('app_service'))){
    if ($model = opendir($c->getDir('app_service'))) {
      while (false !== ($entry = readdir($model))) {
        if ($entry != '.' && $entry != '..') {
          require $c->getDir('app_service').$entry;
        }
      }
      closedir($model);
    }
  }

  // Filters
  if (file_exists($c->getDir('app_filter'))){
    if ($model = opendir($c->getDir('app_filter'))) {
      while (false !== ($entry = readdir($model))) {
        if ($entry != "." && $entry != "..") {
          require $c->getDir('app_filter').$entry;
        }
      }
      closedir($model);
    }
  }

  // App
  if (file_exists($c->getDir('app_model'))){
    if ($model = opendir($c->getDir('app_model'))) {
      while (false !== ($entry = readdir($model))) {
        if ($entry != "." && $entry != "..") {
          require $c->getDir('app_model').$entry;
        }
      }
      closedir($model);
    }
  }

  // Si hay conexión a BD, compruebo drivers
  $dbcontainer = null;
  if ($c->getDB('user')!=='' || $c->getDB('pass')!=='' || $c->getDB('host')!=='' || $c->getDB('name')!==''){
    $pdo_drivers = PDO::getAvailableDrivers();
    if (!in_array($c->getDB('driver'), $pdo_drivers)){
      echo "ERROR: El sistema no dispone del driver ".$c->getDB('driver')." solicitado para realizar la conexión a la base de datos.\n";
      exit();
    }
    $dbcontainer = new ODBContainer();
  }