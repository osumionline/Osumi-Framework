<?php
  // Base
  require($c->getDir('model_base').'G_Base.php');
  require($c->getDir('model_base').'G_DB.php');
  require($c->getDir('model_base').'G_Log.php');
  require($c->getDir('model_base').'G_Url.php');
  require($c->getDir('model_base').'G_Template.php');
  require($c->getDir('model_base').'G_Session.php');
  require($c->getDir('model_base').'G_Cookie.php');
  
  // Opcionales
  if ($c->getDefaultModule('browser')){
    require($c->getDir('model_base').'G_Browser.php');
  }
  if ($c->getDefaultModule('email')){
    require($c->getDir('model_base').'G_Email.php');
  }
  if ($c->getDefaultModule('image')){
    require($c->getDir('model_base').'SimpleImage.php');
    require($c->getDir('model_base').'G_Image.php');
  }
  if ($c->getDefaultModule('pdf')){
    require($c->getDir('model_base').'G_PDF.php');
  }
  if ($c->getDefaultModule('translate')){
    require($c->getDir('model_base').'G_Translate.php');
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