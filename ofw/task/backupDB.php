<?php
/**
 * Performs a database backup using "mysqldump" CLI tool. Generates a file on ofw/export folder with the name of the database.
 */
class backupDBTask {
	/**
	 * Returns description of the task
	 *
	 * @return Description of the task
	 */
	public function __toString() {
		return $this->colors->getColoredString("backupDB", "light_green").": ".OTools::getMessage('TASK_BACKUP_DB');
	}

	private $colors = null;

	/**
	 * Loads class used to colorize messages
	 *
	 * @return void
	 */
	function __construct() {
		$this->colors = new OColors();
	}

	/**
	 * Run the task
	 *
	 * @return string Returns messages generated while performing the backup
	 */
	public function run($silent = false) {
		global $core;
		if ($core->config->getDB('host')=='' || $core->config->getDB('user')=='' || $core->config->getDB('pass')=='' || $core->config->getDB('name')=='') {
			echo "  ".$this->colors->getColoredString(OTools::getMessage('TASK_BACKUP_DB_NO_DB'), "white", "red")."\n\n";
			return false;
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