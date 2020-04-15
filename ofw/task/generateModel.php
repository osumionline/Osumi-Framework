<?php declare(strict_types=1);
/**
 * Generate a SQL file to create all the tables in the database based on user defined models (file generated on ofw/export)
 */
class generateModelTask {
	/**
	 * Returns description of the task
	 *
	 * @return string Description of the task
	 */
	public function __toString() {
		return $this->colors->getColoredString("generateModel", "light_green").": ".OTools::getMessage('TASK_GENERATE_MODEL');
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
	 * @return void Echoes SQL to create database tables and generates a SQL file in export folder
	 */
	public function run(): void {
		OTools::generateModel();
	}
}