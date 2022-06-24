<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Migrations;

use OsumiFramework\OFW\Core\OConfig;
use OsumiFramework\OFW\Tools\OColors;

class OPostInstall {
	private ?OColors $colors = null;
	private ?OConfig $config = null;
	private array    $messages = [
		'es' => [
			'TITLE'                   => "\nPOST INSTALL 8.0.4\n\n",
			'UPDATING_ACTION_FILTERS' => "  Actualizando acciones con filtros...\n",
      'MODULE_PROCESSING'       => "\n    Procesando módulo \"%s\"...\n",
      'ACTION_UPDATED'          => "      Acción \"%s\" actualizada.\n",
			'ACTION_FILTERS_UPDATED'  => "\n  Acciones con filtros actualizados.\n",
      'URL_CACHE_DELETE'        => "  Archivo cache de URLs borrado: \"%s\"\n",
			'END_TITLE'               => "\nPOST INSTALL 8.0.4 finalizado.\n\n"
		],
		'en' => [
			'TITLE'                   => "\nPOST INSTALL 8.0.4\n\n",
			'UPDATING_ACTION_FILTERS' => "  Updating actions with filters...\n",
      'MODULE_PROCESSING'       => "\n    Updating \"%s\" module...\n",
      'ACTION_UPDATED'          => "      \"%s\" action updated.\n",
			'ACTION_FILTERS_UPDATED'  => "\n  Action with filters updated.\n",
      'URL_CACHE_DELETE'        => "  URL cache file deleted: \"%s\"\n",
			'END_TITLE'               => "\nPOST INSTALL 8.0.4 finished.\n\n"
		],
		'eu' => [
			'TITLE'                   => "\nPOST INSTALL 8.0.4\n\n",
			'UPDATING_ACTION_FILTERS' => "  Eguneratzen iragazkiak dituzten ekintzak...\n",
      'MODULE_PROCESSING'       => "\n    Eguneratzen \"%s\" modulua...\n",
      'ACTION_UPDATED'          => "      \"%s\" ekintza eguneratu da.\n",
			'ACTION_FILTERS_UPDATED'  => "\n  Iragazkiak dituzten ekintzak eguneratu dira.\n",
      'URL_CACHE_DELETE'        => "  URLen cache-fitxategia ezabatu da: \"%s\"\n",
			'END_TITLE'               => "\nPOST INSTALL 8.0.4 bukatu du.\n\n"
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
   * Function to update a module's actions
   *
   * @param string $module_actions_path Path to a module's actions folder
   *
   * @return string Result messages
   */
  private function updateModuleActions(string $module_actions_path): string {
    $ret = sprintf($this->messages[$this->config->getLang()]['MODULE_PROCESSING'],
      $this->colors->getColoredString($module_actions_path, 'light_green')
    );

    if ($folder = opendir($module_actions_path)) {
      while (false !== ($file = readdir($folder))) {
        if ($file != '.' && $file != '..') {
          $action_file = $module_actions_path.'/'.$file.'/'.$file.'.action.php';
          $action_content = file_get_contents($action_file);
          preg_match("/\s+filter: '(.*?',*)$/m", $action_content, $match);
          if (!is_null($match) && count($match) > 1) {
            $ret .= sprintf($this->messages[$this->config->getLang()]['ACTION_UPDATED'],
              $this->colors->getColoredString($file, 'light_green')
            );
            $has_comma = (stripos($match[1], ',') !== false);
            $filter = str_ireplace(',', '', $match[1]);
            $action_content = preg_replace("/\s+filter: '(.*?',*)$/m", "\n\tfilters: ['".$filter."]".($has_comma ? ',' : ''), $action_content);
            file_put_contents($action_file, $action_content);
          }
        }
      }
    }

    return $ret;
  }

	/**
	 * Runs the v8.0.4 update post-installation tasks
	 *
	 * @return string
	 */
	public function run(): string {
		$ret = '';
		// Start
		$ret .= $this->messages[$this->config->getLang()]['TITLE'];

		// Update actions with filters
		$ret .= $this->messages[$this->config->getLang()]['UPDATING_ACTION_FILTERS'];

		$module_path = $this->config->getDir('app_module');
		if (file_exists($module_path)) {
			if ($folder = opendir($module_path)) {
				// Every folder on app_module is a module
				while (false !== ($file = readdir($folder))) {
					if ($file != '.' && $file != '..') {
						$module_actions_path = $module_path.$file."/actions/";
            $ret .= $this->updateModuleActions($module_actions_path);
					}
				}
			}
		}

		$ret .= $this->messages[$this->config->getLang()]['ACTION_FILTERS_UPDATED'];

    // Delete the URL cache file
		$url_cache_file = $this->config->getDir('ofw_cache').'urls.cache.json';
		if (file_exists($url_cache_file)) {
			unlink($url_cache_file);
			$ret .= sprintf($this->messages[$this->config->getLang()]['URL_CACHE_DELETE'],
				$this->colors->getColoredString($url_cache_file, 'light_green')
			);
		}

		// End
		$ret .= $this->messages[$this->config->getLang()]['END_TITLE'];

		return $ret;
	}
}
