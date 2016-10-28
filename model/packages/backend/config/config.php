<?php
  // Paquete
  $package_name = 'backend';
  
  // Incluyo model/app y model/static del paquete
  include($c->getDir('model_packages').$package_name.'/config/model.php');
  
  /* Lista de CSS del paquete */
  $c->setCssList( array('angular-material.min','backend') );
  
  /* Lista de JavaScript del paquete */
  $c->setJsList( array(
                   'lib/common',
                   'lib/angular.min',
                   'lib/angular-route.min',
                   'lib/angular-animate.min',
                   'lib/angular-aria.min',
                   'lib/angular-messages.min',
                   'lib/angular-material.min',
                   'app',
                   'services/api-service',
                   'services/data-share-service',
                   'directives/model-detail-directive',
                   'directives/model-detail-field-directive',
                   'controllers/login-controller',
                   'controllers/main-controller',
                   'controllers/model-detail-controller',
                   'controllers/model-detail-field-controller'
                ) );