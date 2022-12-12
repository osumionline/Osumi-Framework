<?php declare(strict_types=1);

namespace OsumiFramework\OFW\DB;

/**
 * Model field class for boolean field types
 */
class OModelFieldBool extends OModelField {
  private bool $original_set = false;
  private bool | null $original_value = null;
  private bool | null $current_value = null;
  public const SET_EXTRA = false;
  public const GET_EXTRA = false;

  function __construct(OModelField $field) {
    parent::fromField($field);
  }

  /**
   * Set value for the boolean type field. Only boolean, int and null values are accepted. Int values get converted to boolean.
   *
   * @param mixed $value Boolean, integer or null value for the field.
   *
   * @return void
   */
  public function set(mixed $value): void {
    if (is_int($value) || is_bool($value) || is_null($value)) {
      if (is_int($value)) {
        $value = boolval($value);
      }
      if ($this->original_set) {
        $this->current_value = $value;
      }
      else {
        $this->original_set = true;
        $this->original_value = $value;
        $this->current_value = $value;
      }
    }
    else {
      throw new \Exception('Value "'.strval($value).'" must be an integer, a boolean or null ('.gettype($value).' given).');
    }
  }

  /**
   * Get fields value.
   *
   * @return bool | null Boolean or null value of the field.
   */
  public function get(): bool | null {
    return $this->current_value;
  }

  /**
   * Get if the field has its original value or if it has changed.
   *
   * @return bool Get if the fields value has changed since it was last saved.
   */
  public function changed(): bool {
    return ($this->original_set && $this->original_value !== $this->current_value);
  }

  /**
   * Reset the fields "original" status setting the current value as the original value.
   *
   * @return void
   */
  public function reset(): void {
  	$this->original_value = $this->current_value;
  }

  /**
   * Get the SQL string needed to update the field.
   *
   * @return string SQL string.
   */
  public function getUpdateStr(): string {
    return "`".$this->getName()."` = ?";
  }

  /**
   * Get the SQL line needed to create this particular field of a whole table.
   *
   * @return string Get the SQL line to create this field.
   */
  public function generate(): string {
    $sql = "  `".$this->getName()."` TINYINT(1)";

    if (!$this->getNullable() || !is_null($this->getRef())) {
      $sql .= " NOT NULL";
    }
    if ($this->getHasDefault()) {
      $sql .= " DEFAULT ".(is_null($this->getDefault()) ? "NULL" : $this->getDefault());
    }
    if (!is_null($this->getComment())) {
      $sql .= " COMMENT '".$this->getComment()."'";
    }

    return $sql;
  }
}
