<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Task;

use OsumiFramework\OFW\Core\OTask;
use OsumiFramework\OFW\Tools\OTools;

/**
 * Generate a SQL file to create all the tables in the database based on user defined models (file generated on ofw/export)
 */
class generateModelTask extends OTask {
	public function __toString() {
		return $this->getColors()->getColoredString('generateModel', 'light_green').': '.OTools::getMessage('TASK_GENERATE_MODEL');
	}

	/**
	 * Run the task
	 *
	 * @return void Echoes SQL to create database tables and generates a SQL file in export folder
	 */
	public function run(): void {
		$path     = $this->getConfig()->getDir('ofw_template').'generateModel/generateModel.php';
		$sql_file = $this->getConfig()->getDir('ofw_export').'model.sql';
		$params   = [
			'colors'      => $this->getColors(),
			'file'        => $sql_file,
			'file_exists' => file_exists($sql_file)
		];

		OTools::generateModel();

		echo OTools::getPartial($path, $params);
	}
}