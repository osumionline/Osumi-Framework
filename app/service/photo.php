<?php
class photoService extends OService{
	function __construct(){
		$this->loadService();
	}

	public function getPhotos($id){
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