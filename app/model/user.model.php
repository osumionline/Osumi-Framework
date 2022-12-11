<?php declare(strict_types=1);

namespace OsumiFramework\App\Model;

use OsumiFramework\OFW\DB\OModel;
use OsumiFramework\OFW\DB\OModelGroup;
use OsumiFramework\OFW\DB\OModelField;

class User extends OModel {
	/**
	 * Configures current model object based on data-base table structure
	 */
	function __construct() {
		$model = new OModelGroup(
			new OModelField(
				name: 'id',
				type: OMODEL_PK,
				comment: 'Unique id for each user'
			),
			new OModelField(
				name: 'user',
				type: OMODEL_TEXT,
				nullable: false,
				comment: 'Users name'
			),
			new OModelField(
				name: 'pass',
				type: OMODEL_TEXT,
				size: 100,
				nullable: false,
				comment: 'Users password'
			),
			new OModelField(
				name: 'num_photos',
				type: OMODEL_NUM,
				default: 0,
				nullable: false,
				comment: 'Number of photos a user has'
			),
			new OModelField(
				name: 'score',
				type: OMODEL_FLOAT,
				nullable: false,
				default: 0,
				comment: 'Users score'
			),
			new OModelField(
				name: 'active',
				type: OMODEL_BOOL,
				default: true,
				nullable: false,
				comment: 'User is active 1 or not 0'
			),
			new OModelField(
				name: 'last_login',
				type: OMODEL_DATE,
				nullable: false,
				comment: 'Last date a user signed in'
			),
			new OModelField(
				name: 'notes',
				type: OMODEL_LONGTEXT,
				comment: 'Notes on the user'
			),
			new OModelField(
				name: 'created_at',
				type: OMODEL_CREATED,
				comment: 'Register creation date'
			),
			new OModelField(
				name: 'updated_at',
				type: OMODEL_UPDATED,
				comment: 'Registers last update date'
			)
		);

		parent::load($model);
	}
}
