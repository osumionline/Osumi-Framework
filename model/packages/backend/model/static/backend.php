<?php
class Backend{
  public static function getModels(){
    global $c;
    $ret = array();
    if ($model = opendir($c->getDir('model_app'))) {
      while (false !== ($entry = readdir($model))) {
        if ($entry != "." && $entry != "..") {
          $table = str_ireplace('.php', '', $entry);
          array_push($ret, $table);
        }
      }
    }

    sort($ret);
    return $ret;
  }
  
  public static function getModelRecords($table,$num_pag,$pag,$order_by,$order_sent){
    $ret = array('num'=>0,'list'=>array());
    $db = new ODB();
    
    $lim = ($pag -1) * $num_pag;
    $sql = "SELECT * FROM `".$table."`";
    if (!is_null($order_by)){
      $sql .= " ORDER BY `".$order_by."` ".$order_sent;
    }
    $sql .= " LIMIT ".$lim.", ".$num_pag;
    $db->query($sql);
    
    while ($res=$db->next()){
      array_push($ret['list'], $res);
    }
    
    $sql = "SELECT COUNT(*) AS `num` FROM `".$table."`";
    $db->query($sql);
    $res = $db->next();
    
    $ret['num'] = $res['num'];

    return $ret;
  }

  public static function deleteRecord($model,$table,$field){
    $db = new ODB();
    $obj = null;
    eval('$'.'obj = new '.$model.'();');
    $pks = $obj->getPks();

    $sql = "DELETE FROM `".$table."` WHERE ";
    $ands = array();
    foreach ($pks as $pk){
      array_push($ands, "`".$pk."` = '".$field[$pk]."'");
      $obj_model = $obj->getModel($pk);

      if (count($obj_model['by'])>0){
        foreach ($obj_model['by'] as $by_item) {
          $by_data = Base::getRefData($by_item);
          $by_sql = "DELETE FROM `" . $by_data['table'] . "` WHERE `" . $by_data['field'] . "` = '" . $field[$pk] . "'";

          $db->query($by_sql);
        }
      }
    }
    $sql .= implode(' AND ',$ands);
    $db->query($sql);
  }

  public static function deleteRecords($model,$table,$list){
    foreach ($list as $item){
      self::deleteRecord($model,$table,$item);
    }
  }
  
  public static function addEditRecord($table, $record){
    $ids = array();
    foreach ($record['fields'] as $field){
      if ($field['type']===1 && !is_null($field['value']) && $field['value']!=''){
        $ids[$field['name']] = $field['value'];
      }
    }
    
    if (count($ids)==0){
      $sql = "INSERT INTO `".$table."` (`";
      $array_fields = array();
      foreach ($record['fields'] as $field){
        array_push($array_fields, $field['name']);
      }
      $sql .= implode('`,`', $array_fields);
      $sql .= "`) VALUES (";

      foreach ($record['fields'] as $field){
        if (array_search($field['type'], array(1,4,7))!==false){
          $sql .= ( (is_null($field['value']) || $field['value']==='')?'NULL':$field['value'] ).",";
        }
        if (array_search($field['type'], array(5,8))!==false){
          $sql .= "'".$field['value']."',";
        }
        if (array_search($field['type'], array(2,3,6))!==false){
          $sql .= "'".date('Y-m-d H:i:s',strtotime($field['value']))."',";
        }
      }
      $sql = substr($sql, 0, strlen($sql)-1);

      $sql .= ")";
    }
    else{
      $sql = "UPDATE `".$table."` SET ";
      foreach ($record['fields'] as $field){
        if ($field['type']!==1){
          $sql .= "`".$field['name']."` = ";
          if (array_search($field['type'], array(2,3,6))!==false){
            $sql .= "'".date('Y-m-d H:i:s',strtotime($field['value']))."',";
          }
          else{
            $sql .= "'".$field['value']."',";
          }
        }
      }

      $sql = substr($sql, 0, strlen($sql)-1);
      $sql .= " WHERE ";
      foreach ($record['fields'] as $field){
        if ($field['type']===1){
          $sql .= "`".$field['name']."` = '".$field['value']."' AND ";
        }
      }
      $sql = substr($sql, 0, strlen($sql)-4);
    }
    
    $db = new ODB();
    $db->query($sql);
  }
}