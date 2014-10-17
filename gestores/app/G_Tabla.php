<?php
class G_Tabla extends G_Base{
  function __construct(){
    $gestor = 'G_Tabla';
    $tablename = 'tabla';
    $model = array(
        'id'         => array('type'=>Base::PK,      'com'=>'Clave primaria'),
        'num'        => array('type'=>Base::NUM,     'com'=>'Campo numérico'),
        'texto'      => array('type'=>Base::TEXTO,   'len'=>100, 'com'=>'Campo de texto, si len > 255 sera TEXT, sino VARCHAR'),
        'fecha'      => array('type'=>Base::FECHA,   'com'=>'Campo de fecha'),
        'booleano'   => array('type'=>Base::BOOL,    'com'=>'Campo booleano, true/false'),
        'created_at' => array('type'=>Base::CREATED, 'com'=>'Fecha de creación del registro'),
        'updated_at' => array('type'=>Base::UPDATED, 'com'=>'Fecha de última modificación del registro')
    );

    parent::load($gestor,$tablename,$model);
  }
}