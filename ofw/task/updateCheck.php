<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Task;

use OsumiFramework\OFW\Core\OTask;
use OsumiFramework\OFW\Core\OUpdate;
use OsumiFramework\OFW\Tools\OTools;

/**
 * Check if there are new updates on the Framework
 */
class updateCheckTask extends OTask {
	public function __toString() {
		return $this->getColors()->getColoredString('updateCheck', 'light_green').': '.OTools::getMessage('TASK_UPDATE_CHECK');
	}

	/**
	 * Run the task
	 *
	 * @return void Echoes update check information
	 */
	public function run(): void {
		$update = new OUpdate();
		$to_be_updated = $update->doUpdateCheck();

		$path   = $this->getConfig()->getDir('ofw_template').'update/update.php';
		$values = [
			'colors' => $this->getColors(),
			'current_version' => $update->getCurrentVersion(),
			'repo_version' => $update->getRepoVersion(),
			'check' => $update->getVersionCheck(),
			'messages' => ''
		];

		if ($values['check']==-1) {
			$values['messages'] = $update->showUpdates();
		}
		echo OTools::getPartial($path, $values);
	}
}