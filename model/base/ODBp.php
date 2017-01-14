<?php
/**
 * Clase para gestionar la Base de datos
 */
class ODBp {
  private $driver     = 'mysql';
	private $host       = null;
	private $user       = null;
	private $pass       = null;
	private $name       = null;
	private $link       = null;
	private $stmt       = null;
	private $fetch_mode = null;

	function __construct($user='', $pass='', $host='', $name=''){
		global $c;
		if(empty($user) ||empty($pass) ||empty($host) ||empty($name) ){
			$this->setHost( $c->getDB('host') );
			$this->setUser( $c->getDB('user') );
			$this->setPass( $c->getDB('pass') );
			$this->setName( $c->getDB('name') );
		}else{
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
	public function setLink($l){
  	$this->link = $l;
	}
	public function getLink(){
  	return $this->link;
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
	
	/*
   * Function to open a connection to the database
   */
	function connect(){
  	try {
  	  $link = new PDO(
  	    $this->getDriver().':host='.$this->getHost().';dbname='.$this->getName().';charset=UTF8',
  	    $this->getUser(),
  	    $this->getPass(),
  	    array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
      );
      $this->setLink($link);
    }
    catch (PDOException $e) {
      return 'Connection failed: ' . $e->getMessage();
    }
  	
		return true;
	}
	
	/*
   * Function to do a query with a prepared statement
   */
	public function query($q, $params=array()){
  	// Get connection
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

  	// Prepare statement
  	$stmt = $pdo->prepare($q);
  	
  	// Pass parameters to statement
  	if (count($params)>0){
      $stmt->execute($params);
    }
    else{
      $stmt->execute();
    }
    
    // If fetch mode is not null, set it into the statement
    if (!is_null($this->getFetchMode())){
      $stmt->setFetchMode(PDO::FETCH_CLASS, $this->getFetchMode());
    }
    
    $this->setStmt($stmt);
	}
	
	/*
   * Function to begin a transaction
   */
  public function beginTransaction(){
    $this->getStmt()->beginTransaction();
  }
  
  /*
   * Function to perform a commit on a transaction
   */
  public function commit(){
    $this->getStmt()->commit();
  }
  
  /*
   * Function to rollback a transaction
   */
  public function rollback(){
    $this->getStmt()->rollback();
  }
	
	/*
   * Function to get one result
   */
	public function fetch(){
  	if (is_null($this->getFetchMode())){
  	  return $this->getStmt()->fetch(PDO::FETCH_ASSOC);
  	}
  	return $this->getStmt()->fetch();
	}
	
	/*
   * Function to get all the results in an array
   */
	public function fetchAll(){
  	return $this->getStmt()->fetchAll();
	}
	
	/*
   * Function to get last inserted id
   */
  public function getLastId(){
    return $this->getStmt()->lastInsertId();
  }
}