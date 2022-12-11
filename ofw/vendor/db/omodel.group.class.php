<?php declare(strict_types=1);

namespace OsumiFramework\OFW\DB;

use OsumiFramework\OFW\DB\OModelFieldNum;
use OsumiFramework\OFW\DB\OModelFieldText;
use OsumiFramework\OFW\DB\OModelFieldDate;
use OsumiFramework\OFW\DB\OModelFieldBool;
use OsumiFramework\OFW\DB\OModelFieldFloat;

/**
 * Class to make a table fields group. Given a list of OModelFields, it turns them into specific type class objects. Also performs some global validations.
 */
class OModelGroup {
	private array   $model   = [];
	private array   $pk      = [];
	private ?string $created = null;
	private ?string $updated = null;

	function __construct() {
		// Get list of arguments / fields
		$args = func_get_args();

		foreach ($args as $item) {
			// Create a "sub model field" based on its type
			switch ($item->getType()) {
				case OMODEL_PK: {
					$field = new OModelFieldNum($item);
					array_push($this->pk, $field->getName());
				}
				break;
				case OMODEL_PK_STR: {
					$field = new OModelFieldText($item);
				}
				break;
				case OMODEL_CREATED: {
					$field = new OModelFieldDate($item);
					$this->created = $field->getName();
				}
				break;
				case OMODEL_UPDATED: {
					$field = new OModelFieldDate($item);
					$this->updated = $field->getName();
				}
				break;
				case OMODEL_NUM: {
					$field = new OModelFieldNum($item);
				}
				break;
				case OMODEL_TEXT: {
					$field = new OModelFieldText($item);
				}
				break;
				case OMODEL_DATE: {
					$field = new OModelFieldDate($item);
				}
				break;
				case OMODEL_BOOL: {
					$field = new OModelFieldBool($item);
				}
				break;
				case OMODEL_LONGTEXT: {
					$field = new OModelFieldText($item);
				}
				break;
				case OMODEL_FLOAT: {
					$field = new OModelFieldFloat($item);
				}
				break;
			}

			// Check if field already exists in the model
			if (array_key_exists($field->getName(), $this->model)) {
				throw new \Exception('Field '.$field->getName().' already exists in the model.');
			}
			$this->model[$field->getName()] = $field;
		}

		// Model validations
		$validate_id = false;
		$validate_id_num = 0;
		$validate_created = false;
		$validate_created_num = 0;
		$validate_updated = false;
		$validate_updated_num = 0;
		$validate_errors = [];
		foreach ($this->model as $field) {
			if ($field->getType() === OMODEL_PK || $field->getType() === OMODEL_PK_STR) {
				$validate_id = true;
				$validate_id_num++;
			}
			if ($field->getType() === OMODEL_CREATED) {
				$validate_created = true;
				$validate_created_num++;
			}
			if ($field->getType() === OMODEL_UPDATED) {
				$validate_updated = true;
				$validate_updated_num++;
			}
		}
		if (!$validate_id) {
			array_push($validate_errors, 'At least one PK or PK_STR field type is mandatory.');
		}
		if ($validate_id_num > 1) {
			foreach ($this->model as $field) {
				if ($field->getType() === OMODEL_PK || $field->getType() === OMODEL_PK_STR) {
					$field->setIncr(false);
				}
			}
		}
		if (!$validate_created) {
			array_push($validate_errors, 'A CREATED field type is mandatory.');
		}
		if ($validate_created_num > 1) {
			array_push($validate_errors, 'There can only be one CREATED field type in a model.');
		}
		if (!$validate_updated) {
			array_push($validate_errors, 'An UPDATED field type is mandatory.');
		}
		if ($validate_updated_num > 1) {
			array_push($validate_errors, 'There can only be one UPDATED field type in a model.');
		}
		// If there is one or more errors, throw all of them at once.
		if (count($validate_errors) > 0) {
			throw new \Exception(implode(' ', $validate_errors));
		}
	}

	/**
	 * Get model field list
	 *
	 * @return array List of model fields
	 */
	public function getFields(): array {
		return $this->model;
	}

	/**
	 * Get list of fields that are PK
	 *
	 * @return array List of PK fields
	 */
	public function getPk(): array {
		return $this->pk;
	}

	/**
	 * Get created types field name
	 *
	 * @return ?string Name of the created type field
	 */
	public function getCreated(): ?string {
		return $this->created;
	}

	/**
	 * Get updated types field name
	 *
	 * @return ?string Name of the updated type field
	 */
	public function getUpdated(): ?string {
		return $this->updated;
	}

	/**
	 * Generate the SQL command to create a models table, with it's columns
	 *
	 * @param string $table_name Name of the table
	 *
	 * @return string SQL command to create the table
	 */
	public function generate(string $table_name): string {
		global $core;

		$sql = "CREATE TABLE `".$table_name."` (\n";
		foreach ($this->model as $field) {
			$sql .= $field->generate().",\n";
		}
		$sql .= "  PRIMARY KEY (`".implode('`,`', $this->pk)."`)\n";
		$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=" . $core->config->getDb('charset') . " COLLATE=" . $core->config->getDb('collate') . ";\n";

		return $sql;
	}

	/**
	 * Generate the SQL commands needed to create the relations between a models table and its referenced tables
	 *
	 * @param string $table_name Name of the table
	 *
	 * @return string SQL commands to create the relations
	 */
	public function generateRefs(string $table_name): string {
		$sql         = '';
		$has_refs    = false;
		$indexes     = [];
		$constraints = [];

		foreach ($this->model as $field) {
			if (!is_null($field->getRef())) {
				$has_refs = true;
				break;
			}
		}
		if ($has_refs) {
			$sql .= "ALTER TABLE `".$table_name."`\n";
		}

		foreach ($this->model as $field) {
			if (!is_null($field->getRef())) {
				$ref = explode('.', $field->getRef());
				array_push($indexes, "  ADD KEY `fk_".$table_name."_".$ref[0]."_idx` (`".$field->getName()."`)");
				array_push($constraints, "  ADD CONSTRAINT `fk_".$table_name."_".$ref[0]."` FOREIGN KEY (`".$field->getName()."`) REFERENCES `".$ref[0]."` (`".$ref[1]."`) ON DELETE NO ACTION ON UPDATE NO ACTION");
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
}
