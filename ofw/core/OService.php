<?php
/**
 * OService - Base class for the service classes providing access to the controller it was invoked from
 */
class OService {
	protected $controller = null;

	/**
	 * Set the controller from wich the service was loaded (circular)
	 *
	 * @param OController Controller from wich the service was loaded
	 *
	 * @return void
	 */
	public final function setController($controller) {
		$this->controller = $controller;
	}

	/**
	 * Get the controller from wich the service was loaded
	 *
	 * @return OController Controller from wich the service was loaded
	 */
	public final function getController() {
		return $this->controller;
	}
}