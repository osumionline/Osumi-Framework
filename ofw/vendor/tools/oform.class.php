<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Tools;

/**
 * OForm - Class with methods to validate user sent forms
 */
class OForm {
	const REQUIRED = 1;
	const VALID_EMAIL = 2;

	const TEXT   = 1;
	const EMAIL  = 2;
	const NUMBER = 3;
	const SELECT = 4;
	const CHECK  = 5;
	const RADIO  = 6;

	// Field: 'name' => ['value', 'type' , 'validation']
	private array $fields = [];

	/**
	 * Set the fields to be validated
	 *
	 * @param array Field list to be validated (value, type, validation)
	 *
	 * @return void
	 */
	public function setFields(array $fields): void {
		$this->fields = $fields;
	}

	/**
	 * Get the field list to be validated
	 *
	 * @return array Field list to be validated (value, type, validation)
	 */
	public function getFields(): array {
		return $this->fields;
	}

	/**
	 * Add one new field to be validated
	 *
	 * @param string $name Key name of the field
	 *
	 * @param array $field Field to be validated (value, type, validation)
	 *
	 * @return void
	 */
	public function addField(string $name, array $field): void {
		$this->fields[$name] = $field;
	}

	/**
	 * Get a field to be validated by its key name
	 *
	 * @param string $name Key name of the field
	 *
	 * @return array Field to be validated (value, type, validation)
	 */
	public function getField(string $name): ?array {
		if (array_key_exists($name, $this->fields)) {
			return $this->fields[$name];
		}
		return null;
	}

	/**
	 * Get fields value
	 *
	 * @param string $name Key name of the field
	 *
	 * @return string Value of the field as a string
	 */
	public function getFieldValue(string $name): ?string {
		if (array_key_exists($name, $this->fields)) {
			return $this->fields[$name]['value'];
		}
		return null;
	}

	/**
	 * Check if is a valid form by checking all the fields with their validation type
	 *
	 * @return bool Returns if the form is valid or not
	 */
	public function isValid(): bool {
		$is_valid = true;

		foreach ($this->fields as $name => $field) {
			$is_valid_field = true;
			foreach ($this->fields[$name]['validation'] as $validation) {
				switch ($validation){
					case self::REQUIRED: {
						if (trim($this->fields[$name]['value']) == '') {
							$is_valid_field = false;
						}
					}
					break;
					case self::VALID_EMAIL: {
						if (!filter_var($this->fields[$name]['value'], FILTER_VALIDATE_EMAIL)) {
							$is_valid_field = false;
						}
					}
					break;
				}
			}
			$this->fields[$name]['valid'] = $is_valid_field;
			if (!$is_valid_field){
				$is_valid = false;
			}
		}

		return $is_valid;
	}

	/**
	 * Load values from a key / value list into a proper validation format list
	 *
	 * @param array List of key / value pairs to be loaded
	 *
	 * @return void
	 */
	public function loadValues(array $list): void {
		echo '<pre>';
		var_dump($list);
		echo '</pre>';
		foreach ($this->fields as $name => $field) {
			$this->fields[$name]['value'] = $list[$name];
			echo "LIST[".$name."]: ".$list[$name]."<br>\n";
		}
	}
}