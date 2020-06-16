<?php declare(strict_types=1);
/**
 * Function to export an application with all its files to a single self-extracting php file
 */
class extractorTask extends OTask {
	public function __toString() {
		return $this->getColors()->getColoredString('extractor', 'light_green').': '.OTools::getMessage('TASK_EXTRACTOR');
	}

	private ?string $base_dir;
	private array $folder_list = [
		'app'  => true,
		'logs' => false,
		'ofw'  => true,
		'web'  => true
	];

	/**
	 * Scan a given path to get all its files. If it founds sub-dirs, it calls recursively to itself
	 *
	 * @param string $path Path to be checked
	 *
	 * @param string[] $name Array of found file names
	 *
	 * @return string[] Array with file names
	 */
	private function scanFileNameRecursivly(string $path = '', array &$name = []): array {
		$path = ($path == '') ? $this->base_dir : $path;
		$lists = @scandir($path);

		if(!empty($lists)) {
			foreach($lists as $f) {
				if ($f=='.' || $f=='..') { continue; }
				if (is_dir($path.DIRECTORY_SEPARATOR.$f) && $f != '..' && $f != '.') {
					$this->scanFileNameRecursivly($path.DIRECTORY_SEPARATOR.$f, $name);
				}
				else {
					array_push($name, $path.DIRECTORY_SEPARATOR.$f);
				}
			}
		}
		return $name;
	}

	/**
	 * Run the task
	 *
	 * @param array $params If $params has one item and is true, generates the backup silently, else it echoes information messages
	 *
	 * @return void Echoes messages generated while performing the export
	 */
	public function run(array $params=[]): void {
		$silent = false;
		if (count($params)==1 && $params[0]===true) {
			$silent = true;
		}
		$this->base_dir = $this->getConfig()->getDir('base');
		$destination    = $this->getConfig()->getDir('ofw_export').'ofw_extractor.php';

		$path   = $this->getConfig()->getDir('ofw_template').'extractor/extractor.php';
		$values = [
			'colors'      => $this->getColors(),
			'file_exists' => file_exists($destination),
			'num_files'   => 0,
			'num_folders' => 0
		];

		if ($values['file_exists']) {
			unlink($destination);
		}
		$folders = [];
		$files   = [];

		file_put_contents($destination, "<?php\n");

		$files['ofw.php'] = OTools::fileToBase64($this->getConfig()->getDir('base') . 'ofw.php');

		// Traverse folders
		foreach ($this->folder_list as $folder => $explore) {
			// If folder has to be explored
			if ($explore) {
				// Get the file list recursively
				$file_names = $this->scanFileNameRecursivly($this->getConfig()->getDir('base') . $folder);

				// Traverse files
				foreach ($file_names as $file_name) {
					// Relative folder and file name
					$key = str_ireplace($this->getConfig()->getDir('base'), '', $file_name);
					// Add to the array the content of the file
					$files[$key] = OTools::fileToBase64($file_name);

					// Get the array with files path eg: 'model/base' => ['model', 'base', 'base.php']
					$folder_name = explode('/', $key);
					// Take out the files name to get just the folders eg: ['model', 'base']
					array_pop($folder_name);
					// Take the first part
					$check_folder = array_shift($folder_name);
					while (count($folder_name)>-1) {
						if (!in_array($check_folder, $folders)) {
							array_push($folders, $check_folder);
						}
						if (count($folder_name)>0) {
							$check_folder .= '/' . array_shift($folder_name);
						}
						else {
							break;
						}
					}
				}
			}
			else {
				// Add folder to the list
				array_push($folders, $folder);
			}
		}

		$values['num_files'] = count($files);

		file_put_contents($destination, "$"."files = [\n", FILE_APPEND);
		$content_array = [];
		foreach ($files as $key => $content) {
			array_push($content_array, "  '".$key."' => '".$content."'");
		}
		file_put_contents($destination, implode(",\n", $content_array), FILE_APPEND);
		file_put_contents($destination, "];\n", FILE_APPEND);

		unset($files);
		unset($content_array);

		$values['num_folders'] = count($folders);

		file_put_contents($destination, "$"."folders = ['", FILE_APPEND);
		file_put_contents($destination, implode("','", $folders), FILE_APPEND);
		file_put_contents($destination, "'];\n", FILE_APPEND);

		unset($files);

		$str = "\n";
		$str .= "fun"."ction base64ToFile($"."base64_string, $"."filename){\n";
		$str .= "	$"."ifp = fopen( $"."filename, 'wb' );\n";
		$str .= "	$"."data = explode( ',', $"."base64_string );\n";
		$str .= "	fwrite( $"."ifp, base64_decode( $"."data[ 1 ] ) );\n";
		$str .= "	fclose( $"."ifp );\n";
		$str .= "}\n\n";

		$str .= "$"."basedir = realpath(dirname(__FILE__));\n";
		$str .= "echo \"".OTools::getMessage('TASK_EXTRACTOR_BASE_FOLDER').": \".$"."basedir.\"\\n\";\n";
		$str .= "echo \"".OTools::getMessage('TASK_EXTRACTOR_CREATE_FOLDERS')." (\".count($"."folders).\")\\n\";\n";
		$str .= "foreach ($"."folders as $"."i => $"."folder){\n";
		$str .= "	echo \"  \".($"."i+1).\"/\".count($"."folders).\" - \".$"."folder.\"\\n\";\n";
		$str .= "	mkdir($"."basedir.\"/\".$"."folder);\n";
		$str .= "}\n\n";

		$str .= "echo \"".OTools::getMessage('TASK_EXTRACTOR_CREATE_FILES')." (\".count($"."files).\")\\n\";\n";
		$str .= "$"."cont = 1;\n";
		$str .= "foreach ($"."files as $"."key => $"."file){\n";
		$str .= "	echo \"  \".$"."cont.\"/\".count($"."files).\" - \".$"."key.\"\\n\";\n";
		$str .= "	base64ToFile($"."file, $"."basedir.'/'.$"."key);\n";
		$str .= "	$"."cont++;\n";
		$str .= "}";
		file_put_contents($destination, $str, FILE_APPEND);

		if (!$silent) {
			echo OTools::getPartial($path, $values);
		}
	}
}