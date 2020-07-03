<?php declare(strict_types=1);
/**
 * Módulo API de prueba
 *
 * @type json
 * @prefix /api
 */
class api extends OModule {
	private ?userService $user_service;

	function __construct() {
		$this->user_service  = new userService();
	}

	/**
	 * Función para obtener la fecha
	 *
	 * @url /getDate
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 * @return void
	 */
	public function getDate(ORequest $req): void {
		$this->getTemplate()->add('date', $this->user_service->getLastUpdate());
	}
}