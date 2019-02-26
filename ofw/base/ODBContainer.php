<?php
class ODBContainer{
  private $connections = [];

  public function getConnection($driver, $host, $user, $pass, $name, $charset){
    $index = sha1($driver.$host.$user.$pass.$name.$charset);

    if (!array_key_exists($index, $this->connections)){
      $conn = new PDO(
  	    $driver.':host='.$host.';dbname='.$name.';charset='.$charset,
  	    $user,
  	    $pass,
        [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
      );
      $this->connections[$index] = $conn;
    }

    return [ 'index' => $index, 'link' => $this->connections[$index] ];
  }

  public function getConnectionByIndex($index){
    if (array_key_exists($index, $this->connections)){
      return [ 'index' => $index, 'link' => $this->connections[$index] ];
    }
    else{
      return null;
    }
  }

  public function closeConnection($index){
    if (array_key_exists($index, $this->connections)){
      $this->connections[$index] = null;
      unset($this->connections[$index]);

      return true;
    }

    return false;
  }

  public function closeAllConnections(){
    foreach ($this->connections as $index => $link){
      $this->connections[$index] = null;
      unset($this->connections[$index]);
    }
  }
}