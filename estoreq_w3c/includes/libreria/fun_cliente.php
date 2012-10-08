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

/** @class_definition oficial_cliente */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

// Clase de funcionalidades del cliente
class oficial_cliente {
	var $nombre;
	var $codCliente;

	function oficial_cliente() 
	{
	 	if (isset($_SESSION["codCliente"]))
			$this->codCliente = $_SESSION["codCliente"];
	}
	
	// Listado de documentos de facturación del cliente
	function docsFacturacion($tipoDoc) 
	{
		global $__BD, $__CAT;
		
		switch ($tipoDoc) {
			case "pedidos":
				$tabla = "pedidoscli";
				$titulo = _PEDIDOS;
			break;
			case "albaranes":
				$tabla = "albaranescli";
				$titulo = _ALBARANES;
			break;
			case "facturas":
				$tabla = "facturascli";
				$titulo = _FACTURAS;
			break;
			default:
				return;
		}
		
		$codigo = '';
// 		$codigo = '<h2>'.$titulo.'</h2>';
		
		$ordenSQL = "select codigo, fecha, total from $tabla where codcliente = '".$this->codCliente."' order by codigo DESC";
		
		if ($__BD->db_num_rows($ordenSQL) == 0) {
			$codigo .= '<p>'. _NO_DATOS;
			return $codigo;
		}
		
		$result = $__BD->db_query($ordenSQL);
		
		$codigo .= '<table class="docsFacturacion">';
		$codigo .= '<tr>';
		
		$codigo .= '<th class="codigo">'._CODIGO.'</th>';
		$codigo .= '<th class="fecha">'._FECHA.'</th>';
		$codigo .= '<th class="total">'._TOTAL.'</th>';
		
		while ($row = $__BD->db_fetch_array($result)) {
			$fecha = date("d-m-Y", strtotime($row["fecha"]));
			$codigo .= '<tr>';
 			$codigo .= '<td><a href="'._WEB_ROOT.'cuenta/'.$tipoDoc.'.php?codigo='.$row["codigo"].'">'.$row["codigo"].'</a></td>';
			$codigo .= '<td class="fecha">'.$fecha.'</td>';
			$codigo .= '<td class="total">'.$__CAT->precioDivisa($row["total"]).'</td>';
			$codigo .= '</tr>';
		}
		
		$codigo .= '</table>';
		
		return $codigo;
	}
	
	
	function nombre()
	{
		global $__BD;
		return $__BD->db_valor("select nombre from clientes where codcliente = '".$this->codCliente."'");
	}
	
	function datosPersonales()
	{
		global $__BD, $__LIB;
		
		$ordenSQL = "select email, contacto, apellidos, nombre, telefono1, fax, esempresa, cifnif from clientes where codcliente = '".$this->codCliente."'";
		$result = $__BD->db_query($ordenSQL);
		$row = $__BD->db_fetch_assoc($result);
		if ($__LIB->esTrue($row["esempresa"])) {
			$row["empresa"] = $row["nombre"];
		}
		else {
			$row["empresa"] = '';
		}
		
		return $row;
	}
	
	function direccionFact()
	{
		global $__BD;
		$ordenSQL = "select direccion, codpostal, ciudad, provincia, codpais, id from dirclientes where codcliente = '".$this->codCliente."' and domfacturacion = true";
		$result = $__BD->db_query($ordenSQL);
		return $__BD->db_fetch_assoc($result);
	}
	
	function direccionEnv()
	{
		// Solo se devuelve si es distinta de la de facturacion
		global $__BD;
		$ordenSQL = "select direccion, codpostal, ciudad, provincia, codpais, id from dirclientes where codcliente = '".$this->codCliente."' and domenvio = true and domfacturacion = false";
		$result = $__BD->db_query($ordenSQL);
		return $__BD->db_fetch_assoc($result);
	}
	
	// Barra de navegacion de las paginas de cuenta
	function seccionCuenta($seccion = '-1')
	{
		global $__LIB;
		
		$secciones = array('editar_cuenta','pedidos','albaranes','facturas','favoritos','salir_sesion');
		
		$codigo = '<div class="seccionesCuenta">';
		while (list ($clave, $val) = each ($secciones)) {
			
			if (!$__LIB->esTrue($_SESSION["opciones"]["mostrarfacturas"]) && $val == 'facturas')
				continue;			
			if (!$__LIB->esTrue($_SESSION["opciones"]["mostraralbaranes"]) && $val == 'albaranes')
				continue;			
			
			$titulo = strtoupper('_'.$val);
			if (defined($titulo)) {
				$titulo = constant($titulo);
			}
			
			if ($clave > 0)
				$codigo .= ' <b>&middot;</b> ';
			
			if ($val == $seccion)
 				$codigo .= '<span class="titSeccionCuenta"><a href="cuenta/'.$val.'.php">'.$titulo.'</a></span>';
			else
				$codigo .= '<a href="cuenta/'.$val.'.php">'.$titulo.'</a>';
		}
		$codigo .= '</div>';
		
		return $codigo;	
	}
	
	// Opciones de la cuenta
	function cuenta()
	{
		global $__LIB;
		
		echo _BIENVENIDO.' <b>'.$this->nombre().'</b>';
		echo '<br/><br/>'. _TEXT_CUENTA;
		echo '<ul>';
		echo '<li><a href="cuenta/editar_cuenta.php">'._EDITAR_CUENTA.'</a></li>';
		echo '<li><a href="cuenta/pedidos.php">'._PEDIDOS.'</a></li>';
		if ($__LIB->esTrue($_SESSION["opciones"]["mostraralbaranes"]))
			echo '<li><a href="cuenta/albaranes.php">'._ALBARANES.'</a></li>';
		if ($__LIB->esTrue($_SESSION["opciones"]["mostrarfacturas"]))
			echo '<li><a href="cuenta/facturas.php">'._FACTURAS.'</a></li>';
		echo '<li><a href="cuenta/favoritos.php">'._FAVORITOS.'</a></li>';
		echo '<li><a href="general/cesta.php">'._VER_CESTA.'</a></li>';
		echo '<li><a href="cuenta/salir_sesion.php">'._SALIR_SESION.'</a></li>';
		echo '</ul>';
	}
	
	// Actualizacion de datos personales
	function actualizarDatos($datos) 
	{
		global $__BD;
		
		if (!$datos["contacto"] || !$datos["apellidos"])
			return _RELLENAR_TODOS_CAMPOS;
		
		if ($datos["nombre"])
			$esEmpresa = "true";
		else {
			$esEmpresa = "false";
			$datos["nombre"] = $datos["contacto"].' '.$datos["apellidos"];
		}
	
		$ordenSQL = 'update clientes set ';
		
		$datosCli = array("contacto", "apellidos", "nombre", "telefono1", "fax");
		while (list ($clave, $campo) = each ($datosCli)) {
			if ($campo != "contacto")
				$ordenSQL .= ', ';
			$ordenSQL .= $campo.' = \''.$datos[$campo].'\'';
		}
			
		$ordenSQL .= ', modificado = true';
		$ordenSQL .= ', esempresa = '.$esEmpresa;
		$ordenSQL .= ' where codcliente = \''.$this->codCliente.'\'';
		
		if ($__BD->db_query($ordenSQL))
			return 'ok';
		else
			return _ERROR_DB;
	}

	function actualizarPassword($datos) 
	{
		global $__BD;
		
		if (!$datos["password"])
			return _RELLENAR_TODOS_CAMPOS;
			
		if (trim(strlen($datos["password"]) < 6))
			return _PASSWORD_MIN_6;
		
		if ($datos["password"] != $datos["confirmacion"])
			return _PASSWORD_DISTINTO;
	
		$password = sha1($datos["password"]);
	
		$ordenSQL = 'update clientes set password =\''.$password.'\', modificado = true where codcliente = \''.$this->codCliente.'\'';
		
		if ($__BD->db_query($ordenSQL))
			return 'ok';
		else
			return _ERROR_DB;
	}

	
	// Si es de envio, $tipo='_env'
	function actualizarDir($datos, $tipo = '')
	{
		global $__BD;
		
		$ordenSQL = 'update dirclientes set ';
		
		$datosDir = array ("direccion", "codpostal", "ciudad", "provincia", "codpais");
		while (list ($clave, $campo) = each ($datosDir)) {
			
			if ($campo != "direccion")
				$ordenSQL .= ', ';
			
 			$ordenSQL .= $campo.' = \''.$datos[$campo.$tipo].'\'';
							
			if (strlen(trim($datos[$campo.$tipo])) == 0)
				return constant("_RELLENAR_TODOS_CAMPOS".strtoupper($tipo));
		}
		
		$ordenSQL .= ', modificado = true';
		$ordenSQL .= ' where id = '.$datos["id"];
		
		if ($__BD->db_query($ordenSQL))
			return 'ok';
		else
			return _ERROR_DB;
	}

	function introducirDirEnv($datos) 
	{
		global $__BD;
		
		$datosDir = array ("direccion", "codpostal", "ciudad", "provincia", "codpais");
		
		$ordenSQL1 = 'insert into dirclientes (codcliente';
		$ordenSQL2 = ' values(\''.$this->codCliente.'\'';
				
		while (list ($clave, $campo) = each ($datosDir)) {
			$ordenSQL1 .= ', '.$campo;
 			$ordenSQL2 .= ', \''.$datos[$campo.'_env'].'\'';		
			if (strlen(trim($datos[$campo.'_env'])) == 0)
				return _RELLENAR_TODOS_CAMPOS_ENV;
		}
		
		$id = $__BD->nextId("dirclientes", "id");
		$ordenSQL = $ordenSQL1.', domenvio, domfacturacion, id)'.$ordenSQL2.', true, false, '.$id.')';
		
		// El resto de direcciones ya no son de envio
		if ($__BD->db_query($ordenSQL)) {
			$__BD->db_query("update dirclientes set domenvio = false, modificado = true where codcliente = '".$this->codCliente."' AND id <> $id");
			return 'ok';
		}
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_cliente */
class cliente extends oficial_cliente {
	function cliente() {
	 	$this->oficial_cliente();
	}
}

?>