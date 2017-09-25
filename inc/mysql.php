<?php
date_default_timezone_set('America/Lima');
header("Content-type: text/html; charset=UTF-8");
class MySQL{

	private $cn;

	public function MySQL(){

		if(!isset($this->cn)){  
			$this->cn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			if($this->cn->connect_error){
				die("Error en la conexion : ".$this->cn->connect_errno."-".$this->cn->connect_error);
			} else {
				mysqli_set_charset($this->cn, "utf8");
				$this->cn->query("SET @@session.time_zone='-05:00';");
			}
		}
	}

	/**
	 * Obtener registros desde una consulta SQL
	 * @param $sql :: sentencia sql
	 * @return object | false
	 */
	public function get($sql, $col = '', $val = ''){
		if(!empty($val)){
			$sql = "SELECT * FROM $sql WHERE $col = '$val'";
		} else if(!empty($col)){
			$sql = "SELECT * FROM $sql WHERE id = $col";
		}
		
		$result = $this->cn->query($sql);
		if($result === TRUE){
			if($result->num_rows > 0 ){
				return $result;
			} else {
				$result = false;
			}
		}
		return $result;
	}

	// Obtener registros, en array
	public function arr($sql){
		$items = [];
		$os = $this->get($sql);
		if($os){
			while($o = $os->fetch_assoc()){
				$items[] = $o;
			}
		}
		return $items;
	}

	/**
	 * Obtener UN registro desde mysql, en objeto
	 * @param: Sentencia SQL
	 * @return: Object Mysql
	 */
	public function o($sql, $col = '', $val = ''){
		if(!empty($val)){
			$sql = "SELECT * FROM $sql WHERE $col = '$val' LIMIT 1";
		} else if(!empty($col)){
			$sql = "SELECT * FROM $sql WHERE id = $col LIMIT 1";
		}
		$result = $this->get($sql);
		return $result ? $result->fetch_object() : false;
	}

	/**
	 * Obtener UN registro desde mysql, en array
	 * @param: Sentencia SQL
	 * @return: Object Mysql
	 */
	public function a($sql, $col = '', $val = ''){
		if(!empty($val)){
			$sql = "SELECT * FROM $sql WHERE $col = '$val' LIMIT 1";
		} else if(!empty($col)){
			$sql = "SELECT * FROM $sql WHERE id = $col LIMIT 1";
		}
		$result = $this->get($sql);
		return $result ? $result->fetch_assoc() : false;
	}

	/**
	 * Saber si una consulta obtuvo resultados
	 * @param $table :: de pasar solo un parametro, se considera sentencia SQL
	 * @param $table, $column, $data :: de pasar los 3 parametros, se considera consulta rapida
	 * @return true | false
	 */
	public function has($sql, $id = -1, $table = -1){
		if($table != -1){
			$sql = "SELECT $id FROM $sql WHERE $id = '$table'";
		} else if($id != -1){
			$sql = "SELECT id FROM $sql WHERE id = $id";
		}
		return $this->total($sql) > 0;
	}

	/**
	 * Retorna el total de resultados en una consulta
	 * @param $sql :: sentencia SQL
	 * @return número
	 */
	public function total($sql){
		$query = $this->get($sql);
		return $query ? $query->num_rows : 0;
	}

	/**
	 * Ejecuta una sentencia SQL
	 */
	public function query($sql){
		return $this->cn->query($sql);
	}

	/**
	 * Insertar valores en determinada tabla
	 * @param $table :: tabla a insertar
	 * @param $data :: array con los datos SQL
	 * @return resultado de la ejecusion
	 */
	public function insert($table, $data){
		$VALS = '';
		foreach($data as $key => $value){
			if($value != 'NOW()'){
				$value = "'".$this->cn->real_escape_string($value)."'";
			}
			$VALS .= $key." = $value,";
		}
		$VALS = trim($VALS, ',');
		$SQL = "INSERT INTO $table SET $VALS";
		return $this->cn->query($SQL);
	}

	/**
	 * @param $table
	 * @param $data array
	 * @param $where string|int Si asigna un numero, se entendera que es el ia, y sera ID=$where
	 * @return bool|mysqli_result
	 */
	public function update($table, $data, $where){
		$VALS = '';
		foreach($data as $key => $value){
			$value = $this->cn->real_escape_string($value);
			$VALS .= $key." = ".($value == 'NOW()' || $value=='NULL' ? $value : "'$value'" ).",";
		}
		$VALS = trim($VALS, ',');
		if(is_numeric($where) && $where > 0){
			$where = "id=$where";
		}
		// Validar que el campo WHERE no este vacio
		if(!empty($where) && $where != ''){
			$SQL = "UPDATE $table SET $VALS WHERE $where";
			return $this->cn->query($SQL);
		} else {
			return false;
		}
	}

	/**
	 * Obtener el último ID insertado
	 */
	public function lastID(){
		return $this->cn->insert_id;
	}

	/**
	 * Escape String
	 */
	public function escape($value){
		return $this->cn->real_escape_string($value);
	}
	
	/**
	 * Obtener ajustes de base de datos
	 */
	public function getSettings(){
		$os = $this->get("SELECT name,value FROM settings");
		$stg = new stdClass();
		while($o = $os->fetch_object()){
			$stg->{$o->name} = $o->value;
		}
		return $stg;
	}

	/**
	 * Obtener ajuste
	 */
	public function getSetting($name){
		$o = $this->o("SELECT * FROM settings WHERE name = '$name'");
		return $o->value;
	}

    /**
     * Guardar Logs
     * @param string $type : Tipo de log
     * @param int $id_1 : id 1 de referencia
     * @param int $id_2 : id 2 de referencia
     * @param string $notes : notas
     * @return bool|mysqli_result
     */
    public function log($type, $id_1, $id_2 = 0, $notes = ''){
        return $this->query("INSERT INTO logs(type, id_1, id_2, notes) VALUE($type, $id_1, $id_2, '$notes')") ? true : false;
    }
	
	// Crear codigo referido unico
	public function mkRefer($table = 'drivers'){
		global $uu;
		return $uu->mkCode();
	}

}