<?php include("../includes/top_left.php") ?>

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

/** @class_definition oficial_favoritos */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_favoritos
{
	// Muestra los articulos favoritos del cliente
	function contenidos() 
	{
		global $__BD, $__CAT, $__LIB, $__SEC, $__CLI;
		global $CLEAN_GET;
	
		$__LIB->comprobarCliente(true);
		
		echo '<div class="titPagina">'._FAVORITOS.'</div>';
		
		echo '<div class="cajaTexto">';
		
		$__CLI->seccionCuenta('favoritos');
		
		echo '<div class="titApartado">'._FAVORITOS.'</div>';
			
		$codCliente = $__CLI->codCliente;
		
		$referencia = '';
		$accion = '';
		
		if (isset($CLEAN_GET["ref"]))
			$referencia = $CLEAN_GET["ref"];
			
		if (isset($CLEAN_GET["acc"]))
			$accion = $CLEAN_GET["acc"];
		
		$error = '';
		
		// Nuevo articulo que se agrega a favoritos
		if ($accion == "add") {
		
			$ordenSQL = "select referencia from favoritos where codcliente = '".$codCliente."' AND referencia = '$referencia'";
			if ($__BD->db_valor($ordenSQL))
				$error = _EN_FAVORITOS;
				
			$ordenSQL = "select referencia from articulos where referencia = '$referencia' AND publico = true";
			if (!$__BD->db_valor($ordenSQL))
				$error = _NO_ARTICULOS;
				
			if (!$error) {
				$codigo = $referencia.$codCliente;
				$ordenSQL = "insert into favoritos (codigo,referencia,codcliente) values ('$codigo', '$referencia', '$codCliente')";
				$result = $__BD->db_query($ordenSQL);
				if (!$result)
					$error = _ERROR;
			}
			
		}
		// Articulo que se elimina de favoritos
		if ($accion == "del") {	
			$ordenSQL = "delete from favoritos where referencia = '$referencia' and codcliente = '$codCliente'";
			$result = $__BD->db_query($ordenSQL);
			if (!$result)
				$error = _ERROR;
		}
		
		if ($error)
			echo '<div class="msgInfo">'.$error.'</div>';
		
		
		// Se muestran los favoritos como un listado de articulos
		$ordenSQL = "select a.referencia as referencia, descripcion, pvp, codimpuesto, stockfis, stockmin, controlstock, codplazoenvio, enoferta, pvpoferta from articulos a inner join favoritos f on a.referencia = f.referencia where f.codcliente = '$codCliente'";
		echo $__CAT->articulosLista($ordenSQL);
		
		echo '</div>';

	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_favoritos */
class favoritos extends oficial_favoritos {};

$iface_favoritos = new favoritos;
$iface_favoritos->contenidos();

?>

<?php include("../includes/right_bottom.php") ?>