<?php
class PhotoTag extends OModel{
	function __construct(){
		$table_name  = 'photo_tag';
		$model = [
			'id_photo' => [
				'type'    => OCore::PK,
				'comment' => 'Id de la foto',
				'ref'     => 'photo.id'
			],
			'id_tag' => [
				'type'    => OCore::PK,
				'comment' => 'Id de la tag',
				'ref'     => 'tag.id'
			],
			'created_at' => [
				'type'    => OCore::CREATED,
				'comment' => 'Fecha de creación del registro'
			]
		];

		parent::load($table_name, $model);
	}
}