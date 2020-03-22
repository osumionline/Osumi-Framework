<?php
/**
 * Get Frameworks current version information
 */
class versionTask {
	/**
	 * Returns description of the task
	 *
	 * @return Description of the task
	 */
	public function __toString() {
		return $this->colors->getColoredString("version", "light_green").": ".OTools::getMessage('TASK_VERSION');
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

	private $repo_url = 'https://github.com/igorosabel/Osumi-Framework';
	private $twitter_url = 'https://twitter.com/osumionline';

	/**
	 * Run the task
	 *
	 * @return string Returns framework information
	 */
	public function run() {
		echo "\n==============================================================================================================\n";
		echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
		echo "    ".OTools::getVersionInformation()."\n\n";
		echo "  ".$this->colors->getColoredString("GitHub", "light_green").":  ".$this->repo_url."\n";
		echo "  ".$this->colors->getColoredString("Twitter", "light_green").": ".$this->twitter_url."\n";
		echo "==============================================================================================================\n\n";
	}
}