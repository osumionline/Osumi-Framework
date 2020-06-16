<?php declare(strict_types=1);
class OPostInstall {
	private ?OConfig $config = null;
	private array    $messages = [
		'es' => [
					'TITLE'          => "\n\nPOST INSTALL 5.7.0\n\n",
					'TASKS_UPDATING' => "Actualizando tasks...\n",
					'TASK_UPDATED'   => "  Task \"%s\" actualizado.\n",
					'END_TITLE'      => "\n\nPOST INSTALL 5.7.0 finalizado.\n\n"
				],
		'en' => [
					'TITLE'          => "\n\nPOST INSTALL 5.7.0\n\n",
					'TASKS_UPDATING' => "Updating tasks...\n",
					'TASK_UPDATED'   => "  Task \"%s\" updated.\n",
					'END_TITLE'      => "\n\nPOST INSTALL 5.7.0 ended.\n\n"
				]
	];

	/**
	 * Updates a task replacing updated syntax
	 *
	 * @param string $task Name of the task file to be updated
	 *
	 * @return string Information messages
	 */
	private function updateTask(string $task): string {
		$path = $this->config->getDir('app_task').$task.'.php';
		$content = file_get_contents($path);
		$content = str_ireplace('Task {', 'Task extends OTask {', $content);

		file_put_contents($path, $content);

		return sprintf($this->messages[$this->config->getLang()]['TASK_UPDATED'], $task);
	}

	/**
	 * Store global configuration locally
	 */
	public function __construct() {
		global $core;
		$this->config = $core->config;
	}

	/**
	 * Runs the v5.7.0 update post-installation tasks
	 *
	 * @return string Information messages
	 */
	public function run(): string {
		$ret = $this->messages[$this->config->getLang()]['TITLE'];

		$ret .= $this->messages[$this->config->getLang()]['TASKS_UPDATING'];
		if (file_exists($this->config->getDir('app_task'))) {
			if ($model = opendir($this->config->getDir('app_task'))) {
				while (false !== ($entry = readdir($model))) {
					if ($entry != '.' && $entry != '..') {
						$task = str_ireplace('.php', '', $entry);
						$ret .= $this->updateTask($task);
					}
				}
				closedir($model);
			}
		}
		$ret .= $this->messages[$this->config->getLang()]['END_TITLE'];

		return $ret;
	}
}