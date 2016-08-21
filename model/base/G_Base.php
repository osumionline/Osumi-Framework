<?php
class G_Base{
  protected $db         = null;
  protected $debug_mode = false;
  protected $log        = null;
  protected $model_name = '';
  protected $tablename  = '';
  // Tipos 1-PK, 2-Created 3-Updated, 4-Num, 5-Texto, 6-Fecha, 7-Boolean
  protected $default_model = array(
    'model_1' => array('type'=>1, 'def'=>0,  'orig'=>0,  'val'=>0,  'clean'=>false, 'incr'=>true,  'len'=>11, 'com'=>''),
    'model_2' => array('type'=>2, 'def'=>'', 'orig'=>'', 'val'=>'', 'clean'=>false, 'incr'=>false, 'len'=>0,  'com'=>''),
    'model_3' => array('type'=>3, 'def'=>'', 'orig'=>'', 'val'=>'', 'clean'=>false, 'incr'=>false, 'len'=>0,  'com'=>''),
    'model_4' => array('type'=>4, 'def'=>0,  'orig'=>0,  'val'=>0,  'clean'=>false, 'incr'=>false, 'len'=>11, 'com'=>''),
    'model_5' => array('type'=>5, 'def'=>'', 'orig'=>'', 'val'=>'', 'clean'=>false, 'incr'=>false, 'len'=>50, 'com'=>''),
    'model_6' => array('type'=>6, 'def'=>'', 'orig'=>'', 'val'=>'', 'clean'=>false, 'incr'=>false, 'len'=>0,  'com'=>''),
    'model_7' => array('type'=>7, 'def'=>0,  'orig'=>0,  'val'=>0,  'clean'=>false, 'incr'=>false, 'len'=>1,  'com'=>'')
  );
  protected $model   = array();
  protected $pk      = array('id');
  protected $created = 'created_at';
  protected $updated = 'updated_at';

  function load($model_name,$tablename,$model,$pk=null,$created=null,$updated=null){
    global $c, $where;
    $this->db=new G_DB();
    $this->setDebugMode($c->getDebugMode());
    if ($this->getDebugMode()){
      $l = new G_Log();
      $this->setLog($l);
      $this->getLog()->setSection($where);
      $this->getLog()->setModel($this->model_name);
    }

    $this->model_name = $model_name;
    $this->tablename  = $tablename;
    $this->model      = $model;
    if (!is_null($pk)){
      $this->pk = $pk;
    }
    if (!is_null($created)){
      $this->created = $created;
    }
    if (!is_null($updated)){
      $this->updated = $updated;
    }

    $full_model = array();
    foreach ($model as $fieldname => $row){
      $temp = $this->default_model['model_'.$row['type']];
      $temp['def']   = array_key_exists('def',$row)   ? $row['def']   : $temp['def'];
      $temp['orig']  = array_key_exists('orig',$row)  ? $row['orig']  : $temp['orig'];
      $temp['val']   = array_key_exists('val',$row)   ? $row['val']   : $temp['val'];
      $temp['clean'] = array_key_exists('clean',$row) ? $row['clean'] : $temp['clean'];
      $temp['incr']  = array_key_exists('incr',$row)  ? $row['incr']  : $temp['incr'];
      $temp['len']   = array_key_exists('len',$row)   ? $row['len']   : $temp['len'];
      $temp['com']   = array_key_exists('com',$row)   ? $row['com']   : $temp['com'];
      $full_model[$fieldname] = $temp;
    }
    $this->model = $full_model;
  }

  public function setDebugMode($dm){
    $this->debug_mode = $dm;
  }

  public function getDebugMode(){
    return $this->debug_mode;
  }

  public function setLog($l){
    $this->log = $l;
  }

  public function getLog(){
    return $this->log;
  }

  public function setModel($m){
    $this->model = $m;
  }

  public function getModel($k=null){
    if (is_null($k)){
      return $this->model;
    }
    else{
      if (array_key_exists($k,$this->model)){
        return $this->model[$k];
      }
      else{
        return false;
      }
    }
  }

  public function set($key,$val){
    $model = $this->getModel();
    if (array_key_exists($key,$model)){
      $model[$key]['val'] = $val;
      $this->setModel($model);
      return true;
    }
    else{
      return false;
    }
  }

  public function get($key,$extra=null){
    $field = $this->getModel($key);
    if ($field){
      if (!is_null($extra) && in_array($field['type'],array(2,3,6))){
        return date($extra,strtotime($field['val']));
      }
      else{
        return $field['val'];
      }
    }
    else{
      return false;
    }
  }

  public function save(){
    if ($this->getDebugMode()){
      $this->getLog()->setFunction('save');
    }
    $save_type = '';

    // Cojo modelo
    $model = $this->getModel();

    // Marco fecha de ultima modificación
    $model[$this->updated]['val'] = date("Y-m-d H:i:s",time());

    // UPDATE
    if ($model[$this->created]['val'] != ''){
      $sql = "UPDATE `".$this->tablename."` SET ";
      $updated_fields = array();
      foreach ($model as $fieldname=>$field){
        $holder = "'";
        $val = $field['val'];
        if (is_null($field['val'])){
          $holder = "";
          $val = "NULL";
        }
        if ($field['type']!=1 && $field['orig']!=$val){
          if ($field['clean']){
            $cad = "`".$fieldname."` = ".$holder.$this->db->cleanStr($val).$holder;
          }
          else{
            $cad = "`".$fieldname."` = ".$holder.$val.$holder;
          }
          array_push($updated_fields, $cad);
        }
      }
      $sql .= implode($updated_fields,", ");
      $sql .= ' WHERE ';
      foreach ($this->pk as $i=>$pk_ind){
        if ($i!=0){
          $sql .= "AND ";
        }
        $sql .= "`".$pk_ind."` = '".$model[$pk_ind]['val']."'";
      }

      $save_type = 'u';
    }
    // INSERT
    else{
      $model[$this->created]['val'] = date("Y-m-d H:i:s",time());

      $sql = "INSERT INTO `".$this->tablename."` (";
      $insert_fields = array();
      foreach ($model as $fieldname=>$field){
        array_push($insert_fields,"`".$fieldname."`");
      }
      $sql .= implode($insert_fields,",");
      $sql .= ") VALUES (";
      $insert_fields = array();
      foreach ($model as $field){
        $holder = "'";
        $val = $field['val'];
        if (is_null($field['val'])){
          $holder = "";
          $val = "NULL";
        }
        if ($field['type']==1 && $field['incr']){
          array_push($insert_fields,"NULL");
        }
        else{
          if ($field['clean']){
            array_push($insert_fields, $holder.$this->db->cleanStr($val).$holder);
          }
          else{
            array_push($insert_fields, $holder.$val.$holder);
          }
        }
      }
      $sql .= implode($insert_fields, ",");
      $sql .= ")";

      $save_type = 'i';
    }

    // Si hay modo debug guardo el sql
    if ($this->getDebugMode()){
      $this->getLog()->putLog($sql);
    }

    // Ejecuto la consulta
    $this->db->query($sql);

    // Si la tabla solo tiene un pk y es incremental lo guardo
    if ($save_type == 'i' && count($this->pk)==1 && $model[$this->pk[0]]['incr']){
      $model[$this->pk[0]]['val'] = $this->db->last_id();
    }

    // Marco en el modelo todo como guardado (original=actual)
    foreach($model as $fieldname=>$field){
      $model[$fieldname]['orig'] = $model[$fieldname]['val'];
    }

    // Guardo el modelo modificado
    $this->setModel($model);
  }
  
  public function check($opt=array()){
    if ($this->find($opt)){
      return true;
    }
    else{
      return false;
    }
  }

  public function find($opt=array()){
    $ret = false;
    $sql = "SELECT * FROM `".$this->tablename."` WHERE ";
    $search_fields = array();
    foreach ($opt as $k=>$v){
      array_push($search_fields, "`".$k."` = '".$v."' ");
    }
    $sql .= implode($search_fields, "AND ");
    $this->db->query($sql);
    $res = $this->db->next();

    if ($res){
      $ret = true;
      $this->update($res);
    }

    return $ret;
  }

  public function update($res){
    $model = $this->getModel();
    foreach ($model as $fieldname=>$field){
      if (array_key_exists($fieldname,$res)){
        $model[$fieldname]['orig'] = in_array($field['type'],array(1,4))?(int)$res[$fieldname]:$res[$fieldname];
        $model[$fieldname]['val']  = in_array($field['type'],array(1,4))?(int)$res[$fieldname]:$res[$fieldname];
      }
    }
    $this->setModel($model);
  }

  public function delete(){
    $model = $this->getModel();
    $sql = "DELETE FROM `".$this->tablename."` WHERE ";
    $delete_fields = array();
    foreach ($this->pk as $pk_field){
      array_push($delete_fields, "`".$pk_field."` = '".$model[$pk_field]['val']."' ");
    }
    $sql .= implode('AND ', $delete_fields);

    if ($this->getDebugMode()){
      $this->getLog()->putLog($sql);
    }

    $this->db->query($sql);
  }
  
  public function generate($type='sql'){
    $model = $this->getModel();
    $ret = '';
    
    switch ($type){
      case 'array':{
        $ret = $model;
      }
      break;
      case 'json':{
        $ret = json_encode($model);
      }
      break;
      case 'sql':{
        $sql = "CREATE TABLE `".$this->tablename."` (\n";
        foreach ($model as $fieldname => $field){
          $sql .= "  `".$fieldname."` ";
          switch ($field['type']){
            case 1:{
              $sql .= "int(11) NOT NULL ";
            }
            break;
            case 2:{
              $sql .= "datetime NOT NULL ";
            }
            break;
            case 3:{
              $sql .= "datetime NOT NULL ";
            }
            break;
            case 4:{
              $sql .= "int(11) NOT NULL ";
            }
            break;
            case 5:{
              if ($field['len']<256){
                $sql .= "varchar(".$field['len'].") COLLATE utf8_unicode_ci NOT NULL ";
              }
              else{
                $sql .= "text COLLATE utf8_unicode_ci  NOT NULL ";
              }
            }
            break;
            case 6:{
              $sql .= "datetime NOT NULL ";
            }
            break;
            case 7:{
              $sql .= "tinyint(1) NOT NULL ";
            }
              break;
          }
          if ($field['com']!=''){
            $sql .= "COMMENT '".$field['com']."' ";
          }
          if ($field['incr']){
            $sql .= "AUTO_INCREMENT";
          }
          $sql .= ",\n";
        }
        $sql .= "  PRIMARY KEY (`".implode('`,`',$this->pk)."`)\n";
        $sql .= ") ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;\n";
        
        $ret = $sql;
      }
      break;
    }
    
    return $ret;
  }
}