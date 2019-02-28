<?php
/**
 * Clase para gestionar la Base de datos
 */
class ODB {
  private $driver           = 'mysql';
	private $host             = null;
	private $user             = null;
	private $pass             = null;
	private $name             = null;
  private $charset          = 'UTF8';
	private $link             = null;
  private $connection_index = null;
	private $stmt             = null;
	private $fetch_mode       = null;
  private $last_query       = null;

	function __construct($user='', $pass='', $host='', $name=''){
		global $c;
		if (empty($user) ||empty($pass) ||empty($host) ||empty($name) ){
      $this->setDriver( $c->getDB('driver') );
			$this->setHost( $c->getDB('host') );
			$this->setUser( $c->getDB('user') );
			$this->setPass( $c->getDB('pass') );
			$this->setName( $c->getDB('name') );
      $this->setCharset( $c->getDB('charset') );
		}
    else{
			$this->setHost( $host );
			$this->setUser( $user );
			$this->setPass( $pass );
			$this->setName( $name );
		}
	}

	/*
   * Getters / Setters
   */
	public function setDriver($d){
  	$this->driver = $d;
	}
	public function getDriver(){
  	return $this->driver;
	}
	public function setHost($h){
  	$this->host = $h;
	}
	public function getHost(){
  	return $this->host;
	}
	public function setUser($u){
  	$this->user = $u;
	}
	public function getUser(){
  	return $this->user;
	}
	public function setPass($p){
  	$this->pass = $p;
	}
	public function getPass(){
  	return $this->pass;
	}
	public function setName($n){
  	$this->name = $n;
	}
	public function getName(){
  	return $this->name;
	}
  public function setCharset($c){
  	$this->charset = $c;
	}
	public function getCharset(){
  	return $this->charset;
	}
	public function setLink($l){
  	$this->link = $l;
	}
	public function getLink(){
  	return $this->link;
	}
  public function setConnectionIndex($ci){
    $this->connection_index = $ci;
  }
  public function getConnectionIndex(){
    return $this->connection_index;
  }
	public function setStmt($s){
  	$this->stmt = $s;
	}
	public function getStmt(){
  	return $this->stmt;
	}
	public function setFetchMode($fm){
  	$this->fetch_mode = $fm;
	}
	public function getFetchMode(){
  	return $this->fetch_mode;
	}
  public function setLastQuery($lq){
  	$this->last_query = $lq;
	}
	public function getLastQuery(){
  	return $this->last_query;
	}

	/*
   * Función para abrir una conexión a la base de datos
   */
	function connect(){
    global $dbcontainer;
    if (!is_null($this->getConnectionIndex())){
      $connection = $dbcontainer->getConnectionByIndex( $this->getConnectionIndex() );
      $this->setConnectionIndex($connection['index']);
      $this->setLink($connection['link']);
    }
    else{
      try{
        $connection = $dbcontainer->getConnection($this->getDriver(), $this->getHost(), $this->getUser(), $this->getPass(), $this->getName(), $this->getCharset());
        $this->setConnectionIndex($connection['index']);
        $this->setLink($connection['link']);
      }
      catch (PDOException $e) {
        return 'Connection failed: ' . $e->getMessage();
      }
    }

    return true;
	}

  /*
   * Función para cerrar una conexión a la base de datos
   */
  function disconnect(){
    if (!is_null($this->getLink())){
      $this->setLink(null);
    }
  }

	/*
   * Función para realizar una consulta
   */
	public function query($q, $params=[]){
  	// Obtener conexión
  	$pdo = $this->getLink();
  	if (!$pdo){
    	$conn = $this->connect();
    	if ($conn===true){
    	  $pdo = $this->getLink();
    	}
    	else{
      	return $conn;
    	}
  	}

  	// Si hay parámetros uso prepared statement
  	if (count($params)>0){
      $stmt = $pdo->prepare($q);
      $stmt->execute($params);
    }
    // Si no hay parámetros hago la consulta directamente
    else{
      $stmt = $pdo->query($q);
    }

    // Si el modo de obtener los resultados está definido, se lo indico al statement
    if (!is_null($this->getFetchMode())){
      $stmt->setFetchMode(PDO::FETCH_CLASS, $this->getFetchMode());
    }

    $this->setStmt($stmt);
    $this->setLastQuery($q);
	}

	/*
   * Función para marcar el inicio de una transacción
   */
  public function beginTransaction(){
    $this->getStmt()->beginTransaction();
  }

  /*
   * Función para finalizar una transacción
   */
  public function commit(){
    $this->getStmt()->commit();
  }

  /*
   * Función para cancelar una transacción
   */
  public function rollback(){
    $this->getStmt()->rollback();
  }

	/*
   * Función para obtener un resultado
   */
	public function next(){
  	if (is_null($this->getFetchMode())){
  	  return $this->getStmt()->fetch(PDO::FETCH_ASSOC);
  	}
  	return $this->getStmt()->fetch();
	}

	/*
   * Función para obtener todos los resultados
   */
	public function fetchAll(){
    if (is_null($this->getFetchMode())){
  	  return $this->getStmt()->fetchAll(PDO::FETCH_ASSOC);
  	}
  	return $this->getStmt()->fetchAll();
	}

  /*
   * Función para obtener el número de filas afectadas
   */
  public function affected(){
    return $this->getStmt()->rowCount();
  }

	/*
   * Función para obtener el último id insertado en una columna auto-increment
   */
  public function lastId(){
    return $this->getLink()->lastInsertId();
  }
}