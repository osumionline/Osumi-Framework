<?php declare(strict_types=1);
class Tag extends OModel {
	function __construct() {
		$table_name  = 'tag';
		$model = [
			'id' => [
				'type'    => OCore::PK,
				'comment' => 'Id único de cada tag'
			],
			'name' => [
				'type'     => OCore::TEXT,
				'size'     => 20,
				'nullable' => false,
				'comment'  => 'Nombre de la tag'
			],
			'id_user' => [
				'type'     => OCore::NUM,
				'nullable' => true,
				'default'  => null,
				'comment'  => 'Id del usuario',
				'ref'      => 'user.id'
			],
			'created_at' => [
				'type'    => OCore::CREATED,
				'comment' => 'Fecha de creación del registro'
			],
			'updated_at' => [
				'type'    => OCore::UPDATED,
				'comment' => 'Fecha de última modificación del registro'
			]
		];

		parent::load($table_name, $model);
	}

	public function __toString() {
		return $this->get('name');
	}
}