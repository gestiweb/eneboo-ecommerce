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

/** @class_definition oficial_editarCuenta */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_editarCuenta
{
	
	// Cambios en los datos del cliente
	function contenidos() 
	{
		global $__BD, $__CAT, $__LIB, $__SEC, $__CLI;
		global $CLEAN_POST;
		
		$__LIB->comprobarCliente(true);
		
		echo '<div class="titPagina">'._MI_CUENTA.'</div>';
		
		echo '<div class="cajaTexto" style="width: 470px">';
		
		$__CLI->seccionCuenta('editar_cuenta');
		
		// Datos modificados
		if (isset($CLEAN_POST["procesarDatos"]))
			if ($CLEAN_POST["procesarDatos"] == 1) {
				$result = $__CLI->actualizarDatos($CLEAN_POST);
				if ($result == 'ok')
					echo '<div class="msgInfo">'._DATOS_CAMBIADOS.'</div>';
 				else
 					echo '<div class="msgError">'.$result.'</div>';
			}
		
		// Password modificado
		if (isset($CLEAN_POST["procesarPassword"]))
			if ($CLEAN_POST["procesarPassword"] == 1) {
				$result = $__CLI->actualizarPassword($CLEAN_POST);
				if ($result == 'ok')
					echo '<div class="msgInfo">'._DATOS_CAMBIADOS.'</div>';
				else
					echo '<div class="msgError">'.$result.'</div>';
			}
		
		// Direccion de facturacion modificada
		if (isset($CLEAN_POST["procesarDireccionFact"]))
			if ($CLEAN_POST["procesarDireccionFact"] == 1) {
				$result = $__CLI->actualizarDir($CLEAN_POST);
				if ($result == 'ok')
					echo '<div class="msgInfo">'._DATOS_CAMBIADOS.'</div>';
				else
					echo '<div class="msgError">'.$result.'</div>';
			}
		
		// Direccion de envio modificada
		if (isset($CLEAN_POST["procesarDireccionEnv"]))
			if ($CLEAN_POST["procesarDireccionEnv"] == 1) {
				// Actualizar
				if ($CLEAN_POST["id"])
					$result = $__CLI->actualizarDir($CLEAN_POST, '_env');
				// Nueva direccion
				else
					$result = $__CLI->introducirDirEnv($CLEAN_POST);
				
				if ($result == 'ok')
					echo '<div class="msgInfo">'._DATOS_CAMBIADOS.'</div>';
				else
					echo '<div class="msgError">'.$result.'</div>';
			}
		
		$datos = $__CLI->datosPersonales();
		
		$destino = "editar_cuenta.php";
?>

		<div class="titApartado"><?php echo _CAMBIAR_DATOS_CUENTA?></div>
		<?php
			//include("form_datos_cuenta.php");
			echo '<form name="datosCuenta" action="'.$destino.'" method="post">';
			$datosPersonales = $__CLI->datosPersonales();
			echo formularios::editarCuentaPersonal($datosPersonales);
 			echo '<p class="separador"/><a class="botGuardar" href="javascript:document.datosCuenta.submit()">'._ENVIAR.'</a>';
 			echo '</form>';
		?>
		
		<p>&nbsp;</p>
		
		
		
		<div class="titApartado"><?php echo _CAMBIAR_PASSWORD?></div>
		<?php
			include("form_password.php");
		?>
		
		<p>&nbsp;</p>
		
		
		
		<div class="titApartado"><?php echo _CAMBIAR_DIRECCION_FACT?></div>
		
		<form name="datosDirFact" id="datosDirFact" action="<?php echo $destino?>" method="post">
		
		<?php
			$dirFact = $__CLI->direccionFact();
			echo formularios::dirFact($dirFact, 'datosDirFact');
		?>
		
		<input size="30" type="hidden" name="id" value="<?php echo $dirFact[5]?>">
		<input size="30" type="hidden" name="procesarDireccionFact" value="1">

		<p style="clear: left; padding-top:30px"/><a class="botGuardar" href="javascript:document.datosDirFact.submit()"><?php echo _ENVIAR ?></a>
		
		</form>
		
				
		<form name="datosDirEnv" id="datosDirEnv" action="<?php echo $destino?>" method="post">
		
		<p>&nbsp;</p>
		
		<div class="titApartado"><?php echo _CAMBIAR_DIRECCION_ENV?></div>
		<?php
			$dirEnv = $__CLI->direccionEnv();
			echo formularios::dirEnv($dirEnv, 'datosDirEnv');
		?>
		
		<input size="30" type="hidden" name="id" value="<?php echo $dirEnv[5]?>">
		<input size="30" type="hidden" name="procesarDireccionEnv" value="1">

		<p style="clear: left; padding-top:30px"/><a class="botGuardar" href="javascript:document.datosDirEnv.submit()"><?php echo _ENVIAR ?></a>
		
		</form>
		
		</div>

<?php

	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_editarCuenta */
class editarCuenta extends oficial_editarCuenta {};

$iface_editarCuenta = new editarCuenta;
$iface_editarCuenta->contenidos();

?>

<?php include("../includes/right_bottom.php") ?>