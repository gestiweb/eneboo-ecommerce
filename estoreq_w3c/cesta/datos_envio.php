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

/** @class_definition oficial_datosEnvio */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_datosEnvio
{
	// Muestra las opciones y los datos de envio
	function contenidos() 
	{	
		global $__BD, $__CAT, $__LIB, $__CLI;
		global $CLEAN_GET, $CLEAN_POST;
		
		$__LIB->comprobarCliente(true);
		
		$codigo = '';
		
		$codigo .= '<h1>'._CREAR_PEDIDO.'</h1>';
		$codigo .= '<div class="cajaTexto">';
	
		if ($_SESSION["cesta"]->cestaVacia()) {
			$codigo .= _CESTA_VACIA;
			$codigo .= '</div>';
			return;
		}
	
		$codigo .= $__LIB->fasesPedido('envio');
	
	
		$codigo .= '<form name="datosDirEnv" id="datosDirEnv" action="'._WEB_ROOT_SSL_L.'cesta/datos_pago.php" method="post">';
		$codigo .= '<input type="hidden" name="ambito" value="cesta_envio">';
		
 		if ($__LIB->esTrue($_SESSION["opciones"]["activarcoddescuento"])) {
			if (isset($CLEAN_POST["coddescuento"]))
				$_SESSION["pedido"]["datosEnv"]["coddescuento"] = $CLEAN_POST["coddescuento"];
 			$codDescuento = isset($_SESSION["pedido"]["datosEnv"]["coddescuento"]) ? $_SESSION["pedido"]["datosEnv"]["coddescuento"] : '';
		}
		
		$codigo .= '<h2>'._DIRECCION_ENV.'</h2>';
		
		// Datos personales (nombre, empresa)
		if (isset($_SESSION["pedido"]["datosEnv"]))
			$datosPer = $_SESSION["pedido"]["datosEnv"];
		else
			$datosPer = $__CLI->datosPersonales();
		
		$codigo .= formularios::nombre($datosPer);
		
		// Direccion de envio
		if (isset($_SESSION["pedido"]["datosEnv"])) {
			$suf = '_env';
			$dirEnv = $_SESSION["pedido"]["datosEnv"];
		}
		else {
			$dirEnv = $__CLI->direccionEnv();
			if (!$dirEnv["direccion"])
				$dirEnv = $__CLI->direccionFact();
			$suf = '';
		}
		
		$codigo .= formularios::dirEnv($dirEnv, 'datosDirEnv', array(), $suf);
	
	
	
	
		$codigo .= '<h2>'._FORMA_ENVIO.'</h2>';
		
		
		$codigo .= '<div id="datosEnvio">';
		
		$datosEnvio = $__LIB->formasEnvio($dirEnv["codpais$suf"], $dirEnv["provincia$suf"]);
		if ($datosEnvio)
			$codigo .= $datosEnvio;
		else
			$codigo .= _NO_FORMAS_ENVIO;
			
		$codigo .= '</div>';
		
		
		if ($__LIB->esTrue($_SESSION["opciones"]["activarcoddescuento"])) {
			$codigo .= '<h2>'._CODIGO_DTO.'</h2>';
			$codigo .= _INTRO_CODIGO_DTO.' <input name="coddescuento" size="10" value="'.$codDescuento.'"> ';
			$codigo .= '<a href="#" class="botLink" onclick="xajax_verificarDto(xajax.getFormValues(\'datosDirEnv\')); return false;">'._VERIFICAR.'</a>';
			$codigo .= ' <div id="msgDescuento"></div>';
		}
		
		$codigo .= '<p class="separador">';
		
		$codigo .= '<div id="divContinuar">';
		$codigo .= '<a class="button" href="general/cesta.php"><span>'._VOLVER.'</span></a>';
		if ($datosEnvio) 
			$codigo .= '<button type="submit" value="'._CONTINUAR.'" class="submitBtn"><span>'._CONTINUAR.'</span></button>';
		$codigo .= '</p>';
		$codigo .= '</div>';
		
		$codigo .= '</form>';
		
		$codigo .= '</div>';
		
		echo $codigo;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_datosEnvio */
class datosEnvio extends oficial_datosEnvio {};

$iface_datosEnvio = new datosEnvio;
$iface_datosEnvio->contenidos();

?>


<?php include("../includes/right_bottom.php") ?>