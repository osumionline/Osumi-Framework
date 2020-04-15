<?php declare(strict_types=1);
/**
 * Get Frameworks current version information
 */
class versionTask {
	/**
	 * Returns description of the task
	 *
	 * @return string Description of the task
	 */
	public function __toString() {
		return $this->colors->getColoredString("version", "light_green").": ".OTools::getMessage('TASK_VERSION');
	}

	private ?OColors $colors      = null;
	private string   $repo_url    = 'https://github.com/igorosabel/Osumi-Framework';
	private string   $twitter_url = 'https://twitter.com/osumionline';

	/**
	 * Loads class used to colorize messages
	 */
	function __construct() {
		$this->colors = new OColors();
	}

	/**
	 * Run the task
	 *
	 * @return void Echoes framework information
	 */
	public function run(): void {
		echo "\n==============================================================================================================\n\n";
		echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
		echo "  ".OTools::getVersionInformation()."\n\n";
		echo "  ".$this->colors->getColoredString("GitHub", "light_green").":  ".$this->repo_url."\n";
		echo "  ".$this->colors->getColoredString("Twitter", "light_green").": ".$this->twitter_url."\n\n";
		echo "==============================================================================================================\n\n";
	}
}