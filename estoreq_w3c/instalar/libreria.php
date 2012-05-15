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

/** @class_definition oficial_funLibreria */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_funLibreria
{
	// Imprime la fase por la que pasa la instalacion
	function fasesInstalacion($fase)
	{
		$fases = array('previo','licencia','web','bd','datos');
		
		$codigo = '<div class="fasesPedido">';
		while (list ($clave, $val) = each ($fases)) {
			
			$paso = $clave + 1;
			$titulo = strtoupper('_FASES_INS_'.$val);
			if (defined($titulo)) {
				$titulo = constant($titulo);
			}
			$titulo = $paso.'. '.$titulo;
			
			if ($clave > 0)
				$codigo .= ' <b>&middot;</b> ';
			
			if ($val == $fase)
				$codigo .= '<span class="titApartadoText">'.$titulo.'</span>';
			else
				$codigo .= $titulo;
		}
		$codigo .= '</div>';
		
		return $codigo;	
	}
	
	// Indica el valor de una variable (setting) de php
	function phpSetting($val)
	{
		$r =  (ini_get($val) == '1' ? 1 : 0);
		return $r ? 'ON' : 'OFF';
	}
	
	// Verifica si existe una funcion PHP y devuelve una fila de tabla HTML con el resultado
	function funcionPHP($valores)
	{
		$codigo = '';
		$codigo .= '<tr>';
		$codigo .= '<td class="dato">'.$valores[0].'</td>';
		$codigo .= '<td class="valor">';
		
		if (function_exists($valores[1]))
			$codigo .= '<b><font color="green">'._SI.'</font></b>';
		else {
			if ($valores[2])
				$codigo .= '<b><font color="orange">'._NO.'</font></b>';
			else
				$codigo .= '<b><font color="red">'._NO.'</font></b>';
		}
		
		$codigo .= '</td></tr>';
		return $codigo;
	}
	
	// Verifica si la version PHP es la minima necesaria y devuelve una fila de tabla HTML con el resultado
	function versionPHP($versionMin)
	{
		$codigo = '';
		$codigo .= '<tr>';
		$codigo .= '<td class="dato">PHP version >= '.$versionMin.'</td>';
		$codigo .= '<td class="valor">';
			
		if (phpversion() < $versionMin)
			$codigo .= '<b><font color="red">'._NO.'</font></b>';
		else
			$codigo .= '<b><font color="green">'._SI.'</font></b>';
	
		$codigo .= '</td></tr>';
		
		return $codigo;
	}
	
	// Verifica un valor de setting de php y lo compara con el requerido
	// Devuelve una fila de tabla HTML con el resultado
	function settingsPHP($sett)
	{
		$codigo = '';
		$codigo .= '<tr>';
		$codigo .= '<td class="dato">'.$sett[0].'</td>';
		$codigo .= '<td>'.$sett[1].'</td>';
		$codigo .= '<td class="valor">';
			
		if ( $this->phpSetting($sett[0]) == $sett[1] )
			$codigo .=	'<font color="green"><b>';
		else
			$codigo .=	'<font color="red"><b>';
	
		$codigo .= $this->phpSetting($sett[0]);
	
		$codigo .= '</b></td></tr>';
		
		return $codigo;
	}
	
	// Verifica si existe permiso de escritura en un directorio
	// Devuelve una fila de tabla HTML con el resultado
	function permisoEscritura( $dir )
	{
		$codigo = '<tr>';
		$codigo .= '<td class="dato">' . $dir . '</td>';
		$codigo .= '<td>';
		$codigo .= is_writable( "../$dir" ) ? '<b><font color="green">'._SI.'</font></b>' : '<b><font color="red">'._NO.'</font></b>' . '</td>';
		$codigo .= '</tr>';
		
		return $codigo;
	}
	
	// Devuelve un combo con los tipos de BD
	function tiposBD($tipoDefecto = '')
	{
		$codigo = '<select name="tipobd">';
		$selected = "selected";
		
		if (function_exists('mysql_connect')) {
			if ($tipoDefecto != "mysql")
				$selected = '';
			$codigo .= '<option '.$selected.' value="mysql">MySQL</option>';
			$selected = '';
		}
		if (function_exists('pg_connect')) {
			if ($tipoDefecto != "mysql")
				$selected = "selected";
			$codigo .= '<option '.$selected.' value="postgresql">PostgreSQL</option>';
		}
		
		return $codigo;
	}
	
	// Comprobaciones de base de datos: conexion y estado
	// La base de datos debe existir y estar vacia (si es distribuida) 
	function accionesBD($arquitectura, $tipo, $servidor, $puerto, $usuario, $password, $nombre) 
	{
		$result = '';
	
		switch($tipo)
		{
			case "mysql":
			
				$link = @mysql_connect($servidor, $usuario, $password)
					or $result = _ERROR_CONN;
					
				if ($result == _ERROR_CONN)
					return _ERROR_CONN;
					
				if (mysql_select_db ($nombre))
					return "OK";
				else
					return _ERROR_CONN_BD;
 					
			break;
		
			case "postgresql":
			
				$options = 'dbname='.$nombre.' port='.$puerto.' user='.$usuario.' host='.$servidor.' password='.$password;
				$link = @pg_connect($options)
 					or $result = _ERROR_CONN_BD;
 					
				if ($link)
					return "OK";
				else
 					return _ERROR_CONN_BD;
 					
			break;
		}
		
		return _ERROR_BD;
    }
    
	
	// Verifica si la base de datos esta vacia
	function bdVacia($tipo, $nombre) 
	{
		$vacia = false;
	
		switch($tipo)
		{
			case "mysql":
				$result = mysql_list_tables ($nombre);
				if (!$result)
					return true;
				if (mysql_num_rows ($result) == 0) 
					return true;
			break;
		
			case "postgresql":
 				$ordenSQL = "select * from empresa";
 				$result = @pg_query($ordenSQL)
 					or $vacia = true;
			break;
		}
		
		return $vacia;
    }
    

	// Crea el fichero configure_bd.php necesario para la web
	function crearConfigureBD($datos) 
	{
		$codigo = "";
		$codigo .= "<?php\n\n";
		$codigo .= "/** @no_class */\n\n";
	
		$codigo .= "define('_DB_TYPE', '".$datos["tipobd"]."');\n";
		$codigo .= "define('_DB_SERVER', '".$datos["servidor"]."');\n";
		$codigo .= "define('_DB_PORT', '".$datos["puerto"]."');\n";
		$codigo .= "define('_DB_SERVER_USERNAME', '".$datos["usuario"]."');\n";
		$codigo .= "define('_DB_SERVER_PASSWORD', '".$datos["password"]."');\n";
		$codigo .= "define('_DB_DATABASE', '".$datos["basedatos"]."');\n";
		$codigo .= "define('_DB_ARQ', '".$datos["arquitectura"]."');\n\n";
		
		$codigo .= "\n?>";
		
		$fD = fopen('../includes/configure_bd.php',"w");
		fwrite ($fD, $codigo);
		fclose($fD);
	}

	// Crea el fichero configure_web.php necesario para la web
	function crearConfigureWeb($datos) 
	{
		if (!isset($datos["ssl_web_root"]))
			$datos["ssl_web_root"] = $datos["web_root"];
		else if (strlen($datos["ssl_web_root"]) == 0)
			$datos["ssl_web_root"] = $datos["web_root"];
	
		// Las direcciones han de terminar en '/'
		if (substr($datos["document_root"], -1) != "/")
			$datos["document_root"] .= '/';
		if (substr($datos["web_root"], -1) != "/")
			$datos["web_root"] .= '/';
		if (substr($datos["ssl_web_root"], -1) != "/")
			$datos["ssl_web_root"] .= '/';
	
		$codigo = "";
		$codigo .= "<?php\n\n";
		$codigo .= "/** @no_class */\n\n";
	
		$codigo .= "define('_DOCUMENT_ROOT', '".$datos["document_root"]."');\n";
		$codigo .= "define('_WEB_ROOT', '".$datos["web_root"]."');\n";
		$codigo .= "define('_WEB_ROOT_SSL', '".$datos["ssl_web_root"]."');\n\n";
		
		$codigo .= "define('_ANCHO_COL', 130);\n";
		$codigo .= "define('_NUM_PAG_GRUPO', 3);\n";		
		
		$codigo .= "\n?>";

		$fD = fopen('../includes/configure_web.php',"w");
		fwrite ($fD, $codigo);
		fclose($fD);
	}

	// Para la base de datos distribuida, se crean las tablas necesarias a partir del fichero sql
	function volcarBD($sqlfile, $database, $forzar = false) 
	{
		$__BD = new funBD;
		$__BD->conectaBD();
		
		// Comprobaciones previas
		if (!$this->bdVacia(_DB_TYPE, _DB_DATABASE) && ! $forzar)
			return true;
	
		$datosSQL = '';
		$fd = fopen ($sqlfile, "r");
		
		$instrucciones = array();
		$numIns = 0;
		$instrucciones[$numIns] = '';
		
		while (!feof($fd)) {
			$buffer = fgets($fd, 4096);
			$buffer = trim($buffer);
			if (strlen($buffer) == 0)
				continue;
			if (substr($buffer, 0, 2) == '--')
				continue;
			
			$buffer = str_replace("\n", " ", $buffer);
			
			$instrucciones[$numIns] .= $buffer.' ';
			if (substr($buffer, -1) == ';') {
				$numIns++;
				$instrucciones[$numIns] = '';
			}
		}
		fclose ($fd);
		
		for ($i=0; $i<count($instrucciones); $i++) {
			$instruccion = $instrucciones[$i];
 			if (strtoupper(substr($instruccion, 0, 6) == 'GRANT ')) continue;
 			if (strtoupper(substr($instruccion, 0, 7) == 'REVOKE ')) continue;
 			if (!strlen(trim($instruccion))) continue;
   			$result = @$__BD->db_query($instruccion);
		}
		
	}


	// Comprueba que la base de datos existe y tiene ciertas tablas indicativas de que ya fue creada
	function comprobarBD($tipo)
	{
		$__BD = new funBD;
		$__BD->conectaBD();
	
		$diagnosis = true;
	
		$tablas = array("clientes_codcliente_seq");
		if ($tipo == "mysql")
			$tablas = array("clientes_seq","dirclientes_seq","pedidoscli_seq","lineaspedidoscli_seq");

		foreach ($tablas as $tabla) {
			$ordenSQL = "select * from ".$tabla;
			$result = $__BD->db_query($ordenSQL) or
				$diagnosis = false;
				
			if (!$diagnosis)
				return false;
		}
		set_time_limit(30);
		
		return $diagnosis;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_funLibreria */
class funLibreria extends oficial_funLibreria {};

?>