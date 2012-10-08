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

/** @class_definition oficial_login */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_login
{
	// Control de login del cliente
	function contenidos() 
	{
		global $__BD, $__CAT, $__LIB, $__SEC, $__CLI;
		global $CLEAN_GET, $CLEAN_POST;
	
		echo '<h1>'._MI_CUENTA.'</h1>';
		
		echo '<div class="cajaTexto">';
		
		if ($__LIB->comprobarCliente()) {
			$__CLI->cuenta();
			echo '</div>';
			include("../includes/right_bottom.php");
			exit;
		}
		
		$error = '';
		$email = '';
		$password = '';
		
		$procesar = 0;
		if (isset($CLEAN_POST["procesar"]))
			$procesar = $CLEAN_POST["procesar"];	
		
		$continua = '';
		if (isset($CLEAN_GET["continua"])) {
			$continua = $CLEAN_GET["continua"];	
			echo _LOGIN_CONTINUA.'<p>';
		}
		
		if ($procesar == 1) {
		
			if (isset($CLEAN_POST["email"]))
				$email = $CLEAN_POST["email"];
				
			if (isset($CLEAN_POST["password"]))
				$password = sha1($CLEAN_POST["password"]);
			
			$codCliente = $__BD->db_valor("select codcliente from clientes where email='$email' and password='$password'");
			// OK
			if ($codCliente) {
				
				// Nuevo valor de clave de sesion aleatorio
				$_SESSION["key"] = strtolower($__LIB->generarPassword(50));
				$__LIB->altaSesion($codCliente);
				
				$_SESSION['initiated'] = false;

				$_SESSION["codCliente"] = $codCliente;
				$cliente = new cliente();
				$cliente->cuenta();
				
				if ($continua == 'pedido') {
					echo '
					<script languaje="javascript">
						window.location = \''._WEB_ROOT_SSL.'cesta/datos_envio.php\';
					</script>';
				}
				
				include("../includes/right_bottom.php");
				exit;
			}
			else
				$error = _ERROR_LOGIN;
		}
		
		if(!$procesar || $error) {
		
			if ($error)
				echo '<div class="msgError">'.$error.'</div>'; 
		
			include('form_login.php');
	
			echo '<br/><br/>';

			if (!$__LIB->esTrue($_SESSION["opciones"]["noautoaccount"])) {
				$continua = '';
				if (isset($CLEAN_GET["continua"]))
					$continua = '?continua=1';
				echo '<a class="button" href="cuenta/crear_cuenta.php'.$continua.'"><span>'._CREAR_CUENTA.'</span></a>';
			}

			echo '<a class="button" href="cuenta/olvide_contra.php"><span>'._OLVIDE_CONTRA.'</span></a>';
		}
	
		echo '</div>';
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_login */
class login extends oficial_login {};

$iface_login = new login;
$iface_login->contenidos();

?>

<?php include("../includes/right_bottom.php") ?>