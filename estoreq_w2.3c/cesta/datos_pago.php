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

/** @class_definition oficial_datosPago */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_datosPago
{
	// Muestra las opciones y los datos de pago
	function contenidos() 
	{	
		global $__BD, $__CAT, $__LIB, $__CLI;
		global $CLEAN_GET, $CLEAN_POST;
		
		$__LIB->comprobarCliente(true);
		
		echo '<div class="titPagina">'._CREAR_PEDIDO.'</div>';
		echo '<div class="cajaTexto">';
	
		echo $__LIB->fasesPedido('pago');
		
		$error = $__LIB->comprobarPedido($CLEAN_POST, false);	
		if ($error) {
			echo '<div class="msgError">'.$error.'</div>';
			echo '<a href="javascript:history.go(-1)">'._VOLVER.'</a>';
			include("../includes/right_bottom.php");
			exit;
		}
		
		// Nueva sesion de pedido
		unset($_SESSION["pedido"]);
		
		// Los datos de envio vienen del post
		$_SESSION["pedido"]["datosEnv"] = $CLEAN_POST;
		
		echo '<form name="datosDir" id="datosDir" action="confirmar_pedido.php" method="post">';
		echo '<input type="hidden" name="ambito" value="cesta_pago">';

		echo '<div class="titApartado">'._DIRECCION_FACT.'</div>';
	
		// Datos personales (nombre, empresa)
		$datosPer = $__CLI->datosPersonales();
		
		
		if ($__LIB->esTrue($_SESSION["opciones"]["solicitarnif"]))
			echo formularios::nombre($datosPer, true);
		else
			echo formularios::nombre($datosPer);
			
		// Direccion de facturacion
		$dirFact = $__CLI->direccionFact();
		echo formularios::dirFact($dirFact, 'datosDir');
		
		echo '<div class="titApartado">'._FORMA_PAGO.'</div><br>';
		
		echo '<div id="datosPago">';
		
		$datosPago = $__LIB->formasPago($dirFact[4], $dirFact[3]);
		if ($datosPago)
			echo $datosPago;
		else
			echo _NO_FORMAS_PAGO;
		
		echo '</div>';
		
		
		
		
		// Formas de pago
/*		$ordenSQL = "select * from formaspago where activo = true order by orden";
		$result = $__BD->db_query($ordenSQL);
		$paso = 0;	
		
		while ($row = $__BD->db_fetch_array($result)) {
			
			$codPago = $row["codpago"];
			if ($row["controlporzonas"])
				if (!$__LIB->pagoEnZona($codPago))
			
			$descripcion = $__LIB->traducir("formaspago", "descripcion", $row["codpago"], $row["descripcion"]);;
			$descLarga = $__LIB->traducir("formaspago", "descripcionlarga", $row["codpago"], $row["descripcionlarga"]);
			
			echo '<div class="formaPago">';

			echo '<div class="checkPago">';
			echo '<input type="radio" name="codpago" value="'.$row["codpago"].'"';
			if ($paso++ == 0)
				echo ' checked';
			echo '></div>';

			echo '<div class="labelPago">';
			echo $descripcion;
			echo '</div>';
			
			if (strlen(trim($descLarga)) > 0) {
				echo '<div class="descPago">';
				echo nl2br($descLarga);
				echo '</div>';
			}
			
			if ($__LIB->esTrue($row["gastos"])) {
				echo '<div class="gastosPago">';
				echo _AVISO_GASTOS_PAGO.' '.$row["gastos"].'%';
				echo '</div>';
			}
			
			if ($__LIB->esTrue($row["gastosfijo"])) {
				echo '<div class="gastosPago">';
				echo _AVISO_GASTOS_PAGO.' '.$__CAT->precioDivisa($row["gastosfijo"]);
				echo '</div>';
			}
			
			echo '<br class="cleanerLeft"/>';
			echo '</div>';
		}
	*/
		echo '</form>';
		
		echo '<p class="separador">';
		
		echo '<div id="divContinuar">';
		if ($datosPago)
	 		echo '<p class="separador"><a class="botContinuar" href="javascript:document.datosDir.submit()">'._CONTINUAR.'</a>';
		echo '</div>';
				
		echo '</div>';
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_datosPago */
class datosPago extends oficial_datosPago {};

$iface_datosPago = new datosPago;
$iface_datosPago->contenidos();
		
?>


<?php include("../includes/right_bottom.php") ?>