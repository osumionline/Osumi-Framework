<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Tools;

use \ReflectionClass;
use \ReflectionMethod;
use \ReflectionObject;
use OsumiFramework\OFW\Cache\OCache;
use OsumiFramework\OFW\DB\OModel;
use OsumiFramework\OFW\Routing\ORoute;
use OsumiFramework\OFW\Routing\OModule;
use OsumiFramework\OFW\Routing\OModuleAction;

/**
 * OTools - Utility class with auxiliary tools
 */
class OTools {
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
	 * Function to get a model object's JSON representstion
	 *
	 * @param any $obj Model object
	 *
	 * @param array $exclude List of fields to be excluded
	 *
	 * @param array $empty List of fields to be returned empty
	 *
	 * @return string JSON string representation of the object or null if given object was null or not a model object
	 */
	public static function getModelComponent($obj, array $exclude=[], array $empty=[]): string {
		return (!is_null($obj) && method_exists($obj, 'generate')) ? $obj->generate('json', $exclude, $empty) : 'null';
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
			'message' => array_key_exists('message', $res) ? $res['message'] : '',
			'res'     => $res
		];

		if ($params['title']=='') {
			$params['title'] = 'Osumi Framework';
		}
		$path = $core->config->getDir('ofw_template').'error.php';

		if ($mode=='403') { header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden'); }
		if ($mode=='404') { header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); }
		if ($mode=='500') { header($_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error'); }

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

		$translation = $core->translate->getTranslation($key);
		if (is_null($translation)) {
			return null;
		}

		$translation = str_ireplace("\\n", "\n", $translation);

		if (is_null($params)){
			return $translation;
		}
		else{
			return vsprintf($translation, $params);
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
	 * @return string|false Result of the curl request or false if the execution failed
	 */
	public static function curlRequest(string $method, string $url, array $data): string|false {
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

		// Convert special characters
		$text = str_replace($bad, $good, $text);

		// Convert special characters
		$text = utf8_decode($text);
		$text = htmlentities($text);
		$text = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde);/', '$1', $text);
		$text = html_entity_decode($text);

		$text = strtolower($text);

		// Strip all non word chars
		$text = preg_replace('/\W/', ' ', $text);

		// Replace all white space sections with a separator
		$text = preg_replace('/\ +/', $separator, $text);

		// Trim separators
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
					$table = "\\OsumiFramework\\App\\Model\\".self::underscoresToCamelCase(str_ireplace('.model.php','',$entry), true);
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
	 * Creates or updates cache file of flattened URLs based on user configured module routes. Also calls to generate new modules/actions/templates that are new.
	 *
	 * @param bool $silent If set to true echoes messages about the update process
	 *
	 * @return ?string Information about the update if silent is false
	 */
	public static function updateUrls(bool $silent=false): ?string {
		global $core;
		$urls = self::getModuleUrls();

		$urls_cache_file = $core->cacheContainer->getItem('urls');
		$urls_cache_file->set(json_encode($urls, JSON_UNESCAPED_UNICODE));
		$urls_cache_file->save();

		return self::updateControllers($silent);
	}

	/**
	 * Get the attribute class from a module or an action.
	 *
	 * @param $class Class from which information will be taken
	 *
	 * @return OModule | OModuleAction Attribute class obtained from the class
	 */
	public static function getClassAttributes($class): OModule | OModuleAction {
		$reflector = new ReflectionClass($class::class);
		foreach ($reflector->getAttributes() as $attr) {
			$attributes = $attr->newInstance();
		}
		return $attributes;
	}

	/**
	 * Get module method's phpDoc information
	 *
	 * @param string $inspectclass Module name
	 *
	 * @return array List of items with module name, method name and associated phpDoc information
	 */
	public static function getDocumentation(string $inspectclass): array {
		global $core;
		$module_path = $core->config->getDir('app_module').$inspectclass.'/'.$inspectclass.'.module.php';
		require_once $module_path;
		$module_name = "\\OsumiFramework\\App\Module\\".$inspectclass."Module";
		$module = new $module_name;
		$module_attributes = self::getClassAttributes($module);

		$class_params = [
			'module'  => $inspectclass,
			'type'    => !is_null($module_attributes->getType()) ? $module_attributes->getType() : 'html',
			'prefix'  => !is_null($module_attributes->getPrefix()) ? $module_attributes->getPrefix() : null
		];
		$actions = $module_attributes->getActions();

		$arr = [];
		foreach($actions as $action_name) {
			$action_path = $core->config->getDir('app_module').$inspectclass.'/actions/'.$action_name.'/'.$action_name.'.action.php';
			require_once $action_path;
			$action_class_name = "\\OsumiFramework\\App\\Module\\Action\\".$action_name.'Action';
			$action = new $action_class_name;
			$action_attributes = self::getClassAttributes($action);

			$action_params = [
				'module'  => $class_params['module'],
				'action'  => $action_name,
				'type'    => (!is_null($action_attributes->getType())) ? $action_attributes->getType() : $class_params['type'],
				'prefix'  => $class_params['prefix'],
				'filters' => $action_attributes->getFilters(),
				'url'     => $action_attributes->getUrl(),
				'layout'  => $action_attributes->getLayout(),
				'utils'   => $action_attributes->getUtils()
			];
			array_push($arr, $action_params);
		}

		return $arr;
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
						require_once $core->config->getDir('app_module').$entry.'/'.$entry.'.module.php';
					}
				}
				closedir($model);
			}
		}

		$list = [];
		foreach ($modules as $module) {
			$actions = self::getDocumentation($module);
			foreach ($actions as $action) {
				if (!is_null($action['prefix'])) {
					$action['url'] = $action['prefix'].$action['url'];
				}
				unset($action['prefix']);
				array_push($list, $action);
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

		$module_path    = $core->config->getDir('app_module').$name;
		$module_actions = $module_path.'/actions';
		$module_file    = $module_path.'/'.$name.'.module.php';

		if (file_exists($module_path) || file_exists($module_file)) {
			return ['status' => 'exists', 'name' => $name];
		}
		mkdir($module_path);
		mkdir($module_actions);
		$str_module = "<"."?php declare(strict_types=1);\n\n";
		$str_module .= "namespace OsumiFramework\App\Module;\n\n";
		$str_module .= "use OsumiFramework\OFW\Routing\OModule;\n\n";
		$str_module .= "/**\n";
		$str_module .= " * New ".$name." module\n";
		$str_module .= " */\n";
		$str_module .= "#[OModule(\n";
		$str_module .= "	type: 'html',\n";
		$str_module .= "	actions: []\n";
		$str_module .= ")]\n";
		$str_module .= "class ".$name."Module {}";
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
	 * @param string $layout Layout of the new action
	 *
	 * @param string $utils "utils" folder's classes to be loaded into the method (comma separated values)
	 *
	 * @return array Status of the operation (status, module name, action name, action url and action type)
	 */
	public static function addAction(string $module, string $action, string $url, string $type=null, string $layout=null, string $utils=null): array {
		global $core;

		$module_path    = $core->config->getDir('app_module').$module;
		$module_actions = $module_path.'/actions';
		$module_file    = $module_path.'/'.$module.'.module.php';
		$status         = [
			'status' => 'ok',
			'module' => $module,
			'action' => $action,
			'url'    => $url,
			'type'   => $type,
			'layout' => $layout,
			'utils'  => $utils
		];

		if (!file_exists($module_path) || !file_exists($module_file)) {
			$status['status'] = 'no-module';
			return $status;
		}
		$module_content = file_get_contents($module_file);
		if (preg_match("/^\s+actions: \[(.*?)".$action."(.*?)\],?$/", $module_content) == 1) {
			$status['status'] = 'action-exists';
			return $status;
		}

		$module_type = false;
		require_once $module_file;

		$module_name = "\\OsumiFramework\\App\\Module\\".$module.'Module';
		$module_class = new $module_name;
		$module_attributes = self::getClassAttributes($module_class);

		$class_params = [
			'module' => $module,
			'action' => null,
			'type'   => $type,
			'prefix' => null,
			'filter' => null,
			'layout' => null,
			'utils'  => null
		];
		if (!is_null($module_attributes->getPrefix())) {
			if (stripos($url, $module_attributes->getPrefix())!==false) {
				$url = str_ireplace($module_attributes->getPrefix(), '', $url);
			}
		}
		if (is_null($type) && !is_null($module_attributes->getType())) {
			$type = $class_params['type'];
			$module_type = true;
		}
		if (is_null($type)) {
			$type = 'html';
		}
		$status['type'] = $type;
		if (is_null($layout)) {
			$layout = 'default';
		}
		$status['layout'] = $layout;
		$status['utils']  = $utils;

		$action_folder = $module_actions.'/'.$action;
		if (file_exists($action_folder)) {
			$status['status'] = 'action-exists';
			return $status;
		}
		$action_file   = $action_folder.'/'.$action.'.action.php';
		if (file_exists($action_file)) {
			$status['status'] = 'action-exists';
			return $status;
		}
		$action_template  = $action_folder.'/'.$action.'.action.'.$type;
		if (file_exists($action_template)) {
			$status['status'] = 'template-exists';
			return $status;
		}

		// Add action to module
		if (stripos($module_content, "actions: []") !== false) {
			$module_content = preg_replace("/actions: \[\]/i", "actions: ['".$action."']", $module_content);
		}
		else {
			preg_match("/actions: \[(.*?)\]/m", $module_content, $match);
			$actions = explode(',', $match[1]);
			for ($i = 0; $i < count($actions); $i++) {
				$actions[$i] = trim($actions[$i]);
			}
			array_push($actions, "'".$action."'");
			$module_content = preg_replace("/actions: \[(.*?)\]/i", "actions: [".implode(', ', $actions)."]", $module_content);
		}

		// Create action's folder
		mkdir($action_folder);

		// New action's content
		$str_template = self::getMessage('TASK_ADD_ACTION_TEMPLATE', [$action]);

		$action_content = "<"."?php declare(strict_types=1);\n\n";
		$action_content .= "namespace OsumiFramework\App\Module\Action;\n\n";
		$action_content .= "use OsumiFramework\OFW\Routing\OModuleAction;\n";
		$action_content .= "use OsumiFramework\OFW\Routing\OAction;\n";
		$action_content .= "use OsumiFramework\OFW\Web\ORequest;\n\n";
		$action_content .= "#[OModuleAction(\n";
		$action_content .= "	url: '".$url."'";
		if (!$module_type) {
			$action_content .= ",\n	type: '".$type."'";
		}
		if (!is_null($layout) && $layout != 'default') {
			$action_content .= ",\n	layout: '".$layout."'";
		}
		if (!is_null($utils)) {
			$action_content .= ",\n	utils: ['".$utils."']";
		}
		$action_content .= "\n)]\n";
		$action_content .= "class ".$action."Action extends OAction {\n";
		$action_content .= "	/**\n";
		$action_content .= "	 * ".$str_template."\n";
		$action_content .= "	 *\n";
		$action_content .= "	 * @param ORequest $"."req Request object with method, headers, parameters and filters used\n";
		$action_content .= "	 * @return void\n";
		$action_content .= "	 */\n";
		$action_content .= "	public function run(ORequest $"."req):void {}\n";
		$action_content .= "}";

		file_put_contents($module_file,     $module_content);
		file_put_contents($action_file,     $action_content);
		file_put_contents($action_template, $str_template);

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

		$service_file = $core->config->getDir('app_service').$name.'.service.php';

		if (file_exists($service_file)) {
			return ['status' => 'exists', 'name' => $name];
		}
		$str_service = "<"."?php declare(strict_types=1);\n\n";
		$str_service .= "namespace OsumiFramework\App\Service;\n\n";
		$str_service .= "use OsumiFramework\OFW\Core\OService;\n\n";
		$str_service .= "class ".$name."Service extends OService {\n";
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

		$task_file = $core->config->getDir('app_task').$name.'.task.php';
		$ofw_task_file = $core->config->getDir('ofw_task').$name.'.task.php';

		if (file_exists($task_file)) {
			return ['status' => 'exists', 'name' => $name];
		}
		if (file_exists($ofw_task_file)) {
			return ['status' => 'ofw-exists', 'name' => $name];
		}

		$str_message = str_ireplace('"', '\"', self::getMessage('TASK_ADD_TASK_MESSAGE', [$name]));

		$str_task = "<"."?php declare(strict_types=1);\n\n";
		$str_task .= "namespace OsumiFramework\App\Task;\n\n";
		$str_task .= "use OsumiFramework\OFW\Core\OTask;\n\n";
		$str_task .= "class ".$name."Task extends OTask {\n";
		$str_task .= "	public function __toString() {\n";
		$str_task .= "		return \"".$name.": ".$str_message."\";\n";
		$str_task .= "	}\n\n";
		$str_task .= "	public function run(array $"."options=[]): void {}\n";
		$str_task .= "}";
		file_put_contents($task_file, $str_task);

		return ['status' => 'ok', 'name' => $name];
	}

	/**
	 * Creates a model component file and a component for lists of such model
	 *
	 * @param array $values Information about the files that have to be created
	 *
	 * @return string Status of the operation
	 */
	public static function addModelComponent(array $values): string {
		if (file_exists($values['list_folder'])) {
			return 'list-folder-exists';
		}
		if (file_exists($values['list_folder'].$values['list_file'])) {
			return 'list-file-exists';
		}
		if (file_exists($values['list_folder'].$values['list_template_file'])) {
			return 'list-file-exists';
		}
		if (file_exists($values['component_folder'])) {
			return 'component-folder-exists';
		}
		if (file_exists($values['component_folder'].$values['component_file'])) {
			return 'component-file-exists';
		}
		if (file_exists($values['component_folder'].$values['component_template_file'])) {
			return 'component-file-exists';
		}
		if (!mkdir($values['list_folder'], 0755, true)) {
			return 'list-folder-cant-create';
		}
		if (!mkdir($values['component_folder'], 0755, true)) {
			return 'component-folder-cant-create';
		}

		$text_fields      = [OMODEL_PK_STR, OMODEL_TEXT, OMODEL_LONGTEXT];
		$urlencode_fields = [OMODEL_TEXT, OMODEL_LONGTEXT];
		$date_fields      = [OMODEL_CREATED, OMODEL_UPDATED, OMODEL_DATE];
		$cont             = 0;

		$component_name = self::underscoresToCamelCase($values['model_name'], true).'Component';

		$list_component_content = "<"."?php declare(strict_types=1);\n\n";
		$list_component_content .= "namespace OsumiFramework\App\Component\Model;\n\n";
		$list_component_content .= "use OsumiFramework\OFW\Core\OComponent;\n\n";
		$list_component_content .= "class ".$values['list_name']." extends OComponent {}";

		$list_template_content = "<"."?php\n";
		$list_template_content .= "use OsumiFramework\\App\\Component\\Model\\".$component_name.";\n\n";
		$list_template_content .= "foreach ($"."values['list'] as $"."i => $".strtolower($values['model_name']).") {\n";
		$list_template_content .= "  $"."component = new ".$values['component_name']."([ '".strtolower($values['model_name'])."' => $".strtolower($values['model_name'])." ]);\n";
		$list_template_content .= "	echo strval($"."component);\n";
		$list_template_content .= "	if ($"."i<count($"."values['list'])-1) {\n";
		$list_template_content .= "		echo \",\\n\";\n";
		$list_template_content .= "	}\n";
		$list_template_content .= "}\n";

		if (file_put_contents($values['list_folder'].$values['list_file'], $list_component_content)===false) {
			return 'list-file-cant-create';
		}
		if (file_put_contents($values['list_folder'].$values['list_template_file'], $list_template_content)===false) {
			return 'list-file-cant-create';
		}

		$component_content = "<"."?php declare(strict_types=1);\n\n";
		$component_content .= "namespace OsumiFramework\App\Component\Model;\n\n";
		$component_content .= "use OsumiFramework\OFW\Core\OComponent;\n\n";
		$component_content .= "class ".$values['component_name']." extends OComponent {}";

		$template_content = "<"."?php if (is_null($"."values['".strtolower($values['model_name'])."'])): ?>\n";
		$template_content .= "null\n";
		$template_content .= "<"."?php else: ?>\n";
		$template_content .= "{\n";
		foreach ($values['model'] as $field) {
			$cont++;
			$template_content .= "	\"".OTools::underscoresToCamelCase($field->getName())."\": ";
			if (in_array($field->getType(), $text_fields) || in_array($field->getType(), $date_fields)) {
				$template_content .= "\"";
			}

			if ($field->getType()===OMODEL_BOOL) {
				$template_content .= "<"."?php echo $"."values['".strtolower($values['model_name'])."']->get('".$field->getName()."') ? 'true' : 'false' ?>";
			}
			elseif ($field->getNullable() && in_array($field->getType(), $date_fields)) {
				$template_content .= "<"."?php echo is_null($"."values['".strtolower($values['model_name'])."']->get('".$field->getName()."')) ? 'null' : $"."values['".strtolower($values['model_name'])."']->get('".$field->getName()."', 'd/m/Y H:i:s') ?>";
			}
			elseif (!$field->getNullable() && in_array($field->getType(), $date_fields)) {
				$template_content .= "<"."?php echo $"."values['".strtolower($values['model_name'])."']->get('".$field->getName()."', 'd/m/Y H:i:s') ?>";
			}
			elseif ($field->getNullable() && !in_array($field->getType(), $urlencode_fields)) {
				$template_content .= "<"."?php echo is_null($"."values['".strtolower($values['model_name'])."']->get('".$field->getName()."')) ? 'null' : $"."values['".strtolower($values['model_name'])."']->get('".$field->getName()."') ?>";
			}
			elseif (!$field->getNullable() && !in_array($field->getType(), $urlencode_fields)) {
				$template_content .= "<"."?php echo $"."values['".strtolower($values['model_name'])."']->get('".$field->getName()."') ?>";
			}
			elseif ($field->getNullable() && in_array($field->getType(), $urlencode_fields)) {
				$template_content .= "<"."?php echo is_null($"."values['".strtolower($values['model_name'])."']->get('".$field->getName()."')) ? 'null' : urlencode($"."values['".strtolower($values['model_name'])."']->get('".$field->getName()."')) ?>";
			}
			elseif (!$field->getNullable() && in_array($field->getType(), $urlencode_fields)) {
				$template_content .= "<"."?php echo urlencode($"."values['".strtolower($values['model_name'])."']->get('".$field->getName()."')) ?>";
			}

			if (in_array($field->getType(), $text_fields) || in_array($field->getType(), $date_fields)) {
				$template_content .= "\"";
			}

			if ($cont<count($values['model'])) {
				$template_content .= ",";
			}

			$template_content .= "\n";
		}
		$template_content .= "}\n";
		$template_content .= "<"."?php endif ?>";

		if (file_put_contents($values['component_folder'].$values['component_file'], $component_content)===false) {
			return 'component-file-cant-create';
		}
		if (file_put_contents($values['component_folder'].$values['component_template_file'], $template_content)===false) {
			return 'component-file-cant-create';
		}

		return 'ok';
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
		$urls_cache_file = $core->cacheContainer->getItem('urls');
		$urls   = json_decode($urls_cache_file->get(), true);
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

			$status = self::addAction($url['module'], $url['action'], $url['url'], $url['type'], $url['layout']);
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
		$task_file = $core->config->getDir('app_task').$task_name.'.task.php';
		if (!file_exists($task_file)) {
			return false;
		}

		require_once $task_file;
		$task_name = "\\OsumiFramework\\App\\Task\\".$task_name."Task";
		$task = new $task_name;
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
		$task_file = $core->config->getDir('ofw_task').$task_name.'.task.php';
		if (!file_exists($task_file)) {
			$ret['status'] = 'error';
			return $ret;
		}

		require_once $task_file;
		$task_name = "\\OsumiFramework\\OFW\\Task\\".$task_name."Task";
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
		$version_file = $core->config->getDir('ofw_vendor').'version.json';
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
		$version_file = $core->config->getDir('ofw_vendor').'version.json';
		$version = json_decode( file_get_contents($version_file), true );
		$current_version = $version['version'];
		return $version['updates'][$current_version]['message'];
	}

	/**
	 * Get user's IP address
	 *
	 * @return string User's IP address
	 */
	public static function getIPAddress(): string {
		// Whether ip is from the share internet
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		// Whether ip is from the proxy
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		// Whether ip is from the remote address
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	/**
	 * Convert underscore notation (snake case) to camel case (eg id_user -> idUser)
	 *
	 * @param string $string Text string to convert
	 *
	 * @param bool $capitalizeFirstCharacter Should first letter be capitalized or not, defaults to no
	 *
	 * @return string Converted text string
	 */
	public static function underscoresToCamelCase(string $string, bool $capitalizeFirstCharacter = false): string {
		$str = str_replace('_', '', ucwords($string, '_'));

		if (!$capitalizeFirstCharacter) {
			$str = lcfirst($str);
		}

		return $str;
	}

	/**
	 * Convert camel case (idUser) or Pascal case (IdUser) notation to snake case (eg IdUser -> id_user)
	 *
	 * @param string $string Text string to convert
	 *
	 * @param stringn $glue Character to use between words, defaults to underscore (_)
	 *
	 * @return string Converted text string
	 */
	public static function toSnakeCase(string $str, string $glue = '_'): string {
		return ltrim(preg_replace_callback('/[A-Z]/', fn($matches) => $glue . strtolower($matches[0]), $str), $glue);
	}
}
