<?php include("../includes/top_left.php") ?>

<?php

error_reporting(E_USER_NOTICE);
		

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
		
		echo '<div class="titPagina">'._CREAR_PEDIDO.'</div>';
		echo '<div class="cajaTexto">';
	
		if ($_SESSION["cesta"]->cestaVacia()) {
			echo _CESTA_VACIA;
			echo '</div>';
			return;
		}
	
		echo $__LIB->fasesPedido('envio');
	
	
		echo '<form name="datosDirEnv" id="datosDirEnv" action="datos_pago.php" method="post">';
		echo '<input type="hidden" name="ambito" value="cesta_envio">';
		
		if ($__LIB->esTrue($_SESSION["opciones"]["activarcoddescuento"])) {
			unset($_SESSION["pedido"]);
			$_SESSION["pedido"]["coddescuento"] = $CLEAN_POST["coddescuento"];
		}
		
		
		echo '<div class="titApartado">'._DIRECCION_ENV.'</div>';
		
		// Datos personales (nombre, empresa)
		$datosPer = $__CLI->datosPersonales();
		echo formularios::nombreEnv($datosPer);
		
		// Direccion de envio
		$dirEnv = $__CLI->direccionEnv();
		if (!$dirEnv[0])
			$dirEnv = $__CLI->direccionFact();
		echo formularios::dirEnv($dirEnv, 'datosDirEnv');
	
	
	
	
		echo '<div class="titApartado">'._FORMA_ENVIO.'</div>';
		
		
		echo '<div id="datosEnvio">';
		
		$datosEnvio = $__LIB->formasEnvio($dirEnv[4], $dirEnv[3]);
		if ($datosEnvio)
			echo $datosEnvio;
		else
			echo _NO_FORMAS_ENVIO;
			
		echo '</div>';
		
		echo '</form>';
		
		echo '<p class="separador">';
		
		echo '<div id="divContinuar">';
		if ($datosEnvio)
	 		echo '<p class="separador"><a class="botContinuar" href="javascript:document.datosDirEnv.submit()">'._CONTINUAR.'</a>';
		echo '</div>';
		
		echo '</div>';
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