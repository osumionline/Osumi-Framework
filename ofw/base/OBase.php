<?php
class OBase {
	protected $db         = null;
	protected $model_name = '';
	protected $table_name = '';
	protected $model      = [];
	protected $pk         = [];
	protected $created    = null;
	protected $updated    = null;
	protected $show_in_backend = true;

	function load($table_name, $model) {
		$this->db         = new ODB();
		$this->model_name = get_class($this);
		$this->table_name = $table_name;
		$this->model      = $model;

		$full_model = [];
		foreach ($model as $field_name => $row){
			if ($row['type']===Base::PK || $row['type']===Base::PK_STR){
				array_push($this->pk, $field_name);
			}
			if ($row['type']===Base::CREATED){
				$this->created = $field_name;
			}
			if ($row['type']===Base::UPDATED){
				$this->updated = $field_name;
			}

			$temp = Base::DEFAULT_MODEL[$row['type']];
			$temp['type']     = $row['type'];
			$temp['default']  = array_key_exists('default',  $row) ? $row['default']  : $temp['default'];
			$temp['original'] = array_key_exists('original', $row) ? $row['original'] : $temp['default'];
			$temp['value']    = array_key_exists('value',    $row) ? $row['value']    : $temp['default'];
			$temp['incr']     = array_key_exists('incr',     $row) ? $row['incr']     : $temp['incr'];
			$temp['size']     = array_key_exists('size',     $row) ? $row['size']     : $temp['size'];
			$temp['nullable'] = array_key_exists('nullable', $row) ? $row['nullable'] : $temp['nullable'];
			$temp['comment']  = array_key_exists('comment',  $row) ? $row['comment']  : $temp['comment'];
			$temp['ref']      = array_key_exists('ref',      $row) ? $row['ref']      : $temp['ref'];
			$temp['by']       = array_key_exists('by',       $row) ? $row['by']       : $temp['by'];
			$temp['expose']   = array_key_exists('expose',   $row) ? $row['expose']   : $temp['expose'];
			$full_model[$field_name] = $temp;
		}
		$this->setModel($full_model);
	}

	public function getModelName() {
		return $this->model_name;
	}

	public function getTableName() {
		return $this->table_name;
	}

	public function setModel($m) {
		$this->model = $m;
	}

	public function getModel($key=null) {
		if (is_null($key)){
			return $this->model;
		}
		else{
			if (array_key_exists($key, $this->model)){
				return $this->model[$key];
			}
			else{
				return false;
			}
		}
	}

	public function set($key, $value) {
		$model = $this->getModel();
		if (array_key_exists($key, $model)){
			$model[$key]['value'] = $value;
			$this->setModel($model);
			return true;
		}
		else{
			return false;
		}
	}

	public function get($key, $extra=null) {
		$field = $this->getModel($key);
		if ($field){
			if (is_null($field['value'])){
				return null;
			}
			if ( !is_null($extra) && in_array($field['type'], [Base::CREATED, Base::UPDATED, Base::DATE]) ){
				return date($extra, strtotime($field['value']));
			}
			if ( !is_null($extra) && ($field['type']==Base::TEXT || $field['type']==Base::LONGTEXT) ){
				if (strlen($field['value'])>$extra){
					return substr($field['value'], 0, $extra).'...';
				}
				else{
					return $field['value'];
				}
			}
			if ($field['type']==Base::NUM || $field['type']==Base::PK){
				return (int)$field['value'];
			}
			if ($field['type']==Base::BOOL){
				return ( ( (int)$field['value'] )==1 );
			}
			if ($field['type']==Base::FLOAT){
				return (float)$field['value'];
			}
			return $field['value'];
		}
		else{
			return false;
		}
	}

	public function getPks() {
		$ret = [];
		$model = $this->getModel();

		foreach ($model as $field_name => $row){
			if ($row['type']===Base::PK || $row['type']===Base::PK_STR){
				array_push($ret, $field_name);
			}
		}
		return $ret;
	}

	public function setShowInBackend($show) {
		$this->show_in_backend = $show;
	}

	public function getShowInBackend(){
		return $this->show_in_backend;
	}

	public function save() {
		$save_type = '';

		// Cojo modelo
		$model = $this->getModel();
		$query_params = [];

		// Marco fecha de ultima modificación
		if (!is_null($this->updated)){
			$model[$this->updated]['value'] = date("Y-m-d H:i:s",time());
		}
		// UPDATE
		if (!is_null($model[$this->created]['value'])){
			$sql = "UPDATE `".$this->table_name."` SET ";
			$updated_fields = [];
			foreach ($model as $field_name => $field){
				$value  = $field['value'];
				if ($field['type']!=Base::PK && $field['type']!=Base::PK_STR && $field['original']!==$value){
					$cad = "`".$field_name."` = ?";
					array_push($updated_fields, $cad);
					array_push($query_params, $value);
				}
			}
			$sql .= implode($updated_fields,", ");
			$sql .= " WHERE ";
			foreach ($this->pk as $i => $pk_ind){
				if ($i!=0){
					$sql .= "AND ";
				}
				$sql .= "`".$pk_ind."` = ?";
				array_push($query_params, $model[$pk_ind]['value']);
			}

			$save_type = 'u';
		}
		// INSERT
		else{
			$model[$this->created]['value'] = date("Y-m-d H:i:s",time());

			$sql = "INSERT INTO `".$this->table_name."` (";
			$insert_fields = [];
			foreach ($model as $field_name => $field){
				array_push($insert_fields, "`".$field_name."`");
			}
			$sql .= implode($insert_fields,",");
			$sql .= ") VALUES (";
			$insert_fields = [];
			foreach ($model as $field){
				$value  = $field['value'];
				array_push($insert_fields, "?");
				if ($field['type']==1 && $field['incr']){
					array_push($query_params, null);
				}
				else{
					array_push($query_params, $value);
				}
			}
			$sql .= implode($insert_fields, ",");
			$sql .= ")";

			$save_type = 'i';
		}

		// Ejecuto la consulta
		$this->db->query($sql, $query_params);

		// Si la tabla solo tiene un pk y es incremental lo guardo
		if ($save_type == 'i' && count($this->pk)==1 && $model[$this->pk[0]]['incr']){
			$model[$this->pk[0]]['value'] = $this->db->lastId();
		}

		// Marco en el modelo todo como guardado (original=actual)
		foreach($model as $fieldname=>$field){
			$model[$fieldname]['original'] = $model[$fieldname]['value'];
		}

		// Guardo el modelo modificado
		$this->setModel($model);
	}

	public function check($opt=[]) {
		if ($this->find($opt)){
			return true;
		}
		else{
			return false;
		}
	}

	public function find($opt=[]) {
		$ret = false;
		$sql = "SELECT * FROM `".$this->table_name."` WHERE ";
		$search_fields = [];
		foreach ($opt as $key => $value){
			array_push($search_fields, "`".$key."` = '".$value."' ");
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

	public function update($res) {
		$model = $this->getModel();
		foreach ($model as $field_name => $field){
			if (array_key_exists($field_name, $res)){
				if (is_null($res[$field_name])){
					$model[$field_name]['original'] = null;
					$model[$field_name]['value']    = null;
				}
				else{
					switch($field['type']){
						case Base::NUM:{
							$model[$field_name]['original'] = (int)$res[$field_name];
							$model[$field_name]['value']    = (int)$res[$field_name];
						}
						break;
						case Base::FLOAT:{
							$model[$field_name]['original'] = (float)$res[$field_name];
							$model[$field_name]['value']    = (float)$res[$field_name];
						}
						break;
						case Base::BOOL:{
							$model[$field_name]['original'] = ($res[$field_name]==1);
							$model[$field_name]['value']    = ($res[$field_name]==1);
						}
						break;
						default: {
							$model[$field_name]['original'] = $res[$field_name];
							$model[$field_name]['value']    = $res[$field_name];
						}
					}
				}
			}
		}
		$this->setModel($model);
	}

	public function delete() {
		$model = $this->getModel();
		$sql = "DELETE FROM `".$this->table_name."` WHERE ";
		$delete_fields = [];
		foreach ($this->pk as $pk_field){
			array_push($delete_fields, "`".$pk_field."` = '".$model[$pk_field]['value']."' ");
		}
		$sql .= implode('AND ', $delete_fields);

		$this->db->query($sql);
	}

	public function generate($type='sql') {
		global $c;
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
				$sql = "CREATE TABLE `".$this->table_name."` (\n";
				foreach ($model as $field_name => $field){
					$sql .= "  `".$field_name."` ";
					switch ($field['type']){
						case Base::PK:{
							$sql .= "INT(11)";
						}
						break;
						case Base::CREATED:
						case Base::UPDATED:
						case Base::DATE:{
							$sql .= "DATETIME";
						}
						break;
						case Base::NUM:{
							$sql .= "INT(11)";
						}
						break;
						case Base::PK_STR:
						case Base::TEXT:{
							if ($field['size']<256){
								$sql .= "VARCHAR(" . $field['size'] . ") COLLATE " . $c->getDb('collate');
							}
							else{
								$sql .= "TEXT COLLATE " . $c->getDb('collate');
							}
						}
						break;
						case Base::BOOL:{
							$sql .= "TINYINT(1)";
						}
						break;
						case Base::LONGTEXT:{
							$sql .= "TEXT COLLATE " . $c->getDb('collate');
						}
						break;
						case Base::FLOAT:{
							$sql .= "FLOAT";
						}
						break;
					}
					if (!$field['nullable'] || $field['ref']!=''){
						$sql .= " NOT";
					}
					$sql .= " NULL";
					if ($field['incr'] && count($this->pk)<2){
						$sql .= " AUTO_INCREMENT";
					}
					if (!$field['nullable'] && !is_null($field['default']) && $field['ref']==''){
						if ($field['type']!=Base::BOOL){
							$sql .= " DEFAULT '".$field['default']."'";
						}
						else{
							$sql .= " DEFAULT '".($field['default'] ? '1' : '0')."'";
						}
					}
					if ($field['comment']!=''){
						$sql .= " COMMENT '".$field['comment']."' ";
					}
					$sql = substr($sql, 0, strlen($sql)-1);
					$sql .= ",\n";
				}
				$sql .= "  PRIMARY KEY (`".implode('`,`',$this->pk)."`)\n";
				$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=" . $c->getDb('charset') . " COLLATE=" . $c->getDb('collate') . ";\n";

				$ret = $sql;
			}
			break;
		}

		return $ret;
	}

	public function generateRefs() {
		$model       = $this->getModel();
		$sql         = '';
		$has_refs    = false;
		$indexes     = [];
		$constraints = [];

		foreach ($model as $field_name => $field){
			if ($field['ref']!=''){
				$has_refs = true;
				break;
			}
		}
		if ($has_refs){
			$sql .= "ALTER TABLE `".$this->table_name."`\n";
		}

		foreach ($model as $field_name => $field){
			if ($field['ref']!=''){
				$ref = explode('.', $field['ref']);
				array_push($indexes, "  ADD KEY `fk_".$this->table_name."_".$ref[0]."_idx` (`".$field_name."`)");
				array_push($constraints, "  ADD CONSTRAINT `fk_".$this->table_name."_".$ref[0]."` FOREIGN KEY (`".$field_name."`) REFERENCES `".$ref[0]."` (`".$ref[1]."`) ON DELETE NO ACTION ON UPDATE NO ACTION");
			}
		}

		if ($has_refs){
			$sql .= implode(",\n", $indexes);
			$sql .= ",\n";
			$sql .= implode(",\n", $constraints);
			$sql .= ";\n";
		}

		return $sql;
	}

	public function cleanValue($val) {
		if (is_null($val)){
			return 'null';
		}
		if (is_numeric($val)){
			return $val;
		}
		$str = str_ireplace("\n", '\n', $val);
		$str = str_ireplace('"', '\"', $str);
		return '"'.$str.'"';
	}

	public function __toString() {
		$ret = '{';
		$fields = [];
		$model = $this->getModel();
		foreach ($model as $fieldname => $field){
			if ($field['expose']){
				$str = '';
				$str .= '"'.$fieldname.'": ';
				$str .= $this->cleanValue($field['value']);
				array_push($fields, $str);
			}
		}
		$ret .= implode(', ', $fields);
		$ret .= '}';
		return $ret;
	}
}