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

$documentRoot = '';
if (isset($CLEAN_POST["document_root"]))
	$documentRoot = $CLEAN_POST["document_root"];

if (!$documentRoot) {
	$path_parts = pathinfo($_SERVER["SCRIPT_FILENAME"]);
	$path_parts = pathinfo($path_parts["dirname"]);
	$documentRoot = $path_parts["dirname"];
}

if (substr($documentRoot, -1) != "/")
	$documentRoot .= '/';


$webRoot = '';
if (isset($CLEAN_POST["web_root"]))
	$webRoot = $CLEAN_POST["web_root"];

if (!$webRoot)
	$webRoot = 'http://'.$_SERVER["SERVER_NAME"];

if (substr($webRoot, -1) != "/")
	$webRoot .= '/';




$sslWebRoot = '';
if (isset($CLEAN_POST["ssl_web_root"])) {

	$sslWebRoot = $CLEAN_POST["ssl_web_root"];
	
	if (strlen($sslWebRoot) > 0 && substr($sslWebRoot, -1) != "/")
		$sslWebRoot .= '/';
}



$procesar = '';
if (isset($CLEAN_POST["procesar"]))
	$procesar = $CLEAN_POST["procesar"];


$datosCompletos = true;
if (!$webRoot || !$documentRoot)
	$datosCompletos = false;

?>

<h1><?php echo _INSTALL_TV ?></h1>

<p>
<?php

	echo $__LIB->fasesInstalacion('web'); 
	echo '<h2>'._FASES_INS_WEB.'</h2>';

	echo _INS_TEXTO_2;

	echo '<br/><br/>';
	
	echo '<a class="button" href="javascript:formDatos.submit()"><span>'._INS_COMPROBAR_2.'</span></a>';
	
	echo '<br/><br/>';
	
	echo '<h2>'._INS_ESTADO_2.'</h2>';
	if (!$procesar)
		echo '<p><div class="msgInfo">'._PENDIENTE_CHECK.'</div>';
	
	$error = '';
	if ($procesar == '1' && !$datosCompletos)
		$error = _RELLENAR_TODOS_CAMPOS; 
	
	
	if ($procesar == 1) {
	
		if (!file_exists($documentRoot.'includes'))
			$error = _DOCUMENT_ROOT_NOK;
			
 		$idF = @fopen ( $webRoot.'includes/index.php', "r")
 			or $error = _WEB_ROOT_NOK;
 		@fclose($idF);			
	
		if ($sslWebRoot) {
			$idF = @fopen ( $sslWebRoot.'includes/index.php', "r")
				or $error = _SSL_WEB_ROOT_NOK;
			@fclose($idF);			
		}
	
		if (!$error) {
			$__LIB->crearConfigureWeb($CLEAN_POST);
			echo '<div class="msgOk">'._INS_OK_2.'</div>';
			echo '<a class="button" href="paso2.php"><span>'._SIGUIENTE_MAS.'</a></span>';
			echo '<br/><br/>';
		}
	
		if ($error)
			echo '<div class="msgError">'.$error.'</div>'; 
	}		
?>

<p>

<h2><?php echo _DATOS_WEB ?></h2>

	<form action="paso1.php" method="post" name="formDatos">
		<table class="formBD">
		<tr>
			<td class="aliasLargo"><?php echo _LBL_DOCUMENT_ROOT ?> *</td>
			<td class="campo"><input size="40" type="text" name="document_root" value="<?php echo $documentRoot ?>" /></td>
		</tr>
		<tr>
			<td class="aliasLargo"><?php echo _LBL_WEB_ROOT ?> *</td>
			<td class="campo"><input size="40" type="text" name="web_root" value="<?php echo $webRoot ?>" /></td>
		</tr>
		<tr>
			<td class="aliasLargo"><?php echo _SSL_LBL_WEB_ROOT ?></td>
			<td class="campo"><input size="40" type="text" name="ssl_web_root" value="<?php echo $sslWebRoot ?>" /></td>
		</tr>
		</table>
	
		<input type="hidden" name="procesar" value="1">
	</form>

<?php require_once( 'right_bottom.php' );
