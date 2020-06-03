<?php declare(strict_types=1);
class photoService extends OService {
	/**
	 * Load service tools
	 */
	function __construct() {
		$this->loadService();
	}

	/**
	 * Get given user's photo list
	 *
	 * @param int $id User's Id
	 *
	 * @return array Photo list
	 */
	public function getPhotos(int $id): array {
		$db = new ODB();
		$sql = "SELECT * FROM `photo` WHERE `id_user` = ?";
		$db->query($sql, [$id]);

		$photos = [];
		while ($res=$db->next()){
			$photo = new Photo();
			$photo->update($res);

			array_push($photos, $photo);
		}

		$this->log->debug('Photos: '.count($photos));
		return $photos;
	}
}