<?php
/**
 * Clase para gestionar la Base de datos
 */
class ODB {
	private $host;
	private $user;
	private $pass;
	private $name;
	private $link;
	private $res;
	private $query;
	private $last_id;
	private $found_rows;

	function __construct($user='', $pass='', $host='', $name=''){
		global $c;
		if(empty($user) ||empty($pass) ||empty($host) ||empty($name) ){
			$this->host = $c->getDB('host');
			$this->user = $c->getDB('user');
			$this->pass = $c->getDB('pass');
			$this->name = $c->getDB('name');
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
		return true;
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
			$this->res = $result;
			if( strpos(strtoupper($q),'INSERT') !== false ){
				$get_last_id = mysqli_query($this->link,'SELECT LAST_INSERT_ID() AS `last_id`');
				$last_id = mysqli_fetch_array($get_last_id,MYSQLI_ASSOC);
				$this->last_id = $last_id['last_id'];
			}
			if( strpos(strtoupper($q),'SQL_CALC_FOUND_ROWS') !== false ){
				$num_found = mysqli_query($this->link,'SELECT FOUND_ROWS() AS `num`');
				$found_rows = mysqli_fetch_array($num_found,MYSQLI_ASSOC);
				$this->found_rows = $found_rows['num'];
			}
			$this->disconnect();
			return true;
		}
	}

	function autoCommit($mode){
		mysqli_autocommit($this->link, $mode);
	}

	function commit(){
		mysqli_commit($this->link);
	}

	function rollback(){
		mysqli_rollback($this->link);
	}

	function info(){
		return mysqli_info($this->link);
	}

	function affected(){
		return mysqli_affected_rows($this->link);
	}

	function howMany(){
		return mysqli_num_rows($this->res);
	}

	function next(){
		return mysqli_fetch_array($this->res,MYSQLI_ASSOC);
	}

	function lastQuery(){
		return $this->query;
	}

	function lastId(){
		return $this->last_id;
	}

	function foundRows(){
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