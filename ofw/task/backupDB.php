<?php declare(strict_types=1);
/**
 * Performs a database backup using "mysqldump" CLI tool. Generates a file on ofw/export folder with the name of the database.
 */
class backupDBTask {
	/**
	 * Returns description of the task
	 *
	 * @return string Description of the task
	 */
	public function __toString() {
		return $this->colors->getColoredString("backupDB", "light_green").": ".OTools::getMessage('TASK_BACKUP_DB');
	}

	private ?OColors $colors = null;

	/**
	 * Loads class used to colorize messages
	 */
	function __construct() {
		$this->colors = new OColors();
	}

	/**
	 * Run the task
	 *
	 * @param array $params If $params has one item and is true, generates the backup silently, else it echoes information messages
	 *
	 * @return void Echoes messages generated while performing the backup
	 */
	public function run(array $params=[]): void {
		global $core;
		$silent = false;
		if (count($params)==1 && $params[0]===true) {
			$silent = true;
		}
		if ($core->config->getDB('host')=='' || $core->config->getDB('user')=='' || $core->config->getDB('pass')=='' || $core->config->getDB('name')=='') {
			echo "  ".$this->colors->getColoredString(OTools::getMessage('TASK_BACKUP_DB_NO_DB'), "white", "red")."\n\n";
			exit;
		}
		echo "\n";
		if (!$silent) {
			echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
		}

		$dump_file = $core->config->getDir('ofw_export').$core->config->getDb('name').'.sql';
		echo OTools::getMessage('TASK_BACKUP_DB_EXPORTING', [
			$this->colors->getColoredString($core->config->getDb('name'), "light_green"),
			$this->colors->getColoredString($dump_file, "light_green")
		]);
		if (file_exists($dump_file)) {
			echo OTools::getMessage('TASK_BACKUP_DB_EXISTS');
			unlink($dump_file);
		}
		$command = "mysqldump --user={$core->config->getDB('user')} --password={$core->config->getDB('pass')} --host={$core->config->getDB('host')} {$core->config->getDB('name')} --result-file={$dump_file} 2>&1";

		exec($command, $output);
		if (is_array($output) && count($output)==0) {
			echo OTools::getMessage('TASK_BACKUP_DB_SUCCESS');
		}
	}
}