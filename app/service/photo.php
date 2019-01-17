<?php
class photoService extends OService{
  function __construct($controller){
    $this->setController($controller);
  }

  public function getPhotos($id){
    $db = $this->getController()->getDB();
    $sql = sprintf("SELECT * FROM `photo` WHERE `id_user` = %s", $id);
    $db->query($sql);
    
    $photos = [];
    while ($res=$db->next()){
      $photo = new Photo();
      $photo->update($res);
      
      array_push($photos, $photo);
    }
    
    return $photos;
  }
}