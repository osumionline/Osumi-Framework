<?php
class pluginsTask{
  public function __toString(){
    return $this->colors->getColoredString("plugins", "light_green").": Función para obtener el listado de plugins disponibles.";
  }

  private $config = null;
  private $colors = null;

  function __construct(){
    global $c;
    $this->config = $c;
    $this->colors = new OColors();
  }

  private $plugins_file = null;
  private $repo_url = 'https://raw.githubusercontent.com/igorosabel/Osumi-Plugins/';

  private function getPluginsFile(){
    if (is_null($this->plugins_file)){
      $this->plugins_file = json_decode( file_get_contents($this->repo_url.'master/plugins.json'), true );
    }
    return $this->plugins_file;
  }

  public function availablePlugins(){
    $plugins = $this->getPluginsFile();
    echo "  Listado de plugins disponibles:\n\n";

    foreach ($plugins['plugins'] as $plugin){
      echo "  · ".$this->colors->getColoredString($plugin['name'], "light_green")." (".$plugin['version']."): ".$plugin['description']."\n";
    }

    echo "\n\n";
    echo "  Para instalar cualquiera de estos plugins ejecuta el siguiente comando:\n\n";
    echo "      ".$this->colors->getColoredString("php ofw.php plugins install (nombre)", "light_green")."\n\n";
    echo "  También puedes ver el listado de plugins que tienes actualmente instalados ejecutando el siguiente comando:\n\n";
    echo "      ".$this->colors->getColoredString("php ofw.php plugins list", "light_green")."\n\n";
    echo "  Para borrar un plugin instalado actualmente ejecuta el siguiente comando:\n\n";
    echo "      ".$this->colors->getColoredString("php ofw.php plugins remove (nombre)", "light_green")."\n\n";
  }

  public function installPlugin($params){
    if (count($params)<2){
      echo "  ".$this->colors->getColoredString("ERROR", "red").": Debes indicar el nombre del plugin que quieres instalar, por ejemplo:\n\n";
      echo "      ".$this->colors->getColoredString("php ofw.php plugins install email", "light_green")."\n\n\n";
      exit;
    }

    $plugins = $this->getPluginsFile();
    $found = null;
    foreach ($plugins['plugins'] as $p){
      if ($p['name']==$params[1]){
        $found = $p;
        break;
      }
    }
    if (is_null($found)){
      echo "  ".$this->colors->getColoredString("ERROR", "red").": El plugin indicado no existe en la lista de plugins instalables.\n\n";
      echo "  Comprueba la lista ejecutando el siguiente comando:\n\n";
      echo "      ".$this->colors->getColoredString("php ofw.php plugins", "light_green")."\n\n\n";
      exit;
    }

    $plugin = new OPlugin($params[1]);
    $plugins_file = $this->config->getDir('app_config').'plugins.json';
    if (file_exists($plugins_file)){
      $plugins_list = json_decode( file_get_contents($plugins_file), true );
    }
    else{
      $plugins_list = ['plugins'=>[]];
    }

    array_push($plugins_list['plugins'], $params[1]);

    $new_plugin_route = $this->config->getDir('ofw_plugins').$plugin->getName();
    if (file_exists($new_plugin_route)){
      echo "  ".$this->colors->getColoredString("ERROR", "red").": La carpeta \"".$new_plugin_route."\" ya existe.\n\n";
      exit;
    }

    // Creo carpeta para el plugin
    mkdir($new_plugin_route);
    echo "  Nueva carpeta creada: \"".$new_plugin_route."\"\n";

    // Obtengo datos del plugin
    $plugin_repo = $this->repo_url.'master/'.$plugin->getName().'/'.$plugin->getName().'.json';
    $plugin_config_file = file_get_contents($plugin_repo);
    $repo_data = json_decode( $plugin_config_file, true);
    file_put_contents($new_plugin_route.'/'.$plugin->getName().'.json', $plugin_config_file);
    echo "  Creado archivo de configuración del plugin: \"".$new_plugin_route."/".$plugin->getName().".json\"\n";

    // Archivo del plugin
    $plugin_file = file_get_contents($this->repo_url.'master/'.$plugin->getName().'/'.$repo_data['file_name']);
    file_put_contents($new_plugin_route.'/'.$repo_data['file_name'], $plugin_file);
    echo "  Creado archivo del plugin: \"".$new_plugin_route."/".$repo_data['file_name']."\"\n";

    // Dependencias
    if (array_key_exists('dependencies', $repo_data)){
      echo "  Descargando dependencias:\n";
      mkdir($new_plugin_route.'/dependencies');
      foreach ($repo_data['dependencies'] as $dep){
        $dep_file = file_get_contents($this->repo_url.'master/'.$plugin->getName().'/'.$dep);
        file_put_contents($new_plugin_route.'/dependencies/'.$dep, $dep_file);
        echo "    Nuevo archivo creado: \"".$new_plugin_route."/dependencies/".$dep."\"\n";
      }
    }

    // Archivo de configuración de plugins
    file_put_contents($plugins_file, json_encode($plugins_list));
    echo "  Listado de plugins actualizado.\n\n";
    echo "  Instalación finalizada.\n\n";
  }

  public function installedPlugins(){
    echo "  Plugins instalados:\n\n";
    if (count($this->config->getPlugins())>0){
      foreach ($this->config->getPlugins() as $p){
        $plugin = new OPlugin($p);
        $plugin->loadConfig();
        echo "  · ".$this->colors->getColoredString($plugin->getName(), "light_green")." (".$plugin->getVersion()."): ".$plugin->getDescription()."\n";
      }
      echo "\n";
    }
    else{
      echo "  No tienes instalado ningún plugin.\n\n";
    }
  }

  public function removePlugin($params){
    if (count($params)<2){
      echo "  ".$this->colors->getColoredString("ERROR", "red").": Debes indicar el nombre del plugin que quieres desinstalar, por ejemplo:\n\n";
      echo "      ".$this->colors->getColoredString("php ofw.php plugins remove email", "light_green")."\n\n\n";
      exit;
    }
    $found = null;
    foreach ($this->config->getPlugins() as $p){
      if ($p==$params[1]){
        $found = $p;
        break;
      }
    }
    if (is_null($found)){
      echo "  ".$this->colors->getColoredString("ERROR", "red").": El plugin indicado no está instalado.\n\n";
      echo "  Comprueba la lista ejecutando el siguiente comando:\n\n";
      echo "      ".$this->colors->getColoredString("php ofw.php plugins list", "light_green")."\n\n\n";
      exit;
    }

    $plugin = new OPlugin($params[1]);
    $plugin->loadConfig();

    $plugins_file = $this->config->getDir('app_config').'plugins.json';
    $plugins_list = json_decode( file_get_contents($plugins_file), true );

    $plugin_index = array_search($plugin->getName(), $plugins_list['plugins']);
    array_splice($plugins_list['plugins'], $plugin_index, 1);

    $plugin_route = $this->config->getDir('ofw_plugins').$plugin->getName();
    if (!file_exists($plugin_route)){
      echo "  ".$this->colors->getColoredString("ERROR", "red").": La carpeta \"".$plugin_route."\" no existe.\n\n";
      exit;
    }

    unlink($plugin_route.'/'.$plugin->getName().'.json');
    echo "  Archivo de configuración \"".$plugin_route."/".$plugin->getName().".json\" borrado.\n";
    unlink($plugin_route.'/'.$plugin->getFileName());
    echo "  Archivo de plugin \"".$plugin_route."/".$plugin->getFileName()."\" borrado.\n";

    if (count($plugin->getDependencies())>0){
      echo "  Borrando dependencias...\n";
      foreach ($plugin->getDependencies() as $dep){
        $dep_route = $plugin_route.'/dependencies/'.$dep;
        unlink($dep_route);
        echo "    Archivo \"".$dep_route."\" borrado.\n";
      }
      rmdir($plugin_route.'/dependencies');
      echo "  Carpeta de dependencias \"".$plugin_route."/dependencies\" borrada.\n";
    }

    rmdir($plugin_route);
    echo "  Carpeta de plugin \"".$plugin_route."\" borrada.\n";

    if (count($plugins_list['plugins'])>0){
      file_put_contents($plugins_file, json_encode($plugins_list));
      echo "  Listado de plugins actualizado.\n\n";
    }
    else{
      unlink($plugins_file);
      echo "  Se ha borrado el archivo de configuración \"".$plugins_file."\" por que no hay ningún plugin instalado actualmente.\n\n";
    }

    echo "  Borrado finalizado.\n\n";
  }

  public function updateCheck(){
    if (count($this->config->getPlugins())==0){
      echo " No hay ningún plugin instalado.\n";
      exit;
    }

    echo "  Buscando actualizaciones...\n\n";
    $updates = false;

    foreach ($this->config->getPlugins() as $p){
      $plugin = new OPlugin($p);
      $plugin->loadConfig();

      echo "  · ".$this->colors->getColoredString($plugin->getName(), "light_green")."\n";
      echo "    Versión instalada: ".$plugin->getVersion()."\n";

      $repo_check = json_decode( file_get_contents($this->repo_url.'master/'.$plugin->getName().'/'.$plugin->getName().'.json'), true );
      echo "    Versión actual: ".$repo_check['version']."\n";
      if (version_compare($plugin->getVersion(), $repo_check['version'])==-1){
        echo "      ¡Actualización disponible!\n";
        $updates = true;
      }
      echo "\n";
    }

    if ($updates){
      echo "  Para actualizar tus plugins a la versión actual ejecuta el siguiente comando:\n\n";
      echo "    ".$this->colors->getColoredString("php ofw.php plugins update", "light_green")."\n\n\n";
    }
  }

  public function update(){
    if (count($this->config->getPlugins())==0){
      echo " No hay ningún plugin instalado.\n";
      exit;
    }

    echo "  Buscando actualizaciones...\n\n";

    foreach ($this->config->getPlugins() as $p){
      $plugin = new OPlugin($p);
      $plugin->loadConfig();
      $deletes = [];
      $backups = [];
      $updates = [];

      echo "  · ".$this->colors->getColoredString($plugin->getName(), "light_green")."\n";
      echo "    Versión instalada: ".$plugin->getVersion()."\n";

      $repo_version_file = file_get_contents($this->repo_url.'master/'.$plugin->getName().'/'.$plugin->getName().'.json');
      $repo_check = json_decode( $repo_version_file, true );
      echo "    Versión actual: ".$repo_check['version']."\n";
      if (version_compare($plugin->getVersion(), $repo_check['version'])==-1){
        echo "    Preparando actualización...\n";
        $update = $repo_check['updates'][$repo_check['version']];
        echo "      ".$update['message']."\n";
        if (array_key_exists('deletes', $update)){
          foreach ($update['deletes'] as $delete){
            $delete_file = $this->config->getDir('ofw_plugins').$plugin->getName().'/'.$delete;
            if (file_exists($delete_file)){
              echo "      El archivo \"".$delete."\" será eliminado.\n";
              array_push($deletes, $delete_file);
            }
            else{
              echo "    ".$this->colors->getColoredString("ERROR", "red").": El archivo \"".$delete_file."\" no existe.\n\n\n";
              exit;
            }
          }
        }

        foreach ($update['files'] as $file){
          $file_url = $this->repo_url.'master/'.$plugin->getName().'/'.$file;
          echo "      Descargando \"".$file_url."\"\n";
          $file_content = file_get_contents($file_url);

          $local_file = $this->config->getDir('ofw_plugins').$plugin->getName().'/'.$file;
          if (file_exists($local_file)){
            echo "        El archivo ya existe, creando copia de seguridad.\n";
            $backup_file = $local_file.'_backup';
            rename($local_file, $backup_file);
            array_push($backups, ['new_file'=>$local_file, 'backup'=>$backup_file]);
            echo "        Archivo actualizado.\n";
          }
          else{
            echo "        Nuevo archivo creado.\n";
          }
          file_put_contents($local_file, $file_content);
        }

        foreach ($deletes as $delete){
          unlink($delete);
        }
        foreach ($backups as $backup){
          unlink($backup['backup']);
        }

        echo "      Actualizando archivo de versión.\n";
        file_put_contents($this->config->getDir('ofw_plugins').$plugin->getName().'/'.$plugin->getName().'.json', $repo_version_file);

        echo "    Actualización terminada.\n";
      }
    }

  }

  public function run($params){
  	$option = (count($params)>0) ? $params[0] : 'none';
  	$this->getPluginsFile();

  	echo "\n";
  	echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";

  	switch ($option){
  		case 'none': {
		    $this->availablePlugins();
  		}
  		break;
  		case 'install': {
        $this->installPlugin($params);
  		}
  		break;
  		case 'list': {
  			$this->installedPlugins();
  		}
  		break;
  		case 'remove': {
  			$this->removePlugin($params);
  		}
  		break;
      case 'updateCheck': {
        $this->updateCheck();
      }
      break;
      case 'update': {
        $this->update();
      }
      break;
  		default: {
  			echo "  ".$this->colors->getColoredString("ERROR", "red").": El comando indicado no es una opción válida.\n\n";
  			echo "  Las opciones disponibles son:\n\n";
  			echo "  · ".$this->colors->getColoredString("list", "light_green").": Lista de plugins instalados.\n";
  			echo "  · ".$this->colors->getColoredString("install", "light_green").": Para instalar un nuevo plugin.\n";
  			echo "  · ".$this->colors->getColoredString("remove", "light_green").": Para borrar un plugin instalado.\n\n";
  			echo "  En caso de no indicar ningún parámetro se muestra la lista de plugins que se pueden instalar.\n\n";
  		}
  	}
  }
}