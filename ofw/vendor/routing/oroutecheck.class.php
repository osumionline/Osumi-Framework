<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Routing;

/**
 * Route matching tools
 * Based on Symfony 1.3 routing lib
 */
class ORouteCheck {
	protected $suffix            = null;
	protected $defaultParameters = [];
	protected $defaultOptions    = [];
	protected $compiled          = false;
	protected $options           = [];
	protected $pattern           = null;
	protected $staticPrefix      = null;
	protected $regex             = null;
	protected $variables         = [];
	protected $defaults          = [];
	protected $requirements      = [];
	protected $tokens            = [];
	protected $customToken       = false;

	/**
	 * Constructor.
	 *
	 * Available options:
	 *
	 *  * variable_prefixes:                An array of characters that starts a variable name (: by default)
	 *  * segment_separators:               An array of allowed characters for segment separators (/ and . by default)
	 *  * variable_regex:                   A regex that match a valid variable name ([\w\d_]+ by default)
	 *  * generate_shortest_url:            Whether to generate the shortest URL possible (true by default)
	 *  * extra_parameters_as_query_string: Whether to generate extra parameters as a query string
	 *
	 * @param string $pattern       The pattern to match
	 *
	 * @param array  $defaults      An array of default parameter values
	 *
	 * @param array  $requirements  An array of requirements for parameters (regexes)
	 *
	 * @param array  $options       An array of options
	 */
	public function __construct(string $pattern, array $defaults = [], array $requirements = [], array $options = []) {
		$this->pattern      = trim($pattern);
		$this->defaults     = $defaults;
		$this->requirements = $requirements;
		$this->options      = $options;
	}

	/**
	 * Returns an array of parameters if the URL matches this route, false otherwise.
	 *
	 * @param  string  $url     The URL
	 *
	 * @param  array   $context The context
	 *
	 * @return array   An array of parameters
	 */
	public function matchesUrl(string $url, array $context = []): ?array {
		if (!$this->compiled) {
			$this->compile();
		}

		// Check the static prefix uf the URL first. Only use the more expensive preg_match when it matches
		if ('' !== $this->staticPrefix  && 0 !== strpos($url, $this->staticPrefix)) {
			return null;
		}
		if (!preg_match($this->regex, $url, $matches)) {
			return null;
		}

		$defaults   = array_merge($this->getDefaultParameters(), $this->defaults);
		$parameters = [];

		if (isset($matches['_star'])) {
			$parameters = $this->parseStarParameter($matches['_star']);
			unset($matches['_star'], $parameters['module'], $parameters['action']);
		}

		// Defaults
		$parameters = $this->mergeArrays($defaults, $parameters);

		// Variables
		foreach ($matches as $key => $value) {
			if (!is_int($key)) {
				$parameters[$key] = urldecode($value);
			}
		}

		return $parameters;
	}

	/**
	 * Compiles the current route instance.
	 *
	 * @return void
	 */
	public function compile(): void {
		if ($this->compiled) {
			return;
		}

		$this->initializeOptions();
		$this->fixRequirements();
		$this->fixDefaults();
		$this->fixSuffix();

		$this->compiled = true;
		$this->firstOptional = 0;
		$this->segments = [];

		$this->preCompile();

		$this->tokenize();

		// Parse
		foreach ($this->tokens as $token) {
			call_user_func_array(array($this, 'compileFor'.ucfirst(array_shift($token))), $token);
		}

		$this->postCompile();

		$separator = '';
		if (count($this->tokens)) {
			$lastToken = $this->tokens[count($this->tokens) - 1];
			$separator = 'separator' == $lastToken[0] ? $lastToken[2] : '';
		}

		$this->regex = "#^".implode("", $this->segments)."".preg_quote($separator, '#')."$#x";
	}

	/**
	 * Pre-compiles a route.
	 *
	 * @return void
	 */
	protected function preCompile(): void {
		// A route must start with a slash
		if (empty($this->pattern) || '/' != $this->pattern[0]) {
			$this->pattern = '/'.$this->pattern;
		}
	}

	/**
	 * Post-compiles a route.
	  *
	  * @return void
	 */
	protected function postCompile(): void {
		// All segments after the last static segment are optional.
		// Be careful, the n-1 is optional only if n is empty.
		for ($i = $this->firstOptional, $max = count($this->segments); $i < $max; $i++) {
			$this->segments[$i] = (0 == $i ? '/?' : '').str_repeat(' ', $i - $this->firstOptional).'(?:'.$this->segments[$i];
			$this->segments[] = str_repeat(' ', $max - $i - 1).')?';
		}

		$this->staticPrefix = '';
		foreach ($this->tokens as $token) {
			switch ($token[0]) {
				case 'separator':
					break;
				case 'text':
					if ($token[2] !== '*') {
						// Non-star text is static
						$this->staticPrefix .= $token[1].$token[2];
						break;
					}
				default:
					// Everything else indicates variable parts. Break switch and for loop.
					break 2;
			}
		}
	}

	/**
	 * Tokenizes the route.
	  *
	  * @return void
	 */
	protected function tokenize(): void {
		$this->tokens = [];
		$buffer = $this->pattern;
		$afterASeparator = false;
		$currentSeparator = '';

		// A route is an array of (separator + variable) or (separator + text) segments
		while (strlen($buffer)) {
			if (false !== $this->tokenizeBufferBefore($buffer, $tokens, $afterASeparator, $currentSeparator)) {
				// A custom token
				$this->customToken = true;
			}
			else if ($afterASeparator && preg_match('#^'.$this->options['variable_prefix_regex'].'('.$this->options['variable_regex'].')#', $buffer, $match)) {
				// A variable
				$this->tokens[] = ['variable', $currentSeparator, $match[0], $match[1]];

				$currentSeparator = '';
				$buffer = substr($buffer, strlen($match[0]));
				$afterASeparator = false;
			}
			else if ($afterASeparator && preg_match('#^('.$this->options['text_regex'].')(?:'.$this->options['segment_separators_regex'].'|$)#', $buffer, $match)) {
				// A text
				$this->tokens[] = ['text', $currentSeparator, $match[1], null];

				$currentSeparator = '';
				$buffer = substr($buffer, strlen($match[1]));
				$afterASeparator = false;
			}
			else if (!$afterASeparator && preg_match('#^/|^'.$this->options['segment_separators_regex'].'#', $buffer, $match)) {
				// Beginning of URL (^/) or a separator
				$this->tokens[] = array('separator', $currentSeparator, $match[0], null);

				$currentSeparator = $match[0];
				$buffer = substr($buffer, strlen($match[0]));
				$afterASeparator = true;
			}
			else if (false !== $this->tokenizeBufferAfter($buffer, $tokens, $afterASeparator, $currentSeparator)) {
				// A custom token
				$this->customToken = true;
			}
			else {
				// Parsing problem
				throw new InvalidArgumentException(sprintf('Unable to parse "%s" route near "%s".', $this->pattern, $buffer));
			}
		}
    
		// Check for suffix
		if ($this->suffix) {
			// Treat as a separator
			$this->tokens[] = array('separator', $currentSeparator, $this->suffix);
		}
	}

	/**
	 * Tokenizes the buffer before default logic is applied.
	 * This method must return false if the buffer has not been parsed.
	 *
	 * @param string   $buffer           The current route buffer
	 *
	 * @param array    $tokens           An array of current tokens
	 *
	 * @param Boolean  $afterASeparator  Whether the buffer is just after a separator
	 *
	 * @param string   $currentSeparator The last matched separator
	 *
	 * @return Boolean true if a token has been generated, false otherwise
	 */
	protected function tokenizeBufferBefore(&$buffer, &$tokens, &$afterASeparator, &$currentSeparator): bool {
    	return false;
	}

	/**
	 * Tokenizes the buffer after default logic is applied.
	 * This method must return false if the buffer has not been parsed.
	 *
	 * @param string   $buffer           The current route buffer
	 *
	 * @param array    $tokens           An array of current tokens
	 *
	 * @param Boolean  $afterASeparator  Whether the buffer is just after a separator
	 *
	 * @param string   $currentSeparator The last matched separator
	 *
	 * @return Boolean true if a token has been generated, false otherwise
	 */
	protected function tokenizeBufferAfter(&$buffer, &$tokens, &$afterASeparator, &$currentSeparator): bool {
		return false;
	}

	/**
	 * Compiles a text type token
	 *
	 * @param string $separator Segment separator
	 *
	 * @param string $text      Token text content
	 *
	 * @return void
	 */
	protected function compileForText(string $separator, string $text): void {
		if ('*' == $text) {
			$this->segments[] = '(?:'.preg_quote($separator, '#').'(?P<_star>.*))?';
		}
		else {
			$this->firstOptional = count($this->segments) + 1;

			$this->segments[] = preg_quote($separator, '#').preg_quote($text, '#');
		}
	}

	/**
	 * Compiles a variable type token
	 *
	 * @param string $separator Segment separator
	 *
	 * @param string $name      Token content value
	 *
	 * @param string $variable  Token variable name
	 *
	 * @return void
	 */
	protected function compileForVariable(string $separator, string $name, string $variable): void {
		if (!isset($this->requirements[$variable])) {
			$this->requirements[$variable] = $this->options['variable_content_regex'];
		}

		$this->segments[] = preg_quote($separator, '#').'(?P<'.$variable.'>'.$this->requirements[$variable].')';
		$this->variables[$variable] = $name;

		if (!isset($this->defaults[$variable])) {
			$this->firstOptional = count($this->segments);
		}
	}

	/**
	 * Compiles a separator type token. A separator has nothing to be compiled.
	 *
	 * @param string $separator      Segment separator
	 *
	 * @param string $regexSeparator Regex used for the separator
	 *
	 * @return void
	 */
	protected function compileForSeparator(string $separator, string $regexSeparator): void {}

	/**
	 * Returns the default parameter list
	 *
	 * @return array Default parameter list
	 */
	public function getDefaultParameters(): array {
		return $this->defaultParameters;
	}

	/**
	 * Returns the default options list
	 *
	 * @return array Default options list
	 */
	public function getDefaultOptions(): array {
		return $this->defaultOptions;
	}

	/**
	 * Initializes the options based on the default options and the ones given
	 *
	 * @return void
	 */
	protected function initializeOptions(): void {
		$this->options = array_merge([
			'suffix'                           => '',
			'variable_prefixes'                => [':'],
			'segment_separators'               => ['/', '.'],
			'variable_regex'                   => '[\w\d_]+',
			'text_regex'                       => '.+?',
			'generate_shortest_url'            => true,
			'extra_parameters_as_query_string' => true,
		], $this->getDefaultOptions(), $this->options);

		$preg_quote_hash = function($a) {
			return preg_quote($a, '#');
		};

		// Compute some regexes
		$this->options['variable_prefix_regex'] = '(?:'.implode('|', array_map($preg_quote_hash, $this->options['variable_prefixes'])).')';

		if (count($this->options['segment_separators'])) {
			$this->options['segment_separators_regex'] = '(?:'.implode('|', array_map($preg_quote_hash, $this->options['segment_separators'])).')';

			$this->options['variable_content_regex'] = '[^'.implode('',
				array_map($preg_quote_hash, $this->options['segment_separators'])
		    ).']+';
		}
		else {
			// Use simplified regexes for case where no separators are used
			$this->options['segment_separators_regex'] = '()';
			$this->options['variable_content_regex']   = '.+';
		}
	}

	/**
	 * Parse a star parameter type
	 *
	 * @param string $star Star parameters
	 *
	 * @return array List of parsed star parameters
	 */
	protected function parseStarParameter(string $star): array {
		$parameters = [];
		$tmp = explode('/', $star);
		for ($i = 0, $max = count($tmp); $i < $max; $i += 2) {
			// Don't allow a param name to be empty - #4173
			if (!empty($tmp[$i])) {
				$parameters[$tmp[$i]] = isset($tmp[$i + 1]) ? urldecode($tmp[$i + 1]) : true;
			}
		}

		return $parameters;
	}

	/**
	 * Return the merge of two arrays
	 *
	 * @param array $arr1 First array where information will be merged
	 *
	 * @param array $arr2 Array to be merged into the first one
	 *
	 * @return array Array with the merged values
	 */
	protected function mergeArrays(array $arr1, array $arr2): array {
		foreach ($arr2 as $key => $value) {
			$arr1[$key] = $value;
		}

		return $arr1;
	}

	/**
	 * Fixes the defaults if any text character is present
	 *
	 * @return void
	 */
	protected function fixDefaults(): void {
		foreach ($this->defaults as $key => $value) {
			if (ctype_digit($key)) {
				$this->defaults[$value] = true;
			}
			else {
				$this->defaults[$key] = urldecode($value);
			}
		}
	}

	/**
	 * Fixes the requirements checking the regexs format
	 *
	 * @return void
	 */
	protected function fixRequirements(): void {
		foreach ($this->requirements as $key => $regex) {
			if (!is_string($regex)) {
				continue;
			}

			if ('^' == $regex[0]) {
				$regex = substr($regex, 1);
			}
			if ('$' == substr($regex, -1)) {
				$regex = substr($regex, 0, -1);
			}

			$this->requirements[$key] = $regex;
		}
	}

	/**
	 * Fixes the suffix checking the given pattern
	 *
	 * @return void
	 */
	protected function fixSuffix(): void {
		$length = strlen($this->pattern);

		if ($length > 0 && '/' == $this->pattern[$length - 1]) {
			// Route ends by / (directory)
			$this->suffix = '/';
		}
		else if ($length > 0 && '.' == $this->pattern[$length - 1]) {
			// Route ends by . (no suffix)
			$this->suffix = '';
			$this->pattern = substr($this->pattern, 0, $length - 1);
		}
		else if (preg_match('#\.(?:'.$this->options['variable_prefix_regex'].$this->options['variable_regex'].'|'.$this->options['variable_content_regex'].')$#i', $this->pattern)) {
			// Specific suffix for this route
			// A . with a variable after or some chars without any separators
			$this->suffix = '';
		}
		else {
			$this->suffix = $this->options['suffix'];
		}
	}
}