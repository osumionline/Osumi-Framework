<?php
/**
 * Check if there are new updates on the Framework
 */
class updateCheckTask {
	/**
	 * Returns description of the task
	 *
	 * @return Description of the task
	 */
	public function __toString() {
		return $this->colors->getColoredString("updateCheck", "light_green").": ".OTools::getMessage('TASK_UPDATE_CHECK');
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
	 * @return string Returns update information
	 */
	public function run() {
		$update = new OUpdate();
		$to_be_updated = $update->doUpdateCheck();

		echo "\n";
		echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
		echo OTools::getMessage('TASK_UPDATE_CHECK_INSTALLED_VERSION', [$update->getCurrentVersion()]);
		echo OTools::getMessage('TASK_UPDATE_CHECK_CURRENT_VERSION', [$update->getRepoVersion()]);

		switch ($update->getVersionCheck()) {
			case -1: {
				echo OTools::getMessage('TASK_UPDATE_CHECK_LIST');
				$update->showUpdates();

				echo OTools::getMessage('TASK_UPDATE_CHECK_DO_UPDATE');
				echo "    ".$this->colors->getColoredString("php ofw.php update", "light_green")."\n\n";
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