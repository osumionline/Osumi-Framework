<?php
/**
 * Clase para gestionar la Base de datos
 */
class OFTP {
  private $server;
  private $user_name;
  private $user_pass;
  
  private $conn;
  private $connected = false;
  private $logged = false;
  private $mode = FTP_ASCII;
  private $auto_disconnect = true;
  
  function __construct($server, $user, $pass){
    $this->server    = $server;
    $this->user_name = $user;
    $this->user_pass = $pass;
  }
  
  public function connect(){
    $this->conn = ftp_connect($this->server);
    if ($this->conn){
      $this->connected = true;
    }
    return $this->connected;
  }
  
  public function disconnect(){
    ftp_close($this->conn);
    $this->connected = false;
    $this->logged = false;
  }
  
  public function login(){
    $this->logged = ftp_login($this->conn, $this->user_name, $this->user_pass);
    return $this->logged;
  }
  
  public function passive($pasv = true){
    ftp_pasv($this->conn, $pasv);
  }
  
  public function autoDisconnect($auto){
    $this->auto_disconnect = $auto;
  }
  
  public function mode($mode){
    switch ($mode){
      case 'ascii':{
        $this->mode = FTP_ASCII;
      }
      break;
      case 'bin':{
        $this->mode = FTP_BINARY;
      }
      break;
    }
  }
  
  private function checkConnection(){
    if (!$this->connected && !$this->connect()){
      throw new Exception('Error de conexión: "'.$this->server.'"');
    }
    if (!$this->logged && !$this->login()){
      throw new Exception('Error al iniciar sesión: "'.$this->user_name.'"');
    }
  }
  
  public function put($local, $remote){
    $this->checkConnection();
    
    $result = ftp_put($this->conn, $remote, $local, $this->mode);
    
    if ($this->auto_disconnect){
      $this->disconnect();
    }
    
    return $result;
  }
  
  public function get($remote, $local){
    $this->checkConnection();
    
    $result = ftp_get($this->conn, $local, $remote, $this->mode);
    
    if ($this->auto_disconnect){
      $this->disconnect();
    }
    
    return $result;
  }
  
  public function delete($remote){
    $this->checkConnection();
    
    $result = ftp_delete($this->conn, $remote);
    
    if ($this->auto_disconnect){
      $this->disconnect();
    }
    
    return $result;
  }
  
  public function chdir($dir){
    $this->checkConnection();
    
    $result = ftp_chdir($this->conn, $dir);
    
    if ($this->auto_disconnect){
      $this->disconnect();
    }
    
    return $result;
  }
  
  public function mkdir($dir){
    $this->checkConnection();
    
    $result = ftp_mkdir($this->conn, $dir);
    
    if ($this->auto_disconnect){
      $this->disconnect();
    }
    
    return $result;
  }
}