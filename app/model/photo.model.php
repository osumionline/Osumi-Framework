<?php declare(strict_types=1);

namespace OsumiFramework\App\Model;

use OsumiFramework\OFW\DB\OModel;
use OsumiFramework\OFW\DB\OModelGroup;
use OsumiFramework\OFW\DB\OModelField;

class Photo extends OModel {
	/**
	 * Configures current model object based on data-base table structure
	 */
	function __construct() {
		$model = new OModelGroup(
			new OModelField(
				name: 'id',
				type: OMODEL_PK,
				comment: 'Unique Id for each photo'
			),
			new OModelField(
				name: 'id_user',
				type: OMODEL_NUM,
				comment: 'User Id',
				ref: 'user.id'
			),
			new OModelField(
				name: 'ext',
				type: OMODEL_TEXT,
				size: 5,
				nullable: false,
				default: '',
				comment: 'Photo extension'
			),
			new OModelField(
				name: 'alt',
				type: OMODEL_TEXT,
				size: 100,
				nullable: false,
				comment: 'alt text for the photo'
			),
			new OModelField(
				name: 'url',
				type: OMODEL_TEXT,
				size: 100,
				nullable: false,
				comment: 'URL for the photo'
			),
			new OModelField(
				name: 'created_at',
				type: OMODEL_CREATED,
				comment: 'Register creation date'
			),
			new OModelField(
				name: 'updated_at',
				type: OMODEL_UPDATED,
				comment: 'Last date register was modified'
			)
		);

		parent::load($model);
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
