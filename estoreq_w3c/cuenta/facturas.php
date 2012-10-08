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

/** @class_definition oficial_facturas */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_facturas
{
	
	// Se muestra el listado de facturas para un cliente
	function contenidos() 
	{
		global $__CLI, $__LIB, $CLEAN_GET, $__BD;
		
		$__LIB->comprobarCliente(true);
		
		echo '<h1>'._MI_CUENTA.'</h1>';
		
		echo '<div class="cajaTexto">';
		
		if (!isset($_SESSION["codCliente"])) {
			echo _DEBES_LOGIN;
			include("../includes/right_bottom.php");
			echo '</div>';
			exit;
		}
			
		if (!$__LIB->esTrue($_SESSION["opciones"]["mostrarfacturas"])) {
			echo _SECCION_NO_DISPONIBLE;
			include("../includes/right_bottom.php");
			echo '</div>';
			exit;
		}
		
		$__CLI->seccionCuenta('facturas');
		
		$codigo = "";
		if (isset($CLEAN_GET["codigo"])) {
			$codigo = $CLEAN_GET["codigo"];	
			$codCliente = $_SESSION["codCliente"];
			$id = $__BD->db_valor("select idfactura from facturascli where codigo='$codigo' and codcliente = '$codCliente'");
		
			if (!$id) {
				echo _FACTURA_ERRONEO;
				echo '</div>';
				include("../includes/right_bottom.php");
				exit;
			}
	
			echo $__LIB->imprimirDocFacturacion("factura", $id);
		}
		else
			echo $__CLI->docsFacturacion('facturas');
		
		echo '<p class="separador"/>';
		echo '</div>';
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_facturas */
class facturas extends oficial_facturas {};

$iface_facturas = new facturas;
$iface_facturas->contenidos();

?>

<?php include("../includes/right_bottom.php") ?>