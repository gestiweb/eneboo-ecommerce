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
ini_set('display_errors', true);
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
		
		$codigo = '';
		
		$codigo .= '<h1>'._CREAR_PEDIDO.'</h1>';
		$codigo .= '<div class="cajaTexto">';
	
		$codigo .= $__LIB->fasesPedido('pago');
		
		
		if (isset($CLEAN_POST["contacto"])) {
			
			$error = $__LIB->comprobarPedido($CLEAN_POST, false);	
			if ($error) {
				$codigo .= '<div class="msgError">'.$error.'</div>';
				$codigo .= '<a href="javascript:history.go(-1)">'._VOLVER.'</a>';
				echo $codigo;
				include("../includes/right_bottom.php");
				exit;
			}
			
			// Nueva sesion de pedido
			unset($_SESSION["pedido"]);
			
			// Los datos de envio vienen del post
			$_SESSION["pedido"]["datosEnv"] = $CLEAN_POST;
			
			// Validado el post, cargamos la misma pagina sin el mismo (para evitar repetir el POST)
			echo '<script type="text/javascript">window.location = "'._WEB_ROOT_SSL_L.'cesta/datos_pago.php"</script>';
 			exit;
		}
		
		$codigo .= '<form name="datosDir" id="datosDir" action="'._WEB_ROOT_SSL_L.'cesta/confirmar_pedido.php" method="post">';
		$codigo .= '<input type="hidden" name="ambito" value="cesta_pago">';

		$codigo .= '<h2>'._DIRECCION_FACT.'</h2>';
	
		// Datos personales (nombre, empresa)
		$datosPer = $__CLI->datosPersonales();
		
		
		if ($__LIB->esTrue($_SESSION["opciones"]["solicitarnif"]))
			$codigo .= formularios::nombre($datosPer, true);
		else
			$codigo .= formularios::nombre($datosPer);
			
		// Direccion de facturacion
		$dirFact = $__CLI->direccionFact();
		$codigo .= formularios::dirFact($dirFact, 'datosDir');
		
		$codigo .= '<h2>'._FORMA_PAGO.'</h2><br/>';
		
		$codigo .= '<div id="datosPago">';
		
		$datosPago = $__LIB->formasPago($dirFact["codpais"], $dirFact["provincia"]);
		if ($datosPago)
			$codigo .= $datosPago;
		else
			$codigo .= _NO_FORMAS_PAGO;
		
		$codigo .= '</div>';
		
		$codigo .= '<p class="separador">';
		
		$codigo .= '<div id="divContinuar">';
		$codigo .= '<p class="separador">';
		$codigo .= '<a class="button" href="cesta/datos_envio.php"><span>'._VOLVER.'</span></a>';
		if ($datosPago) {
			$codigo .= '<button type="submit" value="'._CONTINUAR.'" class="submitBtn"><span>'._CONTINUAR.'</span></button>';
		}
		$codigo .= '</p></div>';
		
		$codigo .= '</form>';
		
		$codigo .= '</div>';
		
		echo $codigo;
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