<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Routing;

use Attribute;

#[Attribute]
class OModule {
	private ?string $type        = null;
	private ?string $prefix      = null;
	private ?string $actions     = null;
	private array   $action_list = [];

	function __construct(
		?string $type    = null,
		?string $prefix  = null,
		?string $actions = null
	) {
		$this->type    = $type;
		$this->prefix  = $prefix;
		$this->actions = $actions;
		if  ($this->actions != '') {
			foreach (explode(',', $this->actions) as $action) {
				array_push($this->action_list, trim($action));
			}
		}
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
	 * @return string Module's actions
	 */
	public function getActions(): ?string {
		return $this->actions;
	}

	/**
	 * Get module's actions as an array
	 *
	 * @return array Module's action list
	 */
	public function getActionList(): array {
		return $this->action_list;
	}
}
