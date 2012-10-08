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

/** @class_definition oficial_pedidos */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_pedidos
{
	
	// Se muestra el listado de pedidos para un cliente
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
			
		echo $__CLI->seccionCuenta('pedidos');
		
		$codigo = "";
		if (isset($CLEAN_GET["codigo"])) {
			$codigo = $CLEAN_GET["codigo"];	
			$codCliente = $_SESSION["codCliente"];
			$id = $__BD->db_valor("select idpedido from pedidoscli where codigo='$codigo' and codcliente = '$codCliente'");
		
			if (!$id) {
				echo _PEDIDO_ERRONEO;
				echo '</div>';
				include("../includes/right_bottom.php");
				exit;
			}
	
			echo $__LIB->imprimirDocFacturacion("pedido", $id);
		}
		else
			echo $__CLI->docsFacturacion('pedidos');
		
		echo '<p class="separador"/>';
		echo '</div>';
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_pedidos */
class pedidos extends oficial_pedidos {};

$iface_pedidos = new pedidos;
$iface_pedidos->contenidos();

?>

<?php include("../includes/right_bottom.php") ?>