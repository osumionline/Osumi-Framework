<?php
  // Base
  include($c->getRutaGestoresBase().'G_Base.php');
  include($c->getRutaGestoresBase().'G_BBDD.php');
  include($c->getRutaGestoresBase().'G_Log.php');
  include($c->getRutaGestoresBase().'G_Url.php');
  include($c->getRutaGestoresBase().'G_Template.php');
  include($c->getRutaGestoresBase().'G_Session.php');
  include($c->getRutaGestoresBase().'G_Cookie.php');

  if ($c->getDefaultModule('browser')){
    include($c->getRutaGestoresBase().'G_Browser.php');
  }
  if ($c->getDefaultModule('email')){
    include($c->getRutaGestoresBase().'G_Email.php');
  }
  if ($c->getDefaultModule('image')){
    include($c->getRutaGestoresBase().'SimpleImage.php');
    include($c->getRutaGestoresBase().'G_Image.php');
  }
  if ($c->getDefaultModule('pdf')){
    include($c->getRutaGestoresBase().'G_PDF.php');
  }
  if ($c->getDefaultModule('translate')){
    include($c->getRutaGestoresBase().'G_Translate.php');
  }
  include($c->getRutaGestoresBase().'base.php');

  // Aplicación
  if ($gestor = opendir($c->getRutaGestoresApp())) {
    while (false !== ($entrada = readdir($gestor))) {
      if ($entrada != "." && $entrada != "..") {
        include($c->getRutaGestoresApp().$entrada);
      }
    }
    closedir($gestor);
  }