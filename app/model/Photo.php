<?php declare(strict_types=1);
class Photo extends OModel {
	function __construct() {
		$table_name  = 'photo';
		$model = [
			'id' => [
				'type'    => OCore::PK,
				'comment' => 'Id único de cada foto'
			],
			'id_user' => [
				'type'    => OCore::NUM,
				'comment' => 'Id del usuario',
				'ref'     => 'user.id'
			],
			'ext' => [
				'type'    => OCore::TEXT,
				'size'    => 5,
				'comment' => 'Extensión de la foto'
			],
			'created_at' => [
				'type'    => OCore::CREATED,
				'comment' => 'Fecha de creación del registro'
			],
			'updated_at' => [
				'type'    => OCore::UPDATED,
				'comment' => 'Fecha de última modificación del registro'
			]
		];

		parent::load($table_name, $model);
	}

	public function __toString() {
		return $this->get('id').'.'.$this->get('ext');
	}

	private ?array $tags = null;

	public function setTags(array $tags): void {
		$this->tags = $tags;
	}

	public function getTags(): array {
		if (is_null($this->tags)) {
			$this->loadTags();
		}
		return $this->tags;
	}

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