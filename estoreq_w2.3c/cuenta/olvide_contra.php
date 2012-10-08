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

error_reporting(E_USER_NOTICE);

/** @class_definition oficial_olvideContra */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_olvideContra
{
	// Si el usuario olvida el password, este se recalcula y se le envia por email previa confirmacion
	function contenidos() 
	{
		global $__BD, $__CAT, $__LIB, $__SEC, $__CLI;
		global $CLEAN_GET, $CLEAN_POST;
	
		echo '<div class="titPagina">'._MI_CUENTA.'</div>';
		
		echo '<div class="cajaTexto">';
		
		$codigo = "";
		if (isset($CLEAN_GET["codigo"]))
			$codigo = $CLEAN_GET["codigo"];	
		
		
		// Usuario de vuelta, ya tiene el codigo enviado por mail
		if ($codigo) {
			
			$email = $__BD->db_valor("select email from recordarcontras where codigo='$codigo'");
			
			// Cambio de contra
			if ($email) {
				$titulo = _CAMBIO_CONTRA;
				$contra = $__LIB->generarPassword(6);
				$contraSha = sha1($contra);
				
				$result = $__BD->db_query("update clientes set password='$contraSha' where email='$email'");
				
				$texto = _NUEVA_CONTRA.' '.$contra;
				
				$__LIB->enviarMail($email, $titulo, $texto);
				
				echo _CONTRA_CAMBIADA;
				echo '<p><a href="'._WEB_ROOT_SSL.'cuenta/login.php">'._ENTRAR.'</a>';
			}
			
			// Codigo incorrecto
			else {
				echo _CODIGO_INCORRECTO;
			}
			
			echo '</div>';
			include("../includes/right_bottom.php");
			exit;
		}
		
		$procesar = 0;
		if (isset($CLEAN_POST["procesar"]))
			$procesar = $CLEAN_POST["procesar"];	
		
		// Primera fase, el usuario indica su email
		if ($procesar == 1) {
		
			$error = "";
			
			$email = $CLEAN_POST["email"];
			
			if (!$email)
				$error = _RELLENAR_CAMPOS;
				
			// control de email
			if (!$error)
				if (!$__BD->db_valor("select codcliente from clientes where email='$email'"))
					$error = _MAIL_NO_REGISTRADO;
				
			if ($error) {
				echo $error;
				echo '<p><a href="'._WEB_ROOT_SSL.'cuenta/olvide_contra.php">'._VOLVER.'</a>';
				include("../includes/right_bottom.php");
				exit;
			}
				
			if (!$error) {
				
				mt_srand((double)microtime()*1000000);
				$randValor = mt_rand();
				$fecha = date("Y-m-d", time());
				$link = _WEB_ROOT_SSL.'cuenta/olvide_contra.php?codigo='.$randValor;
				$texto = '<a href="'.$link.'">'.$link.'</a>';
				
				// Popular tabla de recordar contras
				$id = $__BD->db_valor("select max(id) from recordarcontras");
				if (!$id) $id = 0;
				$id++;
				$result = $__BD->db_query("insert into recordarcontras values($id, '$fecha', $randValor, '$email')");
				if (!$result) {
					echo _ERROR_FATAL;
					include("../includes/right_bottom.php");
					exit;
				}
			
				$titulo = _CAMBIO_CONTRA;
				$texto = _MAIL_CONTRA.'<p>'.$texto;
				
				// Envio del correo
				$__LIB->enviarMail($email, $titulo, $texto);
				
				echo _CONTRA_RECORDADA;
				include("../includes/right_bottom.php");
				exit;
			}
		}
		
		echo _RECORDAR_CONTRA;	
	
?>

		<p>
		
		<form name="olvide" action="olvide_contra.php" method="post">
		<div class="labelForm"><?php echo _EMAIL?></div>
		<div class="datoForm"><input style="width:250px" type="text" name="email"></div>

		<input type="hidden" name="procesar" value="1">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" value="<?php echo _ENVIAR?>">
		
		</form>
	
	</div>


<?php

	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_olvideContra */
class olvideContra extends oficial_olvideContra {};

$iface_olvideContra = new olvideContra;
$iface_olvideContra->contenidos();

?>

<?php include("../includes/right_bottom.php") ?>