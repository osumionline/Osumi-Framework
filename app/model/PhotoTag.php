<?php
class PhotoTag extends OBase{
  function __construct(){
    $model_name = get_class($this);
    $table_name  = 'photo_tag';
    $model = [
        'id_photo' => [
          'type'    => Base::PK,
          'comment' => 'Id de la foto',
          'ref'     => 'photo.id'
        ],
        'id_tag' => [
          'type'    => Base::PK,
          'comment' => 'Id de la tag',
          'ref'     => 'tag.id'
        ],
        'created_at' => [
          'type'    => Base::CREATED,
          'comment' => 'Fecha de creación del registro'
        ]
    ];

    parent::load($model_name, $table_name, $model);
  }
}
