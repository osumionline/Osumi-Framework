<?php
  /*
   * Filtro de seguridad para validar el token
   */
  function tokenFilter($req){
    $req['filter'] = array('status'=>'error', 'data'=>null);
    
    if (array_key_exists('X-Auth-Token', $req['headers'])){
      $token = $req['headers']['X-Auth-Token'];
      
      // Compruebo el token
      
      if ($data){
        $req['filter']['status'] = 'ok';
        $req['filter']['data'] = $data;
      }
    }
    
    return $req;
  }