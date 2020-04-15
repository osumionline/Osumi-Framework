<?php declare(strict_types=1);
/**
 * Update Framework files to a newer version
 */
class updateTask {
	/**
	 * Returns description of the task
	 *
	 * @return string Description of the task
	 */
	public function __toString() {
		return $this->colors->getColoredString("update", "light_green").": ".OTools::getMessage('TASK_UPDATE');
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
	 * @return void Echoes update information
	 */
	public function run(): void {
		$update = new OUpdate();
		$to_be_updated = $update->doUpdateCheck();

		echo "\n";
		echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
		echo OTools::getMessage('TASK_UPDATE_CHECK_INSTALLED_VERSION', [$update->getCurrentVersion()]);
		echo OTools::getMessage('TASK_UPDATE_CHECK_CURRENT_VERSION', [$update->getRepoVersion()]);

		switch ($update->getVersionCheck()) {
			case -1: {
				echo OTools::getMessage('TASK_UPDATE_CHECK_LIST');
				$update->doUpdate();
			}
			break;
			case 0: {
				echo "  ".$this->colors->getColoredString(OTools::getMessage('TASK_UPDATE_CHECK_UPDATED'), "light_green")."\n\n";
			}
			break;
			case 1: {
				echo "  ".$this->colors->getColoredString(OTools::getMessage('TASK_UPDATE_CHECK_NEWER'), "white", "red")."\n\n";
			}
			break;
		}
	}
}