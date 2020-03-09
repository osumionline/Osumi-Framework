<?php
class Base {
	/*
	 * Field types
	 */
	const PK       = 1;
	const PK_STR   = 10;
	const CREATED  = 2;
	const UPDATED  = 3;
	const NUM      = 4;
	const TEXT     = 5;
	const DATE     = 6;
	const BOOL     = 7;
	const LONGTEXT = 8;
	const FLOAT    = 9;

	const DEFAULT_MODEL = [
		self::PK       => ['default'=>null,  'original'=>null,  'value'=>null,  'incr'=>true,  'size'=>11, 'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>'', 'expose'=>true],
		self::PK_STR   => ['default'=>null,  'original'=>null,  'value'=>null,  'incr'=>false, 'size'=>50, 'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>'', 'expose'=>true],
		self::CREATED  => ['default'=>null,  'original'=>null,  'value'=>null,  'incr'=>false, 'size'=>0,  'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>'', 'expose'=>true],
		self::UPDATED  => ['default'=>null,  'original'=>null,  'value'=>null,  'incr'=>false, 'size'=>0,  'nullable'=>true,  'comment'=>'', 'ref'=>'', 'by'=>'', 'expose'=>true],
		self::NUM      => ['default'=>0,     'original'=>0,     'value'=>0,     'incr'=>false, 'size'=>11, 'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>'', 'expose'=>true],
		self::TEXT     => ['default'=>'',    'original'=>'',    'value'=>'',    'incr'=>false, 'size'=>50, 'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>'', 'expose'=>true],
		self::DATE     => ['default'=>null,  'original'=>null,  'value'=>'',    'incr'=>false, 'size'=>0,  'nullable'=>true,  'comment'=>'', 'ref'=>'', 'by'=>'', 'expose'=>true],
		self::BOOL     => ['default'=>false, 'original'=>false, 'value'=>false, 'incr'=>false, 'size'=>1,  'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>'', 'expose'=>true],
		self::LONGTEXT => ['default'=>'',    'original'=>'',    'value'=>'',    'incr'=>false, 'size'=>0,  'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>'', 'expose'=>true],
		self::FLOAT    => ['default'=>0,     'original'=>0,     'value'=>0,     'incr'=>false, 'size'=>0,  'nullable'=>false, 'comment'=>'', 'ref'=>'', 'by'=>'', 'expose'=>true]
	];

	public static function getCache($key) {
		global $c;
		if (!array_key_exists('cache_container', $GLOBALS)){
			$GLOBALS['cache_container'] = new OCacheContainer();
		}
		if ($GLOBALS['cache_container']->get($key)!==false){
			return $GLOBALS['cache_container']->get($key);
		}
		else{
			$route = $c->getDir('app_cache').$key.'.json';
			if (file_exists($route)){
				$data = file_get_contents($route);
				$json = json_decode($data,true);
				$GLOBALS['cache_container']->set($key, $json);
				return $json;
			}
			else{
				return false;
			}
		}
	}

	public static function getRandomCharacters($options) {
		$num     = isset($options['num'])     ? $options['num']     : 5;
		$lower   = isset($options['lower'])   ? $options['lower']   : false;
		$upper   = isset($options['upper'])   ? $options['upper']   : false;
		$numbers = isset($options['numbers']) ? $options['numbers'] : false;
		$special = isset($options['special']) ? $options['special'] : false;

		$seed = '';
		if ($lower){ $seed .= 'abcdefghijklmnopqrstuvwxyz'; }
		if ($upper){ $seed .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; }
		if ($numbers){ $seed .= '0123456789'; }
		if ($special){ $seed .= '!@#$%^&*()'; }

		$seed = str_split($seed);
		shuffle($seed);
		$rand = '';
		foreach (array_rand($seed, $num) as $k) $rand .= $seed[$k];

		return $rand;
	}

	public static function getParam($key, $list, $default=false) {
		if (array_key_exists($key, $list)){
			return $list[$key];
		}
		else{
			return $default;
		}
	}

	public static function getParamList($key_list, $list) {
		$params = [];
		foreach ($key_list as $key){
			$check = self::getParam($key, $list, false);
			if (!array_key_exists($key, $list)){
				return false;
			}
			$params[$key] = $check;
		}

		return $params;
	}

	public static function getTemplate($ruta,$html,$params) {
		if ($ruta!=''){
			$html = file_get_contents($ruta);
		}

		foreach ($params as $param_name => $param){
			$html = str_ireplace('{{'.$param_name.'}}', $param, $html);
		}

		return $html;
	}

	public static function fileToBase64($filename) {
		if (file_exists($filename)){
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$filebinary = (filesize($filename)>0) ? fread(fopen($filename, "r"), filesize($filename)) : '';
			return 'data:' . finfo_file($finfo, $filename) . ';base64,' . base64_encode($filebinary);
		}
		return false;
	}

	public static function base64ToFile($base64_string, $filename) {
		$ifp = fopen( $filename, 'wb' );
		$data = explode( ',', $base64_string );
		fwrite( $ifp, base64_decode( $data[ 1 ] ) );
		fclose( $ifp );
	}

	public static function bbcode($texto) {
		$bbcode = [
			"/\<(.*?)>/is",
			"/\[i\](.*?)\[\/i\]/is",
			"/\[b\](.*?)\[\/b\]/is",
			"/\[u\](.*?)\[\/u\]/is",
			"/\[g\](.*?)\[\/g\]/is",
			"/\[quote\](.*?)\[\/quote\]/is",
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
			"<span class=\"green_text\">$1</span>",
			"<div class=\"quote_text\">Cita:<br />$1</div>",
			"<img src=\"$1\" />",
			"<a href=\"$1\" target=\"_blank\">$2</a>",
			"<a href=\"mailto:$1\">$2</a>",
			"<span style=\"color:$1\">$2</span>"
		];
		$texto = preg_replace($bbcode, $html, $texto);
		return $texto;
	}

	public static function showErrorPage($res,$mode) {
		global $c;
		if (!is_null($c->getErrorPage($mode))){
			header('Location:'.$c->getErrorPage($mode));
			exit;
		}

		if ($mode=='403'){ header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden"); }
		if ($mode=='404'){ header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); }
		echo "<html>\n";
		echo "  <head>\n";
		echo "    <meta charset=\"UTF-8\">\n";
		echo "    <title>Error</title>\n";
		echo "  </head>\n";
		echo "  <body>\n";
		if ($mode=='404' || $mode=='403' || $mode=='general'){
			if ($mode=='403'){
				echo "403 - Error de autenticación\n";
			}
			if ($mode=='404'){
				echo "404 - Página no encontrada\n";
			}
			if ($mode=='general'){
				echo $res['message'];
			}
		}
		else{
			if ($mode=='module'){
				echo "M&oacute;dulo <strong>".$res['module']."</strong> no encontrado\n";
			}
			if ($mode=='action'){
				echo "    Funci&oacute;n <strong>".ucfirst($res['action'])."</strong> no encontrada en <strong>".$res['module']."</strong>\n";
			}
			echo "    <br /><br />\n";
			echo "    <a href=\"#\" onclick=\"showMore();\" id=\"show_more_link\">\n";
			echo "      Ver m&aacute;s detalles\n";
			echo "    </a>\n";
			echo "    <div id=\"detalles\" style=\"display:none;\">\n";
			echo "      RES: <pre>\n";
			var_dump($res);
			echo "      </pre>\n";
			echo "    </div>";
			echo "    <script type=\"text/javascript\">\n";
			echo "      function showMore(){\n";
			echo "        document.getElementById('detalles').style.display='block';\n";
			echo "        document.getElementById('show_more_link').style.display='none';\n";
			echo "        return false;\n";
			echo "      }\n";
			echo "    </script>";
		}
		echo "  </body>\n";
		echo "</html>";
		exit;
	}

	public static function doPostRequest($url, $data) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$result = curl_exec($ch);

		return $result;
	}

	public static function doDeleteRequest($url, $data) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$result = curl_exec($ch);

		return $result;
	}

	public static function slugify($text, $separator = '-'){
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

	public static function getModelList() {
		global $c;
		$ret = [];

		if ($model = opendir($c->getDir('app_model'))) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != "." && $entry != "..") {
					$table = str_ireplace('.php','',$entry);
					eval("$"."mod = new ".$table."();");
					array_push($ret,$mod);
				}
			}
			closedir($model);
		}

		sort($ret);
		return $ret;
	}

	public static function generateModel() {
		global $c;
		echo "Modelo\n\n";
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

		$sql_file = $c->getDir('ofw_export').'model.sql';
		if (file_exists($sql_file)){
			unlink($sql_file);
		}

		file_put_contents($sql_file,$sql);
	}

	public static function updateUrls($silent=false) {
		global $c;
		$urls_file = json_decode( file_get_contents($c->getDir('app_config').'urls.json'), true);
		$urls = self::getUrlList($urls_file);

		$urls_cache_file = $c->getDir('app_cache').'urls.cache.json';
		if (file_exists($urls_cache_file)){
			unlink($urls_cache_file);
		}

		file_put_contents($urls_cache_file, json_encode($urls, JSON_UNESCAPED_UNICODE ));

		self::updateControllers($silent);
	}

	public static function getUrlList($item) {
		$list = self::getUrls($item);
		for ($i=0;$i<count($list);$i++){
			$keys = array_keys($list[$i]);
			foreach ($keys as $key){
				if (is_null($list[$i][$key])){
					unset($list[$i][$key]);
				}
			}
		}
		return $list;
	}

	public static function getUrls($item) {
		$list = [];
		if (array_key_exists('urls', $item)){
			foreach ($item['urls'] as $elem){
				$list = array_merge($list, self::getUrls($elem));
			}
			for ($i=0;$i<count($list);$i++){
				$list[$i]['url']    = ((array_key_exists('prefix', $item) && !is_null($item['prefix'])) ? $item['prefix'] : '') . $list[$i]['url'];
				$list[$i]['layout'] = (array_key_exists('layout', $list[$i])  && !is_null($list[$i]['layout'])) ? $list[$i]['layout'] : ( (array_key_exists('layout', $item) && !is_null($item['layout'])) ? $item['layout'] : null);
				$list[$i]['module'] = (array_key_exists('module', $list[$i])  && !is_null($list[$i]['module'])) ? $list[$i]['module'] : ( (array_key_exists('module', $item) && !is_null($item['module'])) ? $item['module'] : null);
				$list[$i]['filter'] = (array_key_exists('filter', $list[$i])  && !is_null($list[$i]['filter'])) ? $list[$i]['filter'] : ( (array_key_exists('filter', $item) && !is_null($item['filter'])) ? $item['filter'] : null);
				$list[$i]['type']   = (array_key_exists('type',   $list[$i])  && !is_null($list[$i]['type']))   ? $list[$i]['type']   : ( (array_key_exists('type',   $item) && !is_null($item['type']))   ? $item['type']   : null);
			}
		}
		else{
			array_push($list, $item);
		}
		return $list;
	}

	public static function updateControllers($silent=false) {
		global $c;
		$colors = new OColors();
		$urls   = json_decode( file_get_contents($c->getDir('app_cache').'urls.cache.json'), true);
		$errors = false;

		if (!$silent){
			echo "\n";
			echo "  ".$colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
			echo "  Actualizando módulos/controladores...\n\n";
		}

		$reserved_modules = ['private', 'protected', 'public'];
		foreach ($urls as $url){
			if (in_array($url['module'], $reserved_modules)){
				if (!$silent) {
					echo $colors->getColoredString("ERROR", "white", "red").": El nombre del módulo es una palabra reservada (".$url['module']."). El módulo no puede llamarse de las siguientes maneras:\n";
					foreach ($reserved_modules as $module){
						echo "  · ".$module."\n";
					}
					$errors = true;
				}
				continue;
			}

			if ($url['action']==$url['module']){
				if (!$silent) {
					echo $colors->getColoredString("ERROR", "white", "red").": Una acción no puede llamarse igual que el módulo que la contiene:\n";
					echo "  Módulo: ".$url['module']."\n";
					echo "  Acción: ".$url['action']."\n";
					$errors = true;
				}
				continue;
			}

			$ruta_controller = $c->getDir('app_controller') . $url['module'] . '.php';
			if (!file_exists($ruta_controller)){
				file_put_contents($ruta_controller, "<"."?php\nclass ".$url['module']." extends OController{\n}");
				if (!$silent) {
					echo "  Nuevo controlador ".$colors->getColoredString("\"" . $url['module'] . "\"", "light_green")." creado en el archivo ".$colors->getColoredString("\"" . $ruta_controller . "\"", "light_green").".\n";
				}
			}

			$ruta_templates = $c->getDir('app_template') . $url['module'];
			if (!file_exists($ruta_templates) && !is_dir($ruta_templates)){
				mkdir($ruta_templates);
				if (!$silent) {
					echo "  Nueva carpeta para templates ".$colors->getColoredString("\"" . $ruta_templates . "\"", "light_green")." creada.\n";
				}
			}

			$controller_str = file_get_contents($ruta_controller);
			if (stripos($controller_str, "function ".$url['action']) === false){
				file_put_contents($ruta_controller, substr_replace($controller_str, '', strrpos($controller_str, '}'), 1));

				$str = "\n";
				$str .= "	/*\n";
				$str .= "	 * ".$url['comment']."\n";
				$str .= "	 */\n";
				$str .= "	function ".$url['action']."($"."req){}\n";
				file_put_contents($ruta_controller, $str."}", FILE_APPEND);

				if (!$silent) {
					echo "  Nueva acción ".$colors->getColoredString("\"" . $url['action'] . "\"", "light_green")." creada en el controlador ".$colors->getColoredString("\"" . $url['module'] . "\"", "light_green").".\n";
				}

				$ruta_template = $c->getDir('app_template') . $url['module'] . '/' . $url['action'] . '.php';
				if (!file_exists($ruta_template)){
					file_put_contents($ruta_template, '');
					if (!$silent) {
						echo "  Nuevo template ".$colors->getColoredString("\"" . $ruta_template . "\"", "light_green")." creado.\n";
					}
				}
			}
		}

		if ($errors && !$silent){
			echo "\n";
			echo $colors->getColoredString("----------------------------------------------------------------------------------------------------------------------", "white", "red")."\n";
			echo $colors->getColoredString("  Ocurrieron errores al actualizar módulos y controladores. Revisa los errores y vuelve a ejecutar la tarea updateUrls", "white", "red")."\n";
			echo $colors->getColoredString("----------------------------------------------------------------------------------------------------------------------", "white", "red")."\n";
		}
		if (!$silent){
			echo "\n";
		}
	}

	public static function runTask($task_name, $params=[]) {
		global $c;
		$task_file = $c->getDir('app_task').$task_name.'.php';
		if (!file_exists($task_file)){
			return false;
		}
		require_once $task_file;
		$task_name .= 'Task';
		$task = new $task_name();
		$task->run($params);
	}

	public static function runOFWTask($task_name, $params=[]) {
		global $c;
		$task_file = $c->getDir('ofw_task').$task_name.'.php';
		if (!file_exists($task_file)){
			return false;
		}
		require_once $task_file;
		$task_name .= 'Task';
		$task = new $task_name();
		$task->run($params);
	}

	public static function getVersion() {
		global $c;
		$version_file = $c->getDir('ofw_base').'version.json';
		$version = json_decode( file_get_contents($version_file), true );
		return $version['version'];
	}

	public static function getVersionInformation() {
		global $c;
		$version_file = $c->getDir('ofw_base').'version.json';
		$version = json_decode( file_get_contents($version_file), true );

		$current_version = $version['version'];
		return $version['updates'][$current_version]['message'];
	}
}