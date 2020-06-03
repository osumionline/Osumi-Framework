<?php declare(strict_types=1);
class Tag extends OModel {
	/**
	 * Configures current model object based on data-base table structure
	 */
	function __construct() {
		$table_name  = 'tag';
		$model = [
			'id' => [
				'type'    => OCore::PK,
				'comment' => 'Unique Id for each tag'
			],
			'name' => [
				'type'     => OCore::TEXT,
				'size'     => 20,
				'nullable' => false,
				'comment'  => 'Tag name'
			],
			'id_user' => [
				'type'     => OCore::NUM,
				'nullable' => true,
				'default'  => null,
				'comment'  => 'User Id',
				'ref'      => 'user.id'
			],
			'created_at' => [
				'type'    => OCore::CREATED,
				'comment' => 'Register creation date'
			],
			'updated_at' => [
				'type'    => OCore::UPDATED,
				'comment' => 'Last date register was modified'
			]
		];

		parent::load($table_name, $model);
	}

	/**
	 * Get tag's name
	 */
	public function __toString() {
		return $this->get('name');
	}
}