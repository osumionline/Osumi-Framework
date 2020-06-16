<?php declare(strict_types=1);
require dirname(__FILE__).'/ofw/core/OCore.php' ;
$core = new OCore();
$core->load(true);

function taskOptions(array $task_list): string {
	$ret = "";
	$ret .= OTools::getMessage('OFW_OPTIONS');
	asort($task_list);
	foreach ($task_list as $task) {
		$task_name = $task."Task";
		$task = new $task_name();
		$task->loadTask();
		$ret .= "  ·  ".$task."\n";
	}
	$ret .= "\n".OTools::getMessage('OFW_EXAMPLE').": php ofw.php ".$task_list[0]."\n\n";
	return $ret;
}

$task_list = [];
$colors = new OColors();

// OFW Tasks
if ($model = opendir($core->config->getDir('ofw_task'))) {
	while (false !== ($entry = readdir($model))) {
		if ($entry != "." && $entry != "..") {
			array_push($task_list, str_ireplace(".php", "", $entry));
		}
	}
	closedir($model);
}

// App Tasks
if ($model = opendir($core->config->getDir('app_task'))) {
	while (false !== ($entry = readdir($model))) {
		if ($entry != "." && $entry != "..") {
			require $core->config->getDir('app_task').$entry;
			array_push($task_list, str_ireplace('.php', '', $entry));
		}
	}
	closedir($model);
}

if (!array_key_exists(1, $argv)) {
	echo "\n  ".$colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
	echo OTools::getMessage('OFW_INDICATE_OPTION');
	echo taskOptions($task_list);
	exit;
}

$option = $argv[1];
if (!in_array($option, $task_list)) {
	echo OTools::getMessage('OFW_WRONG_OPTION', [$option]);
	echo taskOptions($task_list);
	exit;
}

array_shift($argv);
array_shift($argv);

$task_name = $option.'Task';
$task = new $task_name();
$task->loadTask();
$task->run($argv);