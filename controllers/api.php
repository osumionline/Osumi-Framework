<?php
class api extends OController{
  /*
   * Ejemplo de función API que devuelve un JSON
   */
  public function apiCall($req){
    /*
     * Código de la función
     */

    $status = 'ok';

    $this->getTemplate()->add('status', $status);
  }
}