<?php
  /*
   * Ejemplo de función API que devuelve un JSON
   */
  function executeApiCall($req, $t){
    global $c, $s;
    /*
     * Código de la página
     */

    $status = 'ok';

    $t->setLayout(false);
    $t->setJson(true);

    $t->add('status',$status);
    $t->process();
  }