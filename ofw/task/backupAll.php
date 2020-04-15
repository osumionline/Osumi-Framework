<?php declare(strict_types=1);
/**
 * Generate a backup file (composer file) of the whole application (database and code). Calls internally to "backupDB" and "composer" tasks.
 */
class backupAllTask {
	/**
	 * Returns description of the task
	 *
	 * @return string Description of the task
	 */
	public function __toString() {
		return $this->colors->getColoredString("backupAll", "light_green").": ".OTools::getMessage('TASK_BACKUP_ALL');
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
	 * @return void Echoes messages generated while performing the backup
	 */
	public function run(): void {
		echo "\n";
		echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";

		OTools::runOFWTask('backupDB', [true]);
		OTools::runOFWTask('composer', [true]);

		echo "\n  ".$this->colors->getColoredString(OTools::getMessage('TASK_BACKUP_ALL_DONE'), "light_green")."\n\n";
	}
}