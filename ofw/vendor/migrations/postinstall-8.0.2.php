<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Migrations;

use OsumiFramework\OFW\Core\OConfig;
use OsumiFramework\OFW\Tools\OColors;

class OPostInstall {
	private ?OColors $colors = null;
	private ?OConfig $config = null;
	private array    $messages = [
		'es' => [
			'TITLE'               => "\nPOST INSTALL 8.0.2\n\n",
			'UPDATING_COMPONENTS' => "  Actualizando componentes...\n",
			'COMPONENTS_UPDATED'  => "  Componentes actualizados.\n",
			'END_TITLE'           => "\nPOST INSTALL 8.0.2 finalizado.\n\n"
		],
		'en' => [
			'TITLE'               => "\nPOST INSTALL 8.0.2\n\n",
			'UPDATING_COMPONENTS' => "  Updating components...\n",
			'COMPONENTS_UPDATED'  => "  Components updated.\n",
			'END_TITLE'           => "\nPOST INSTALL 8.0.2 finished.\n\n"
		],
		'eu' => [
			'TITLE'               => "\nPOST INSTALL 8.0.2\n\n",
			'UPDATING_COMPONENTS' => "  Eguneratzen osagaiak...\n",
			'COMPONENTS_UPDATED'  => "  Osagaiak eguneratu dira.\n",
			'END_TITLE'           => "\nPOST INSTALL 8.0.2 bukatu du.\n\n"
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
	 * Search the given folder looking for components and update them to the new OComponent dependencies structure
	 */
	public function searchAndUpdateComponents(string $path): void {
		if ($folder = opendir($path)) {
			while (false !== ($file = readdir($folder))) {
				if ($file != '.' && $file != '..') {
					// It is a component folder if there is a file named like the folder with the '.php' extension on it
					if (is_dir($path.$file)) {
						$file_name = $path.$file.'/'.$file.'.component.php';
						if (file_exists($file_name)) {
							$component_content = file_get_contents($file_name);
							$pattern = '/^\s+private string \$depends = \'(.*?)\';$/m';
							preg_match($pattern, $component_content, $match);
							// If the component has dependencies
							if (count($match) > 0) {
								$dependencies = explode(',', $match[1]);
								for ($i = 0; $i < count($dependencies); $i++) {
									$dependencies[$i] = "'".trim($dependencies[$i])."'";
								}
								$new_dependencies = implode(', ', $dependencies);
								$new_pattern = '	public array \$depends = ['.$new_dependencies.'];';
								$component_content = preg_replace($pattern, $new_pattern, $component_content);
								
								file_put_contents($file_name, $component_content);
							}
						}
						else {
							$this->searchAndUpdateComponents($path.$file.'/');
						}
					}
				}
			}
		}
	}

	/**
	 * Runs the v8.0.2 update post-installation tasks
	 *
	 * @return string
	 */
	public function run(): string {
		$ret = '';
		// Start
		$ret .= $this->messages[$this->config->getLang()]['TITLE'];

		// Update components to the new OComponent
		$ret .= $this->messages[$this->config->getLang()]['UPDATING_COMPONENTS'];

		$this->searchAndUpdateComponents($this->config->getDir('app_component'));

		$ret .= $this->messages[$this->config->getLang()]['COMPONENTS_UPDATED'];

		// End
		$ret .= $this->messages[$this->config->getLang()]['END_TITLE'];

		return $ret;
	}
}	