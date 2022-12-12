<?php declare(strict_types=1);

namespace OsumiFramework\OFW\DB;

/**
 * Model field class for date field types
 */
class OModelFieldDate extends OModelField {
  private bool $original_set = false;
  private string | null $original_value = null;
  private string | null $current_value = null;
  private string | null $extra = null;
  public const SET_EXTRA = true;
  public const GET_EXTRA = true;

  function __construct(OModelField $field) {
    parent::fromField($field);
  }

  /**
   * Set value for the date type field. Only string and null values are accepted. Extra field makes the insert/update commands to be masked for date formatting.
   *
   * @param mixed $value String or null value for the field.
   *
   * @param string | null $extra MySQL DATE_FORMAT mask to be used on insert or update commands
   *
   * @return void
   */
  public function set(mixed $value, string | null $extra = null): void {
    if (is_string($value) || is_null($value)) {
      if ($this->original_set) {
        $this->current_value = $value;
      }
      else {
        $this->original_set = true;
        $this->original_value = $value;
        $this->current_value = $value;
      }
      if (!is_null($extra)) {
        $this->extra = $extra;
      }
    }
    else {
      throw new \Exception('Value "'.strval($value).'" must be a string or null ('.gettype($value).' given).');
    }
  }

  /**
   * Get fields value.
   *
   * @param string | null $extra PHP date format mask on result retrieval
   *
   * @return string | null String or null value of the field.
   */
  public function get(string | null $extra = null): string | null {
    if (!is_null($this->current_value) && !is_null($extra)) {
      return date($extra, strtotime($this->current_value));
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
    if (!is_null($this->extra)) {
      return 'DATE_FORMAT(`'.$this->getName().'`, "'.$this->extra.'") = ?';
    }
    return "`".$this->getName()."` = ?";
  }

  /**
   * Get the SQL line needed to create this particular field of a whole table.
   *
   * @return string Get the SQL line to create this field.
   */
  public function generate(): string {
    $sql = "  `".$this->getName()."` DATETIME";

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
