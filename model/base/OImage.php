<?php
class OImage{
  private $db;
  private $debug_mode  = false;
  private $l           = null;
  private $tmp_dir     = '';
  private $img_dir     = '';
  private $thumb_dir   = '';
  private $image_types = array();
  private $img_id      = '';
  private $si          = null;

  function __construct() {
    global $c, $where;
    $this->db=new ODB();
    $this->setDebugMode($c->getDebugMode());
    
    $l = new OLog();
    $this->setLog($l);
    $this->getLog()->setSection($where);
    $this->getLog()->setModel('OImage');
    
    $this->setTmpDir($c->getDir('tmp'));
    $this->setImgDir($c->getDir('img'));
    $this->setThumbDir($c->getDir('thumb'));
    $this->setImageTypes($c->getImageTypes());
    
    $si = new SimpleImage();
    $this->setSI($si);
	}
	
	public function setDebugMode($dm){
    $this->debug_mode = $dm;
	}
	public function getDebugMode(){
    return $this->debug_mode;
	}
	
	public function setLog($l){
    $this->l = $l;
	}
	public function getLog(){
    return $this->l;
	}
	
	public function setTmpDir($td){
    $this->tmp_dir = $td;
	}
	public function getTmpDir(){
    return $this->tmp_dir;
	}
	
	public function setImgDir($id){
    $this->img_dir = $id;
	}
	public function getImgDir(){
    return $this->img_dir;
	}
	
	public function setThumbDir($td){
    $this->thumb_dir = $td;
	}
	public function getThumbDir(){
    return $this->thumb_dir;
	}
	
	public function setImageTypes($it){
    $this->image_types = $it;
	}
	public function getImageTypes(){
    return $this->image_types;
	}
	
	public function setImgId($ii){
    $this->img_id = $ii;
	}
	public function getImgId(){
    return $this->img_id;
	}
	
	public function setSI($si){
    $this->si = $si;
	}
	public function getSI(){
    return $this->si;
	}

  public function copyImg($image, $imgId){
		$tmpfile = tempnam($this->getTmpDir(), 'import');
		$imageTypes = $this->getImageTypes();

		if (@copy($image, $tmpfile))
		{
		  // Cargo la imagen
		  $this->getSI()->load($tmpfile);
		  
		  $size = getimagesize($tmpfile);
		  
		  // Creo thumbs
    	foreach ($imageTypes as $imageType){
        if ($size[0] > $size[1]){
          $this->getSI()->resizeToWidth($imageType['width']);
        }
        else{
          $this->getSI()->resizeToHeight($imageType['height']);
        }
        $this->getSI()->save($this->getThumbDir().$imgId.'-'.$imageType['name'].'.jpg');
      }
      
      // Copio el original
      copy($image, $this->getImgDir().$imgId.'.jpg');
    }
    @unlink($tmpfile);
	}
	
	public function delete($id=null){
    if (!is_null($id)){
      $this->setImgId($id);
    }
    
    $imageTypes = $this->getImageTypes();
    
    // Borro thumbs
    foreach ($imageTypes as $imageType){
      $route_img = $this->getThumbDir().$this->getImgId().'-'.$imageType['name'].'.jpg';
      if ($this->getDebugMode()){
        $this->getLog()->putLog('Borro imagen thumb '.$route_img);
      }
      unlink($route_img);
    }
    // Borro el original
    $route_img = $this->getImgDir().$this->getImgId().'.jpg';
    if ($this->getDebugMode()){
      $this->getLog()->putLog('Borro imagen original'.$route_img);
    }
    unlink($route_img);
    
    // Borro la foto de BD
    $sql = "DELETE FROM `photo` WHERE `id` = ".$this->getImgId();
        
    if ($this->getDebugMode()){
      $this->getLog()->putLog($sql);
    }
    $this->db->query($sql);
	}
	
	public static function generateUrl($id,$type='full'){
    global $c;
    
    if ($type == 'full'){
      return '/'.str_replace($c->getDir('web'),'',$c->getDir('img').$id.'.jpg');
    }
    else{
      return '/'.str_replace($c->getDir('web'),'',$c->getDir('thumb').$id.'-'.$type.'.jpg');
    }
	}
}