<?php declare(strict_types=1);
/**
 * Creates new modules / actions / templates based on user configured urls.json
 */
class updateUrlsTask {
	/**
	 * Returns description of the task
	 *
	 * @return string Description of the task
	 */
	public function __toString() {
		return $this->colors->getColoredString("updateUrls", "light_green").": ".OTools::getMessage('TASK_UPDATE_URLS');
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
	 * @return void Echoes messages generated while performing the update
	 */
	public function run(): void {
		OTools::updateUrls();
	}
}