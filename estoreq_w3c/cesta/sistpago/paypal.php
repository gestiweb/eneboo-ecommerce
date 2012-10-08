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

/** @class_definition oficial_paypal */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_paypal
{
	/** A esta pagina se viene cuando la forma de pago corresponde a una pasarela, antes de crear el pedido. Desde aqui se crea un registro para el nuevo pedido, y a continuacion se redirecciona a la pasarela de pago.
	*/
	function contenidos($codPedido) 
	{
		global $__LIB, $__BD;

		$keyPedido = strtolower($__LIB->generarPassword(30));
		
		$keySesion = '';
		if (isset($_SESSION["key"]))
			$keySesion = $_SESSION["key"];
			
		$codCliente = '';
		if (isset($_SESSION["codCliente"]))
			$codCliente = $_SESSION["codCliente"];
		
		$timeStamp = time();
			
		$error = '';
		if (!$keySesion || !$codCliente)
			$error = _ERROR_PAGO;
		
		// Se eliminan los registros de la misma sesion y mismo cliente
		$ordenSQL = "delete from pagospedidos where codcliente = '$codCliente' and sessionid = '$keySesion'";
		$result = $__BD->db_query($ordenSQL);
		if (!$result)
			$error = _ERROR_PAGO;
		
		if ($error) {
			echo '<div class="msgError">'.$error.'</div>'; 
			include("../../../includes/right_bottom.php");
			exit;
		}
		
		// Se registra el pedido
		$result = false;
		while (!$result) {
			$ordenSQL = "insert into pagospedidos (keypedido, codigo, timestamp, codcliente, sessionid) values('$keyPedido','$codPedido','$timeStamp','$codCliente','$keySesion')";
			$result = $__BD->db_query($ordenSQL);
			if (!$result)
				$keyPedido = strtolower($__LIB->generarPassword(30));
		}

		// Se obtiene de la forma de pago
		$total = round(100 * $_SESSION["cesta"]->total()) / 100;
	
		$urlBack = _WEB_ROOT_SSL.'cesta/sistpago/retorno_pasarela.php?code='.$keyPedido;
		$urlCancel = _WEB_ROOT_SSL.'cesta/sistpago/retorno_pasarela.php';

		$ordenSQL = "select codpasarela from formaspago fp inner join pedidoscli p on fp.codpago = p.codpago where p.codigo = '$codPedido'";
		$codPasarela = $__BD->db_valor($ordenSQL);

		if (!$codPasarela) {
			echo '<div class="msgError">'.$error.'</div>'; 
			include("../../../includes/right_bottom.php");
			exit;
		}

		$amount = $this->calcularImporte($codPedido);

		$ordenSQL = "select parametro, valor from parametrospasarela where codpasarela = '$codPasarela'";
		$result = $__BD->db_query($ordenSQL);
		$parametros = '';
		while ($row = $__BD->db_fetch_array($result))
			$parametros .= '<input type="hidden" name="'.$row["parametro"].'" value="'.$row["valor"].'">';

		echo '<p>'._REDIRECCION_PAGO;


// 		echo $urlBack;return;
	
// 		echo '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" name="paypal">
 		echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="paypal">
			<input type="hidden" name="no_note" value="1">
			<input type="hidden" name="cmd" value="_xclick">
			<input type="hidden" name="item_name" value="'._PEDIDO.'">
			<input type="hidden" name="item_number" value="'.$codPedido.'">
			<input type="hidden" name="amount" value="'.$amount.'">
			<input type="hidden" name="no_shipping" value="1">
			<input type="hidden" name="rm" value="2">
			<input type="hidden" name="return" value="'.$urlBack.'">
			<input type="hidden" name="cancel_return" value="'.$urlCancel.'">
			'.$parametros.'
			</form>';

// 		echo '<p><br/><br/><a class="botlink" href="javascript:document.paypal.submit()">'._REALIZAR_PAGO_MEDIANTE.' PayPal</a>';

		echo '
		<SCRIPT language="JavaScript">
			setTimeout(\'sendForm()\',5000)
				
			function sendForm(){
			  document.paypal.submit();
			}
		</SCRIPT> ';
	}

	function calcularImporte($codPedido)
	{
		global $__BD;
		$ordenSQL = "select codpago,codenvio from pedidoscli where codigo = '$codPedido'";
		$codPE = $__BD->db_row($ordenSQL);

		$precio = $_SESSION["cesta"]->total($codPE[0], $codPE[1]);
		$valor = number_format($precio,2,".","");
		return $valor;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_paypal */
class paypal extends oficial_paypal {};

$iface_paypal = new paypal;
$iface_paypal->contenidos($codigo);


?>