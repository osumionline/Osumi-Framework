<?php
class versionTask {
	public function __toString() {
		return $this->colors->getColoredString("version", "light_green").": Función para obtener el número de versión actual del Framework.";
	}

	private $colors = null;

	function __construct() {
		$this->colors = new OColors();
	}

	private $repo_url = 'https://github.com/igorosabel/Osumi-Framework';
	private $twitter_url = 'https://twitter.com/igorosabel';

	public function run() {
		echo "\n==============================================================================================================\n";
		echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n";
		echo "    ".Base::getVersionInformation()."\n\n";
		echo "  ".$this->colors->getColoredString("GitHub", "light_green").":  ".$this->repo_url."\n";
		echo "  ".$this->colors->getColoredString("Twitter", "light_green").": ".$this->twitter_url."\n";
		echo "==============================================================================================================\n\n";
	}
}