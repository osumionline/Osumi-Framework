<?php
class User extends OBase{
  function __construct(){
    $table_name  = 'user';
    $model = [
        'id' => [
          'type'    => Base::PK,
          'comment' => 'Id único de un usuario'
        ],
        'user' => [
          'type'     => Base::TEXT,
          'size'     => 50,
          'nullable' => false,
          'comment'  => 'Nombre de usuario'
        ],
        'pass' => [
          'type'     => Base::TEXT,
          'size'     => 100,
          'nullable' => false,
          'comment'  => 'Contraseña del usuario'
        ],
        'num_photos' => [
          'type'     => Base::NUM,
          'default'  => 0,
          'nullable' => false,
          'comment'  =>'Número de fotos de un usuario'
        ],
        'score' => [
          'type'    => Base::FLOAT,
          'comment' => 'Puntuación del usuario'
        ],
        'active' => [
          'type'     => Base::BOOL,
          'default'  => true,
          'nullable' => false,
          'comment'  => 'Usuario activo 1 o no 0'
        ],
        'last_login' => [
          'type'     => Base::DATE,
          'nullable' => false,
          'comment'  => 'Fecha de la última vez que inició sesión'
        ],
        'notes' => [
          'type'    => Base::LONGTEXT,
          'comment' => 'Notas sobre el usuario'
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

    parent::load($table_name, $model);
  }
}