<?php declare(strict_types=1);
/**
 * Updates URLs cache file
 */
class updateUrlsTask extends OTask {
	public function __toString() {
		return $this->getColors()->getColoredString('updateUrls', 'light_green').': '.OTools::getMessage('TASK_UPDATE_URLS');
	}

	/**
	 * Run the task
	 *
	 * @return void Echoes messages generated while performing the update
	 */
	public function run(): void {
		$path   = $this->getConfig()->getDir('ofw_template').'updateUrls/updateUrls.php';
		$values = [
			'colors' => $this->getColors(),
			'messages' => OTools::updateUrls()
		];

		echo OTools::getPartial($path, $values);
	}
}