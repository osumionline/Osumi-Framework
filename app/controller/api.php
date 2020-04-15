<?php declare(strict_types=1);
class api extends OController {
	private ?userService $user_service;

	function __construct() {
		$this->user_service  = new userService();
	}

	/*
	 * Función para obtener la fecha
	 */
	function getDate($req): void {
		$this->getTemplate()->add('date', $this->user_service->getLastUpdate());
	}
}