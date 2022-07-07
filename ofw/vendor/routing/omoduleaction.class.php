<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Routing;

use Attribute;

#[Attribute]
class OModuleAction {
	private ?string $url      = null;
	private array $services   = [];
	private array $filters    = [];
	private ?string $layout   = null;
	private ?string $type     = null;
	private array $inline_css = [];
	private array $css        = [];
	private array $inline_js  = [];
	private array $js         = [];
	private array $utils      = [];

	function __construct(
		?string $url      = null,
		array $services   = [],
		array $filters    = [],
		?string $layout   = null,
		?string $type     = null,
		array $inlineCSS  = [],
		array $css        = [],
		array $inlineJS   = [],
		array $js         = [],
		array $utils      = []
	) {
		$this->url        = $url;
		$this->services   = $services;
		$this->filters    = $filters;
		$this->layout     = $layout;
		$this->type       = $type;
		$this->inline_css = $inlineCSS;
		$this->css        = $css;
		$this->inline_js  = $inlineJS;
		$this->js         = $js;
		$this->utils      = $utils;
	}

	/**
	 * Get action's URL
	 *
	 * @return ?string Action's URL
	 */
	public function getUrl(): ?string {
		return $this->url;
	}

	/**
	 * Get list of services used on an action
	 *
	 * @return array Action's services
	 */
	public function getServices(): array {
		return $this->services;
	}

	/**
	 * Get method's filters
	 *
	 * @return array Method's filters
	 */
	public function getFilters(): array {
		return $this->filters;
	}

	/**
	 * Get method's layout
	 *
	 * @return string Method's layout
	 */
	public function getLayout(): ?string {
		return $this->layout;
	}

	/**
	 * Get method's type
	 *
	 * @return string Method's type
	 */
	public function getType(): ?string {
		return $this->type;
	}

	/**
	 * Get list of CSS files to be inlined on an action
	 *
	 * @return array Action's inline CSS files
	 */
	public function getInlineCss(): array {
		return $this->inline_css;
	}

	/**
	 * Get list of CSS files to be included on an action
	 *
	 * @return array Action's included CSS files
	 */
	public function getCss(): array {
		return $this->css;
	}

	/**
	 * Get list of JS files to be inlined on an action
	 *
	 * @return array Action's inline JS files
	 */
	public function getInlineJs(): array {
		return $this->inline_js;
	}

	/**
	 * Get list of JS files to be included on an action
	 *
	 * @return array Action's included JS files
	 */
	public function getJs(): array {
		return $this->js;
	}

	/**
	 * Get "utils" folder's classes to be loaded into the method (comma separated values)
	 *
	 * @return array "utils" folder's classes to be loaded
	 */
	public function getUtils(): array {
		return $this->utils;
	}
}
