<?php
/**
 * Creates new modules / actions / templates based on user configured urls.json
 */
class updateUrlsTask {
	/**
	 * Returns description of the task
	 *
	 * @return Description of the task
	 */
	public function __toString() {
		return $this->colors->getColoredString("updateUrls", "light_green").": ".OTools::getMessage('TASK_UPDATE_URLS');
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
	 * @return string Returns messages generated while performing the update
	 */
	public function run() {
		OTools::updateUrls();
	}
}