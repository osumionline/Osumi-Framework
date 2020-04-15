<?php declare(strict_types=1);
/**
 * Function to export an application with all its files to a single self-extracting php file
 */
class composerTask {
	/**
	 * Returns description of the task
	 *
	 * @return string Description of the task
	 */
	public function __toString() {
		return $this->colors->getColoredString("composer", "light_green").": ".OTools::getMessage('TASK_COMPOSER');
	}

	private ?OColors $colors = null;

	/**
	 * Loads class used to colorize messages
	 *
	 * @return void
	 */
	function __construct() {
		$this->colors = new OColors();
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
		global $core;
		$silent = false;
		if (count($params)==1 && $params[0]===true) {
			$silent = true;
		}
		$this->base_dir = $core->config->getDir('base');

		echo "\n";
		if (!$silent) {
			echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
		}

		echo "  ".$this->colors->getColoredString(OTools::getMessage('TASK_COMPOSER_EXPORTING'), "light_green")."\n\n";
		$destination = $core->config->getDir('ofw_export').'ofw_composer.php';
		if (file_exists($destination)) {
			echo OTools::getMessage('TASK_COMPOSER_EXISTS');
			unlink($destination);
		}
		$folders = [];
		$files = [];

		file_put_contents($destination, "<?php\n");

		echo OTools::getMessage('TASK_COMPOSER_GETTING_FILES');

		$files['ofw.php'] = OTools::fileToBase64($core->config->getDir('base') . 'ofw.php');

		// Traverse folders
		foreach ($this->folder_list as $folder => $explore) {
			// If folder has to be explored
			if ($explore) {
				// Get the file list recursively
				$file_names = $this->scanFileNameRecursivly($core->config->getDir('base') . $folder);

				// Traverse files
				foreach ($file_names as $file_name) {
					// Relative folder and file name
					$key = str_ireplace($core->config->getDir('base'), '', $file_name);
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

		echo OTools::getMessage('TASK_COMPOSER_EXPORTING_FILES', [count($files)]);
		file_put_contents($destination, "$"."files = [\n", FILE_APPEND);
		$content_array = [];
		foreach ($files as $key => $content) {
			array_push($content_array, "  '".$key."' => '".$content."'");
		}
		file_put_contents($destination, implode(",\n", $content_array), FILE_APPEND);
		file_put_contents($destination, "];\n", FILE_APPEND);

		unset($files);
		unset($content_array);

		echo OTools::getMessage('TASK_COMPOSER_EXPORTING_FOLDERS', [count($folders)]);
		file_put_contents($destination, "$"."folders = ['", FILE_APPEND);
		file_put_contents($destination, implode("','", $folders), FILE_APPEND);
		file_put_contents($destination, "'];\n", FILE_APPEND);

		unset($files);

		echo OTools::getMessage('TASK_COMPOSER_GETTING_READY');
		$str = "\n";
		$str .= "fun"."ction base64ToFile($"."base64_string, $"."filename){\n";
		$str .= "	$"."ifp = fopen( $"."filename, 'wb' );\n";
		$str .= "	$"."data = explode( ',', $"."base64_string );\n";
		$str .= "	fwrite( $"."ifp, base64_decode( $"."data[ 1 ] ) );\n";
		$str .= "	fclose( $"."ifp );\n";
		$str .= "}\n\n";

		$str .= "$"."basedir = realpath(dirname(__FILE__));\n";
		$str .= "echo \"".OTools::getMessage('TASK_COMPOSER_BASE_FOLDER').": \".$"."basedir.\"\\n\";\n";
		$str .= "echo \"".OTools::getMessage('TASK_COMPOSER_CREATE_FOLDERS')." (\".count($"."folders).\")\\n\";\n";
		$str .= "foreach ($"."folders as $"."i => $"."folder){\n";
		$str .= "	echo \"  \".($"."i+1).\"/\".count($"."folders).\" - \".$"."folder.\"\\n\";\n";
		$str .= "	mkdir($"."basedir.\"/\".$"."folder);\n";
		$str .= "}\n\n";

		$str .= "echo \"".OTools::getMessage('TASK_COMPOSER_CREATE_FILES')." (\".count($"."files).\")\\n\";\n";
		$str .= "$"."cont = 1;\n";
		$str .= "foreach ($"."files as $"."key => $"."file){\n";
		$str .= "	echo \"  \".$"."cont.\"/\".count($"."files).\" - \".$"."key.\"\\n\";\n";
		$str .= "	base64ToFile($"."file, $"."basedir.'/'.$"."key);\n";
		$str .= "	$"."cont++;\n";
		$str .= "}";
		file_put_contents($destination, $str, FILE_APPEND);

		echo "  ".$this->colors->getColoredString(OTools::getMessage('TASK_COMPOSER_END'), "light_green")."\n";
	}
}