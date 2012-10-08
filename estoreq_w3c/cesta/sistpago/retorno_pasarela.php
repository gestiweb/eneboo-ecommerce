<?php include("../../includes/top_left.php") ?>

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

/** @class_definition oficial_retornoPasarela */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_retornoPasarela
{
	/**	Verifica a la vuelta de la pasarela que los datos son correctos.
	*/
	function contenidos()
	{
		global $__BD, $CLEAN_GET, $CLEAN_POST, $__LIB;
		
		$resultado["error"] = '';
		
		$keyPedido = '';
		if (isset($CLEAN_GET["code"]))
			$keyPedido = $CLEAN_GET["code"];
		
		$keySesion = '';
		if (isset($_SESSION["key"]))
			$keySesion = $_SESSION["key"];
			
		$codCliente = '';
		if (isset($_SESSION["codCliente"]))
			$codCliente = $_SESSION["codCliente"];
		
		$timeStamp = time();
		
		echo '<h1>'._CREAR_PEDIDO.'</h1>';
		echo '<div class="cajaTexto">';

		// Se eliminan los registros de la misma sesion y mismo cliente
		$ordenSQL = "select codigo, keypedido, timestamp from pagospedidos where codcliente = '$codCliente' and sessionid = '$keySesion'";
		$result = $__BD->db_query($ordenSQL);
		$row = $__BD->db_fetch_array($result);
		
		if ($keyPedido != $row["keypedido"]) {
			echo _ERROR_PAGO;
			include("../../includes/right_bottom.php");
			echo '</div>';
			exit;
		}
			
		$ordenSQL = "select idpedido from pedidoscli where codigo='".$row["codigo"]."'";
		$idPedido = $__BD->db_valor($ordenSQL);

		// øYa se registrÛ el pago?
		$ordenSQL = "select pagado from pedidoscli where codigo='".$row["codigo"]."'";
		$pagado = $__BD->db_valor($ordenSQL);
		if (!$__LIB->esTrue($pagado)) {
			$ordenSQL = "update pedidoscli set pagado = true where codigo='".$row["codigo"]."'";
			$result = $__BD->db_query($ordenSQL);
			if (!$result) {
				echo _ERROR_PAGO;
				include("../../includes/right_bottom.php");
				echo '</div>';
				exit;
			}
			$__LIB->enviarMailPedido($idPedido);

			// Vaciar la cesta y el pedido
			unset($_SESSION["pedido"]);
			unset($_SESSION["cesta"]);
		}
 
		echo $__LIB->fasesPedido('creado');
		echo '<p>';
		echo '<div class="msgInfo">'._PEDIDO_PAGO_CORRECTO.'</div>';

		echo $__LIB->imprimirDocFacturacion("pedido", $idPedido);
		echo '<p><br/><a href="'._WEB_ROOT_SSL.'cuenta/pedidos.php">'._LISTA_PEDIDOS.'</a>';

		echo '</div>';
	}

}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_retornoPasarela */
class retornoPasarela extends oficial_retornoPasarela {};

$iface_retornoPasarela = new retornoPasarela;
$iface_retornoPasarela->contenidos();

?>

<?php include("../../includes/right_bottom.php") ?>