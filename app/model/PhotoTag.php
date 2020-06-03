<?php declare(strict_types=1);
class PhotoTag extends OModel {
	/**
	 * Configures current model object based on data-base table structure
	 */
	function __construct() {
		$table_name  = 'photo_tag';
		$model = [
			'id_photo' => [
				'type'    => OCore::PK,
				'comment' => 'Photo Id',
				'ref'     => 'photo.id'
			],
			'id_tag' => [
				'type'    => OCore::PK,
				'comment' => 'Tag Id',
				'ref'     => 'tag.id'
			],
			'created_at' => [
				'type'    => OCore::CREATED,
				'comment' => 'Register creation date'
			]
		];

		parent::load($table_name, $model);
	}
}