<?php  
  // App
  if ($model = opendir($c->getDir('model_packages').$package_name.'/model/app/')) {
    while (false !== ($entry = readdir($model))) {
      if ($entry != "." && $entry != "..") {
        require($c->getDir('model_packages').$package_name.'/model/app/'.$entry);
      }
    }
    closedir($model);
  }
  
  // Static
  if ($model = opendir($c->getDir('model_packages').$package_name.'/model/static/')) {
    while (false !== ($entry = readdir($model))) {
      if ($entry != "." && $entry != "..") {
        require($c->getDir('model_packages').$package_name.'/model/static/'.$entry);
      }
    }
    closedir($model);
  }