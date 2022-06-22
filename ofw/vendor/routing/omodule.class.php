<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Routing;

use Attribute;

#[Attribute]
class OModule {
	private ?string $type        = null;
	private ?string $prefix      = null;
	private array   $actions     = [];

	function __construct(
		?string $type    = null,
		?string $prefix  = null,
		array   $actions = []
	) {
		$this->type    = $type;
		$this->prefix  = $prefix;
		$this->actions = $actions;
	}

	/**
	 * Get module's response type
	 *
	 * @return ?string Module's response type
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
	 * Get module's actions
	 *
	 * @return array Module's actions
	 */
	public function getActions(): array {
		return $this->actions;
	}
}
