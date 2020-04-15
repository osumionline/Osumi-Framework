<?php declare(strict_types=1);
class User extends OModel {
	function __construct() {
		$table_name  = 'user';
		$model = [
			'id' => [
				'type'    => OCore::PK,
				'comment' => 'Id único de un usuario'
			],
			'user' => [
				'type'     => OCore::TEXT,
				'size'     => 50,
				'nullable' => false,
				'comment'  => 'Nombre de usuario'
			],
			'pass' => [
				'type'     => OCore::TEXT,
				'size'     => 100,
				'nullable' => false,
				'comment'  => 'Contraseña del usuario'
			],
			'num_photos' => [
				'type'     => OCore::NUM,
				'default'  => 0,
				'nullable' => false,
				'comment'  =>'Número de fotos de un usuario'
			],
			'score' => [
				'type'    => OCore::FLOAT,
				'comment' => 'Puntuación del usuario'
			],
			'active' => [
				'type'     => OCore::BOOL,
				'default'  => true,
				'nullable' => false,
				'comment'  => 'Usuario activo 1 o no 0'
			],
			'last_login' => [
				'type'     => OCore::DATE,
				'nullable' => false,
				'comment'  => 'Fecha de la última vez que inició sesión'
			],
			'notes' => [
				'type'    => OCore::LONGTEXT,
				'comment' => 'Notas sobre el usuario'
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
}