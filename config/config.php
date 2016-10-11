<?php
  /* Datos generales */
  date_default_timezone_set('Europe/Madrid');

  $basedir = realpath(dirname(__FILE__));
  $basedir = str_ireplace('config','',$basedir);

  require($basedir.'model/base/OConfig.php');
  $c = new OConfig();
  $c->setBaseDir($basedir);

  /* Carga de módulos */
  $c->loadDefaultModules();

  /* Datos de la Base De Datos */
  $c->setDB('host','host');
  $c->setDB('user','user');
  $c->setDB('pass','pass');
  $c->setDB('name','dbname');

  /* Datos para cookies */
  $c->setCookiePrefix('osumifw');
  $c->setCookieUrl('.osumi.es');
  
  /* Activa/desactiva el modo debug que guarda en log las consultas SQL e información variada */
  $c->setDebugMode(false);

  /* URL del sitio */
  $c->setBaseUrl('http://example.com/');
  
  /* Email del administrador al que se notificarán varios eventos */
  $c->setAdminEmail('inigo.gorosabel@osumi.es');
  
  /* Lista de CSS por defecto */
  $c->setCssList( array('common') );
  
  /* Lista de JavaScript por defecto */
  $c->setJsList( array('jquery-3.1.0.min','common') );
  
  /* Título de la página */
  $c->setDefaultTitle('Osumi Framework');

  /* Idioma de la página */
  $c->setLang('es');
  
  /* Para cerrar la página descomentar la siguiente linea */
  //$c->setPaginaCerrada(true);