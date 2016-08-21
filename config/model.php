<?php
  // Base
  require($c->getModelDirBase().'G_Base.php');
  require($c->getModelDirBase().'G_DB.php');
  require($c->getModelDirBase().'G_Log.php');
  require($c->getModelDirBase().'G_Url.php');
  require($c->getModelDirBase().'G_Template.php');
  require($c->getModelDirBase().'G_Session.php');
  require($c->getModelDirBase().'G_Cookie.php');
  
  // Opcionales
  if ($c->getDefaultModule('browser')){
    require($c->getModelDirBase().'G_Browser.php');
  }
  if ($c->getDefaultModule('email')){
    require($c->getModelDirBase().'G_Email.php');
  }
  if ($c->getDefaultModule('image')){
    require($c->getModelDirBase().'SimpleImage.php');
    require($c->getModelDirBase().'G_Image.php');
  }
  if ($c->getDefaultModule('pdf')){
    require($c->getModelDirBase().'G_PDF.php');
  }
  if ($c->getDefaultModule('translate')){
    require($c->getModelDirBase().'G_Translate.php');
  }
  
  // Funciones base
  require($c->getModelDirBase().'base.php');
  
  // App
  if ($model = opendir($c->getModelDirApp())) {
    while (false !== ($entry = readdir($model))) {
      if ($entry != "." && $entry != "..") {
        require($c->getModelDirApp().$entry);
      }
    }
    closedir($model);
  }
  
  // Static
  if ($model = opendir($c->getModelDirStatic())) {
    while (false !== ($entry = readdir($model))) {
      if ($entry != "." && $entry != "..") {
        require($c->getModelDirStatic().$entry);
      }
    }
    closedir($model);
  }