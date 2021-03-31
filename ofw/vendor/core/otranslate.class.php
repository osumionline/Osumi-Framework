<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Core;

/**
 * Utility class to internationalize an application
 */
class OTranslate {
	private ?string $path = null;
	private ?string $lang = null;
	private array $headers = [];
	private array $translations = [];

	/**
	 * Set path to the PO file
	 *
	 * @param string $path Path to the PO file
	 *
	 * @return void
	 */
	public function setPath(string $path): void {
		$this->path = $path;
	}

	/**
	 * Get path to the PO file
	 *
	 * @return string Path to the PO file
	 */
	public function getPath(): string {
		return $this->path;
	}

	/**
	 * Set language code of the PO file
	 *
	 * @param string $lang Language code of the PO file (eg: en/es/eu)
	 *
	 * @return void
	 */
	public function setLang(string $lang): void {
		$this->lang = $lang;
		$this->headers['Language'] = $lang;
	}

	/**
	 * Get language code of the PO file
	 *
	 * @return string Language code of the PO file (eg: en/es/eu)
	 */
	public function getLang(): string {
		return $this->lang;
	}

	/**
	 * Get required translation
	 *
	 * @return string $key Key of the required translation or null if not found
	 */
	public function getTranslation(string $key): ?string {
		return array_key_exists($key, $this->translations) ? $this->translations[trim($key)] : null;
	}

	/**
	 * Set list of translations strings
	 *
	 * @param array $t List of translation strings
	 *
	 * @return void
	 */
	public function setTranslations(array $t): void {
		$this->translations = $t;
	}

	/**
	 * Get list of translation strings
	 *
	 * @return array List of translation strings
	 */
	public function getTranslations(): array {
		return $this->translations;
	}

	/**
	 * Set list of PO file headers
	 *
	 * @param array $h List of headers
	 *
	 * @return void
	 */
	public function setHeaders(array $h): void {
		$this->headers = $h;
	}

	/**
	 * Get list of PO file headers
	 *
	 * @return array List of headers
	 */
	public function getHeaders(): array {
		return $this->headers;
	}

	/**
	 * Create or edit a header
	 *
	 * @param string $key Name of the header
	 *
	 * @param string $header Value of the header
	 *
	 * @return void
	 */
	public function setHeader(string $key, string $header): void {
		$this->headers[$key] = $header;
	}

	/**
	 * Create or edit a translation
	 *
	 * @param string $key Text to be translated
	 *
	 * @param string $translation Translated text
	 *
	 * @return void
	 */
	public function setTranslation(string $key, string $translation): void {
		$this->translations[$key] = $translation;
	}

	/**
	 * Load translations from required PO file
	 *
	 * @param string $path Path to the PO file
	 *
	 * @return void
	 */
	public function load(string $path): void {
		$translations = [];
		$headers = [];
		if (file_exists($path)) {
			$this->path  = $path;
			$po = file($this->path);
			$first_msgid = array_shift($po);
			$first_msgstr = array_shift($po);
			$current = [];
			$doing_keys = false;
			$doing_translations = false;
			foreach ($po as $i => $line) {
				if (trim($line) === '') {
					continue;
				}
				if (substr($line, 0, 1) === '#') {
					continue;
				}
				if (substr($line, 0, 1) === '"') {
					if ($doing_keys) {
						array_push($current, trim(substr(trim($line), 1, -1)));
					}
					elseif ($doing_translations) {
						array_push($translation, trim(substr(trim($line), 1, -1)));
					}
					else {
						$header = explode(':', trim(substr(trim($line), 1, -1)));
						$header[1] = str_ireplace("\\n", "", $header[1]);
						$headers[$header[0]] = trim($header[1]);
						if ($header[0] === 'Language') {
							$this->lang = trim($header[1]);
						}
					}
				}
				if (substr($line, 0, 5) === 'msgid') {
					$headers_end = true;
					if (count($current)!=0 && count($translation)!=0) {
						$translations[implode("\n", $current)] = implode("\n", $translation);
					}
					$doing_keys = true;
					$doing_translations = false;
					$key = trim(substr(trim(substr($line,5)), 1, -1));
					$current = [];
					if ($key !== '') {
						array_push($current, $key);
					}
				}
				if (substr($line, 0, 6) === 'msgstr') {
					$doing_keys = false;
					$doing_translations = true;
					$value = trim(substr(trim(substr($line, 6)), 1, -1));
					$translation = [];
					if ($value !== '') {
						array_push($translation, $value);
					}
				}
			}

			if (count($current)!=0 && count($translation)!=0) {
				$translations[implode("\n", $current)] = implode("\n", $translation);
			}
		}
		$this->translations = $translations;
		$this->headers = $headers;
	}

	/**
	 * Save current loaded translations and headers into a file
	 *
	 * @param string $path Path of the file
	 *
	 * @return bool Returns if save operation was successful or not
	 */
	public function save(string $path = null): bool {
		if (is_null($path)) {
			$path = $this->path;
		}
		if (is_null($path)) {
			return false;
		}

		$str = "msgid \"\"\n";
		$str .= "msgstr \"\"\n";
		foreach ($this->headers as $key => $value) {
			$str .= "\"" . $key . ": " . $value . "\"\n";
		}
		$str .= "\n";
		foreach ($this->translations as $key => $value) {
			$str .= "msgid ";
			foreach (explode("\n", $key) as $key_part) {
				$str .= "\"" . $key_part . "\"\n";
			}
			$str .= "msgstr ";
			foreach (explode("\n", $value) as $value_part) {
				$str .= "\"" . $value_part . "\"\n";
			}
			$str .= "\n";
		}

		if (file_exists($path)) {
			unlink($path);
		}
		file_put_contents($path, $str);
		return true;
	}

	/**
	 * Create a new PO file
	 *
	 * @param string $path Path to the new PO file
	 *
	 * @param string $lang Language code of the new PO file (eg: en/es/eu)
	 *
	 * @return bool Returns if create operation was successful or not
	 */
	public function new(string $path = null, string $lang = 'en'): bool {
		if (is_null($path)) {
			$path = $this->path;
		}
		if (is_null($path)) {
			return false;
		}
		$this->path = $path;
		$this->lang = $lang;

		$this->headers = [
			'Project-Id-Version' => '',
			'POT-Creation-Date' => '',
			'PO-Revision-Date' => '',
			'Last-Translator' => '',
			'Language-Team' => '',
			'Language' => $this->lang,
			'MIME-Version: 1.0' => '',
			'Content-Type: text/plain; charset=UTF-8' => '',
			'Content-Transfer-Encoding: 8bit' => ''
		];

		return true;
	}
}