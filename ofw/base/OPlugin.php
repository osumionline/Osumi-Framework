<?php
class OPlugin{
  private $name = null;
  private $version = null;
  private $description = null;
  private $file_name = null;
  private $dependencies = [];

  function __construct($name){
    $this->setName($name);
  }

  public function setName($n){
    $this->name = $n;
  }

  public function getName(){
    return $this->name;
  }

  public function setVersion($v){
    $this->version = $v;
  }

  public function getVersion(){
    return $this->version;
  }
  
  public function setDescription($d){
    $this->description = $d;
  }

  public function getDescription(){
    return $this->description;
  }

  public function setFileName($fn){
    $this->file_name = $fn;
  }

  public function getFileName(){
    return $this->file_name;
  }

  public function setDependencies($d){
    $this->dependencies = $d;
  }

  public function getDependencies(){
    return $this->dependencies;
  }

  public function loadConfig(){
    global $c;
    $conf_route = $c->getDir('ofw_plugins').$this->getName().'/'.$this->getName().'.json';
    if (!file_exists($conf_route)){
      echo 'ERROR: '.$this->getName().' plugin configuration file not found in '.$conf_route.'.';
      exit;
    }
    $config = json_decode( file_get_contents($conf_route), true);

    $this->setVersion($config['version']);
    $this->setDescription($config['description']);
    $this->setFileName($config['file_name']);
    $this->setDependencies(array_key_exists('dependencies', $config) ? $config['dependencies'] : []);
  }

  public function load(){
    global $c;
    $this->loadConfig();

    foreach ($this->getDependencies() as $dep){
      $dep_route = $c->getDir('ofw_plugins').$this->getName().'/dependencies/'.$dep;
      if (!file_exists($dep_route)){
        echo 'ERROR: '.$dep.' dependency file not found in '.$dep_route.'.';
        exit;
      }
      require $dep_route;
    }

    $route = $c->getDir('ofw_plugins').$this->getName().'/'.$this->getFileName();
    if (!file_exists($route)){
      echo 'ERROR: '.$this->getFileName().' plugin file not found in '.$route.'.';
      exit;
    }
    require $route;
  }
}