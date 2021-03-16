<?php declare(strict_types=1);

namespace OsumiFramework\App\Model;

use OsumiFramework\OFW\DB\OModel;

class User extends OModel {
	/**
	 * Configures current model object based on data-base table structure
	 */
	function __construct() {
		$table_name  = 'user';
		$model = [
			'id' => [
				'type'    => OModel::PK,
				'comment' => 'Unique id for each user'
			],
			'user' => [
				'type'     => OModel::TEXT,
				'size'     => 50,
				'nullable' => false,
				'comment'  => 'Users name'
			],
			'pass' => [
				'type'     => OModel::TEXT,
				'size'     => 100,
				'nullable' => false,
				'comment'  => 'Users password'
			],
			'num_photos' => [
				'type'     => OModel::NUM,
				'default'  => 0,
				'nullable' => false,
				'comment'  =>'Number of photos a user has'
			],
			'score' => [
				'type'    => OModel::FLOAT,
				'comment' => 'Users score'
			],
			'active' => [
				'type'     => OModel::BOOL,
				'default'  => true,
				'nullable' => false,
				'comment'  => 'User is active 1 or not 0'
			],
			'last_login' => [
				'type'     => OModel::DATE,
				'nullable' => false,
				'comment'  => 'Last date a user signed in'
			],
			'notes' => [
				'type'    => OModel::LONGTEXT,
				'comment' => 'Notes on the user'
			],
			'created_at' => [
				'type'    => OModel::CREATED,
				'comment' => 'Register creation date'
			],
			'updated_at' => [
				'type'    => OModel::UPDATED,
				'comment' => 'Registers last update date'
			]
		];

		parent::load($table_name, $model);
	}
}