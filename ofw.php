<?php declare(strict_types=1);

namespace OsumiFramework;

require dirname(__FILE__).'/ofw/vendor/core/ocore.class.php' ;

use OsumiFramework\OFW\Core\OCore;

$core = new OCore();
$core->load(true);

use OsumiFramework\OFW\Tools\OTools;
use OsumiFramework\OFW\Tools\OColors;

function taskOptions(array $ofw_task_list, array $app_task_list): string {
	$ret = "";
	$ret .= OTools::getMessage('OFW_OPTIONS');
	asort($ofw_task_list);
	foreach ($ofw_task_list as $task) {
		$task_name = "\\OsumiFramework\\OFW\\Task\\".$task."Task";
		$task = new $task_name;
		$task->loadTask();
		$ret .= "  ·  ".$task."\n";
	}
	asort($app_task_list);
	foreach ($app_task_list as $task) {
		$task_name = "\\OsumiFramework\\App\\Task\\".$task."Task";
		$task = new $task_name;
		$task->loadTask();
		$ret .= "  ·  ".$task."\n";
	}
	$ret .= "\n".OTools::getMessage('OFW_EXAMPLE').": php ofw.php ".$ofw_task_list[0]."\n\n";
	return $ret;
}

$ofw_task_list = [];
$app_task_list = [];
$colors = new OColors();

// OFW Tasks
if ($model = opendir($core->config->getDir('ofw_task'))) {
	while (false !== ($entry = readdir($model))) {
		if ($entry != "." && $entry != "..") {
			array_push($ofw_task_list, str_ireplace(".php", "", $entry));
		}
	}
	closedir($model);
}

// App Tasks
if (file_exists($core->config->getDir('app_task'))) {
	if ($model = opendir($core->config->getDir('app_task'))) {
		while (false !== ($entry = readdir($model))) {
			if ($entry != "." && $entry != "..") {
				require $core->config->getDir('app_task').$entry;
				array_push($app_task_list, str_ireplace('.php', '', $entry));
			}
		}
		closedir($model);
	}
}

if (!array_key_exists(1, $argv)) {
	echo "\n  ".$colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
	echo OTools::getMessage('OFW_INDICATE_OPTION');
	echo taskOptions($ofw_task_list, $app_task_list);
	exit;
}

$option = $argv[1];
if (!in_array($option, $ofw_task_list) && !in_array($option, $app_task_list)) {
	echo OTools::getMessage('OFW_WRONG_OPTION', [$option]);
	echo taskOptions($ofw_task_list, $app_task_list);
	exit;
}

array_shift($argv);
array_shift($argv);

if (in_array($option, $ofw_task_list)) {
	$task_name = "\\OsumiFramework\\OFW\\Task\\".$option.'Task';
}
if (in_array($option, $app_task_list)) {
	$task_name = "\\OsumiFramework\\App\\Task\\".$option.'Task';
}
$task = new $task_name;
$task->loadTask();
$task->run($argv);