<?php declare(strict_types=1);

namespace OsumiFramework\OFW\DB;

/**
 * Model field class for text field types
 */
class OModelFieldText extends OModelField {
  private bool $original_set = false;
  private string | null $original_value = null;
  private string | null $current_value = null;
  public const SET_EXTRA = true;
  public const GET_EXTRA = true;

  function __construct(OModelField $field) {
    parent::fromField($field);
  }

  /**
   * Set value for the text type field. Only string and null values are accepted. Extra field truncates the string to given character count.
   *
   * @param mixed $value String or null value for the field.
   *
   * @param int | null $extra Number of characters for the field.
   *
   * @return void
   */
  public function set(mixed $value, int | null $extra = null): void {
    if (is_string($value) || is_null($value)) {
      if (!is_null($value) && !is_null($extra)) {
        $value = substr($value, 0, $extra);
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
      throw new \Exception('Value "'.strval($value).'" must be a string or null ('.gettype($value).' given).');
    }
  }

  /**
   * Get fields value.
   *
   * @param int | null $extra Number of characters for the field.
   *
   * @return string | null String or null value of the field.
   */
  public function get(int | null $extra = null): string | null {
    if (!is_null($this->current_value) && !is_null($extra)) {
      return substr($this->current_value, 0, $extra);
    }
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
    global $core;
    $sql = "  `".$this->getName()."` ";

    if ($this->getType() === OMODEL_PK_STR || $this->getType() === OMODEL_TEXT) {
      $sql .= "VARCHAR(" . $this->getSize() . ") COLLATE " . $core->config->getDb('collate');
    }
    else {
      $sql .= "TEXT COLLATE " . $core->config->getDb('collate');
    }
    if (!$this->getNullable() || !is_null($this->getRef())) {
      $sql .= " NOT NULL";
    }
    if ($this->getHasDefault()) {
      $sql .= " DEFAULT ".(is_null($this->getDefault()) ? "NULL" : "'".$this->getDefault()."'");
    }
    if (!is_null($this->getComment())) {
      $sql .= " COMMENT '".$this->getComment()."'";
    }

    return $sql;
  }
}
