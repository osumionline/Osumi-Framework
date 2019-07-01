<?php
class OFile{
  public static function copy($source, $destination){
    return copy($source, $destination);
  }

  public static function rename($old_name, $new_name){
    return rename($old_name, $new_name);
  }

  public static function delete($name){
    return unlink($name);
  }

  public static function rrmdir($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? self::rrmdir("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  }

  public static function getOFWFolders(){
    return [
      'app',
      'app/cache',
      'app/config',
      'app/controller',
      'app/filter',
      'app/model',
      'app/service',
      'app/task',
      'app/template',
      'app/template/layout',
      'app/template/partials',
      'logs',
      'ofw',
      'ofw/base',
      'ofw/export',
      'ofw/lib',
      'ofw/lib/email',
      'ofw/lib/pdf',
      'ofw/lib/routing',
      'ofw/task',
      'ofw/tmp',
      'web'
    ];
  }

  public static function getOFWFiles(){
    return [
      'ofw/base/base.php',
      'ofw/base/OBase.php',
      'ofw/base/OBrowser.php',
      'ofw/base/OCache.php',
      'ofw/base/OColors.php',
      'ofw/base/OConfig.php',
      'ofw/base/OController.php',
      'ofw/base/OCookie.php',
      'ofw/base/OCrypt.php',
      'ofw/base/ODB.php',
      'ofw/base/ODBContainer.php',
      'ofw/base/OEmail.php',
      'ofw/base/OFile.php',
      'ofw/base/OForm.php',
      'ofw/base/OFTP.php',
      'ofw/base/OImage.php',
      'ofw/base/OLog.php',
      'ofw/base/OPDF.php',
      'ofw/base/OService.php',
      'ofw/base/OSession.php',
      'ofw/base/OTemplate.php',
      'ofw/base/OToken.php',
      'ofw/base/OTranslate.php',
      'ofw/base/OUrl.php',
      'ofw/base/start.php',
      'ofw/base/version.json',
      'ofw/lib/email/.gitignore',
      'ofw/lib/email/email.txt',
      'ofw/lib/email/Exception.php',
      'ofw/lib/email/PHPMailer.php',
      'ofw/lib/email/SMTP.php',
      'ofw/lib/pdf/.gitignore',
      'ofw/lib/pdf/pdf.txt',
      'ofw/lib/routing/sfObjectRoute.class.php',
      'ofw/lib/routing/sfObjectRouteCollection.class.php',
      'ofw/lib/routing/sfPatternRouting.class.php',
      'ofw/lib/routing/sfRequestRoute.class.php',
      'ofw/lib/routing/sfRoute.class.php',
      'ofw/lib/routing/sfRouteCollection.class.php',
      'ofw/lib/routing/sfRouting.class.php',
      'ofw/task/backupAll.php',
      'ofw/task/backupDB.php',
      'ofw/task/composer.php',
      'ofw/task/generateModel.php',
      'ofw/task/update.php',
      'ofw/task/updateCheck.php',
      'ofw/task/updateUrls.php',
      'ofw/task/version.php',
      'web/index.php',
      'ofw.php'
    ];
  }

  private $zip_file = null;

  private function addDir($location, $name){
    $this->zip_file->addEmptyDir($name);
    $this->addDirDo($location, $name);
  }

  private function addDirDo($location, $name){
    $name .= '/';
    $location .= '/';
    $dir = opendir($location);
    while ($file = readdir($dir)){
      if ($file == '.' || $file == '..') continue;
      if (filetype( $location . $file) == 'dir'){
        $this->addDir($location . $file, $name . $file);
      }
      else{
        $this->zip_file->addFile($location . $file, $name . $file);
      }
    }
  }

  public function zip($route, $zip_route, $basename=null){
    if (file_exists($zip_route)){
      unlink($zip_route);
    }

    $this->zip_file = new ZipArchive();
    $this->zip_file->open($zip_route, ZipArchive::CREATE);
    $this->addDir($route, is_null($basename) ? basename($route) : $basename);
    $this->zip_file->close();
  }
}