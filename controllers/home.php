<?php
  /*
   * Página temporal, sitio cerrado
   */
  function executeClosed($req, $t){
    $t->process();
  }

  /*
   * Home pública
   */
  function executeIndex($req, $t){
    global $c, $s;
    /*
     * Código de la página
     */

    $t->process();
  }