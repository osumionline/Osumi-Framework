<?php
class composerTask{
  public function __toString(){
    return $this->colors->getColoredString("composer", "light_green").": Función para exportar una aplicación con todos sus archivos a un solo archivo autoextraíble.";
  }

  private $colors = null;

  function __construct(){
    $this->colors = new OColors();
  }

  private $base_dir;
  private $folder_list = [
    'app'  => true,
    'logs' => false,
    'ofw'  => true,
    'web'  => true
  ];

  private function scanFileNameRecursivly($path = '', &$name = []){
    $path = ($path == '') ? $this->base_dir : $path;
    $lists = @scandir($path);

    if(!empty($lists)){
      foreach($lists as $f){
        if ($f=='.' || $f=='..'){ continue; }
        if(is_dir($path.DIRECTORY_SEPARATOR.$f) && $f != '..' && $f != '.'){
          $this->scanFileNameRecursivly($path.DIRECTORY_SEPARATOR.$f, $name);
        }
        else{
          $name[] = $path.DIRECTORY_SEPARATOR.$f;
        }
      }
    }
    return $name;
  }

  public function run($silent = false){
    global $c;
    $this->base_dir = $c->getDir('base');

    echo "\n";
    if (!$silent){
      echo "  ".$this->colors->getColoredString("Osumi Framework", "white", "blue")."\n\n";
    }

    echo "  ".$this->colors->getColoredString("Exportando proyecto", "light_green")."\n\n";
    $destination = $c->getDir('ofw_export').'ofw_composer.php';
    if (file_exists($destination)){
      echo "    Archivo destino ya existía, se ha borrado.\n\n";
      unlink($destination);
    }
    $folders = [];
    $files = [];

    file_put_contents($destination, "<?php\n");

    echo "  Obteniendo carpetas y archivos a exportar...\n";

    $files['ofw.php'] = Base::fileToBase64($c->getDir('base') . 'ofw.php');

    // Recorro carpetas
    foreach ($this->folder_list as $folder => $explore){
      // Si hay que explorar la carpeta
      if ($explore) {
        // Obtengo la lista de archivos recursivamente
        $file_names = $this->scanFileNameRecursivly($c->getDir('base') . $folder);

        // Recorro cada archivo
        foreach ($file_names as $file_name){
          // Carpeta y nombre del archivo relativos
          $key = str_ireplace($c->getDir('base'), '', $file_name);
          // Contenido del archivo
          $content = Base::fileToBase64($file_name);
          // Añado al array el contenido del archivo
          $files[$key] = $content;

          // Obtengo el array con la ruta del archivo ej: 'model/base' => ['model', 'base', 'base.php']
          $folder_name = explode('/', $key);
          // Quito el archivo del array para quedarme solo con las carpetas ej: ['model', 'base']
          array_pop($folder_name);
          // Cojo la primera parte
          $check_folder = array_shift($folder_name);
          while (count($folder_name)>-1){
            if (!in_array($check_folder, $folders)){
              array_push($folders, $check_folder);
            }
            if (count($folder_name)>0) {
              $check_folder .= '/' . array_shift($folder_name);
            }
            else{
              break;
            }
          }
        }
      }
      else{
        // Añado la carpeta a la lista
        array_push($folders, $folder);
      }
    }

    echo "  Exportando ".count($files)." archivos.\n";
    file_put_contents($destination, "$"."files = [\n", FILE_APPEND);
    $content_array = [];
    foreach ($files as $key => $content){
      array_push($content_array, "  '".$key."' => '".$content."'");
    }
    file_put_contents($destination, implode(",\n", $content_array), FILE_APPEND);
    file_put_contents($destination, "];\n", FILE_APPEND);

    unset($files);
    unset($content_array);

    echo "  Exportando ".count($folders)." carpetas.\n";
    file_put_contents($destination, "$"."folders = ['", FILE_APPEND);
    file_put_contents($destination, implode("','", $folders), FILE_APPEND);
    file_put_contents($destination, "'];\n", FILE_APPEND);

    unset($files);

    echo "  Preparando composer...\n";
    $str = "\n";
    $str .= "fun"."ction base64ToFile($"."base64_string, $"."filename){\n";
    $str .= "  $"."ifp = fopen( $"."filename, 'wb' );\n";
    $str .= "  $"."data = explode( ',', $"."base64_string );\n";
    $str .= "  fwrite( $"."ifp, base64_decode( $"."data[ 1 ] ) );\n";
    $str .= "  fclose( $"."ifp );\n";
    $str .= "}\n\n";

    $str .= "$"."basedir = realpath(dirname(__FILE__));\n";
    $str .= "echo \"RUTA BASE: \".$"."basedir.\"\\n\";\n";
    $str .= "echo \"CREANDO CARPETAS (\".count($"."folders).\")\\n\";\n";
    $str .= "foreach ($"."folders as $"."i => $"."folder){\n";
    $str .= "  echo \"  \".($"."i+1).\"/\".count($"."folders).\" - \".$"."folder.\"\\n\";\n";
    $str .= "  mkdir($"."basedir.\"/\".$"."folder);\n";
    $str .= "}\n\n";

    $str .= "echo \"CREANDO ARCHIVOS (\".count($"."files).\")\\n\";\n";
    $str .= "$"."cont = 1;\n";
    $str .= "foreach ($"."files as $"."key => $"."file){\n";
    $str .= "  echo \"  \".$"."cont.\"/\".count($"."files).\" - \".$"."key.\"\\n\";\n";
    $str .= "  base64ToFile($"."file, $"."basedir.'/'.$"."key);\n";
    $str .= "  $"."cont++;\n";
    $str .= "}";
    file_put_contents($destination, $str, FILE_APPEND);

    echo "  ".$this->colors->getColoredString("Proyecto exportado.", "light_green")."\n";
  }
}
