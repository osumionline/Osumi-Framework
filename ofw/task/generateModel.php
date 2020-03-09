<?php
class generateModelTask {
	public function __toString() {
		return $this->colors->getColoredString("generateModel", "light_green").": Función para generar el script con el que crear la base de datos a partir del modelo.";
	}

	private $colors = null;

	function __construct() {
		$this->colors = new OColors();
	}

	public function run() {
		Base::generateModel();
	}
}