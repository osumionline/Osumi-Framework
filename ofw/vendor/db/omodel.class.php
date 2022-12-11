<?php declare(strict_types=1);

namespace OsumiFramework\OFW\DB;

use OsumiFramework\OFW\DB\OModelGroup;
use OsumiFramework\OFW\DB\OModelFieldNum;
use OsumiFramework\OFW\DB\OModelFieldText;
use OsumiFramework\OFW\DB\OModelFieldDate;
use OsumiFramework\OFW\DB\OModelFieldBool;
use OsumiFramework\OFW\DB\OModelFieldFloat;
use OsumiFramework\OFW\Log\Olog;
use \ReflectionClass;

/**
 * OModel - Base class for the model classes with all the methods necessary to interact with the database.
 */
class OModel {
	private   bool         $debug      = false;
	private   ?OLog        $l          = null;
	protected ?ODB         $db         = null;
	protected string       $model_name = '';
	protected string       $table_name = '';
	protected ?OModelGroup $model      = null;
	protected array        $pk         = [];
	protected ?string      $created    = null;
	protected ?string      $updated    = null;

	/**
	 * Load model information
	 *
	 * @param OModelGroup $model Model group object with the fields of the table (type, default value, nullable, comment explaining the field and references to another table fields)
	 *
	 * @param string $table_name Optional name of the database table. If ommitted the name of the model is used as the table name.
	 *
	 * @return void
	 */
	function load(OModelGroup $model, string $table_name = null): void {
		global $core;
		$this->debug = ($core->config->getLog('level') == 'ALL');
		if ($this->debug) {
			$this->l = new OLog('OModel');
		}

		if (is_null($table_name)) {
			$rc = new ReflectionClass(get_class($this));
			$full_path = $rc->getFileName();
			$data = explode('/', $full_path);
			$file_name = array_pop($data);
			$table_name = str_ireplace('.model.php', '', $file_name);
		}

		$this->db         = new ODB();
		$this->model_name = get_class($this);
		$this->table_name = $table_name;

		$this->pk      = $model->getPk();
		$this->created = $model->getCreated();
		$this->updated = $model->getUpdated();
		$this->model   = $model;
	}

	/**
	 * Logs internal information of the class
	 *
	 * @param string $str String to be logged
	 *
	 * @return void
	 */
	private function log(string $str): void {
		if ($this->debug) {
			$this->l->debug($str);
		}
	}

	/**
	 * Function to get the whole models information or information about a certain field
	 *
	 * @param string $key Optional fieldname, if defined retrieves information about a certain field and if omitted retrieves information about the whole model
	 *
	 * @return mixed Whole model (array of ModelFields) or field model (OModelFieldNum, OModelFieldText, OModelFieldDate, OModelFieldBool or OModelFieldFloat)
	 */
	public function getModel(string $key=null): array | OModelFieldNum | OModelFieldText | OModelFieldDate | OModelFieldBool | OModelFieldFloat | null {
		if (is_null($key)) {
			return $this->model->getFields();
		}
		else {
			if (array_key_exists($key, $this->model->getFields())) {
				return $this->model->getFields()[$key];
			}
			else {
				return null;
			}
		}
	}

	/**
	 * Function to set a fields value
	 *
	 * @param string $key Field name
	 *
	 * @param mixed Field value
	 *
	 * @return bool Field value was successfully updated or not
	 */
	public function set(string $key, mixed $value, string | int | null $extra = null): bool {
		if (array_key_exists($key, $this->model->getFields())) {
			if ($this->model->getFields()[$key]::SET_EXTRA) {
				$this->model->getFields()[$key]->set($value, $extra);
			}
			else {
				$this->model->getFields()[$key]->set($value);
			}
			return true;
		}
		return false;
	}

	/**
	 * Function to get a fields value.
	 *
	 * @param string $key Field name
	 *
	 * @param string|int|null $extra Extra value to modify the output
	 *
	 * @return string|int|float|bool|null Field value
	 */
	public function get(string $key, string | int | null $extra = null) {
		$field = $this->getModel($key);
		if (!is_null($field)) {
			if ($field::GET_EXTRA) {
				return $field->get($extra);
			}
			else {
				return $field->get();
			}
		}
		else {
			return null;
		}
	}

	/**
	 * Function to save current model into the database
	 *
	 * @return bool Returns true if everything went OK or false if an error happened
	 */
	public function save() {
		$save_type = '';
		$query_params = [];

		// Set last updated date
		if (!is_null($this->updated)) {
			$this->model->getFields()[$this->updated]->set(date('Y-m-d H:i:s', time()));
		}

		// UPDATE
		if (!is_null($this->model->getFields()[$this->created]->get())) {
			$sql = "UPDATE `".$this->table_name."` SET ";
			$updated_fields = [];
			foreach ($this->model->getFields() as $field) {
				if ($field->getType() !== OMODEL_PK && $field->getType() !== OMODEL_PK_STR && $field->changed()) {
					array_push($updated_fields, $field->getUpdateStr());
					array_push($query_params, $field->get());
				}
			}
			// If there is nothing to update, just return
			if (count($updated_fields)==0){
				return false;
			}
			$sql .= implode(", ", $updated_fields);
			$sql .= " WHERE ";
			foreach ($this->pk as $i => $pk_ind) {
				if ($i != 0) {
					$sql .= "AND ";
				}
				$sql .= "`".$pk_ind."` = ?";
				array_push($query_params, $this->model->getFields()[$pk_ind]->get());
			}

			$save_type = 'u';
		}
		// INSERT
		else {
			$this->model->getFields()[$this->created]->set(date('Y-m-d H:i:s', time()));

			$sql = "INSERT INTO `".$this->table_name."` (";
			$insert_fields = [];
			foreach ($this->model->getFields() as $field) {
				array_push($insert_fields, "`".$field->getName()."`");
			}
			$sql .= implode(",", $insert_fields);
			$sql .= ") VALUES (";
			$insert_fields = [];
			foreach ($this->model->getFields() as $field) {
				$value  = $field->get();
				array_push($insert_fields, "?");
				if ($field->getType() === OMODEL_PK && $field->getIncr()) {
					array_push($query_params, null);
				}
				else {
					array_push($query_params, $value);
				}
			}
			$sql .= implode(",", $insert_fields);
			$sql .= ")";

			$save_type = 'i';
		}

		$this->log('save - Query: '.$sql);
		$this->log('save - Params:');
		$this->log(var_export($query_params, true));

		// Run the query
		try {
			$this->db->query($sql, $query_params);
		}
		catch(Exception $ex) {
			$this->log('ERROR: '.$ex->getMessage());
			return false;
		}

		// If table has only a PK and it is incremental, save it
		if ($save_type == 'i' && count($this->pk)==1 && $this->model->getFields()[$this->pk[0]]->getIncr()) {
			$this->model->getFields()[$this->pk[0]]->set($this->db->lastId());
		}

		// Set every field in the model as saved (original = current)
		foreach($this->model->getFields() as $field){
			$field->reset();
		}

		return true;
	}

	/**
	 * Function to search the database and if found populates the model object
	 *
	 * @param string[] $opt Fieldname / value pairs to look up in the database
	 *
	 * @return bool Data found based on given parameters
	 */
	public function find(array $opt=[]): bool {
		if (count($opt) == 0) {
			return false;
		}
		$sql = "SELECT * FROM `".$this->table_name."` WHERE ";
		$search_fields = [];
		foreach ($opt as $key => $value) {
			if (!is_null($value)) {
				if ($this->model->getFields()[$key]->getType() != OMODEL_BOOL) {
					array_push($search_fields, "`".$key."` = '".$value."' ");
				}
				else {
					array_push($search_fields, "`".$key."` = ".($value ? 1 : 0)." ");
				}
			}
			else {
				array_push($search_fields, "`".$key."` IS NULL ");
			}
		}
		$sql .= implode("AND ", $search_fields);

		$this->log('find - Query: '.$sql);

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
	public function update(array $res): void {
		foreach ($this->model->getFields() as $field){
			if (array_key_exists($field->getName(), $res)){
				if (is_null($res[$field->getName()])) {
					$this->model->getFields()[$field->getName()]->set(null);
				}
				else {
					switch($field->getType()) {
						case OMODEL_NUM: {
							$this->model->getFields()[$field->getName()]->set( intval($res[$field->getName()]) );
						}
						break;
						case OMODEL_FLOAT: {
							$this->model->getFields()[$field->getName()]->set( floatval($res[$field->getName()]) );
						}
						break;
						case OMODEL_BOOL: {
							$this->model->getFields()[$field->getName()]->set( ($res[$field->getName()]==1) );
						}
						break;
						default: {
							$this->model->getFields()[$field->getName()]->set( $res[$field->getName()] );
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
	public function delete(): void {
		$sql = "DELETE FROM `".$this->table_name."` WHERE ";
		$delete_fields = [];
		foreach ($this->pk as $pk_field) {
			array_push($delete_fields, "`".$pk_field."` = '".$this->model->getFields()[$pk_field]->get()."' ");
		}
		$sql .= implode("AND ", $delete_fields);

		$this->db->query($sql);

		$this->log('delete - Query: '.$sql);
	}

	/**
	 * Return a representation of the model as data (php array / json / sql create)
	 *
	 * @return array|string Representation of the model
	 */
	public function generate() {
		return $this->model->generate($this->table_name);
	}

	/**
	 * Function to generate the sql commands needed to create the Foreign Keys based on references made in the model
	 *
	 * @return string SQL commands to create the Foreign Keys
	 */
	public function generateRefs(): string {
		return $this->model->generateRefs($this->table_name);
	}

	/**
	 * Function to return a safe string / numeric based representation of a value
	 *
	 * @param string|int $value Value needed to be cleaned
	 *
	 * @return string|int Safe representation of the given value
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
		foreach ($this->model->getFields() as $field){
			$fields[$field->getName()] = $this->cleanValue($field->get());
		}
		return json_encode($fields);
	}
}
