<?php
class Photo extends OBase{
  function __construct(){
    $model_name = get_class($this);
    $table_name  = 'photo';
    $model = [
        'id' => [
          'type'    => Base::PK,
          'comment' => 'Id único de cada foto'
        ],
        'id_user' => [
          'type'    => Base::NUM,
          'comment' => 'Id del usuario',
          'ref'     => 'user.id'
        ],
        'ext' => [
          'type'    => Base::TEXT,
          'size'    => 5,
          'comment' => 'Extensión de la foto'
        ],
        'created_at' => [
          'type'    => Base::CREATED,
          'comment' => 'Fecha de creación del registro'
        ],
        'updated_at' => [
          'type'    => Base::UPDATED,
          'comment' => 'Fecha de última modificación del registro'
        ]
    ];

    parent::load($model_name, $table_name, $model);
  }
  
  public function __toString(){
    return $this->get('id').'.'.$this->get('ext');
  }
  
  private $tags = null;
  
  public function setTags($tags){
    $this->tags = $tags;
  }
  
  public function getTags(){
    if (is_null($this->tags)){
      $this->loadTags();
    }
    return $this->tags;
  }
  
  private function loadTags(){
    $list = [];
    $sql = sprintf( "SELECT * FROM `tag` WHERE `id` IN (SELECT `id_tag` FROM `photo_tag` WHERE `id_photo` = %s)", $this->get('id') );
    $this->db->query($sql);

    while($res=$this->db->next()){
      $tag = new Tag();
      $tag->update($res);

      array_push($list, $tag);
    }

    $this->tags = $list;
  }
}
