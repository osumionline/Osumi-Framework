<?php declare(strict_types=1);

namespace OsumiFramework\App\Model;

use OsumiFramework\OFW\DB\OModel;

class Tag extends OModel {
	/**
	 * Configures current model object based on data-base table structure
	 */
	function __construct() {
		$table_name  = 'tag';
		$model = [
			'id' => [
				'type'    => OModel::PK,
				'comment' => 'Unique Id for each tag'
			],
			'name' => [
				'type'     => OModel::TEXT,
				'size'     => 20,
				'nullable' => false,
				'comment'  => 'Tag name'
			],
			'id_user' => [
				'type'     => OModel::NUM,
				'nullable' => true,
				'default'  => null,
				'comment'  => 'User Id',
				'ref'      => 'user.id'
			],
			'created_at' => [
				'type'    => OModel::CREATED,
				'comment' => 'Register creation date'
			],
			'updated_at' => [
				'type'    => OModel::UPDATED,
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