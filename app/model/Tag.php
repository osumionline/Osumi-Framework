<?php
class Tag extends OBase{
  function __construct(){
    $model_name = get_class($this);
    $table_name  = 'tag';
    $model = [
        'id' => [
          'type'    => Base::PK,
          'comment' => 'Id único de cada tag'
        ],
        'name' => [
          'type'     => Base::TEXT,
          'size'     => 20,
          'nullable' => false,
          'comment'  => 'Nombre de la tag'
        ],
        'id_user' => [
          'type'     => Base::NUM,
          'nullable' => true,
          'default'  => null,
          'comment'  => 'Id del usuario',
          'ref'      => 'user.id'
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
    return $this->get('name');
  }
}
