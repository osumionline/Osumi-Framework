<?php declare(strict_types=1);

namespace OsumiFramework\App\Model;

use OsumiFramework\OFW\DB\OModel;

class Photo extends OModel {
	/**
	 * Configures current model object based on data-base table structure
	 */
	function __construct() {
		$table_name  = 'photo';
		$model = [
			'id' => [
				'type'    => OModel::PK,
				'comment' => 'Unique Id for each photo'
			],
			'id_user' => [
				'type'    => OModel::NUM,
				'comment' => 'User Id',
				'ref'     => 'user.id'
			],
			'ext' => [
				'type'    => OModel::TEXT,
				'size'    => 5,
				'comment' => 'Photo extension'
			],
			'alt' => [
				'type'    => OModel::TEXT,
				'size'    => 100,
				'comment' => 'alt text for the photo'
			],
			'url' => [
				'type'    => OModel::TEXT,
				'size'    => 100,
				'comment' => 'URL for the photo'
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
	 * Get photo's full name
	 */
	public function __toString() {
		return $this->get('id').'.'.$this->get('ext');
	}

	private ?array $tags = null;

	/**
	 * Save photo's tag list
	 *
	 * @param array $tags Tag list
	 *
	 * @return void
	 */
	public function setTags(array $tags): void {
		$this->tags = $tags;
	}

	/**
	 * Get photo's tag list
	 *
	 * @return array Photo's tag list
	 */
	public function getTags(): array {
		if (is_null($this->tags)) {
			$this->loadTags();
		}
		return $this->tags;
	}

	/**
	 * Load photo's tag list
	 *
	 * @return void
	 */
	private function loadTags(): void {
		$list = [];
		$sql = "SELECT * FROM `tag` WHERE `id` IN (SELECT `id_tag` FROM `photo_tag` WHERE `id_photo` = ?)";
		$this->db->query($sql, [$this->get('id')]);

		while($res=$this->db->next()) {
			$tag = new Tag();
			$tag->update($res);

			array_push($list, $tag);
		}

		$this->tags = $list;
	}
}