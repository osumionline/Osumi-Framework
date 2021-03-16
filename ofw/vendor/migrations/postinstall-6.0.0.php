<?php declare(strict_types=1);
class OPostInstall {
	private ?OColors $colors = null;
	private ?OConfig $config = null;
	private array    $messages = [
		'es' => [
					'TITLE'             => "\nPOST INSTALL 6.0.0\n\n",
					'MOVE_CACHE_FOLDER' => "  Carpeta cache reubicada: \"%s\" -> \"%s\".\n",
					'UPDATE_ACTIONS'    => "  Actualizando acciones.\n",
					'UPDATING_MODULE'   => "\n    Actualizando módulo: \"%s\".\n",
					'UPDATING_ACTION'   => "      Actualizando acción: \"%s\".\n",
					'DELETE_URLS'       => "\n  Archivo urls.json borrado.\n",
					'DELETE_URLS_CACHE' => "  Archivo cache de URLs borrado.\n",
					'END_TITLE'         => "\nPOST INSTALL 6.0.0 finalizado.\n\n"
				],
		'en' => [
					'TITLE'             => "\n\nPOST INSTALL 6.0.0\n\n",
					'MOVE_CACHE_FOLDER' => "  Cache folder moved: \"%s\" -> \"%s\".\n",
					'UPDATE_ACTIONS'    => "  Updating actions.\n",
					'UPDATING_MODULE'   => "\n    Updating module: \"%s\".\n",
					'UPDATING_ACTION'   => "      Updating action: \"%s\".\n",
					'DELETE_URLS'       => "\n  urls.json file deleted.\n",
					'DELETE_URLS_CACHE' => "  URLs cache file deleted.\n",
					'END_TITLE'         => "\nPOST INSTALL 6.0.0 finished.\n\n"
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
	 * Generate documentation block for a module
	 *
	 * @param array $options List of options needed in the documentation block
	 *
	 * @return string Properly formatted documentation block
	 */
	private function generateModuleDoc(array $options): string {
		unset($options['module']);
		$str = "/**\n";
		foreach ($options as $key => $value) {
			$str .= " * @".$key." ".$value."\n";
		}
		$str .= "*/\n";
		return $str;
	}

	/**
	 * Generate documentation block for an action
	 *
	 * @param array $options List of options needed in the documentation block
	 *
	 * @return string Properly formatted documentation block
	 */
	private function generateActionDoc(array $options): string {
		$str = "/**\n";
		$str .= "	 * ".$options['comment']."\n";
		unset($options['comment']);
		$str .= "	 *\n";
		foreach ($options as $key => $value) {
			$str .= "	 * @".$key." ".$value."\n";
		}
		$str .= "	 * @param ORequest $"."req Request object with method, headers, parameters and filters used\n";
		$str .= "	 * @return void\n";
		$str .= "	 */\n";
		return $str;
	}

	private function processUrl(array $url): string {
		$ret = '';
		$ret .= sprintf($this->messages[$this->config->getLang()]['UPDATING_MODULE'],
			$this->colors->getColoredString($url['module'], 'light_green')
		);
		$module_file = $this->config->getDir('app_module').$url['module'].'/'.$url['module'].'.php';
		$module_content = file_get_contents($module_file);
		$module_options = [];

		foreach ($url as $option => $value) {
			if ($option!='id' && $option!='urls' && $option!='module') {
				$module_options[$option] = $value;
			}
		}

		if (count($module_options)>0) {
			$docblock = $this->generateModuleDoc($module_options);
			$ind = stripos($module_content, 'class '.$url['module']);
			$module_content = substr($module_content, 0, $ind) . $docblock . substr($module_content, $ind);
		}

		foreach ($url['urls'] as $action_options) {
			$action = $action_options['action'];
			$ret .= sprintf($this->messages[$this->config->getLang()]['UPDATING_ACTION'],
				$this->colors->getColoredString($action, 'light_green')
			);
			unset($action_options['id']);
			unset($action_options['action']);
			$docblock = $this->generateActionDoc($action_options);
			$ind = stripos($module_content, 'public function '.$action.'(') -1;
			$partial_content = substr($module_content, 0, $ind);
			$doc_ind = strripos($partial_content, "/**");
			$module_content = substr($module_content, 0, $doc_ind) . $docblock . substr($module_content, $ind);
		}

		file_put_contents($module_file, $module_content);

		return $ret;
	}

	/**
	 * Runs the v6.0.0 update post-installation tasks
	 *
	 * @return string
	 */
	public function run(): string {
		$ret = '';
		$ret .= $this->messages[$this->config->getLang()]['TITLE'];

		// Move cache folder from app to ofw
		$source = $this->config->getDir('app').'cache';
		$destination = $this->config->getDir('base').'ofw/cache';
		rename($source, $destination);
		$ret .= sprintf($this->messages[$this->config->getLang()]['MOVE_CACHE_FOLDER'],
			$this->colors->getColoredString($source, 'light_green'),
			$this->colors->getColoredString($destination, 'light_green')
		);

		// Get all URLs
		$ret .= $this->messages[$this->config->getLang()]['UPDATE_ACTIONS'];
		$urls_file = $this->config->getDir('app_config').'urls.json';
		$urls = json_decode( file_get_contents($urls_file), true);

		// Update each actions phpDoc block
		foreach ($urls['urls'] as $url) {
			$ret .= $this->processUrl($url);
		}

		// Delete urls.json file
		$ret .= $this->messages[$this->config->getLang()]['DELETE_URLS'];
		unlink($urls_file);

		// Delete cached urls file
		$ret .= $this->messages[$this->config->getLang()]['DELETE_URLS_CACHE'];
		$urls_cache_file = $destination.'/urls.cache.json';
		if (file_exists($urls_cache_file)) {
			unlink($urls_cache_file);
		}

		$ret .= $this->messages[$this->config->getLang()]['END_TITLE'];

		return $ret;
	}
}