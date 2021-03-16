<?php declare(strict_types=1);

namespace OsumiFramework\App\Model;

use OsumiFramework\OFW\DB\OModel;

class PhotoTag extends OModel {
	/**
	 * Configures current model object based on data-base table structure
	 */
	function __construct() {
		$table_name  = 'photo_tag';
		$model = [
			'id_photo' => [
				'type'    => OModel::PK,
				'comment' => 'Photo Id',
				'ref'     => 'photo.id'
			],
			'id_tag' => [
				'type'    => OModel::PK,
				'comment' => 'Tag Id',
				'ref'     => 'tag.id'
			],
			'created_at' => [
				'type'    => OModel::CREATED,
				'comment' => 'Register creation date'
			]
		];

		parent::load($table_name, $model);
	}
}