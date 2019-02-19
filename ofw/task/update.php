<?php
class updateTask{
  public function __toString(){
    return "update: Función para actualizar el Framework.";
  }

  private $repo_url = 'https://raw.githubusercontent.com/igorosabel/Osumi-Framework/master/';

  function doUpdate($repo_version){
    global $c;
    $updates = json_decode( file_get_contents($this->repo_url.'ofw/base/updates.json'), true );
    echo "Actualizando a versión: ".$repo_version.":\n";
    echo "  ".$updates[$repo_version]['message']."\n\n";
    $backups = [];
    $result = true;

    foreach ($updates[$repo_version]['files'] as $file){
      $file_url = $this->repo_url.$file;
      echo "Descargando ".$file_url."\n";
      $file_content = file_get_contents($file_url);

      $local_file = $c->getDir('base').$file;
      if (file_exists($local_file)){
        echo "  El archivo ya existe, creando copia de seguridad\n";
        $backup_file = $local_file.'_backup';
        rename($local_file, $backup_file);
        array_push($backups, ['new_file'=>$local_file, 'backup'=>$backup_file]);
      }
      else{
        echo "Creando nuevo archivo\n";
      }

      $result_file = file_put_contents($local_file, $file_content);
      if ($result_file===false){
        $result = false;
        break;
      }
    }

    if ($result){
      $version_file = $c->getDir('ofw_base').'VERSION';
      file_put_contents($version_file, $repo_version);
      echo "\nTodos los archivos han sido actualizados. La nueva versión instalada es: ".$repo_version."\n";
      if (count($backups)>0){
        echo "Se procede a eliminar las copias de seguridad realizadas.\n";
        foreach ($backups as $backup){
          unlink($backup['backup']);
        }
      }
    }
    else{
      echo "Ocurrió un error al actualizar los archivos, se procede a restaurar las copias de seguridad.\n";
      foreach ($backups as $backup){
        unlink($backup['new_file']);
        rename($backup['backup'], $backup['new_file']);
      }
    }
  }

  public function run(){
    $current_version = trim( Base::getVersion() );
    $repo_version = trim( file_get_contents($this->repo_url.'ofw/base/VERSION') );

    echo "Versión instalada: ".$current_version."\n";
    echo "Versión actual: ".$repo_version."\n";

    $compare = version_compare($current_version, $repo_version);

    switch ($compare){
      case -1: {
        echo "Se procede a la actualización.\n";
        $this->doUpdate($repo_version);
      }
      break;
      case 0: {
        echo "La versión instalada está actualizada.\n";
      }
      break;
      case 1: {
        echo "¡¡La versión instalada está MÁS actualizada que la del repositorio!!\n";
      }
      break;
    }
  }
}