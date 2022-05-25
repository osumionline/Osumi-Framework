<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Routing;

use Attribute;

#[Attribute]
class OModuleAction {
	private ?string $url             = null;
	private ?string $services        = null;
	private array   $service_list    = [];
	private ?string $components      = null;
	private array   $component_list  = [];
	private ?string $filter          = null;
	private ?string $layout          = null;
	private ?string $type            = null;
	private ?string $inline_css      = null;
	private array   $inline_css_list = [];
	private ?string $css             = null;
	private array   $css_list        = [];
	private ?string $inline_js       = null;
	private array   $inline_js_list  = [];
	private ?string $js              = null;
	private array   $js_list         = [];
	private ?string $utils           = null;
	private array   $util_list       = [];

	function __construct(
		?string $url        = null,
		?string $services   = null,
		?string $components = null,
		?string $filter     = null,
		?string $layout     = null,
		?string $type       = null,
		?string $inlineCSS  = null,
		?string $css        = null,
		?string $inlineJS   = null,
		?string $js         = null
	) {
		$this->url        = $url;
		$this->services   = $services;
		$this->components = $components;
		$this->filter     = $filter;
		$this->layout     = $layout;
		$this->type       = $type;
		$this->inline_css = $inlineCSS;
		$this->css        = $css;
		$this->inline_js  = $inlineJS;
		$this->js         = $js;

		if (!is_null($this->services)) {
			foreach (explode(',', $this->services) as $service) {
				array_push($this->service_list, trim($service));
			}
		}
		if (!is_null($this->components)) {
			foreach (explode(',', $this->components) as $component) {
				array_push($this->component_list, trim($component));
			}
		}
		if (!is_null($this->inline_css)) {
			foreach (explode(',', $this->inline_css) as $css) {
				array_push($this->inline_css_list, trim($css));
			}
		}
		if (!is_null($this->css)) {
			foreach (explode(',', $this->css) as $css_item) {
				array_push($this->css_list, trim($css_item));
			}
		}
		if (!is_null($this->inline_js)) {
			foreach (explode(',', $this->inline_js) as $js) {
				array_push($this->inline_js_list, trim($js));
			}
		}
		if (!is_null($this->js)) {
			foreach (explode(',', $this->js) as $js_item) {
				array_push($this->js_list, trim($js_item));
			}
		}
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
	 * @return ?string Action's services
	 */
	public function getServices(): ?string {
		return $this->services;
	}

	/**
	 * Get list of services used on an action as an array
	 *
	 * @return array Array of services used on an action
	 */
	public function getServiceList(): array {
		return $this->service_list;
	}

	/**
	 * Get list of components used on an action
	 *
	 * @return ?string Action's components
	 */
	public function getComponents(): ?string {
		return $this->components;
	}

	/**
	 * Get list of components used on an action as an array
	 *
	 * @return array Action's component list
	 */
	public function getComponentList(): array {
		return $this->component_list;
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
	 * @return ?string Action's inline CSS files
	 */
	public function getInlineCss(): ?string {
		return $this->inline_css;
	}

	/**
	 * Get list of CSS files to be inlined on an action as an array
	 *
	 * @return array Action's inline CSS file list
	 */
	public function getInlineCssList(): array {
		return $this->inline_css_list;
	}

	/**
	 * Get list of CSS files to be included on an action
	 *
	 * @return ?string Action's included CSS files
	 */
	public function getCss(): ?string {
		return $this->css;
	}

	/**
	 * Get list of CSS files to be included on an action as an array
	 *
	 * @return array Action's included CSS file list
	 */
	public function getCssList(): array {
		return $this->css_list;
	}

	/**
	 * Get list of JS files to be inlined on an action
	 *
	 * @return ?string Action's inline JS files
	 */
	public function getInlineJs(): ?string {
		return $this->inline_js;
	}

	/**
	 * Get list of JS files to be inlined on an action as an array
	 *
	 * @return array Action's inline JS file list
	 */
	public function getInlineJsList(): array {
		return $this->inline_js_list;
	}

	/**
	 * Get list of JS files to be included on an action
	 *
	 * @return ?string Action's included JS files
	 */
	public function getJs(): ?string {
		return $this->js;
	}

	/**
	 * Get list of JS files to be included on an action as an array
	 *
	 * @return array Action's included JS file list
	 */
	public function getJsList(): array {
		return $this->js_list;
	}

	/**
	 * Get "utils" folder's classes to be loaded into the method (comma separated values)
	 *
	 * @return string "utils" folder's classes to be loaded
	 */
	public function getUtils(): ?string {
		return $this->utils;
	}

	/**
	 * Get list of utils used on an action as an array
	 *
	 * @return array Action's utils classes list
	 */
	public function getUtilList(): array {
		return $this->util_list;
	}
}
