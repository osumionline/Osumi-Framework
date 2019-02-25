<?php
class updateCheckTask{
  public function __toString(){
    return "updateCheck: Función para comprobar si existen actualizaciones del Framework.";
  }

  private $repo_url = 'https://raw.githubusercontent.com/igorosabel/Osumi-Framework/master/';

  function doUpdateCheck($current_version){
    global $c;
    $updates = json_decode( file_get_contents($this->repo_url.'ofw/base/updates.json'), true );

    $to_be_updated = [];
    foreach ($updates as $update_version => $update){
      if (version_compare($current_version, $update_version)==-1){
        array_push($to_be_updated, $update_version);
      }
    }
    asort($to_be_updated);
    echo "  Se han encontrado ".count($to_be_updated)." actualizaciones pendientes.\n\n";

    foreach ($to_be_updated as $repo_version){
      echo "  ".$updates[$repo_version]['message']."\n";
      echo "==============================================================================================================\n\n";

      if (array_key_exists('deletes', $updates[$repo_version]) && count($updates[$repo_version]['deletes'])>0){
        echo "  Archivos que serán eliminados:\n\n";
        foreach ($updates[$repo_version]['deletes'] as $delete){
          $local_delete = $c->getDir('base').$delete;
          if (file_exists($local_delete)){
            echo "    ".$delete."\n";
          }
        }
        echo "\n";
      }
      if (array_key_exists('files', $updates[$repo_version]) && count($updates[$repo_version]['files'])>0){
        echo "  Archivos actualizados:\n\n";
        foreach ($updates[$repo_version]['files'] as $file){
          $local_file = $c->getDir('base').$file;
          if (file_exists($local_file)){
            echo "    Actualización: \"".$file."\"\n";
          }
          else{
            echo "    Nuevo: \"".$file."\"\n";
          }
        }
        echo "\n";
      }
    }

    echo "  Para proceder a la actualización ejecuta el siguiente comando:\n\n";
    echo "    php ofw.php update\n\n";
  }

  public function run(){
    $current_version = trim( Base::getVersion() );
    $repo_version = trim( file_get_contents($this->repo_url.'ofw/base/VERSION') );

    echo "\n";
    echo "  Osumi Framework\n";
    echo "  Versión instalada: ".$current_version."\n";
    echo "  Versión actual: ".$repo_version."\n";

    $compare = version_compare($current_version, $repo_version);

    switch ($compare){
      case -1: {
        echo "  La actualización modificará los siguientes archivos:\n";
        $this->doUpdateCheck($current_version);
      }
      break;
      case 0: {
        echo "  La versión instalada está actualizada.\n\n";
      }
      break;
      case 1: {
        echo "  ¡¡La versión instalada está MÁS actualizada que la del repositorio!!\n\n";
      }
      break;
    }
  }
}