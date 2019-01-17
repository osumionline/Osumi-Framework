<?php
class api extends OController{
  /*
   * Función para obtener los datos de un usuario
   */
  function getUser($req){
    $this->getTemplate()->add('status', 'ok');
    $this->getTemplate()->add('user', 'igorosabel');
  }
}