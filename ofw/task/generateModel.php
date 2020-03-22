<?php
/**
 * Generate a SQL file to create all the tables in the database based on user defined models (file generated on ofw/export)
 */
class generateModelTask {
	/**
	 * Returns description of the task
	 *
	 * @return Description of the task
	 */
	public function __toString() {
		return $this->colors->getColoredString("generateModel", "light_green").": ".OTools::getMessage('TASK_GENERATE_MODEL');
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
	 * @return string Returns SQL to create database tables
	 */
	public function run() {
		OTools::generateModel();
	}
}