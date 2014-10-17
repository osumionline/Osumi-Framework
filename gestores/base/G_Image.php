<?php
class G_Image{
  private $BD;
  private $modo_debug  = false;
  private $l           = null;
  private $ruta_tmp    = '';
  private $ruta_img    = '';
  private $ruta_thumb  = '';
  private $image_types = array();
  private $img_id      = '';
  private $si          = null;

  function G_Image() {
    global $c, $where;
    $this->BD=new G_BBDD();
    $this->setModoDebug($c->getModoDebug());
    
    $l = new G_Log();
    $this->setLog($l);
    $this->getLog()->setPagina($where);
    $this->getLog()->setGestor('G_Image');
    
    $this->setRutaTmp($c->getRutaTmp());
    $this->setRutaImg($c->getRutaImagenes());
    $this->setRutaThumb($c->getRutaThumbs());
    $this->setImageTypes($c->getImageTypes());
    
    $si = new SimpleImage();
    $this->setSi($si);
	}
	
	public function setModoDebug($md){
    $this->modo_debug = $md;
	}
	
	public function getModoDebug(){
    return $this->modo_debug;
	}
	
	public function setLog($l){
    $this->l = $l;
	}
	
	public function getLog(){
    return $this->l;
	}
	
	public function setRutaTmp($rt){
    $this->ruta_tmp = $rt;
	}
	
	public function getRutaTmp(){
    return $this->ruta_tmp;
	}
	
	public function setRutaImg($ri){
    $this->ruta_img = $ri;
	}
	
	public function getRutaImg(){
    return $this->ruta_img;
	}
	
	public function setRutaThumb($rt){
    $this->ruta_thumb = $rt;
	}
	
	public function getRutaThumb(){
    return $this->ruta_thumb;
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
	
	public function setSi($si){
    $this->si = $si;
	}
	
	public function getSi(){
    return $this->si;
	}

  public function copyImg($image, $imgId)
	{
		$tmpfile = tempnam($this->getRutaTmp(), 'import');
		$imageTypes = $this->getImageTypes();

		if (@copy($image, $tmpfile))
		{
		  // Cargo la imagen
		  $this->getSi()->load($tmpfile);
		  
		  $size = getimagesize($tmpfile);
		  
		  // Creo thumbs
    	foreach ($imageTypes as $imageType){
        if ($size[0] > $size[1]){
          $this->getSi()->resizeToWidth($imageType['width']);
        }
        else{
          $this->getSi()->resizeToHeight($imageType['height']);
        }
        $this->getSi()->save($this->getRutaThumb().$imgId.'-'.$imageType['name'].'.jpg');
      }
      
      // Copio el original
      copy($image, $this->getRutaImg().$imgId.'.jpg');
    }
    @unlink($tmpfile);
	}
	
	public function borrar($id=null)
	{
    if (!is_null($id)){
      $this->setImgId($id);
    }
    
    $imageTypes = $this->getImageTypes();
    
    // Borro thumbs
    foreach ($imageTypes as $imageType){
      $route_img = $this->getRutaThumb().$this->getImgId().'-'.$imageType['name'].'.jpg';
      if ($this->getModoDebug()){
        $this->getLog()->putLog('Borro imagen thumb '.$route_img);
      }
      unlink($route_img);
    }
    // Borro el original
    $route_img = $this->getRutaImg().$this->getImgId().'.jpg';
    if ($this->getModoDebug()){
      $this->getLog()->putLog('Borro imagen original'.$route_img);
    }
    unlink($route_img);
    
    // Borro la foto de BD
    $sql = "DELETE FROM `photo` WHERE `id` = '".$this->getImgId()."'";
        
    if ($this->getModoDebug()){
      $this->getLog()->putLog($sql);
    }
    $this->BD->consulta($sql);
	}
	
	public static function generateUrl($id,$type='full'){
    global $c;
    
    if ($type == 'full'){
      return '/'.str_replace($c->getRutaWeb(),'',$c->getRutaImagenes().$id.'.jpg');
    }
    else{
      return '/'.str_replace($c->getRutaWeb(),'',$c->getRutaThumbs().$id.'-'.$type.'.jpg');
    }
	}
}