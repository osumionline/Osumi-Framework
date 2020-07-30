<?php declare(strict_types=1);
class User extends OModel {
	/**
	 * Configures current model object based on data-base table structure
	 */
	function __construct() {
		$table_name  = 'user';
		$model = [
			'id' => [
				'type'    => OCore::PK,
				'comment' => 'Unique id for each user'
			],
			'user' => [
				'type'     => OCore::TEXT,
				'size'     => 50,
				'nullable' => false,
				'comment'  => 'Users name'
			],
			'pass' => [
				'type'     => OCore::TEXT,
				'size'     => 100,
				'nullable' => false,
				'comment'  => 'Users password'
			],
			'num_photos' => [
				'type'     => OCore::NUM,
				'default'  => 0,
				'nullable' => false,
				'comment'  =>'Number of photos a user has'
			],
			'score' => [
				'type'    => OCore::FLOAT,
				'comment' => 'Users score'
			],
			'active' => [
				'type'     => OCore::BOOL,
				'default'  => true,
				'nullable' => false,
				'comment'  => 'User is active 1 or not 0'
			],
			'last_login' => [
				'type'     => OCore::DATE,
				'nullable' => false,
				'comment'  => 'Last date a user signed in'
			],
			'notes' => [
				'type'    => OCore::LONGTEXT,
				'comment' => 'Notes on the user'
			],
			'created_at' => [
				'type'    => OCore::CREATED,
				'comment' => 'Register creation date'
			],
			'updated_at' => [
				'type'    => OCore::UPDATED,
				'comment' => 'Registers last update date'
			]
		];

		parent::load($table_name, $model);
	}
}