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
		
		echo '<h1>'._MI_CUENTA.'</h1>';
		
		echo '<div class="cajaTexto">';
		
		echo $__CLI->seccionCuenta('editar_cuenta');
		
		// Datos modificados
		$resultDatos = '';
		if (isset($CLEAN_POST["procesarDatos"])) {
			$resultDatos = $__CLI->actualizarDatos($CLEAN_POST);
			if ($resultDatos == 'ok')
				$resultDatos = '<div class="msgInfo">'._DATOS_CAMBIADOS.'</div>';
			else
				$resultDatos = '<div class="msgError">'.$resultDatos.'</div>';
		}
		
		// Password modificado
		$resultPass = '';
		if (isset($CLEAN_POST["procesarPassword"])) {
			$resultPass = $__CLI->actualizarpassword($CLEAN_POST);
			if ($resultPass == 'ok')
				$resultPass = '<div class="msgInfo">'._DATOS_CAMBIADOS.'</div>';
			else
				$resultPass = '<div class="msgError">'.$resultPass.'</div>';
		}
		
		// Direccion de facturacion modificada
		$resultDirFact = '';
		if (isset($CLEAN_POST["procesarDireccionFact"])) {
			$resultDirFact = $__CLI->actualizarDir($CLEAN_POST);
			if ($resultDirFact == 'ok')
				$resultDirFact = '<div class="msgInfo">'._DATOS_CAMBIADOS.'</div>';
			else
				$resultDirFact = '<div class="msgError">'.$resultDirFact.'</div>';
		}
		
		// Direccion de envio modificada
		$resultDirEnv = '';
		if (isset($CLEAN_POST["procesarDireccionEnv"])) {
			// Actualizar
			if ($CLEAN_POST["id"])
				$resultDirEnv = $__CLI->actualizarDir($CLEAN_POST, '_env');
			// Nueva direccion
			else
				$resultDirEnv = $__CLI->introducirDirEnv($CLEAN_POST);
			
			if ($resultDirEnv == 'ok')
				$resultDirEnv = '<div class="msgInfo">'._DATOS_CAMBIADOS.'</div>';
			else
				$resultDirEnv = '<div class="msgError">'.$resultDirEnv.'</div>';
		}
		
		$datos = $__CLI->datosPersonales();
		
		$destino = "cuenta/editar_cuenta.php";
?>

		<a name="datosCuenta"></a>
		<h2><?php echo _CAMBIAR_DATOS_CUENTA?></h2>
		<?php
			//include("form_datos_cuenta.php");
			echo '<form name="datosCuenta" action="'.$destino.'#datosCuenta" method="post">';
			echo $resultDatos;
			$datosPersonales = $__CLI->datosPersonales();
			echo formularios::editarCuentaPersonal($datosPersonales);
			echo formularios::botEnviar();
			echo '<input type="hidden" name="procesarDatos" value="1">';
 			echo '</form>';
		?>
		
		<p>&nbsp;</p>
		
		
		
		<a name="datosPass"></a>
		<h2><?php echo _CAMBIAR_PASSWORD?></h2>
		<?php
			echo '<form name="datosPass" action="'.$destino.'#datosPass" method="post">';
			echo $resultPass;
			echo formularios::editarPassword();
			echo formularios::botEnviar();
			echo '<input type="hidden" name="procesarPassword" value="1">';
 			echo '</form>';
		?>
		
		<p>&nbsp;</p>
		
		
		
		
		<a name="direccionFact"></a>
		<h2><?php echo _CAMBIAR_DIRECCION_FACT?></h2>
		
		<form name="datosDirFact" id="datosDirFact" action="<?php echo $destino?>#direccionFact" method="post">
		
		<?php
			$dirFact = $__CLI->direccionFact();
			echo $resultDirFact;
			echo formularios::dirFact($dirFact, 'datosDirFact');
		?>
		
		<input size="30" type="hidden" name="id" value="<?php echo $dirFact["id"]?>">
		<input size="30" type="hidden" name="procesarDireccionFact" value="1">
				
		<?php
			echo formularios::botEnviar();
		?>

		</form>
		
				
				
				
		<a name="direccionEnv"></a>
		<form name="datosDirEnv" id="datosDirEnv" action="<?php echo $destino?>#direccionEnv" method="post">
		
		<p>&nbsp;</p>
		
		<h2><?php echo _CAMBIAR_DIRECCION_ENV?></h2>
		<?php
			$dirEnv = $__CLI->direccionEnv();
			echo $resultDirEnv;
			echo formularios::dirEnv($dirEnv, 'datosDirEnv');
		?>
		
		<input size="30" type="hidden" name="id" value="<?php echo $dirEnv["id"]?>">
		<input size="30" type="hidden" name="procesarDireccionEnv" value="1">

		<?php
			echo formularios::botEnviar();
		?>
		
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