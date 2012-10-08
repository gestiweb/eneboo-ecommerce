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

require_once( 'top_left.php' );
require_once( '../idiomas/esp/main.php' );

$tipobd = '';
if (isset($CLEAN_POST["tipobd"]))
	$tipobd = $CLEAN_POST["tipobd"];

$arquitectura = '';
if (isset($CLEAN_POST["arquitectura"]))
	$arquitectura = $CLEAN_POST["arquitectura"];

$servidor = '';
if (isset($CLEAN_POST["servidor"]))
	$servidor = $CLEAN_POST["servidor"];

$usuario = '';
if (isset($CLEAN_POST["usuario"]))
	$usuario = $CLEAN_POST["usuario"];

$password = '';
if (isset($CLEAN_POST["password"]))
	$password = $CLEAN_POST["password"];

$puerto = '';
if (isset($CLEAN_POST["puerto"]))
	$puerto = $CLEAN_POST["puerto"];

$basedatos = '';
if (isset($CLEAN_POST["basedatos"]))
	$basedatos = $CLEAN_POST["basedatos"];
	
$procesar = '';
if (isset($CLEAN_POST["procesar"]))
	$procesar = $CLEAN_POST["procesar"];

$datosCompletos = true;
if (!$arquitectura || !$tipobd || !$servidor || !$usuario || !$basedatos)
	$datosCompletos = false;

?>

<h1><?php echo _INSTALL_TV ?></h1>

<p>
<?php

	echo $__LIB->fasesInstalacion('bd');
	echo '<h2>'._FASES_INS_BD.'</h2>';
	
	echo _INS_TEXTO_1;

	echo '<p><a class="button" href="javascript:formBD.submit()"><span>'._INS_COMPROBAR_1.'</span></a>';

	echo '<br/><br/><h2>'._ESTADO_CONEXION.'</h2>';
	if (!$procesar)
		echo '<div class="msgInfo">'._PENDIENTE_CHECK.'</div>';
	
	$error = '';
	if ($procesar == '1' && !$datosCompletos) {
		$error = _RELLENAR_TODOS_CAMPOS; 
	}
	
	if (!$error && $procesar == '1' && $datosCompletos) {
		$conexionBD = $__LIB->accionesBD($arquitectura, $tipobd, $servidor, $puerto, $usuario, $password, $basedatos);
			
		if ($conexionBD != "OK")
			$error = $conexionBD;
			
		// Si es unificada hay que verificar que NO esta vacia
		if (!$error && $arquitectura == "1")
			if($__LIB->bdVacia($tipobd, $basedatos))
				$error = _BD_VACIA;
		
		// Si es distribuida hay que verificar que esta vacia
		if (!$error && $arquitectura == "2")
			if(!$__LIB->bdVacia($tipobd, $basedatos))
				$error = _BD_NO_VACIA;
	
		if (!$error) {
			$__LIB->crearConfigureBD($CLEAN_POST); 
			echo '<p><div class="msgOk">'._INS_OK_1.'</div>';
			echo '<p><a class="button" href="paso3.php"><span>'._SIGUIENTE_MAS.'<span/></a>';
			echo '<br/><br/>';
		}
	}
		
	if ($error) {
		echo '<p><div class="msgError">'.$error.'</div>'; 
	}
		
	
?>


<h2><?php echo _DATOS_CONEXION ?></h2>

	<form action="paso2.php" method="post" name="formBD">
		<table class="formBD">
		<tr>
			<td class="alias"><?php echo _ARQUITECTURA_BD ?> *</td>
			<td class="campo">
			<select name="arquitectura">
			<option value="1" <?php if (! $arquitectura || $arquitectura == "1") echo " selected" ?>><?php echo _ARQUITECTURA_BD1 ?></option>
			<option value="2" <?php if ($arquitectura == "2") echo " selected" ?>><?php echo _ARQUITECTURA_BD2 ?></option>
			</select>
			</td>
		</tr>
		<tr>
			<td class="alias"><?php echo _TIPO_BD ?> *</td>
			<td class="campo">
			<?php
				echo $__LIB->tiposBD($tipobd);
			?>
			</td>
		</tr>
		<tr>
			<td class="alias"><?php echo _NOM_SERVIDOR ?> *</td>
			<td class="campo"><input type="text" name="servidor" value="<?php echo $servidor ?>" /></td>
		</tr>
		<tr>
			<td class="alias"><?php echo _PUERTO ?></td>
			<td class="campo"><input type="text" name="puerto" value="<?php echo $puerto ?>" /></td>
		</tr>
		<tr>
			<td class="alias"><?php echo _USUARIO ?> *</td>
			<td class="campo"><input type="text" name="usuario" value="<?php echo $usuario ?>" /></td>
		</tr>
		<tr>
			<td class="alias"><?php echo _PASSWORD ?></td>
			<td class="campo"><input type="password" name="password" value="<?php echo $password ?>" /></td>
		</tr>
		<tr>
			<td class="alias"><?php echo _NOM_BD ?> *</td>
			<td class="campo"><input type="text" name="basedatos" value="<?php echo $basedatos ?>" /></td>
		</tr>
		</table>
	
		<input type="hidden" name="procesar" value="1">
	</form>

<?php require_once( 'right_bottom.php' );
