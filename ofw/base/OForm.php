<?php
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
	private $fields = [];

	public function setFields($fields) {
		$this->fields = $fields;
	}
	public function getFields() {
		return $this->fields;
	}
	public function addField($name, $field) {
		$this->fields[$name] = $field;
	}
	public function getField($name) {
		if (array_key_exists($name, $this->fields)){
			return $this->fields[$name];
		}
		return false;
	}
	public function getFieldValue($name) {
		if (array_key_exists($name, $this->fields)){
			return $this->fields[$name]['value'];
		}
		return false;
	}

	public function isValid() {
		$is_valid = true;

		foreach ($this->fields as $name => $field){
			$is_valid_field = true;
			foreach ($this->fields[$name]['validation'] as $validation){
				switch ($validation){
					case self::REQUIRED: {
						if (trim($this->fields[$name]['value']) == ''){
							$is_valid_field = false;
						}
					}
					break;
					case self::VALID_EMAIL:{
						if (!filter_var($this->fields[$name]['value'], FILTER_VALIDATE_EMAIL)) {
							$is_valid_field = false;
						}
					}
					break;
				}
			}
			$this->fields[$name]['valid'] = $is_valid_field;
			if (!$is_valid_field){ $is_valid = false; }
		}

		return $is_valid;
	}

	public function loadValues($list) {
		echo '<pre>';
		var_dump($list);
		echo '</pre>';
		foreach ($this->fields as $name => $field){
			$this->fields[$name]['value'] = $list[$name];
			echo "LIST[".$name."]: ".$list[$name]."<br>\n";
		}
	}
}