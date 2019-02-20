<?php
class versionTask{
  public function __toString(){
    return "version: Función para obtener el número de versión actual del Framework.";
  }

  private $repo_url = 'https://github.com/igorosabel/Osumi-Framework';
  private $twitter_url = 'https://twitter.com/igorosabel';

  public function run(){
    echo "\n==============================================================================================================\n";
    echo "  Osumi Framework\n";
    echo "    ".Base::getVersionInformation()."\n\n";
    echo "  GitHub: ".$this->repo_url."\n";
    echo "  Twitter: ".$this->twitter_url."\n";
    echo "==============================================================================================================\n\n";
  }
}