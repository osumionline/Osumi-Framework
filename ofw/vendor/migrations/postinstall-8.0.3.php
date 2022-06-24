<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Migrations;

use OsumiFramework\OFW\Core\OConfig;
use OsumiFramework\OFW\Tools\OColors;

class OPostInstall {
	private ?OColors $colors = null;
	private ?OConfig $config = null;
	private array    $messages = [
		'es' => [
			'TITLE'            => "\nPOST INSTALL 8.0.3\n\n",
			'UPDATING_MODULES' => "  Actualizando módulos...\n",
			'MODULES_UPDATED'  => "  Módulos actualizados.\n",
			'END_TITLE'        => "\nPOST INSTALL 8.0.3 finalizado.\n\n"
		],
		'en' => [
			'TITLE'            => "\nPOST INSTALL 8.0.3\n\n",
			'UPDATING_MODULES' => "  Updating modules...\n",
			'MODULES_UPDATED'  => "  Modules updated.\n",
			'END_TITLE'        => "\nPOST INSTALL 8.0.3 finished.\n\n"
		],
		'eu' => [
			'TITLE'            => "\nPOST INSTALL 8.0.3\n\n",
			'UPDATING_MODULES' => "  Eguneratzen moduluak...\n",
			'MODULES_UPDATED'  => "  Moduluak eguneratu dira.\n",
			'END_TITLE'        => "\nPOST INSTALL 8.0.3 bukatu du.\n\n"
		]
	];

	/**
	 * Store global configuration locally
	 */
	public function __construct() {
		global $core;
		$this->config = $core->config;
		$this->colors = new OColors();
	}

	/**
	 * Runs the v8.0.3 update post-installation tasks
	 *
	 * @return string
	 */
	public function run(): string {
		$ret = '';
		// Start
		$ret .= $this->messages[$this->config->getLang()]['TITLE'];

		// Update modules action lists
		$ret .= $this->messages[$this->config->getLang()]['UPDATING_MODULES'];

		$module_path = $this->config->getDir('app_module');
		if (file_exists($module_path)) {
			if ($folder = opendir($module_path)) {
				// Every folder on app_module is a module
				while (false !== ($file = readdir($folder))) {
					if ($file != '.' && $file != '..') {
						$module_file_path = $module_path.$file."/".$file.".module.php";

						// Read module files content
						$module_content = file_get_contents($module_file_path);

						$actions = [];
						preg_match("/^\s+actions: '(.*?)',*$/m", $module_content, $match);

						if (!is_null($match[1])) {
							$actions = explode(',', $match[1]);
							for ($i = 0; $i < count($actions); $i++) {
								$actions[$i] = "'".trim($actions[$i])."'";
							}
						}

						$module_content = preg_replace("/^\s+actions: (.*?)$/m", "\tactions: [".implode(', ', $actions)."]", $module_content);

						file_put_contents($module_file_path, $module_content);
					}
				}
			}
		}

		$ret .= $this->messages[$this->config->getLang()]['MODULES_UPDATED'];

		// End
		$ret .= $this->messages[$this->config->getLang()]['END_TITLE'];

		return $ret;
	}
}
