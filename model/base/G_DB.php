<?php
/**
 * Clase para gestionar la Base de datos
 */
class G_DB {
	private $host;
	private $user;
	private $pass;
	private $name;
	private $link;
	private $res;
	private $query;
	private $last_id;
	private $found_rows;
	private $affected;
	
	function __construct($user='', $pass='', $host='', $name=''){
    global $c;
    if(empty($user) ||empty($pass) ||empty($host) ||empty($name) ){
			$this->host = $c->getDbHost();
			$this->user = $c->getDbUser();
			$this->pass = $c->getDbPass();
			$this->name = $c->getDbName();
		}else{
			$this->host = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->name = $name;
		}
	}
	
	function connect(){
		$link = mysqli_connect($this->host,$this->user,$this->pass,$this->name);
		if (!$link){
			return mysqli_errno($link);  # Error Conectando la DB
		}else{
			$this->link=$link;
			mysqli_set_charset($link,'utf8');
		}
		return 0;
	}
	
	function query($q,$silent=false){
		$this->query = $q;
		$this->connect();
		$result = mysqli_query($this->link,$q);
		if (!$result){
			if(!$silent){
				echo "ERROR en la consulta: ".$q."\n".mysqli_error($this->link)."\n";
			}
			$this->disconnect();
			return mysqli_error($this->link);
		}else{
			$this->res=$result;
			if( strpos(strtoupper($q),'INSERT') !== false){
				$get_last_id = mysqli_query($this->link,'SELECT LAST_INSERT_ID() AS `last_id`');
				$this->last_id = mysqli_fetch_array($get_last_id);
				$this->last_id = $this->last_id['last_id'];
			}
			if( strpos(strtoupper($q),'SQL_CALC_FOUND_ROWS') !== false){
				$num_found = mysqli_query($this->link,'SELECT FOUND_ROWS() AS `num`');
				$this->found_rows = mysqli_fetch_array($num_found);
				$this->found_rows = $this->found_rows['num'];
			}
			$this->affected = mysqli_affected_rows($this->link);
			$this->disconnect();
			return 0;
		}
	}

	function info(){
		return mysqli_info($this->link);
	}
	
	function affected(){
		return $this->affected;
	}
	
	function how_many(){
		return mysqli_num_rows($this->res);
	}
	
	function next(){
		return mysqli_fetch_array($this->res);
	}

	function last_query(){
		return $this->query;
	}

	function last_id(){
		return $this->last_id;
	}

	function found_rows(){
		return $this->found_rows;
	}
	
	function disconnect(){
		mysqli_close($this->link);
		$this->link = null;
	}
	
	function cleanStr($str){
	  if (is_null($this->link)){
  	  $this->connect();
	  }
  	return mysqli_real_escape_string($this->link,$str);
	}
}