<?php
class updateTask {
	public function __toString() {
		return $this->colors->getColoredString("update", "light_green").": Función para actualizar el Framework.";
	}

	private $colors = null;

	function __construct() {
		$this->colors = new OColors();
	}

	private $repo_url = 'https://raw.githubusercontent.com/igorosabel/Osumi-Framework/';
	private $version_file = null;

	private function getVersionFile() {
		if (is_null($this->version_file)){
			$this->version_file = json_decode( file_get_contents($this->repo_url.'master/ofw/base/version.json'), true );
		}
		return $this->version_file;
	}

	private function getRepoVersion() {
		$version = $this->getVersionFile();
		return $version['version'];
	}

	function doUpdate($current_version) {
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
		echo "  ".$this->colors->getColoredString("Se han encontrado ".count($to_be_updated)." actualizaciones pendientes. Se procede a la instalación ordenada.", "light_green")."\n\n";

		foreach ($to_be_updated as $repo_version){
			$backups = [];
			$result = true;
			echo "  ".$this->colors->getColoredString($updates[$repo_version]['message'], "black", "yellow")."\n";
			echo "==============================================================================================================\n";

			if (array_key_exists('deletes', $updates[$repo_version]) && count($updates[$repo_version]['deletes'])>0){
				foreach ($updates[$repo_version]['deletes'] as $delete){
					$local_delete = $c->getDir('base').$delete;
					if (file_exists($local_delete)){
						echo " El archivo \"".$delete."\" será eliminado.\n";
						$backup_file = $local_delete.'_backup';
						rename($local_delete, $backup_file);
						array_push($backups, ['new_file'=>$local_delete, 'backup'=>$backup_file]);
					}
				}
				echo "\n";
			}
			if (array_key_exists('files', $updates[$repo_version]) && count($updates[$repo_version]['files'])>0){
				foreach ($updates[$repo_version]['files'] as $file){
					$file_url = $this->repo_url.'v'.$repo_version.'/'.$file;
					echo "  Descargando \"".$file_url."\"\n";
					$file_content = file_get_contents($file_url);

					$local_file = $c->getDir('base').$file;
					if (file_exists($local_file)){
						echo "    El archivo ya existe, creando copia de seguridad.\n";
						$backup_file = $local_file.'_backup';
						rename($local_file, $backup_file);
						array_push($backups, ['new_file'=>$local_file, 'backup'=>$backup_file]);
					}
					else{
						echo "    Creando nuevo archivo.\n";
					}

					$dir = dirname($local_file);
					if (!file_exists($dir)){
						mkdir($dir, 0777, true);
					}

					$result_file = file_put_contents($local_file, $file_content);
					if ($result_file===false){
						$result = false;
						break;
					}
				}
			}
			echo "==============================================================================================================\n";

			if ($result){
				echo "\n  ".$this->colors->getColoredString("Todos los archivos han sido actualizados. La nueva versión instalada es: ".$repo_version, "light_green")."\n";
				if (count($backups)>0){
					echo "  Se procede a eliminar las copias de seguridad realizadas.\n";
					foreach ($backups as $backup){
						unlink($backup['backup']);
					}
				}
			}
			else{
				echo "  ".$this->colors->getColoredString("Ocurrió un error al actualizar los archivos, se procede a restaurar las copias de seguridad.", "white", "red")."\n";
				foreach ($backups as $backup){
					if (file_exists($backup['new_file'])){
						unlink($backup['new_file']);
					}
					rename($backup['backup'], $backup['new_file']);
				}
			}
			echo "\n";
		}
	}

	public function run() {
		$current_version = trim( Base::getVersion() );
		$repo_version = $this->getRepoVersion();

		echo "\n";
		echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
		echo "  Versión instalada: ".$current_version."\n";
		echo "  Versión actual:    ".$repo_version."\n\n";

		$compare = version_compare($current_version, $repo_version);

		switch ($compare){
			case -1: {
				echo "  Se procede a la actualización.\n";
				$this->doUpdate($current_version);
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