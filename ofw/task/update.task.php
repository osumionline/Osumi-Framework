<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Task;

use OsumiFramework\OFW\Core\OTask;
use OsumiFramework\OFW\Core\OUpdate;
use OsumiFramework\OFW\Tools\OTools;

/**
 * Update Framework files to a newer version
 */
class updateTask extends OTask {
	public function __toString() {
		return $this->getColors()->getColoredString('update', 'light_green').': '.OTools::getMessage('TASK_UPDATE');
	}

	/**
	 * Run the task
	 *
	 * @return void Echoes update information
	 */
	public function run(): void {
		$update = new OUpdate();

		$path   = $this->getConfig()->getDir('ofw_template').'update/update.php';
		$values = [
			'colors' => $this->getColors(),
			'current_version' => $update->getCurrentVersion(),
			'repo_version' => $update->getRepoVersion(),
			'check' => $update->getVersionCheck(),
			'messages' => ''
		];

		if ($values['check']==-1) {
			$values['messages'] = $update->doUpdate();
		}
		echo OTools::getPartial($path, $values);
	}
}