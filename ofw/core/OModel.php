<?php
/**
 * OModel - Base class for the model classes with all the methods necessary to interact with the database.
 */
class OModel {
	private $debug        = false;
	private $l            = null;
	protected $db         = null;
	protected $model_name = '';
	protected $table_name = '';
	protected $model      = [];
	protected $pk         = [];
	protected $created    = null;
	protected $updated    = null;

	/**
	 * Load model information
	 *
	 * @param string $table_name Name of the table in the database
	 *
	 * @param array $model Array with the fields of the table (type, default value, nullable, comment explaining the field and references to another table fields)
	 *
	 * @return void
	 */
	function load($table_name, $model) {
		global $core;
		$this->debug = ($core->config->getLog('level') == 'ALL');
		if ($this->debug) {
			$this->l = new OLog();
		}

		$this->db         = new ODB();
		$this->model_name = get_class($this);
		$this->table_name = $table_name;
		$this->model      = $model;

		$full_model = [];
		foreach ($model as $field_name => $row) {
			if ($row['type']===OCore::PK || $row['type']===OCore::PK_STR) {
				array_push($this->pk, $field_name);
			}
			if ($row['type']===OCore::CREATED) {
				$this->created = $field_name;
			}
			if ($row['type']===OCore::UPDATED) {
				$this->updated = $field_name;
			}

			$temp = OCore::DEFAULT_MODEL[$row['type']];
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
			$full_model[$field_name] = $temp;
		}
		$this->model = $full_model;
	}

	/**
	 * Logs internal information of the class
	 *
	 * @param string $str String to be logged
	 *
	 * @return void
	 */
	private function log($str) {
		if ($this->debug) {
			$this->l->debug($str);
		}
	}

	/**
	 * Function to get the whole models information or information about a certain field
	 *
	 * @param string $key Optional fieldname, if defined retrieves information about a certain field and if omitted retrieves information about the whole model
	 *
	 * @return array Whole model (array of arrays) or field model (single array)
	 */
	public function getModel($key=null) {
		if (is_null($key)) {
			return $this->model;
		}
		else {
			if (array_key_exists($key, $this->model)) {
				return $this->model[$key];
			}
			else {
				return false;
			}
		}
	}

	/**
	 * Function to set a fields value
	 *
	 * @param string $key Field name
	 *
	 * @param string|integer|float|boolean Field value
	 *
	 * @return boolean Field value was successfully updated or not
	 */
	public function set($key, $value) {
		if (array_key_exists($key, $this->model)){
			$this->model[$key]['value'] = $value;
			return true;
		}
		return false;
	}

	/**
	 * Function to get a fields value. $extra parameter formats the output (string and date fields)
	 *
	 * @param string $key Field name
	 *
	 * @param string|integer $extra php date format for date field types or number to limit number of characters for string field types
	 *
	 * @return string|integer|float|boolean Field value
	 */
	public function get($key, $extra=null) {
		$field = $this->getModel($key);
		if ($field) {
			if (is_null($field['value'])) {
				return null;
			}
			if ( !is_null($extra) && in_array($field['type'], [OCore::CREATED, OCore::UPDATED, OCore::DATE]) ) {
				return date($extra, strtotime($field['value']));
			}
			if ( !is_null($extra) && ($field['type']==OCore::TEXT || $field['type']==OCore::LONGTEXT) ) {
				if (strlen($field['value'])>$extra) {
					return substr($field['value'], 0, $extra).'...';
				}
				else{
					return $field['value'];
				}
			}
			if ($field['type']==OCore::NUM || $field['type']==OCore::PK) {
				return intval($field['value']);
			}
			if ($field['type']==OCore::BOOL) {
				return ( ( intval($field['value']) )==1 );
			}
			if ($field['type']==OCore::FLOAT) {
				return floatval($field['value']);
			}
			return $field['value'];
		}
		else {
			return false;
		}
	}

	/**
	 * Function to get array of Primary Keys
	 *
	 * @return string[] Array with the names of the fields that are Primary Keys
	 */
	public function getPks() {
		$ret = [];

		foreach ($this->model as $field_name => $row) {
			if ($row['type']===OCore::PK || $row['type']===OCore::PK_STR) {
				array_push($ret, $field_name);
			}
		}
		return $ret;
	}

	/**
	 * Function to save current model into the database
	 *
	 * @return void
	 */
	public function save() {
		$save_type = '';
		$query_params = [];

		// Set last updated date
		if (!is_null($this->updated)){
			$this->model[$this->updated]['value'] = date('Y-m-d H:i:s', time());
		}
		// UPDATE
		if (!is_null($this->model[$this->created]['value'])) {
			$sql = "UPDATE `".$this->table_name."` SET ";
			$updated_fields = [];
			foreach ($this->model as $field_name => $field) {
				$value  = $field['value'];
				if ($field['type']!=OCore::PK && $field['type']!=OCore::PK_STR && $field['original']!==$value) {
					$str = "`".$field_name."` = ?";
					array_push($updated_fields, $str);
					array_push($query_params, $value);
				}
			}
			$sql .= implode($updated_fields, ", ");
			$sql .= " WHERE ";
			foreach ($this->pk as $i => $pk_ind) {
				if ($i!=0) {
					$sql .= "AND ";
				}
				$sql .= "`".$pk_ind."` = ?";
				array_push($query_params, $model[$pk_ind]['value']);
			}

			$save_type = 'u';
		}
		// INSERT
		else {
			$this->model[$this->created]['value'] = date('Y-m-d H:i:s', time());

			$sql = "INSERT INTO `".$this->table_name."` (";
			$insert_fields = [];
			foreach ($this->model as $field_name => $field) {
				array_push($insert_fields, "`".$field_name."`");
			}
			$sql .= implode($insert_fields, ",");
			$sql .= ") VALUES (";
			$insert_fields = [];
			foreach ($this->model as $field) {
				$value  = $field['value'];
				array_push($insert_fields, "?");
				if ($field['type']==OCore::PK && $field['incr']) {
					array_push($query_params, null);
				}
				else {
					array_push($query_params, $value);
				}
			}
			$sql .= implode($insert_fields, ",");
			$sql .= ")";

			$save_type = 'i';
		}

		$this->log('[OBase] - save');
		$this->log('Query: '.$sql);
		$this->log('Params: '.var_export($query_params, true));

		// Run the query
		$this->db->query($sql, $query_params);

		// If table has only a PK and it is incremental, save it
		if ($save_type == 'i' && count($this->pk)==1 && $this->model[$this->pk[0]]['incr']) {
			$this->model[$this->pk[0]]['value'] = $this->db->lastId();
		}

		// Set every field in the model as saved (original = current)
		foreach($this->model as $field_name=>$field){
			$this->model[$field_name]['original'] = $model[$field_name]['value'];
		}
	}

	/**
	 * Function to search the database and if found populates the model object
	 *
	 * @param string[] $opt Fieldname / value pairs to look up in the database
	 *
	 * @return boolean Data found based on given parameters
	 */
	public function find($opt=[]) {
		$sql = "SELECT * FROM `".$this->table_name."` WHERE ";
		$search_fields = [];
		foreach ($opt as $key => $value) {
			array_push($search_fields, "`".$key."` = '".$value."' ");
		}
		$sql .= implode($search_fields, "AND ");

		$this->log('[OBase] - find');
		$this->log('Query: '.$sql);

		$this->db->query($sql);
		$res = $this->db->next();

		if ($res) {
			$this->update($res);
			return true;
		}

		return false;
	}

	/**
	 * Function to populate model fields with an array of key/value obtained from a query
	 *
	 * @param array $res Key/value (fieldname / value) array of data representing a row in the database
	 *
	 * @return void
	 */
	public function update($res) {
		foreach ($this->model as $field_name => $field){
			if (array_key_exists($field_name, $res)){
				if (is_null($res[$field_name])) {
					$this->model[$field_name]['original'] = null;
					$this->model[$field_name]['value']    = null;
				}
				else{
					switch($field['type']) {
						case OCore::NUM: {
							$this->model[$field_name]['original'] = intval($res[$field_name]);
							$this->model[$field_name]['value']    = intval($res[$field_name]);
						}
						break;
						case OCore::FLOAT: {
							$this->model[$field_name]['original'] = floatval($res[$field_name]);
							$this->model[$field_name]['value']    = floatval($res[$field_name]);
						}
						break;
						case OCore::BOOL: {
							$this->model[$field_name]['original'] = ($res[$field_name]==1);
							$this->model[$field_name]['value']    = ($res[$field_name]==1);
						}
						break;
						default: {
							$this->model[$field_name]['original'] = $res[$field_name];
							$this->model[$field_name]['value']    = $res[$field_name];
						}
					}
				}
			}
		}
	}

	/**
	 * Function to delete a row in the database representing the current model
	 *
	 * @return void
	 */
	public function delete() {
		$sql = "DELETE FROM `".$this->table_name."` WHERE ";
		$delete_fields = [];
		foreach ($this->pk as $pk_field) {
			array_push($delete_fields, "`".$pk_field."` = '".$this->model[$pk_field]['value']."' ");
		}
		$sql .= implode('AND ', $delete_fields);

		$this->db->query($sql);

		$this->log('[OBase] - delete');
		$this->log('Query: '.$sql);
	}

	/**
	 * Return a representation of the model as data (php array / json / sql create)
	 *
	 * @param string $type Type of return wanted (array / json / sql)
	 *
	 * @return array|string Representation of the model
	 */
	public function generate($type='sql') {
		global $core;
		$ret = '';

		switch ($type) {
			case 'array': {
				$ret = $this->model;
			}
			break;
			case 'json': {
				$ret = json_encode($this->model);
			}
			break;
			case 'sql': {
				$sql = "CREATE TABLE `".$this->table_name."` (\n";
				foreach ($this->model as $field_name => $field) {
					$sql .= "  `".$field_name."` ";
					switch ($field['type']) {
						case OCore::PK: {
							$sql .= "INT(11)";
						}
						break;
						case OCore::CREATED:
						case OCore::UPDATED:
						case OCore::DATE: {
							$sql .= "DATETIME";
						}
						break;
						case OCore::NUM: {
							$sql .= "INT(11)";
						}
						break;
						case OCore::PK_STR:
						case OCore::TEXT: {
							if ($field['size']<256) {
								$sql .= "VARCHAR(" . $field['size'] . ") COLLATE " . $core->config->getDb('collate');
							}
							else {
								$sql .= "TEXT COLLATE " . $core->config->getDb('collate');
							}
						}
						break;
						case OCore::BOOL: {
							$sql .= "TINYINT(1)";
						}
						break;
						case OCore::LONGTEXT: {
							$sql .= "TEXT COLLATE " . $core->config->getDb('collate');
						}
						break;
						case OCore::FLOAT:{
							$sql .= "FLOAT";
						}
						break;
					}
					if (!$field['nullable'] || $field['ref']!='') {
						$sql .= " NOT";
					}
					$sql .= " NULL";
					if ($field['incr'] && count($this->pk)<2) {
						$sql .= " AUTO_INCREMENT";
					}
					if (!$field['nullable'] && !is_null($field['default']) && $field['ref']=='') {
						if ($field['type']!=OCore::BOOL) {
							$sql .= " DEFAULT '".$field['default']."'";
						}
						else {
							$sql .= " DEFAULT '".($field['default'] ? '1' : '0')."'";
						}
					}
					if ($field['comment']!='') {
						$sql .= " COMMENT '".$field['comment']."' ";
					}
					$sql = substr($sql, 0, strlen($sql)-1);
					$sql .= ",\n";
				}
				$sql .= "  PRIMARY KEY (`".implode('`,`',$this->pk)."`)\n";
				$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=" . $core->config->getDb('charset') . " COLLATE=" . $core->config->getDb('collate') . ";\n";

				$ret = $sql;
			}
			break;
		}

		return $ret;
	}

	/**
	 * Function to generate the sql commands needed to create the Foreign Keys based on references made in the model
	 *
	 * @return string SQL commands to create the Foreign Keys
	 */
	public function generateRefs() {
		$sql         = '';
		$has_refs    = false;
		$indexes     = [];
		$constraints = [];

		foreach ($this->model as $field_name => $field) {
			if ($field['ref']!='') {
				$has_refs = true;
				break;
			}
		}
		if ($has_refs) {
			$sql .= "ALTER TABLE `".$this->table_name."`\n";
		}

		foreach ($this->model as $field_name => $field) {
			if ($field['ref']!='') {
				$ref = explode('.', $field['ref']);
				array_push($indexes, "  ADD KEY `fk_".$this->table_name."_".$ref[0]."_idx` (`".$field_name."`)");
				array_push($constraints, "  ADD CONSTRAINT `fk_".$this->table_name."_".$ref[0]."` FOREIGN KEY (`".$field_name."`) REFERENCES `".$ref[0]."` (`".$ref[1]."`) ON DELETE NO ACTION ON UPDATE NO ACTION");
			}
		}

		if ($has_refs) {
			$sql .= implode(",\n", $indexes);
			$sql .= ",\n";
			$sql .= implode(",\n", $constraints);
			$sql .= ";\n";
		}

		return $sql;
	}

	/**
	 * Function to return a safe string / numeric based representation of a value
	 *
	 * @param string|integer $value Value needed to be cleaned
	 *
	 * @return string|integer Safe representation of the given value
	 */
	public function cleanValue($value) {
		if (is_null($value)) {
			return 'null';
		}
		if (is_numeric($value)) {
			return $value;
		}
		$str = str_ireplace("\n", '\n', $value);
		$str = str_ireplace('"', '\"', $str);
		return '"'.$str.'"';
	}

	/**
	 * Function to echo a json representation of the model if the object is treated as a string
	 *
	 * @return string JSON representation of the object
	 */
	public function __toString() {
		$fields = [];
		foreach ($this->model as $field_name => $field){
			$fields[$field_name] = $this->cleanValue($field['value']);
		}
		return json_encode($fields);
	}
}