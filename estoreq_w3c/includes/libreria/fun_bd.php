<?php

/***************************************************************************
    begin                : vie sep 29 2006
    copyright            : (C) 2006 by InfoSiAL S.L.
    email                : mail@infosial.com
 ***************************************************************************/
/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 ***************************************************************************/

/** @class_definition oficial_funBD */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_funBD
{

	function conectaBD()
	{
		switch(_DB_TYPE) {
			
			case 'mysql':
				$link_id = @mysql_connect(_DB_SERVER, _DB_SERVER_USERNAME, _DB_SERVER_PASSWORD)
 					or die(include(_DOCUMENT_ROOT."general/error_db.php"));
				mysql_select_db(_DB_DATABASE)
 					or die(include(_DOCUMENT_ROOT."general/error_db.php"));
			break;
			
			case 'postgresql':
			
				if (isset($_SESSION["connId"])) {
					$stat = pg_connection_status($_SESSION["connId"]);
					if ($stat === 0)
						return $link_id;
				}
			
				$options = 'dbname='._DB_DATABASE.' port='._DB_PORT.' user='._DB_SERVER_USERNAME.' host='._DB_SERVER.' password='._DB_SERVER_PASSWORD;
				$link_id = pg_connect($options);
//  					or die(include(_DOCUMENT_ROOT."general/error_db.php"));
 					
 				$_SESSION["connId"] = $link_id; 
	
			break;
		}
		
		return $link_id;
	}
	
	function db_query($ordenSQL)
	{
		switch(_DB_TYPE) {
			case 'mysql':
				$result = mysql_query($ordenSQL);
			break;
			
			case 'postgresql':
				$result = pg_query($ordenSQL);
			break;
		}		
		
		return $result;
	}
	
	function db_fetch_array($result)
	{
		switch(_DB_TYPE) {
			case 'mysql':
				$rows = mysql_fetch_array($result);
			break;
			
			case 'postgresql':
				$rows = pg_fetch_array($result);
			break;
		}		
		
		return $rows;
	}
	
	function db_fetch_assoc($result)
	{
		switch(_DB_TYPE) {
			case 'mysql':
				$rows = mysql_fetch_assoc($result);
			break;
			
			case 'postgresql':
				$rows = pg_fetch_assoc($result);
			break;
		}		
		
		return $rows;
	}
	
	function db_fetch_row($result)
	{
		switch(_DB_TYPE) {
			case 'mysql':
				$row = mysql_fetch_row($result);
			break;
			
			case 'postgresql':
				$row = pg_fetch_row($result);
			break;
		}		
		
		return $row;
	}
	
	function db_num_rows($ordenSQL)
	{
		$result = $this->db_query($ordenSQL);
	
		switch(_DB_TYPE) {
			case 'mysql':
				$numRows = mysql_num_rows($result);
			break;
			
			case 'postgresql':
				$numRows = pg_num_rows($result);
			break;
		}		
		
		return $numRows;
	}
	
	function db_valor($ordenSQL)
	{
		$result = $this->db_query($ordenSQL);
		$row = $this->db_fetch_row($result);
		return $row[0];
	}
	
	function db_row($ordenSQL)
	{
		$result = $this->db_query($ordenSQL);
		$row = $this->db_fetch_row($result);
		return $row;
	}
	
	function db_rows($ordenSQL)
	{
		$result = $this->db_query($ordenSQL);
		$rows = $this->db_fetch_array($result);
		return $rows;
	}
	
	// Devuelve un string con el siguiente contador para el campo de una tabla
	function nextCounter($tabla, $campo, $lenCounter, $valorMin = '')
	{
		$result = true;
		while($result) {
			$strCounter = $this->nextId($tabla, $campo, false);
		
			if ($valorMin)
				$strCounter += $valorMin;
				
			settype($strCounter,"string");
			$lenStrCounter = strlen($strCounter);
			
			for ($i = 0; $i < $lenCounter - $lenStrCounter; $i++) {
				$strCounter = "0".$strCounter;
			}
			
			$result = $this->db_valor("select $campo from $tabla where $campo='$strCounter'");
		}
	
		
		return $strCounter;
	}
	
	// Devuelve el siguiente id secuencial de una tabla.
	function nextId($tabla, $campo, $check = true) 
	{
		switch (_DB_TYPE) {
			// Para postgre usamos secuencias
			case "postgresql":
				do {
					$result = $this->db_valor("select nextval('".$tabla."_".$campo."_seq')");
				}
				while($this->db_valor("select $campo from $tabla where $campo = '$result'") && $campo != 'codcliente');
			break;
				
			// Para mysql usamos tablas especiales con campos auto_increment
			case "mysql":
				do {
					$this->db_query("insert into $tabla"."_seq (texto) values ('$tabla')");
					$result = mysql_insert_id();
				}
				while($this->db_valor("select $campo from $tabla where $campo = '$result'") && $campo != 'codcliente');
			break;
		}
		
		return $result;
	}
	
	function escape_string($string)
	{
		switch (_DB_TYPE) {
			case "postgresql":
				$string = pg_escape_string($string);
			break;
				
			// Para mysql usamos tablas especiales con campos auto_increment
			case "mysql":
				$string = mysql_real_escape_string($string);
			break;
		}

		return $string;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_funBD */
class funBD extends oficial_funBD {};

?>