<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Task;

use OsumiFramework\OFW\Core\OTask;
use OsumiFramework\OFW\Tools\OTools;

/**
 * Get Frameworks current version information
 */
class versionTask extends OTask {
	public function __toString() {
		return $this->getColors()->getColoredString('version', 'light_green').': '.OTools::getMessage('TASK_VERSION');
	}

	private string $repo_url    = 'https://github.com/igorosabel/Osumi-Framework';
	private string $twitter_url = 'https://twitter.com/osumionline';

	/**
	 * Run the task
	 *
	 * @return void Echoes framework information
	 */
	public function run(): void {
		$path   = $this->getConfig()->getDir('ofw_template').'version/version.php';
		$values = [
			'colors'      => $this->getColors(),
			'repo_url'    => $this->repo_url,
			'twitter_url' => $this->twitter_url
		];

		echo OTools::getPartial($path, $values);
	}
}