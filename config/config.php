<?php
  /* Datos generales */
  date_default_timezone_set('Europe/Madrid');

  $ruta_base = realpath(dirname(__FILE__));
  $ruta_base = str_ireplace("config","",$ruta_base);

  include($ruta_base."gestores/base/G_Config.php");
  $c = new G_Config();
  $c->setRutaBase($ruta_base);

  /* Datos de la Base De Datos */
  $c->setDbHost('host');
  $c->setDbUser('user');
  $c->setDbPass('pass');
  $c->setDbName('dbname');
  
  /* Activa/desactiva el modo debug que guarda en log las consultas SQL e información variada */
  $c->setModoDebug(false);

  /* URL del sitio */
  $c->setUrlBase('http://www.example.com/');
  
  /* Email del administrador al que se notificarán varios eventos */
  $c->setAdminEmail('inigo.gorosabel@osumi.es');
  
  /* Lista de CSS por defecto */
  $css = array('common');
  $c->setCssList( $css );
  
  /* Lista de JavaScript por defecto */
  $js = array('jquery-1.11.0.min','common');
  $c->setJsList( $js );
  
  /* Título de la página */
  $c->setDefaultTitle('Osumi Framework');

  /* Idioma de la página */
  $c->setLang('es');
  
  /* Para cerrar la página descomentar la siguiente linea */
  //$c->setPaginaCerrada(true);