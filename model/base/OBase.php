<?php
class OBase{
  protected $db         = null;
  protected $debug_mode = false;
  protected $log        = null;
  protected $model_name = '';
  protected $tablename  = '';
  // Tipos 1-PK, 2-Created 3-Updated, 4-Num, 5-Varchar, 6-Fecha, 7-Boolean, 8-Text, 9-Float
  protected $default_model = array(
    'model_1' => array('type'=>Base::PK,       'def'=>0,  'orig'=>0,  'val'=>0,  'clean'=>false, 'incr'=>true,  'len'=>11, 'com'=>'', 'ref'=>'', 'by'=>''),
    'model_2' => array('type'=>Base::CREATED,  'def'=>'', 'orig'=>'', 'val'=>'', 'clean'=>false, 'incr'=>false, 'len'=>0,  'com'=>'', 'ref'=>'', 'by'=>''),
    'model_3' => array('type'=>Base::UPDATED,  'def'=>'', 'orig'=>'', 'val'=>'', 'clean'=>false, 'incr'=>false, 'len'=>0,  'com'=>'', 'ref'=>'', 'by'=>''),
    'model_4' => array('type'=>Base::NUM,      'def'=>0,  'orig'=>0,  'val'=>0,  'clean'=>false, 'incr'=>false, 'len'=>11, 'com'=>'', 'ref'=>'', 'by'=>''),
    'model_5' => array('type'=>Base::TEXT,     'def'=>'', 'orig'=>'', 'val'=>'', 'clean'=>false, 'incr'=>false, 'len'=>50, 'com'=>'', 'ref'=>'', 'by'=>''),
    'model_6' => array('type'=>Base::DATE,     'def'=>'', 'orig'=>'', 'val'=>'', 'clean'=>false, 'incr'=>false, 'len'=>0,  'com'=>'', 'ref'=>'', 'by'=>''),
    'model_7' => array('type'=>Base::BOOL,     'def'=>0,  'orig'=>0,  'val'=>0,  'clean'=>false, 'incr'=>false, 'len'=>1,  'com'=>'', 'ref'=>'', 'by'=>''),
    'model_8' => array('type'=>Base::LONGTEXT, 'def'=>'', 'orig'=>'', 'val'=>'', 'clean'=>false, 'incr'=>false, 'len'=>0,  'com'=>'', 'ref'=>'', 'by'=>''),
    'model_9' => array('type'=>Base::FLOAT,    'def'=>0,  'orig'=>0,  'val'=>0,  'clean'=>false, 'incr'=>false, 'len'=>0,  'com'=>'', 'ref'=>'', 'by'=>'')
  );
  protected $model   = array();
  protected $pk      = array('id');
  protected $created = 'created_at';
  protected $updated = 'updated_at';
  protected $show_in_backend = true;

  function load($model_name,$tablename,$model,$pk=null,$created=null,$updated=null){
    global $c, $where;
    $this->db=new ODB();
    $this->setDebugMode($c->getDebugMode());
    if ($this->getDebugMode()){
      $l = new OLog();
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
      $temp['def']   = array_key_exists('def',   $row) ? $row['def']   : $temp['def'];
      $temp['orig']  = array_key_exists('orig',  $row) ? $row['orig']  : $temp['orig'];
      $temp['val']   = array_key_exists('val',   $row) ? $row['val']   : $temp['val'];
      $temp['clean'] = array_key_exists('clean', $row) ? $row['clean'] : $temp['clean'];
      $temp['incr']  = array_key_exists('incr',  $row) ? $row['incr']  : $temp['incr'];
      $temp['len']   = array_key_exists('len',   $row) ? $row['len']   : $temp['len'];
      $temp['com']   = array_key_exists('com',   $row) ? $row['com']   : $temp['com'];
      $temp['ref']   = array_key_exists('ref',   $row) ? $row['ref']   : $temp['ref'];
      $temp['by']    = array_key_exists('by',    $row) ? $row['by']    : $temp['by'];
      $full_model[$fieldname] = $temp;
    }
    $this->model = $full_model;
  }

  public function getModelName(){
    return $this->model_name;
  }

  public function getTableName(){
    return $this->tablename;
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
      if (!is_null($extra) && in_array($field['type'],array(Base::CREATED,Base::UPDATED,Base::DATE))){
        return date($extra,strtotime($field['val']));
      }
      if (!is_null($extra) && ($field['type']==Base::TEXT || $field['type']==Base::LONGTEXT)){
        if (strlen($field['val'])>$extra){
          return substr($field['val'], 0, $extra).'...';
        }
        else{
          return $field['val'];
        }
      }
      if ($field['type']==Base::NUM){
        return (int)$field['val'];
      }
      if ($field['type']==Base::BOOL){
        return ( ( (int)$field['val'] )==1 );
      }
      if ($field['type']==Base::FLOAT){
        return (float)$field['val'];
      }
      return $field['val'];
    }
    else{
      return false;
    }
  }

  public function getPks(){
    $ret = array();
    $model = $this->getModel();

    foreach ($model as $fieldname => $row){
      if ($row['type']==Base::PK){
        array_push($ret,$fieldname);
      }
    }
    return $ret;
  }

  public function setShowInBackend($show){
    $this->show_in_backend = $show;
  }

  public function getShowInBackend(){
    return $this->show_in_backend;
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
      $model[$this->pk[0]]['val'] = $this->db->lastId();
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
        $model[$fieldname]['orig'] = ($field['type']==4)?(int)$res[$fieldname]:$res[$fieldname];
        $model[$fieldname]['val']  = ($field['type']==4)?(int)$res[$fieldname]:$res[$fieldname];
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
        $array_refs = array();
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
            case 8:{
              $sql .= "text ";
            }
            break;
            case 9:{
              $sql .= "float ";
            }
            break;
          }
          if ($field['incr']){
            $sql .= "AUTO_INCREMENT ";
          }
          if ($field['com']!=''){
            $sql .= "COMMENT '".$field['com']."' ";
          }
          $sql = substr($sql, 0, strlen($sql)-1);
          $sql .= ",\n";

          if ($field['ref']!=''){
            $ref_data = explode('.',$field['ref']);
            $ref_pre_data = explode('(',$ref_data[0]);
            $ref = array('table'=>str_ireplace(')','',$ref_pre_data[1]),'field'=>$ref_data[1]);
            array_push($array_refs, "  ADD CONSTRAINT `fk_".$ref['table']."` FOREIGN KEY (`".$fieldname."`) REFERENCES `".$ref['table']."` (`".$ref['field']."`) ON DELETE NO ACTION ON UPDATE NO ACTION");
          }
        }
        $sql .= "  PRIMARY KEY (`".implode('`,`',$this->pk)."`)\n";
        $sql .= ") ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;\n";
        
        $ret = $sql;

        if (count($array_refs)>0){
          $ret .= "\n\n";
          $ret .= "ALTER TABLE `".$this->tablename."`\n";
          $ret .= implode(",\n", $array_refs).";\n";
        }
      }
      break;
    }
    
    return $ret;
  }
}