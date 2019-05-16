<?php
class updateCheckTask{
  public function __toString(){
    return $this->colors->getColoredString("updateCheck", "light_green").": Función para comprobar si existen actualizaciones del Framework.";
  }

  private $colors = null;

  function __construct(){
    $this->colors = new OColors();
  }

  private $repo_url     = 'https://raw.githubusercontent.com/igorosabel/Osumi-Framework/master/';
  private $version_file = null;

  private function getVersionFile(){
    if (is_null($this->version_file)){
      $this->version_file = json_decode( file_get_contents($this->repo_url.'ofw/base/version.json'), true );
    }
    return $this->version_file;
  }

  private function getRepoVersion(){
    $version = $this->getVersionFile();
    return $version['version'];
  }

  private function doUpdateCheck($current_version){
    global $c;
    $version = $this->getVersionFile();
    $updates = $version['updates'];

    $to_be_updated = [];
    foreach ($updates as $update_version => $update){
      if (version_compare($current_version, $update_version)==-1){
        array_push($to_be_updated, $update_version);
      }
    }
    asort($to_be_updated);
    echo "  ".$this->colors->getColoredString("Se han encontrado ".count($to_be_updated)." actualizaciones pendientes", "light_green")."\n\n";

    foreach ($to_be_updated as $repo_version){
      echo "  ".$this->colors->getColoredString($updates[$repo_version]['message'], "black", "yellow")."\n";
      echo "==============================================================================================================\n\n";

      if (array_key_exists('deletes', $updates[$repo_version]) && count($updates[$repo_version]['deletes'])>0){
        echo "  Archivos que serán eliminados:\n\n";
        foreach ($updates[$repo_version]['deletes'] as $delete){
          $local_delete = $c->getDir('base').$delete;
          echo "    \"".$delete."\"";
          if (!file_exists($local_delete)){
            echo " ".$this->colors->getColoredString("(El archivo no existe)", "white", "red");
          }
          echo "\n";
        }
        echo "\n";
      }
      if (array_key_exists('files', $updates[$repo_version]) && count($updates[$repo_version]['files'])>0){
        echo "  Archivos que serán actualizados:\n\n";
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

    echo "==============================================================================================================\n\n";
    echo "  Para proceder a la actualización ejecuta el siguiente comando:\n\n";
    echo "    ".$this->colors->getColoredString("php ofw.php update", "light_green")."\n\n";
  }

  public function run(){
    $current_version = trim( Base::getVersion() );
    $repo_version = $this->getRepoVersion();

    echo "\n";
    echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
    echo "  Versión instalada: ".$current_version."\n";
    echo "  Versión actual:    ".$repo_version."\n\n";

    $compare = version_compare($current_version, $repo_version);

    switch ($compare){
      case -1: {
        echo "  La actualización modificará los siguientes archivos:\n";
        $this->doUpdateCheck($current_version);
      }
      break;
      case 0: {
        echo "  ".$this->colors->getColoredString("La versión instalada está actualizada.", "light_green")."\n\n";
      }
      break;
      case 1: {
        echo "  ".$this->colors->getColoredString("¡¡La versión instalada está MÁS actualizada que la del repositorio!!", "white", "red")."\n\n";
      }
      break;
    }
  }
}