<?php
/**
 * Clase para gestionar la Base de datos
 */
class G_BBDD {
	private $host;
	private $user;
	private $pass;
	private $name;
	private $bd;
	private $res;
	private $query;
	private $last_id;
	private $found_rows;
	private $afectadas;
	
	function G_BBDD($user="", $pass="", $host="", $name=""){
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
	
	function conecta(){
		$link = mysql_connect($this->host,$this->user,$this->pass);
		if (!$link){
			return mysql_errno();  # Error Conectando la DB
		}else{
			$this->bd=$link;
			if (!mysql_select_db($this->name, $this->bd)) {
				return mysql_errno(); # Error Seleccionando la DB
			}else{
				mysql_set_charset('utf8',$link);
			}
		}
		return 0;
	}
	
	function consulta($consulta,$silencioso=false){
		$this->query=$consulta;
		$this->conecta();
		$result = mysql_query($consulta,$this->bd); #TODO: Anyadir Slashes
		if (!$result){
			if(!$silencioso){
				echo "ERROR en la consulta: ".$consulta."\n".mysql_error()."\n";
			}
			$this->desconecta();
			return mysql_error();
		}else{
			$this->res=$result;
			if( strpos(strtoupper($consulta),'INSERT') !== false){
				$wop= mysql_query("SELECT LAST_INSERT_ID() AS lid;",$this->bd);
				$this->last_id=mysql_fetch_array($wop);
				$this->last_id=$this->last_id["lid"];
			}
			if( strpos(strtoupper($consulta),'SQL_CALC_FOUND_ROWS') !== false){
				$wop= mysql_query("SELECT FOUND_ROWS() AS n;",$this->bd);
				$this->found_rows=mysql_fetch_array($wop);
				$this->found_rows=$this->found_rows["n"];
			}
			$this->afectadas = mysql_affected_rows($this->bd);
			$this->desconecta();;
			return 0;
		}
	}

  function consulta_persistente($consulta,$silencioso=false){
    $this->query=$consulta;
    $result = mysql_query($consulta,$this->bd); #TODO: Anyadir Slashes
    if (!$result){
      if(!$silencioso){
        echo "ERROR en la consulta: $consulta";
      }
      $this->afectadas = mysql_affected_rows($this->bd);
      return mysql_error();
    }else{
      $this->res=$result;
      if( strpos(strtoupper($consulta),'INSERT') !== false){
        $wop= mysql_query("SELECT LAST_INSERT_ID() AS lid;",$this->bd);
        $this->last_id=mysql_fetch_array($wop);
        $this->last_id=$this->last_id["lid"];
      }
      if( strpos(strtoupper($consulta),'SQL_CALC_FOUND_ROWS') !== false){
        $wop= mysql_query("SELECT FOUND_ROWS() AS n;",$this->bd);
        $this->found_rows=mysql_fetch_array($wop);
        $this->found_rows=$this->found_rows["n"];
      }
      $this->afectadas = mysql_affected_rows($this->bd);
      return 0;
    }
  }

	function info(){
		return mysql_info($this->bd);
	}
	
	function afectadas(){
		return $this->afectadas;
	}
	
	function cuantas(){	
		return mysql_num_rows($this->res);
	}
	
	function sig(){
		return mysql_fetch_array($this->res);
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
	
	function desconecta(){
		mysql_close($this->bd);
		$this->bd = null;
	}
	
	function cleanStr($str){
	  if (is_null($this->bd)){
  	  $this->conecta();
	  }
  	return mysql_real_escape_string($str,$this->bd);
	}
	
}