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
	 * Render a template from a file or a given template with given parameters
	 *
	 * @param string $path Path to a template file
	 *
	 * @param string $html Template as a string
	 *
	 * @param array $values Key / value pair array to be rendered
	 *
	 * @return string Loaded template with rendered parameters
	 */
	public static function getTemplate(string $path, string $html, array $values): ?string  {
		if ($path!='') {
			if (file_exists($path)) {
				$html = file_get_contents($path);
			}
			else{
				return null;
			}
		}

		foreach ($values as $key => $value) {
			$html = str_ireplace('{{'.$key.'}}', $value, $html);
		}

		return $html;
	}

	/**
	 * Interprets and renders a template from a file with given parameters
	 *
	 * @param string $path Path to a template file
	 *
	 * @param array $values Key / value pair array to be rendered
	 *
	 * @return string Loaded template with rendered parameters
	 */
	public static function getPartial(string $path, array $values): ?string {
		if (file_exists($path)) {
			ob_start();
			include($path);
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
		return null;
	}

	/**
	 * Get a component's content anywhere, even in a template-less execution
	 *
	 * @param string $name Name of the component file that will be loaded
	 *
	 * @param array $values Array of information that will be loaded into the component
	 *
	 * @return string Loaded component with rendered parameters
	 */
	public static function getComponent(string $name, array $values=[]): ?string {
		global $core;
		$component_name = $name;
		if (stripos($component_name, '/')!==false) {
			$component_name = array_pop(explode('/', $component_name));
		}

		$component_file = $core->config->getDir('app_component').$name.'/'.$component_name.'.php';
		$output = self::getPartial($component_file, $values);

		if (is_null($output)) {
			$output = 'ERROR: File '.$name.' not found';
		}

		return $output;
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

		$params = [
			'mode'    => $mode,
			'version' => self::getVersion(),
			'title'   => $core->config->getDefaultTitle(),
			'message' => $res['message'],
			'res'     => $res
		];

		if ($params['title']=='') {
			$params['title'] = 'Osumi Framework';
		}
		$path = $core->config->getDir('ofw_template').'error.php';

		if ($mode=='403') { header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden'); }
		if ($mode=='404') { header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); }

		echo self::getPartial($path, $params);
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
	 * @return string SQL string to build all the tables in the database (also written to ofw/export/model.sql)
	 */
	public static function generateModel(): string  {
		global $core;
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

		$sql_file = $core->config->getDir('ofw_export').'model.sql';
		if (file_exists($sql_file)) {
			unlink($sql_file);
		}

		file_put_contents($sql_file, $sql);

		return $sql;
	}

	/**
	 * Creates or updates cache file of flattened URLs based on user configured urls.json. Also calls to generate new modules/actions/templates that are new.
	 *
	 * @param bool $silent If set to true echoes messages about the update process
	 *
	 * @return ?string Information about the update if silent is false
	 */
	public static function updateUrls(bool $silent=false): ?string {
		global $core;
		$urls = self::getModuleUrls();

		$urls_cache_file = $core->config->getDir('ofw_cache').'urls.cache.json';
		if (file_exists($urls_cache_file)) {
			unlink($urls_cache_file);
		}

		file_put_contents($urls_cache_file, json_encode($urls, JSON_UNESCAPED_UNICODE ));

		return self::updateControllers($silent);
	}

	public static function getModuleDocumentation(string $module): ?string {
		global $core;

		require_once $core->config->getDir('app_module').$module.'/'.$module.'.php';
		$class = new ReflectionClass($module);
		$class_doc = $class->getDocComment();
		if ($class_doc !== false) {
			return $class_doc;
		}

		return null;
	}

	/**
	 * Get module methods phpDoc information
	 *
	 * @param string $inspectclass Module name
	 *
	 * @return array List of items with module name, method name and associated phpDoc information
	 */
	public static function getDocumentation(string $inspectclass): array {
		$class = new ReflectionClass($inspectclass);

		$class_params = [
			'module' => $inspectclass,
			'action' => null,
			'type'   => 'html',
			'prefix' => null,
			'filter' => null,
			'doc'    => null
		];
		$class_doc = self::getModuleDocumentation($inspectclass);
		if (!is_null($class_doc)) {
			$class_params['doc'] = $class_doc;
			$class_params = self::parseAnnotations($class_params);
		}

		$methods = [];
		foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
			if ($method->class == $class->getName() && $method->name != '__construct') {
				 array_push($methods, $method->name);
			}
		}

		$arr = [];
		foreach($methods as $method) {
			$ref = new ReflectionMethod($inspectclass, $method);
			array_push($arr, [
				'module' => $class_params['module'],
				'action' => $method,
				'type'   => $class_params['type'],
				'prefix' => $class_params['prefix'],
				'filter' => $class_params['filter'],
				'doc' => $ref->getDocComment()
			]);
		}
		return $arr;
	}

	/**
	 * Get OFW annotations from a method's phpDoc information block
	 *
	 * @param array $item getDocumentation return element with name of the module, name of the method and associated phpDoc information
	 *
	 * @return array Received method information and new information gathered from the phpDoc block
	 */
	function parseAnnotations(array $item): array {
		$docs = explode("\n", $item['doc']);
		$info = [
			'module' => $item['module'],
			'action' => $item['action'],
			'type'   => $item['type'],
			'prefix' => $item['prefix'],
			'filter' => $item['filter']
		];
		foreach ($docs as $line) {
			$line = trim($line);
			if ($line!='/**' && $line!='*' && $line!='*/') {
				if (substr($line, 0, 2)=='* ') {
					$line = substr($line, 2);
				}
				if (substr($line, 0, 1)!='@') {
					$info['comment'] = $line;
				}
				else {
					$words = explode(' ', $line);
					$command = substr(array_shift($words), 1);
					$command_list = ['url', 'type', 'prefix', 'filter'];
					if (in_array($command, $command_list)) {
						$info[$command] = implode(' ', $words);
					}
				}
			}
		}

		return $info;
	}

	/**
	 * Get information from all the modules and actions to build the url cache file
	 *
	 * @return array List of every action with it's information: module, action, type, url, prefix and filter
	 */
	public static function getModuleUrls(): array {
		global $core;
		$modules = [];
		if (file_exists($core->config->getDir('app_module'))) {
			if ($model = opendir($core->config->getDir('app_module'))) {
				while (false !== ($entry = readdir($model))) {
					if ($entry != '.' && $entry != '..') {
						array_push($modules, $entry);
						require_once $core->config->getDir('app_module').$entry.'/'.$entry.'.php';
					}
				}
				closedir($model);
			}
		}

		$list = [];
		foreach ($modules as $module) {
			$methods = self::getDocumentation($module);
			foreach ($methods as $method) {
				$info = self::parseAnnotations($method);
				if (!is_null($info['prefix'])) {
					$info['url'] = $info['prefix'].$info['url'];
				}
				unset($info['prefix']);
				array_push($list, $info);
			}
		}

		return $list;
	}

	/**
	 * Creates a new empty module with the given name
	 *
	 * @param string $name Name of the new module
	 *
	 * @return array Status of the operation (status and module name)
	 */
	public static function addModule(string $name): array {
		global $core;

		$module_path      = $core->config->getDir('app_module').$name;
		$module_templates = $module_path.'/template';
		$module_file      = $module_path.'/'.$name.'.php';

		if (file_exists($module_path) || file_exists($module_file)) {
			return ['status' => 'exists', 'name' => $name];
		}
		mkdir($module_path);
		mkdir($module_templates);
		$str_module = "<"."?php declare(strict_types=1);\n";
		$str_module .= "class ".$name." extends OModule {}";
		file_put_contents($module_file, $str_module);

		return ['status' => 'ok', 'name' => $name];
	}

	/**
	 * Creates a new empty action with the given name, URL and type into the given module
	 *
	 * @param string $module Name of the module where the action should go
	 *
	 * @param string $action Name of the new action
	 *
	 * @param string $url URL of the new action
	 *
	 * @param string $type Type of the return the new action will make
	 *
	 * @return array Status of the operation (status, module name, action name, action url and action type)
	 */
	public static function addAction(string $module, string $action, string $url, string $type=null): array {
		global $core;

		$module_path      = $core->config->getDir('app_module').$module;
		$module_templates = $module_path.'/template';
		$module_file      = $module_path.'/'.$module.'.php';
		$status           = [
			'status' => 'ok',
			'module' => $module,
			'action' => $action,
			'url'    => $url,
			'type'   => $type
		];

		if (!file_exists($module_path) || !file_exists($module_file)) {
			$status['status'] = 'no-module';
			return $status;
		}
		$module_content = file_get_contents($module_file);
		if (stripos($module_content, 'function '.$action.'(')!==false) {
			$status['status'] = 'action-exists';
			return $status;
		}

		$module_type = false;
		$class_doc = self::getModuleDocumentation($module);
		if (!is_null($class_doc)) {
			$class_params = [
				'module' => $module,
				'action' => null,
				'type'   => $type,
				'prefix' => null,
				'filter' => null,
				'doc'    => $class_doc
			];
			$class_params = self::parseAnnotations($class_params);
			if (!is_null($class_params['prefix'])) {
				if (stripos($url, $class_params['prefix'])!==false) {
					$url = str_ireplace($class_params['prefix'], '', $url);
				}
			}
			if (is_null($type) && !is_null($class_params['type'])) {
				$type = $class_params['type'];
				$module_type = true;
			}
		}
		if (is_null($type)) {
			$type = 'html';
		}
		$status['type'] = $type;

		$action_template  = $module_templates.'/'.$action.'.'.$type;
		if (file_exists($action_template)) {
			$status['status'] = 'template-exists';
			return $status;
		}

		$module_content = substr($module_content, 0, -1);

		$str_action = "\n	/**\n";
		$str_action .= "	 * ".self::getMessage('TASK_ADD_ACTION_MESSAGE', [$action])."\n";
		$str_action .= "	 *\n";
		$str_action .= "	 * @url ".$url."\n";
		if (!$module_type) {
			$str_action .= "	 * @type ".$type."\n";
		}
		$str_action .= "	 * @param ORequest $"."req Request object with method, headers, parameters and filters used\n";
		$str_action .= "	 * @return void\n";
		$str_action .= "	 */\n";
		$str_action .= "	public function ".$action."(ORequest $"."req): void {}\n";
		$str_action .= "}";

		file_put_contents($module_file, $module_content.$str_action);

		$str_template = self::getMessage('TASK_ADD_ACTION_TEMPLATE', [$action]);

		file_put_contents($action_template, $str_template);

		self::updateUrls(true);

		return $status;
	}

	/**
	 * Creates a new empty service with the given name
	 *
	 * @param string $name Name of the new service
	 *
	 * @return array Status of the operation (status and service name)
	 */
	public static function addService(string $name): array {
		global $core;

		$service_file = $core->config->getDir('app_service').$name.'.php';

		if (file_exists($service_file)) {
			return ['status' => 'exists', 'name' => $name];
		}
		$str_service = "<"."?php declare(strict_types=1);\n";
		$str_service .= "class ".$name." extends OService {\n";
		$str_service .= "	function __construct() {\n";
		$str_service .= "		$"."this->loadService();\n";
		$str_service .= "	}\n";
		$str_service .= "}";
		file_put_contents($service_file, $str_service);

		return ['status' => 'ok', 'name' => $name];
	}

	/**
	 * Creates a new empty task with the given name
	 *
	 * @param string $name Name of the new task
	 *
	 * @return array Status of the operation (status and task name)
	 */
	public static function addTask(string $name): array {
		global $core;

		$task_file = $core->config->getDir('app_task').$name.'.php';
		$ofw_task_file = $core->config->getDir('ofw_task').$name.'.php';

		if (file_exists($task_file)) {
			return ['status' => 'exists', 'name' => $name];
		}
		if (file_exists($ofw_task_file)) {
			return ['status' => 'ofw-exists', 'name' => $name];
		}
		$str_task = "<"."?php declare(strict_types=1);\n";
		$str_task .= "class ".$name."Task extends OTask {\n";
		$str_task .= "	public function __toString() {\n";
		$str_task .= "		return \"".$name.": ".self::getMessage('TASK_ADD_TASK_MESSAGE', [$name])."\";\n";
		$str_task .= "	}\n\n";
		$str_task .= "	public function run(array $"."options=[]): void {}\n";
		$str_task .= "}";
		file_put_contents($task_file, $str_task);

		return ['status' => 'ok', 'name' => $name];
	}

	/**
	 * Update the controllers based on cached-flattened urls.json file. Creates the modules/controllers/templates that are configured but are not found.
	 *
	 * @param bool $silent If true doesn't give an output and performs the actions silently
	 *
	 * @return ?string Result of performed actions or null if $silent parameter is true
	 */
	public static function updateControllers(bool $silent=false): ?string {
		global $core;
		$ret = null;
		$urls   = json_decode( file_get_contents($core->config->getDir('ofw_cache').'urls.cache.json'), true);
		$errors = false;
		$all_updated = true;

		if (!$silent) {
			$colors = new OColors();
			$ret = "";
		}

		$reserved_modules = ['private', 'protected', 'public'];
		foreach ($urls as $url) {
			if (in_array($url['module'], $reserved_modules)) {
				if (!$silent) {
					$ret .= $colors->getColoredString('ERROR', 'white', 'red').": ".self::getMessage('TASK_UPDATE_URLS_RESERVED')."\n";
					foreach ($reserved_modules as $module) {
						$ret .= "  · ".$module."\n";
					}
					$errors = true;
				}
				continue;
			}

			if ($url['action']==$url['module']) {
				if (!$silent) {
					$ret .= $colors->getColoredString('ERROR', 'white', 'red').": ".self::getMessage('TASK_UPDATE_URLS_ACTION_MODULE')."\n";
					$ret .= "  ".self::getMessage('TASK_UPDATE_URLS_MODULE').": ".$url['module']."\n";
					$ret .= "  ".self::getMessage('TASK_UPDATE_URLS_ACTION').": ".$url['action']."\n";
					$errors = true;
				}
				continue;
			}

			$status = self::addModule($url['module']);
			if ($status=='ok') {
				$all_updated = false;
				if (!$silent) {
					$ret .= "    ".self::getMessage('TASK_UPDATE_URLS_NEW_MODULE', [
						$colors->getColoredString($url['module'], 'light_green'),
						$colors->getColoredString($route_module, 'light_green')
					])."\n";
					$ret .= "    ".self::getMessage('TASK_UPDATE_URLS_NEW_TEMPLATE_FOLDER', [
						$colors->getColoredString($route_templates, 'light_green')
					])."\n";
				}

			}

			$status = self::addAction($url['module'], $url['action'], $url['url'], $url['type']);
			if ($status=='ok') {
				$all_updated = false;
				if (!$silent) {
					$ret .= "    ".self::getMessage('TASK_UPDATE_URLS_NEW_ACTION', [
						$colors->getColoredString($url['action'], 'light_green'),
						$colors->getColoredString($url['module'], 'light_green')
					])."\n";
					$ret .= "    ".self::getMessage('TASK_UPDATE_URLS_NEW_TEMPLATE', [
							$colors->getColoredString($route_template, 'light_green')
						])."\n";
				}
			}
		}

		if ($errors && !$silent) {
			$ret .= "\n";
			$ret .= $colors->getColoredString('----------------------------------------------------------------------------------------------------------------------', 'white', 'red')."\n";
			$ret .= $colors->getColoredString(self::getMessage('TASK_UPDATE_URLS_ERROR'), 'white', 'red')."\n";
			$ret .= $colors->getColoredString('----------------------------------------------------------------------------------------------------------------------', 'white', 'red')."\n";
		}
		if (!$silent && $all_updated) {
			$ret .= "\n  ".self::getMessage('TASK_UPDATE_URLS_ALL_UPDATED');
		}

		return $ret;
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
		$task->loadTask();
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
	 * @param bool $return Lets the task echo or captures everything and returns it
	 *
	 * @return array Returns the status ok/error if task was run and it's return messages if $return is set to true
	 */
	public static function runOFWTask(string $task_name, array $params=[], bool $return=false): array {
		global $core;
		$ret = [
			'status' => 'ok',
			'return' => ''
		];
		$task_file = $core->config->getDir('ofw_task').$task_name.'.php';
		if (!file_exists($task_file)) {
			$ret['status'] = 'error';
			return $ret;
		}

		require_once $task_file;
		$task_name .= 'Task';
		$task = new $task_name();
		$task->loadTask();
		if (!$return) {
			$task->run($params);
		}
		else {
			ob_start();
			$task->run($params);
			$ret['return'] = ob_get_contents();
			ob_end_clean();
		}

		return $ret;
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

	/**
	 * Minify a given JSON string (based on https://github.com/t1st3/php-json-minify)
	 * @param string $json JSON string to minify
	 *
	 * @return string Minified JSON string
	 */
	public static function minifyJSON (string $json): string {
		$tokenizer = "/\"|(\/\*)|(\*\/)|(\/\/)|\n|\r/";
		$in_string = false;
		$in_multiline_comment = false;
		$in_singleline_comment = false;
		$tmp; $tmp2; $new_str = []; $ns = 0; $from = 0; $lc; $rc; $lastIndex = 0;
		while (preg_match($tokenizer, $json, $tmp, PREG_OFFSET_CAPTURE, $lastIndex)) {
			$tmp = $tmp[0];
			$lastIndex = $tmp[1] + strlen($tmp[0]);
			$lc = substr($json, 0, $lastIndex - strlen($tmp[0]));
			$rc = substr($json, $lastIndex);
			if (!$in_multiline_comment && !$in_singleline_comment) {
				$tmp2 = substr($lc, $from);
				if (!$in_string) {
					$tmp2 = preg_replace("/(\n|\r|\s)*/", "", $tmp2);
				}
				array_push($new_str, $tmp2);
			}
			$from = $lastIndex;
			if ($tmp[0] == "\"" && !$in_multiline_comment && !$in_singleline_comment) {
				preg_match("/(\\\\)*$/", $lc, $tmp2);
				if (!$in_string || !$tmp2 || (strlen($tmp2[0]) % 2) == 0) { // start of string with ", or unescaped " character found to end string
					$in_string = !$in_string;
				}
				$from--; // include " character in next catch
				$rc = substr($json, $from);
			}
			else if ($tmp[0] == "/*" && !$in_string && !$in_multiline_comment && !$in_singleline_comment) {
				$in_multiline_comment = true;
			}
			else if ($tmp[0] == "*/" && !$in_string && $in_multiline_comment && !$in_singleline_comment) {
				$in_multiline_comment = false;
			}
			else if ($tmp[0] == "//" && !$in_string && !$in_multiline_comment && !$in_singleline_comment) {
				$in_singleline_comment = true;
			}
			else if (($tmp[0] == "\n" || $tmp[0] == "\r") && !$in_string && !$in_multiline_comment && $in_singleline_comment) {
				$in_singleline_comment = false;
			}
			else if (!$in_multiline_comment && !$in_singleline_comment && !(preg_match("/\n|\r|\s/",$tmp[0]))) {
				$new_str[] = $tmp[0];
			}
		}
		if (!isset($rc)) {
			$rc = $json;
		}
		array_push($new_str, preg_replace("/(\n|\r|\s)*/" ,"", $rc));
		return implode("", $new_str);
	}
}