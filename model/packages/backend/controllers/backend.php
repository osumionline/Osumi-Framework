<?php
  /*
   * Formulario de login en el backend
   */
  function executeBackend($req, $t){
    global $c, $s;

    $t->setCssList(array());
    $t->setJsList(array());
    $t->process();
  }

  /*
   * Página de entrada al backend
   */
  function executeBackendLogin($req, $t){
    global $c, $s;

    $status = 'ok';
    $user   = Base::getParam('user', $req['url_params'], false);
    $pass   = Base::getParam('pass', $req['url_params'], false);
    $token  = '';
    
    if ($user===false || $pass===false){
      $status = 'error';
    }
    
    if ($status=='ok'){
      if ($c->getBackend('user')==$user && $c->getBackend('pass')==sha1('b_'.$pass.'_b')){
        $token = sha1('b_'.time().'_b');
        $s->addParam('token',$token);
      }
      else{
        $status = 'error';
      }
    }
    
    $t->setLayout(false);
    $t->setJson(true);

    $t->add('status', $status);
    $t->add('token',  $token);

    $t->process();
  }

  /*
   * Función para obtener la lista de gestores
   */
  function executeBackendGetModels($req, $t){
    global $c, $s;

    $status = 'ok';
    $token  = Base::getParam('token', $req['url_params'], false);
    $list   = array();

    if ($token===false){
      $status = 'error';
    }

    if ($status=='ok'){
      $check_token = $s->getParam('token');
      if ($check_token==$token){
        $list = Base::getModelList();
      }
      else{
        $status = 'error';
      }
    }

    $t->setLayout(false);
    $t->setJson(true);

    $t->add('status', $status);
    $t->addPartial('list', 'backend/model_list', array('list'=>$list, 'extra'=>'nourlencode'));

    $t->process();
  }
  
  /*
   * Función para obtener la lista de registros de una tabla
   */
  function executeBackendGetRecords($req, $t){
    global $c, $s;

    $status     = 'ok';
    $token      = Base::getParam('token',    $req['url_params'], false);
    $table      = Base::getParam('table',    $req['url_params'], false);
    $num_pag    = Base::getParam('num_pag',  $req['url_params'], false);
    $pag        = Base::getParam('pag',      $req['url_params'], false);
    $order_by   = Base::getParam('order_by', $req['url_params'], null);
    $order_sent = Base::getParam('order_sent', $req['url_params'], null);
    $data     = array('num'=>0,'list'=>array());

    if ($token===false || $table===false || $num_pag===false || $pag===false){
      $status = 'error';
    }

    if ($status=='ok'){
      $check_token = $s->getParam('token');
      if ($check_token==$token){
        $data = Backend::getModelRecords($table,$num_pag,$pag,$order_by,$order_sent);
      }
      else{
        $status = 'error';
      }
    }

    $t->setLayout(false);
    $t->setJson(true);

    $t->add('status', $status);
    $t->add('num',    $data['num']);
    $t->addPartial('list', 'backend/model_records', array('list'=>$data['list'], 'extra'=>'nourlencode'));

    $t->process();
  }
  
  /*
   * Función para obtener las relaciones entre tablas
   */
  function executeBackendGetRefs($req, $t){
    global $c, $s;

    $status = 'ok';
    $token  = Base::getParam('token', $req['url_params'], false);
    $refs   = Base::getParam('refs',  $req['url_params'], false);
    
    if ($token===false || $refs===false){
      $status = 'error';
    }
    
    if ($status=='ok'){
      $check_token = $s->getParam('token');
      if ($check_token==$token){
        $obj = null;
        foreach ($refs as $key=>$ref){
          $list = array();
          foreach ($ref['list'] as $item=>$value){
            array_push($list,str_ireplace('item_','',$item));
          }
          $results = Base::getResults($ref['table'],$ref['id'],$list);

          foreach ($results as $res) {
            eval('$' . 'obj = new ' . $ref['model'] . '();');
            $obj->update($res);
            $refs[$key]['list']['item_'.$res[$ref['id']]] = (string)$obj;
          }
        }
      }
      else{
        $status = 'error';
      }
    }
    
    $t->setLayout(false);
    $t->setJson(true);

    $t->add('status', $status);
    $t->add('refs',json_encode($refs),'nourlencode');

    $t->process();
  }

  /*
   * Función para borrar un registro
   */
  function executeBackendDeleteRecord($req, $t){
    global $c, $s;
  
    $status = 'ok';
    $token  = Base::getParam('token', $req['url_params'], false);
    $model  = Base::getParam('model', $req['url_params'], false);
    $table  = Base::getParam('table', $req['url_params'], false);
    $field  = Base::getParam('field', $req['url_params'], false);
  
    if ($token===false || $model===false || $table===false || $field===false){
      $status = 'error';
    }
  
    if ($status=='ok'){
      $check_token = $s->getParam('token');
      if ($check_token==$token){
        Backend::deleteRecord($model,$table,$field);
      }
      else{
        $status = 'error';
      }
    }
  
    $t->setLayout(false);
    $t->setJson(true);
  
    $t->add('status', $status);

    $t->process();
  }

  /*
   * Función para borrar una lista de registros
   */
  function executeBackendDeleteRecords($req, $t){
    global $c, $s;

    $status = 'ok';
    $token  = Base::getParam('token', $req['url_params'], false);
    $model  = Base::getParam('model', $req['url_params'], false);
    $table  = Base::getParam('table', $req['url_params'], false);
    $list   = Base::getParam('list',  $req['url_params'], false);

    if ($token===false || $model===false || $table===false || $list===false){
      $status = 'error';
    }

    if ($status=='ok'){
      $check_token = $s->getParam('token');
      if ($check_token==$token){
        Backend::deleteRecords($model,$table,$list);
      }
      else{
        $status = 'error';
      }
    }

    $t->setLayout(false);
    $t->setJson(true);

    $t->add('status', $status);

    $t->process();
  }
  
  /*
   * Función para añadir o editar un registro
   */
  function executeBackendAddEditRecord($req, $t){
    global $c, $s;

    $status  = 'ok';
    $token   = Base::getParam('token',  $req['url_params'], false);
    $table   = Base::getParam('table',  $req['url_params'], false);
    $record  = Base::getParam('record', $req['url_params'], false);

    if ($token===false || $table===false || $record===false){
      $status = 'error';
    }

    if ($status=='ok'){
      $check_token = $s->getParam('token');
      if ($check_token==$token){
        Backend::addEditRecord($table,$record);
      }
      else{
        $status = 'error';
      }
    }

    $t->setLayout(false);
    $t->setJson(true);

    $t->add('status', $status);

    $t->process();
  }