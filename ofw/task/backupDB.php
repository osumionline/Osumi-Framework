<?php
class backupDBTask{
  public function __toString(){
    return $this->colors->getColoredString("backupDB", "light_green").": Función para crear una copia de seguridad de la base de datos.";
  }

  private $colors = null;

  function __construct(){
    $this->colors = new OColors();
  }

  public function run($silent = false){
    global $c;
    if ($c->getDB('host')=='' || $c->getDB('user')=='' || $c->getDB('pass')=='' || $c->getDB('name')==''){
    	echo "  ".$this->colors->getColoredString("No hay ninguna base de datos configurada.", "white", "red")."\n\n";
    	return false;
    }
    echo "\n";
    if (!$silent){
      echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
    }

    $dump_file = $c->getDir('ofw_export').$c->getDb('name').'.sql';
    $dir = dirname(__FILE__) . '/dump.sql';
    echo "  Exportando base de datos \"".$this->colors->getColoredString($c->getDb('name'), "light_green")."\" al archivo \"".$this->colors->getColoredString($dump_file, "light_green")."\".\n\n";
    if (file_exists($dump_file)){
      echo "    Archivo destino ya existía, se ha borrado.\n\n";
      unlink($dump_file);
    }
    $command = "mysqldump --user={$c->getDB('user')} --password={$c->getDB('pass')} --host={$c->getDB('host')} {$c->getDB('name')} --result-file={$dump_file} 2>&1";

    exec($command, $output);
    if (is_array($output) && count($output)==0){
      echo "  Base de datos exportada con éxito.\n\n";
    }
  }
}