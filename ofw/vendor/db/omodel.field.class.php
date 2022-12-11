<?php declare(strict_types=1);

namespace OsumiFramework\OFW\DB;

/**
 * Table field definition object class
 */
class OModelField {
	private string | null $name        = null;
	private int | null    $type        = null;
	private bool          $has_default = true;
	private int | float | string | bool | null $default = null;
	private bool | null   $incr        = null;
	private int | null    $size        = null;
	private bool          $nullable    = true;
	private string | null $comment     = null;
	private string | null $ref         = null;

	function __construct(
		string $name = null,
		int $type = null,
		int | float | string | bool | null $default = '__OFW_DEFAULT__',
		bool | null $incr = null,
		int $size = null,
		bool $nullable = true,
		string $comment = null,
		string $ref = null
	) {
		$this->validate($name, $type, $default, $incr, $size, $nullable, $comment, $ref);
	}

	/**
	 * Load an OModelField from a previously loaded OModelField
	 *
	 * @param OModelField $field Previously loaded OModelField
	 *
	 * @return void
	 */
	public function fromField(OModelField $field): void {
		$this->validate(
			$field->getName(),
			$field->getType(),
			$field->getDefault(),
			$field->getIncr(),
			$field->getSize(),
			$field->getNullable(),
			$field->getComment(),
			$field->getRef()
		);
		$this->has_default = $field->getHasDefault();
	}

	/**
	 * Validate a new OModelField options
	 *
	 * @param string $name Name of the field (mandatory)
	 *
	 * @param int $type Type of the field (PK, number, date, bool...) (mandatory)
	 *
	 * @param int | float | string | bool | null $default Default value for the field. Accepts many inputs because it depends on the type of the field.
	 *
	 * @param bool | null $incr Mark of an incremental PK
	 *
	 * @param int $size Character size or byte size of the field
	 *
	 * @param bool $nullable Marks the field to be nullable or not
	 *
	 * @param string $comment Comment describing the field
	 *
	 * @param string $ref Reference to another tables column
	 *
	 * @return void
	 */
	public function validate(
		string $name = null,
		int $type = null,
		int | float | string | bool | null $default = '__OFW_DEFAULT__',
		bool | null $incr = null,
		int $size = null,
		bool $nullable = true,
		string $comment = null,
		string $ref = null
	): void {
		// Field name is mandatory
		if (is_null($name)) {
			throw new \Exception('Name is mandatory for a field');
		}
		// Field type is mandatory
		if (is_null($type)) {
			throw new \Exception('Type is mandatory for a field');
		}
		// Primary keys are incremental by default
		if ($type === OMODEL_PK && is_null($incr)) {
			$incr = true;
			$nullable = false;
		}
		// If size is null field type defines the default size
		if (is_null($size)) {
			// Primary keys (numbers) and number are int fields, so length is 11
			if (
				$type === OMODEL_PK ||
				$type === OMODEL_NUM
			) {
				$size = 11;
			}
			// Varchar types are 50 characters long
			if (
				$type === OMODEL_PK_STR ||
				$type === OMODEL_TEXT
			) {
				$size = 50;
			}
			// Booleans are tinyints of 1 character long
			if ($type === OMODEL_BOOL) {
				$size = 1;
			}
		}
		// PK_STR and TEXT field length can not be more than 255
		if (($type === OMODEL_PK_STR || $type === OMODEL_TEXT) && !is_null($size) && $size > 255) {
			$size = 255;
		}
		// Created at fields cannot be nullable
		if ($type === OMODEL_CREATED) {
			$nullable = false;
		}
		// If the field is updated, it is nullable
		if ($type === OMODEL_UPDATED) {
			$default = null;
		}
		// If default is the preassigned code, there is no default value
		if ($default === '__OFW_DEFAULT__') {
			$this->has_default = false;
			$this->default = null;
		}
		else {
			$this->has_default = true;
			$this->default = $default;
		}
		if ($type === OMODEL_BOOL && !is_null($default) && is_bool($default)) {
			$default = ($default ? 1 : 0);
		}

		$this->name = $name;
		$this->type = $type;
		$this->incr = $incr;
		$this->size = $size;
		$this->nullable = $nullable;
		$this->comment = $comment;
		$this->ref = $ref;
	}

	/**
	 * Get fields name
	 *
	 * @return string Name of the field
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * Get fields type
	 *
	 * @return ?int Type of the field
	 */
	public function getType(): ?int {
		return $this->type;
	}

	/**
	 * Get if the field has a default value or not
	 *
	 * @return bool Field has a default value or not
	 */
	public function getHasDefault(): bool {
		return $this->has_default;
	}

	/**
	 * Get default value of the field
	 *
	 * @return int | float | string | bool | null Default value of the field
	 */
	public function getDefault(): int | float | string | bool | null {
		return $this->default;
	}

	/**
	 * Get if the field is autoincremental
	 *
	 * @return bool | null Field is autoincremental
	 */
	public function getIncr(): bool | null {
		return $this->incr;
	}

	/**
	 * Set the field to be autoincremental
	 *
	 * @param bool $incr Marks the field to be autoincremental or not
	 *
	 * @return void
	 */
	public function setIncr(bool $incr): void {
		$this->incr = $incr;
	}

	/**
	 * Get size of characters in text fields or byte size in numerical fields
	 *
	 * @return ?int Size of the field
	 */
	public function getSize(): ?int {
		return $this->size;
	}

	/**
	 * Get if the field is nullable, ie can have null values
	 *
	 * @return bool The field can be nullable or not
	 */
	public function getNullable(): bool {
		return $this->nullable;
	}

	/**
	 * Get the comment describing the field
	 *
	 * @return ?string Comment of the field
	 */
	public function getComment(): ?string {
		return $this->comment;
	}

	/**
	 * Get the reference to another tables field
	 *
	 * @return ?string Reference to another tables field
	 */
	public function getRef(): ?string {
		return $this->ref;
	}
}
