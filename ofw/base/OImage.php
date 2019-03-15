<?php
class OImage{
   private $image;
   private $image_type;

   public function load($filename){
     $image_info       = getimagesize($filename);
     $this->image_type = $image_info[2];

     switch ($this->image_type){
       case IMAGETYPE_JPEG: { $this->image = imagecreatefromjpeg($filename); }
       break;
       case IMAGETYPE_GIF: {  $this->image = imagecreatefromgif($filename);  }
       break;
       case IMAGETYPE_PNG: {  $this->image = imagecreatefrompng($filename);  }
       break;
     }
   }

   public function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null){
     switch ($image_type){
       case IMAGETYPE_JPEG: { imagejpeg($this->image, $filename, $compression); }
       break;
       case IMAGETYPE_GIF: {  imagegif($this->image,  $filename); }
       break;
       case IMAGETYPE_PNG: {  imagepng($this->image,  $filename); }
       break;
     }
     if (!is_null($permissions)){
       chmod($filename, $permissions);
     }
   }

   function output($image_type=IMAGETYPE_JPEG){
     switch ($image_type){
       case IMAGETYPE_JPEG: { imagejpeg($this->image); }
       break;
       case IMAGETYPE_GIF: {  imagegif($this->image);  }
       break;
       case IMAGETYPE_PNG: {  imagepng($this->image);  }
       break;
     }
   }

   function getWidth(){
     return imagesx($this->image);
   }

   function getHeight(){
     return imagesy($this->image);
   }

   function resizeToHeight($height){
     $ratio = $height / $this->getHeight();
     $width = $this->getWidth() * $ratio;
     $this->resize($width, $height);
   }

   function resizeToWidth($width){
     $ratio  = $width / $this->getWidth();
     $height = $this->getheight() * $ratio;
     $this->resize($width, $height);
   }

   function scale($scale){
     $width  = $this->getWidth() * $scale/100;
     $height = $this->getheight() * $scale/100;
     $this->resize($width, $height);
   }

   function resize($width, $height) {
     $new_image = imagecreatetruecolor($width, $height);
     imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
     $this->image = $new_image;
   }
}