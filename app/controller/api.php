<?php declare(strict_types=1);
class api extends OController {
	private ?userService $user_service;

	function __construct() {
		$this->user_service  = new userService();
	}

	/**
	 * Función para obtener la fecha
	 *
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 *
	 * @return void
	 */
	function getDate(ORequest $req): void {
		$this->getTemplate()->add('date', $this->user_service->getLastUpdate());
	}
}