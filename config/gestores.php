<?php
  // Base
  include($c->getRutaGestoresBase().'G_Base.php');
  include($c->getRutaGestoresBase().'G_BBDD.php');
  include($c->getRutaGestoresBase().'G_Log.php');
  include($c->getRutaGestoresBase().'G_Url.php');
  include($c->getRutaGestoresBase().'G_Template.php');
  include($c->getRutaGestoresBase().'G_Session.php');
  include($c->getRutaGestoresBase().'G_Cookie.php');
  include($c->getRutaGestoresBase().'G_Browser.php');
  include($c->getRutaGestoresBase().'SimpleImage.php');
  include($c->getRutaGestoresBase().'G_Image.php');
  include($c->getRutaGestoresBase().'G_Email.php');
  include($c->getRutaGestoresBase().'G_Translate.php');
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