<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Routing;

#[\Attribute]
class ORoute {
	private ?string $url    = null;
	private ?string $type   = null;
	private ?string $prefix = null;
	private ?string $filter = null;
	private ?string $layout = null;

	function __construct(
		?string $url    = null,
		?string $type   = null,
		?string $prefix = null,
		?string $filter = null,
		?string $layout = null
	) {
		$this->url    = $url;
		$this->type   = $type;
		$this->prefix = $prefix;
		$this->filter = $filter;
		$this->layout = $layout;
	}

	/**
	 * Get method's URL
	 *
	 * @return ?string Method's URL
	 */
	public function getUrl(): ?string {
		return $this->url;
	}

	/**
	 * Get method's response type
	 *
	 * @return ?string Method's response type
	 */
	public function getType(): ?string {
		return $this->type;
	}

	/**
	 * Get URL prefix string
	 *
	 * @return ?string URL's prefix string
	 */
	public function getPrefix(): ?string {
		return $this->prefix;
	}

	/**
	 * Get method's filter
	 *
	 * @return string Method's filter
	 */
	public function getFilter(): ?string {
		return $this->filter;
	}

	/**
	 * Get method's layout
	 *
	 * @return string Method's layout
	 */
	public function getLayout(): ?string {
		return $this->layout;
	}
}