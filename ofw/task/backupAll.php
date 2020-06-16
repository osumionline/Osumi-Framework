<?php declare(strict_types=1);
/**
 * Generate a backup file (composer file) of the whole application (database and code). Calls internally to "backupDB" and "composer" tasks.
 */
class backupAllTask extends OTask {
	public function __toString() {
		return $this->getColors()->getColoredString("backupAll", "light_green").": ".OTools::getMessage('TASK_BACKUP_ALL');
	}

	/**
	 * Run the task
	 *
	 * @return void Echoes messages generated while performing the backup
	 */
	public function run(): void {
		$path             = $this->getConfig()->getDir('ofw_template').'backupAll/backupAll.php';
		$backupdb_result  = OTools::runOFWTask('backupDB',  [true, true], true);
		$extractor_result = OTools::runOFWTask('extractor', [true], true);

		$params = [
			'colors'           => $this->getColors(),
			'backupdb_result'  => $backupdb_result['return'],
			'extractor_result' => $extractor_result['return']
		];

		echo OTools::getPartial($path, $params);
	}
}