<?php
class api extends OController{
	private $user_service;

	function __construct(){
		$this->user_service  = new userService($this);
	}

	/*
	 * Función para obtener la fecha
	 */
	function getDate($req){
		$this->getTemplate()->add('date', $this->user_service->getLastUpdate());
	}
}