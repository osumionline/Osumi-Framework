<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Core;

use OsumiFramework\OFW\Tools\OTools;
use \ReflectionClass;

/**
 * Base class for components
 */
class OComponent {
	private array $values = [];
	private string $path = '';
	private string $template = '';

	function __construct(array $values = []) {
		$rc = new ReflectionClass(get_class($this));
		$full_path = $rc->getFileName();
		$data = explode('/', $full_path);
		$file_name = array_pop($data);
		$name = str_ireplace('.component.php', '', $file_name);

		$this->path = implode('/', $data).'/';
		$this->values = $values;
		$this->template = $this->path.$name.'.template.php';
	}

	/**
	 * Get all component values
	 *
	 * @return array All defined values
	 */
	public function getValues(): array {
		return $this->values;
	}

	/**
	 * Get a specific values from the defined values
	 *
	 * @return mixed Requested value or null if not found
	 */
	public function getValue(string $key) {
		if (array_key_exists($key, $this->values)) {
			return $this->values[$key];
		}
		return null;
	}

	/**
	 * Function to get the path of the component
	 *
	 * @return string Path of the component
	 */
	public function getPath(): string {
		return $this->path;
	}

	/**
	 * Function that takes the values, renders into the template and returns the result
	 *
	 * @return string Template with the values parsed
	 */
	public function __toString() {
		$output = OTools::getPartial($this->template, $this->values);

		if (is_null($output)) {
			$output = 'ERROR: File '.$name.' not found';
		}

		return $output;
	}
}
