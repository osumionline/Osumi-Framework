<?php declare(strict_types=1);
/**
 * OTools - Utility class with auxiliary tools
 */
class OTools {
	/**
	 * Shortcut function to load a json cache file into a OCache class object
	 *
	 * @param string $key Name of the json cache file
	 *
	 * @param bool $raw Sets if the cache file has expiration date and if it has to be checked
	 *
	 * @return OCache|null Returns loaded OCache class object or null if there was an error
	 */
	public static function getCache(string $key, bool $raw=false): ?OCache {
		global $core;
		if (!is_null($core->cacheContainer->get($key))) {
			return $core->cacheContainer->get($key);
		}

		$cache = new OCache($key, $raw);
		if ($cache->getStatus()!='ok') {
			return null;
		}

		$core->cacheContainer->set($key, $cache);
		return $cache;
	}

	/**
	 * Get a string with a random number of characters (letters, numbers or special characters)
	 *
	 * @param array $options Array of options to generate the string (num -number of characters to return-, lower -include lower case letters-, upper -include upper case letters-, numbers -include numbers- and special -include special characters-)
	 *
	 * @return string Generated string based on given options
	 */
	public static function getRandomCharacters(array $options): string {
		$num     = array_key_exists('num',     $options) ? $options['num']     : 5;
		$lower   = array_key_exists('lower',   $options) ? $options['lower']   : false;
		$upper   = array_key_exists('upper',   $options) ? $options['upper']   : false;
		$numbers = array_key_exists('numbers', $options) ? $options['numbers'] : false;
		$special = array_key_exists('special', $options) ? $options['special'] : false;

		$seed = '';
		if ($lower) { $seed .= 'abcdefghijklmnopqrstuvwxyz'; }
		if ($upper) { $seed .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; }
		if ($numbers) { $seed .= '0123456789'; }
		if ($special) { $seed .= '!@#$%^&*()'; }

		$seed = str_split($seed);
		shuffle($seed);
		$rand = '';
		$list = array_rand($seed, $num);
		if (!is_array($list)){
			$list = [$list];
		}

		foreach ($list as $k) {
			$rand .= $seed[$k];
		}

		return $rand;
	}

	/**
	 * Builds an array of parameters based on URL result, received parameters and filter result (if any)
	 *
	 * @param array Array of information created with the matched URL, received parameters and filter result
	 *
	 * @return array Clean array of parameters, headers and filter result
	 */
	public static function getControllerParams(array $url_result): array {
		$ret = [
			'params' => $url_result['params'],
			'headers' => $url_result['headers']
		];
		if (array_key_exists('filter', $url_result)) {
			$ret[$url_result['filter']] = $url_result[$url_result['filter']];
		}
		return $ret;
	}

	/**
	 * Find a value from a list and return a default value if not found
	 *
	 * @param string $key Name of the parameter to find
	 *
	 * @param array $list List of parameters
	 *
	 * @param string|int|float|bool $default Default value if required key is not found
	 *
	 * @return string|int|float|bool Found value of the list or default value instead
	 */
	public static function getParam(string $key, array $list, $default=false) {
		if (array_key_exists($key, $list)) {
			return $list[$key];
		}
		else{
			return $default;
		}
	}

	/**
	 * Get a selected list of values from a list, but all are required and if anyone fail it returns false
	 *
	 * @param string[] $key_list List of parameter names
	 *
	 * @param array $list List of parameters
	 *
	 * @return string[]|bool List of values if all the keys are found or false if anyone fails
	 */
	public static function getParamList(array $key_list, array $list) {
		$params = [];
		foreach ($key_list as $key) {
			$check = self::getParam($key, $list, false);
			if (!array_key_exists($key, $list)) {
				return false;
			}
			$params[$key] = $check;
		}

		return $params;
	}

	/**
	 * Render a template from a file or a given template with given parameters
	 *
	 * @param string $path Path to a template file
	 *
	 * @param string $html Template as a string
	 *
	 * @param array $params Key / value pair array to be rendered
	 *
	 * @return string Loaded template with rendered parameters
	 */
	public static function getTemplate(string $path, string $html, array $params): string  {
		if ($path!='') {
			$html = file_get_contents($path);
		}

		foreach ($params as $param_name => $param) {
			$html = str_ireplace('{{'.$param_name.'}}', $param, $html);
		}

		return $html;
	}

	/**
	 * Get a files content as a Base64 string
	 *
	 * @param string $filename Route of the filename to be loaded
	 *
	 * @return string Content of the file as a Base64 string
	 */
	public static function fileToBase64(string $filename): ?string {
		if (file_exists($filename)) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$filebinary = (filesize($filename)>0) ? fread(fopen($filename, 'r'), filesize($filename)) : '';
			return 'data:' . finfo_file($finfo, $filename) . ';base64,' . base64_encode($filebinary);
		}
		return null;
	}

	/**
	 * Save a Base64 string back to a file
	 *
	 * @param string $base64_string Base64 string containing a file
	 *
	 * @param string $filename Route to the file to be saved
	 *
	 * @return void
	 */
	public static function base64ToFile(string $base64_string, string $filename): void {
		$ifp = fopen($filename, 'wb');
		$data = explode(',', $base64_string);
		fwrite($ifp, base64_decode($data[1]));
		fclose($ifp);
	}

	/**
	 * Encode data to Base64URL (credit to https://base64.guru/developers/php/examples/base64url)
	 *
	 * @param string $data Data to be encoded
	 *
	 * @return string Data encoded in Base64URL or null if there was an error
	 */
	public static function base64urlEncode(string $data): ?string {
		$b64 = base64_encode($data);

		// Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
		if ($b64 === false) {
			return null;
		}

		// Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
		$url = strtr($b64, '+/', '-_');

		// Remove padding character from the end of line and return the Base64URL result
		return rtrim($url, '=');
	}

	/**
	 * Decode data from Base64URL (credit to https://base64.guru/developers/php/examples/base64url)
	 *
	 * @param string $data Data to be decoded
	 *
	 * @param bool $strict Optional parameter for strict base64_decode
	 *
	 * @return bool|string Data decoded or false if there was an error
	 */
	public static function base64urlDecode(string $data, bool $strict = false) {
		// Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
		$b64 = strtr($data, '-_', '+/');

		// Decode Base64 string and return the original data
		return base64_decode($b64, $strict);
	}

	/**
	 * Parse a string with bbcode tags (i / b / u / img / url / mailto / color)
	 *
	 * @param string $str String to be parsed with bbcodes
	 *
	 * @return string String with parsed bbcodes
	 */
	public static function bbcode(string $str): string {
		$bbcode = [
			"/\<(.*?)>/is",
			"/\[i\](.*?)\[\/i\]/is",
			"/\[b\](.*?)\[\/b\]/is",
			"/\[u\](.*?)\[\/u\]/is",
			"/\[img\](.*?)\[\/img\]/is",
			"/\[url=(.*?)\](.*?)\[\/url\]/is",
			"/\[mailto=(.*?)\](.*?)\[\/mailto\]/is",
			"/\[color=(.*?)\](.*?)\[\/color\]/is"
		];
		$html = [
			"<$1>",
			"<i>$1</i>",
			"<b>$1</b>",
			"<u>$1</u>",
			"<img src=\"$1\" />",
			"<a href=\"$1\" target=\"_blank\">$2</a>",
			"<a href=\"mailto:$1\">$2</a>",
			"<span style=\"color:$1\">$2</span>"
		];
		$str = preg_replace($bbcode, $html, $str);
		return $str;
	}

	/**
	 * Show an error page instead of template (403 / 404 / 500 errors) if user hasn't defined a custom ones
	 *
	 * @param array $res Array containing information about the error
	 *
	 * @param string $mode Error mode (403 / 404 / 500 / module / action)
	 *
	 * @return void
	 */
	public static function showErrorPage(array $res, string $mode): void {
		global $core;
		if (!is_null($core->config->getErrorPage($mode))) {
			header('Location:'.$core->config->getErrorPage($mode));
			exit;
		}

		if ($mode=='403') { header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden"); }
		if ($mode=='404') { header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); }
		$version = self::getVersion();
		$title = $core->config->getDefaultTitle();
		if ($title=='') {
			$title = 'Osumi Framework';
		}
		include($core->config->getDir('ofw_core').'error.php');
		exit;
	}

	/**
	 * Get a framework specific localized message
	 *
	 * @param string $key Key code of the message
	 *
	 * @param array $params Key / value array with parameters to be rendered on the message
	 *
	 * @return string Localized message with parameters rendered
	 */
	public static function getMessage(string $key, array $params=null): string {
		global $core;
		if (is_null($core->locale)){
			include($core->config->getDir('ofw_locale').$core->config->getLang().'.php');
			$core->locale = $locale;
		}

		if (array_key_exists($key, $core->locale)){
			if (is_null($params)){
				return $core->locale[$key];
			}
			else{
				return vsprintf($core->locale[$key], $params);
			}
		}
		else{
			return null;
		}
	}

	/**
	 * Performs a curl request to an outside URL with the given method and data
	 *
	 * @param string $method Method of the request (get / post / delete)
	 *
	 * @param string $url URL to be called
	 *
	 * @param array $data Key / value array with parameters to be sent
	 *
	 * @return string Result of the curl request
	 */
	public static function curlRequest(string $method, string $url, array $data): string {
		$ch = curl_init();
		if ($method=='get') {
			$url .= '?';
			$params = [];
			foreach ($data as $key => $value) {
				array_push($params, $key.'='.$value);
			}
			$url .= implode('&', $params);
		}
		if ($method=='post') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		if ($method=='delete') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	/**
	 * Creates a slug (safe-text-string) from a given string (word or sentence)
	 *
	 * @param string $text Text to be slugified
	 *
	 * @param string $separator Character used to split words in case of a sentence is given
	 *
	 * @return string Slug of the given text
	 */
	public static function slugify(string $text, string $separator = '-'): string {
		$bad = [
			'À','à','Á','á','Â','â','Ã','ã','Ä','ä','Å','å','Ă','ă','Ą','ą',
			'Ć','ć','Č','č','Ç','ç',
			'Ď','ď','Đ','đ',
			'È','è','É','é','Ê','ê','Ë','ë','Ě','ě','Ę','ę',
			'Ğ','ğ',
			'Ì','ì','Í','í','Î','î','Ï','ï',
			'Ĺ','ĺ','Ľ','ľ','Ł','ł',
			'Ñ','ñ','Ň','ň','Ń','ń',
			'Ò','ò','Ó','ó','Ô','ô','Õ','õ','Ö','ö','Ø','ø','ő',
			'Ř','ř','Ŕ','ŕ',
			'Š','š','Ş','ş','Ś','ś',
			'Ť','ť','Ť','ť','Ţ','ţ',
			'Ù','ù','Ú','ú','Û','û','Ü','ü','Ů','ů',
			'Ÿ','ÿ','ý','Ý',
			'Ž','ž','Ź','ź','Ż','ż',
			'Þ','þ','Ð','ð','ß','Œ','œ','Æ','æ','µ',
			'”','“','‘','’',"'","\n","\r",'_','º','ª','¿'];

		$good = [
			'A','a','A','a','A','a','A','a','Ae','ae','A','a','A','a','A','a',
			'C','c','C','c','C','c',
			'D','d','D','d',
			'E','e','E','e','E','e','E','e','E','e','E','e',
			'G','g',
			'I','i','I','i','I','i','I','i',
			'L','l','L','l','L','l',
			'N','n','N','n','N','n',
			'O','o','O','o','O','o','O','o','Oe','oe','O','o','o',
			'R','r','R','r',
			'S','s','S','s','S','s',
			'T','t','T','t','T','t',
			'U','u','U','u','U','u','Ue','ue','U','u',
			'Y','y','Y','y',
			'Z','z','Z','z','Z','z',
			'TH','th','DH','dh','ss','OE','oe','AE','ae','u',
			'','','','','','','','-','','',''];

		// convert special characters
		$text = str_replace($bad, $good, $text);

		// convert special characters
		$text = utf8_decode($text);
		$text = htmlentities($text);
		$text = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde);/', '$1', $text);
		$text = html_entity_decode($text);

		$text = strtolower($text);

		// strip all non word chars
		$text = preg_replace('/\W/', ' ', $text);

		// replace all white space sections with a separator
		$text = preg_replace('/\ +/', $separator, $text);

		// trim separators
		$text = trim($text, $separator);

		return $text;
	}

	/**
	 * Returns an array of model objects (one object per model)
	 *
	 * @return array Array of model objects
	 */
	public static function getModelList(): array {
		global $core;
		$ret = [];

		if ($model = opendir($core->config->getDir('app_model'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..') {
					$table = str_ireplace('.php','',$entry);
					array_push($ret, new $table());
				}
			}
			closedir($model);
		}

		sort($ret);
		return $ret;
	}

	/**
	 * Generates a SQL file to build the database based on models defined by the user
	 *
	 * @return void Echoes SQL string to build all the tables in the database (also written to ofw/export/model.sql)
	 */
	public static function generateModel(): void  {
		global $core;
		echo self::getMessage('TASK_GENERATE_MODEL_MODEL');
		$sql = "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n\n";
		$models = self::getModelList();

		foreach ($models as $model) {
			if (method_exists($model, 'generate')) {
				$sql .= $model->generate() . "\n\n";
			}
		}
		foreach ($models as $model) {
			if (method_exists($model, 'generateRefs')) {
				$refs = $model->generateRefs();
				if ($refs!=''){
					$sql .= $refs . "\n\n";
				}
			}
		}

		$sql .= "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n";
		echo $sql;

		$sql_file = $core->config->getDir('ofw_export').'model.sql';
		if (file_exists($sql_file)) {
			unlink($sql_file);
		}

		file_put_contents($sql_file, $sql);
	}

	/**
	 * Creates or updates cache file of flattened URLs based on user configured urls.json. Also calls to generate new modules/actions/templates that are new.
	 *
	 * @param bool $silent If set to true echoes messages about the update process
	 *
	 * @return void
	 */
	public static function updateUrls(bool $silent=false): void {
		global $core;
		$urls_file = json_decode( file_get_contents($core->config->getDir('app_config').'urls.json'), true);
		$urls = self::getUrlList($urls_file);

		$urls_cache_file = $core->config->getDir('app_cache').'urls.cache.json';
		if (file_exists($urls_cache_file)) {
			unlink($urls_cache_file);
		}

		file_put_contents($urls_cache_file, json_encode($urls, JSON_UNESCAPED_UNICODE ));

		self::updateControllers($silent);
	}

	/**
	 * Returns flattened list of URLs from the user configured urls.json
	 *
	 * @param array $item $item of the URL list
	 *
	 * @return array Array of configured urls
	 */
	public static function getUrlList(array $item): array {
		$list = self::getUrls($item);
		for ($i=0; $i<count($list); $i++){
			$keys = array_keys($list[$i]);
			foreach ($keys as $key) {
				if (is_null($list[$i][$key])) {
					unset($list[$i][$key]);
				}
			}
		}
		return $list;
	}

	/**
	 * Returns list of URLs of a given element, used in conjunction with getUrlList
	 *
	 * @param array $item Item of the urls.json that can have many "sub-URLs"
	 *
	 * @param array Flattened array of a single urls.json element
	 */
	public static function getUrls(array $item): array {
		$list = [];
		if (array_key_exists('urls', $item)) {
			foreach ($item['urls'] as $elem) {
				$list = array_merge($list, self::getUrls($elem));
			}
			for ($i=0;$i<count($list);$i++) {
				$list[$i]['url']    = ((array_key_exists('prefix', $item) && !is_null($item['prefix'])) ? $item['prefix'] : '') . $list[$i]['url'];
				$list[$i]['layout'] = (array_key_exists('layout', $list[$i])  && !is_null($list[$i]['layout'])) ? $list[$i]['layout'] : ( (array_key_exists('layout', $item) && !is_null($item['layout'])) ? $item['layout'] : null);
				$list[$i]['module'] = (array_key_exists('module', $list[$i])  && !is_null($list[$i]['module'])) ? $list[$i]['module'] : ( (array_key_exists('module', $item) && !is_null($item['module'])) ? $item['module'] : null);
				$list[$i]['filter'] = (array_key_exists('filter', $list[$i])  && !is_null($list[$i]['filter'])) ? $list[$i]['filter'] : ( (array_key_exists('filter', $item) && !is_null($item['filter'])) ? $item['filter'] : null);
				$list[$i]['type']   = (array_key_exists('type',   $list[$i])  && !is_null($list[$i]['type']))   ? $list[$i]['type']   : ( (array_key_exists('type',   $item) && !is_null($item['type']))   ? $item['type']   : null);
			}
		}
		else {
			array_push($list, $item);
		}
		return $list;
	}

	/**
	 * Update the controllers based on cached-flattened urls.json file. Creates the modules/controllers/templates that are configured but are not found.
	 *
	 * @param bool $silent If true doesn't give an output and performs the actions silently
	 *
	 * @return void Echoes result of performed actions or void if $silent parameter is true
	 */
	public static function updateControllers(bool $silent=false): void {
		global $core;
		$colors = new OColors();
		$urls   = json_decode( file_get_contents($core->config->getDir('app_cache').'urls.cache.json'), true);
		$errors = false;

		if (!$silent) {
			echo "\n";
			echo "  ".$colors->getColoredString('Osumi Framework', 'white', 'blue')."\n\n";
			echo self::getMessage('TASK_UPDATE_URLS_UPDATING');
		}

		$reserved_modules = ['private', 'protected', 'public'];
		foreach ($urls as $url) {
			if (in_array($url['module'], $reserved_modules)) {
				if (!$silent) {
					echo $colors->getColoredString('ERROR', 'white', 'red').": ".self::getMessage('TASK_UPDATE_URLS_RESERVED')."\n";
					foreach ($reserved_modules as $module) {
						echo "  · ".$module."\n";
					}
					$errors = true;
				}
				continue;
			}

			if ($url['action']==$url['module']) {
				if (!$silent) {
					echo $colors->getColoredString('ERROR', 'white', 'red').": ".self::getMessage('TASK_UPDATE_URLS_ACTION_MODULE')."\n";
					echo "  Módulo: ".$url['module']."\n";
					echo "  Acción: ".$url['action']."\n";
					$errors = true;
				}
				continue;
			}

			$route_controller = $core->config->getDir('app_controller') . $url['module'] . '.php';
			if (!file_exists($route_controller)) {
				file_put_contents($route_controller, "<"."?php declare(strict_types=1);\n\nclass ".$url['module']." extends OController {\n}");
				if (!$silent) {
					echo self::getMessage('TASK_UPDATE_URLS_NEW_CONTROLLER', [
						$colors->getColoredString("\"" . $url['module'] . "\"", "light_green"),
						$colors->getColoredString("\"" . $route_controller . "\"", "light_green")
					]);
				}
			}

			$route_templates = $core->config->getDir('app_template') . $url['module'];
			if (!file_exists($route_templates) && !is_dir($route_templates)) {
				mkdir($route_templates);
				if (!$silent) {
					echo self::getMessage('TASK_UPDATE_URLS_NEW_TEMPLATE_FOLDER', [
						$colors->getColoredString("\"" . $route_templates . "\"", "light_green")
					]);
				}
			}

			$controller_str = file_get_contents($route_controller);
			if (stripos($controller_str, "function ".$url['action']) === false) {
				file_put_contents($route_controller, substr_replace($controller_str, '', strrpos($controller_str, '}'), 1));

				$str = "\n";
				$str .= "	/**\n";
				$str .= "	 * ".$url['comment']."\n";
				$str .= "	 *\n";
				$str .= "	 * @return void\n";
				$str .= "	 */\n";
				$str .= "	function ".$url['action']."(array $"."req): void {}\n";
				file_put_contents($route_controller, $str."}", FILE_APPEND);

				if (!$silent) {
					echo self::getMessage('TASK_UPDATE_URLS_NEW_ACTION', [
						$colors->getColoredString("\"" . $url['action'] . "\"", "light_green"),
						$colors->getColoredString("\"" . $url['module'] . "\"", "light_green")
					]);
				}

				$route_template = $core->config->getDir('app_template') . $url['module'] . '/' . $url['action'] . '.php';
				if (!file_exists($route_template)) {
					file_put_contents($route_template, '');
					if (!$silent) {
						echo self::getMessage('TASK_UPDATE_URLS_NEW_TEMPLATE', [
							$colors->getColoredString("\"" . $route_template . "\"", "light_green")
						]);
					}
				}
			}
		}

		if ($errors && !$silent) {
			echo "\n";
			echo $colors->getColoredString("----------------------------------------------------------------------------------------------------------------------", "white", "red")."\n";
			echo $colors->getColoredString(self::getMessage('TASK_UPDATE_URLS_ERROR'), "white", "red")."\n";
			echo $colors->getColoredString("----------------------------------------------------------------------------------------------------------------------", "white", "red")."\n";
		}
		if (!$silent) {
			echo "\n";
		}
	}

	/**
	 * Run a user defined task (app/task)
	 *
	 * @param string $task_name Name of the task
	 *
	 * @param array $params Array of parameters passed to the task
	 *
	 * @return bool Returns true after the task is complete or false if task file doesn't exist
	 */
	public static function runTask(string $task_name, array $params=[]): bool {
		global $core;
		$task_file = $core->config->getDir('app_task').$task_name.'.php';
		if (!file_exists($task_file)) {
			return false;
		}

		require_once $task_file;
		$task_name .= 'Task';
		$task = new $task_name();
		$task->run($params);

		return true;
	}

	/**
	 * Run a Framework specific task (ofw/task)
	 *
	 * @param string $task_name Name of the task
	 *
	 * @param array $params Array of parameters passed to the task
	 *
	 * @return bool Returns true after the task is complete or false if task file doesn't exist
	 */
	public static function runOFWTask(string $task_name, array $params=[]): bool {
		global $core;
		$task_file = $core->config->getDir('ofw_task').$task_name.'.php';
		if (!file_exists($task_file)) {
			return false;
		}

		require_once $task_file;
		$task_name .= 'Task';
		$task = new $task_name();
		$task->run($params);

		return true;
	}

	/**
	 * Return version number of the Framework
	 *
	 * @return string Version number of the Framework (eg 5.0.0)
	 */
	public static function getVersion(): string {
		global $core;
		$version_file = $core->config->getDir('ofw_core').'version.json';
		$version = json_decode( file_get_contents($version_file), true );
		return $version['version'];
	}

	/**
	 * Returns current versions information message
	 *
	 * @return string Current versions information message
	 */
	public static function getVersionInformation(): string {
		global $core;
		$version_file = $core->config->getDir('ofw_core').'version.json';
		$version = json_decode( file_get_contents($version_file), true );

		$current_version = $version['version'];
		return $version['updates'][$current_version]['message'];
	}
}