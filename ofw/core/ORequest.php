<?php declare(strict_types=1);
class ORequest {
	private ?string $method = null;
	private array $headers = [];
	private array $params = [];
	private array $filters = [];

	function __construct(array $url_result) {
		$this->setMethod($url_result['method']);
		$this->setHeaders($url_result['headers']);
		$this->setParams($url_result['params']);
		if (array_key_exists('filter', $url_result) && !is_null($url_result['filter'])) {
			$this->setFilter($url_result['filter'], $url_result[$url_result['filter']]);
		}
	}

	/**
	 * Set HTTP method used in the call (GET/POST...)
	 *
	 * @param string $method HTTP method used in the call
	 *
	 * @return void
	 */
	public function setMethod(string $method): void {
		$this->method = $method;
	}

	/**
	 * Get HTTP method used in the call
	 *
	 * @return string HTTP method used in the call
	 */
	public function getMethod(): ?string {
		return $this->method;
	}

	/**
	 * Set HTTP headers used in the call
	 *
	 * @param array $headers List of HTTP headers used in the call
	 *
	 * @return void
	 */
	public function setHeaders(array $headers): void {
		$this->headers = $headers;
	}

	/**
	 * Get list of HTTP headers used in the call
	 *
	 * @return array List of HTTP headers used in the call
	 */
	public function getHeaders(): array {
		return $this->headers;
	}

	/**
	 * Get a specific HTTP header, null if not found
	 *
	 * @param string $key Key code of the HTTP header
	 *
	 * @return ?string Value of the requested HTTP header or null if not found
	 */
	public function getHeader(string $key): ?string {
		return array_key_exists($key, $this->headers) ? $this->headers[$key] : null;
	}

	/**
	 * Set list of parameters passed in the call
	 *
	 * @param array $params List of parameters passed in the call
	 *
	 * @return void
	 */
	public function setParams(array $params): void {
		$this->params = $params;
	}

	/**
	 * Get list of parameters passed in the call
	 *
	 * @return array List of parameters passed in the call
	 */
	public function getParams(): array {
		return $this->params;
	}

	/**
	 * Get a specific parameter or a default value if not found
	 *
	 * @param string $key Key code of the value to be retrieved
	 *
	 * @param mixed $default Default value if key not found
	 */
	public function getParam(string $key, $default=null) {
		return array_key_exists($key, $this->params) ? $this->params[$key] : $default;
	}

	/**
	 * Get a specific parameter as a string
	 *
	 * @param string $key Key code of the value to be retrieved
	 *
	 * @param mixed $default Default value if key not found
	 *
	 * @return ?string String value of the required parameter
	 */
	public function getParamString(string $key, $default=null): ?string {
		$param = $this->getParam($key, $default);
		return !is_null($param) ? strval($param) : null;
	}

	/**
	 * Get a specific parameter as an int
	 *
	 * @param string $key Key code of the value to be retrieved
	 *
	 * @param mixed $default Default value if key not found
	 *
	 * @return ?int Int value of the required parameter
	 */
	public function getParamInt(string $key, $default=null): ?int {
		$param = $this->getParam($key, $default);
		return !is_null($param) ? intval($param) : null;
	}

	/**
	 * Get a specific parameter as a float
	 *
	 * @param string $key Key code of the value to be retrieved
	 *
	 * @param mixed $default Default value if key not found
	 *
	 * @return ?float Float value of the required parameter
	 */
	public function getParamFloat(string $key, $default=null): ?float {
		$param = $this->getParam($key, $default);
		return !is_null($param) ? floatval($param) : null;
	}

	/**
	 * Get a specific parameter as a boolean
	 *
	 * @param string $key Key code of the value to be retrieved
	 *
	 * @param mixed $default Default value if key not found
	 *
	 * @return ?bool Boolean value of the required parameter
	 */
	public function getParamBool(string $key, $default=null): ?bool {
		$param = $this->getParam($key, $default);
		return !is_null($param) ? boolval($param) : null;
	}

	/**
	 * Set filters returned values
	 *
	 * @param array $filters List of values returned by filters
	 *
	 * @return void
	 */
	public function setFilters(array $filters): void {
		$this->filters = $filters;
	}

	/**
	 * Get list of filters returned values
	 *
	 * @return array List of filters returned values
	 */
	public function getFilters(): array {
		return $this->filters;
	}

	/**
	 * Set the values returned of a specific filter
	 *
	 * @param string $key Name of the filter
	 *
	 * @param array $values Values returned by the filter
	 *
	 * @return false
	 */
	public function setFilter(string $key, array $values): void {
		$this->filters[$key] = $values;
	}

	/**
	 * Get the values returned by a specific filter or null if not found
	 *
	 * @param string $key Name of the filter
	 *
	 * @return ?array Values returned by the filter or null if not found
	 */
	public function getFilter(string $key): ?array {
		return array_key_exists($key, $this->filters) ? $this->filters[$key] : null;
	}
}