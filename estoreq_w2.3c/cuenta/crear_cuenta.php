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
error_reporting(E_PARSE);

/** @class_definition oficial_crearCuenta */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_crearCuenta
{
	var $camposNoNulos;
	
	// Crea un nuevo registro de cliente
	function contenidos() 
	{
		global $__BD, $__CAT, $__LIB, $__SEC;
		global $CLEAN_POST, $CLEAN_GET;
		
		$this->setNoNulos();
		$noNulos = $this->camposNoNulos;
		
		echo '<div class="titPagina">'._CREAR_CUENTA.'</div>';
	
		echo '<div class="cajaTexto" style="width: 470px">';
		
		$procesar = $CLEAN_POST["procesar"];
		
		$continua = '';
		if (isset($CLEAN_GET["continua"]))
			$continua = '?continua=1';

		// Comprueba si el cliente esta logeado
		if ($__LIB->comprobarCliente()) {
			echo _YA_TENGO_CUENTA;
			echo '<p><a href="'._WEB_ROOT_SSL.'cuenta/login.php">'._ENTRAR_CUENTA.'</a>';
			echo '</div>';
			include("../includes/right_bottom.php");
			exit;
		}
		
		if ($__LIB->esTrue($_SESSION["opciones"]["noautoaccount"])) {
			echo _NO_AUTO_ACCOUNT;
			echo '</div>';
			include("../includes/right_bottom.php");
			exit;
		}


		// Si hay que procesar los datos
		if ($procesar) {
		
			$error = "";
			
			while (list ($clave, $campo) = each ($CLEAN_POST)) {
				$$clave = $CLEAN_POST[$clave];
				if (strlen(trim($$clave)) == 0 && in_array($clave, $noNulos))
					$error = _RELLENAR_TODOS_CAMPOS;
			}
			
			// Comprobacion de email
			if (!$__SEC->comprobarMail($email)) {
					$error = _EMAIL_NOVALIDO;
			}
			
			// Comprobacion de email y confirmacion
			if (!$error) {
				if ($email != $emailconf)
					$error = _EMAIL_DISTINTO;
			}
			
			// Comprobacion de password
			if (!$error) {
				if ($password != $confirmacion)
					$error = _PASSWORD_DISTINTO;
			}
			
			// Comprobacion de longitud de password
			if (!$error) {
				if (strlen(trim($password)) < 6)
					$error = _PASSWORD_MIN_6;
			}
			
			// Comprobacion de email existente
			if (!$error) {
				$result = $__BD->db_query("select email from clientes where email = '$email'");
				$row = $__BD->db_fetch_row($result);
 				if ($row[0]) $error = _EMAIL_EXISTENTE;
			}
			
			// Comprobacion de direccion de envio: Todos vacios o todos rellenos
			if (!$error) {
				$numVacios = 0;
				$datosEnvio = array ("direccion_env", "codpostal_env", "ciudad_env", "codpais_env", "provincia_env");
				while (list ($clave, $campo) = each ($datosEnvio)) {
					if (strlen(trim($CLEAN_POST[$campo])) == 0)
						$numVacios++;
				}
				if ($numVacios != 0 && $numVacios != count($datosEnvio))
					$error = _RELLENAR_CAMPOS_ENVIO;
			}
			
			// Debe existir una serie
			$ordenSQL = "select codserie from empresa";
			$codSerie = $__BD->db_valor($ordenSQL);
			if (!$codSerie)
				$error = _ERROR_CREAR_CUENTA;
			
			if ($error) {
				echo '<div class="msgError">'.$error.'</div>';
				include("form_crear_cuenta.php");
			}
			else {
				
				// Cuando el cliente define una empresa, se registra
				if (strlen(trim($empresa)) > 0) {
					$nomCliente = $empresa;
					$esEmpresa = "true";
				}
				else {
					$nomCliente = $nombre.' '.$apellidos;
					$esEmpresa = "false";
				}
				
				$password = sha1($password);
				
				$result = false;
				$fechaAlta = date("Y-m-d");
				
				// Codigos de cliente web desde el 500000
				$codCliente = $__BD->nextCounter("clientes", "codcliente", 6, '500000');
				
				$listaCampos = "codcliente, nombre, contacto, apellidos, email, telefono1, fax, password, cifnif,clienteweb,codserie, esempresa,modificado, regimeniva, tipoidfiscal, fechaaltaweb";
				$listaValores = "'$codCliente', '$nomCliente', '$nombre','$apellidos', '$email', '$telefono', '$fax','$password', '...', true, '$codSerie', $esEmpresa, true, 'General', 'NIF', '$fechaAlta'";
				
				$listaCampos .= $this->masCampos();
				$listaValores .= $this->masValores();
				
				$ordenSQL = "insert into clientes ($listaCampos) values ($listaValores)";
					
				$result = $__BD->db_query($ordenSQL);
				
				// Direccion de envio si existe
				if ($result) {
					// id de la direccion
					$id = $__BD->nextId("dirclientes", "id");
					$domEnvio = "true";
					if (strlen(trim($direccion_env)) > 0) {
						$ordenSQL = "insert into dirclientes
							(id,codcliente, direccion, codpostal, ciudad, provincia, codpais, domfacturacion, domenvio, modificado)
							values ($id,'$codCliente', '$direccion_env', '$codpostal_env','$ciudad_env', '$provincia_env','$codpais_env', false, true, true)";
						$result = $__BD->db_query($ordenSQL);
						$domEnvio = "false";
					}
				}
				
				// Direccion de facturacion
				if ($result) {			
					// id de la direccion
					$id = $__BD->nextId("dirclientes", "id");
					$ordenSQL = "insert into dirclientes
						(id, codcliente, direccion, codpostal, ciudad, provincia, codpais, domfacturacion, domenvio, modificado)
						values ($id,'$codCliente', '$direccion', '$codpostal','$ciudad', '$provincia','$codpais', true, $domEnvio, true)";
					$result = $__BD->db_query($ordenSQL);
				}
				
				if (!$result)
					echo _ERROR_CREAR_CUENTA;
				else {
				
				
					$_SESSION["key"] = strtolower($__LIB->generarPassword(50));
					$__LIB->altaSesion($codCliente);
					
					$_SESSION['initiated'] = false;
	
					$_SESSION["codCliente"] = $codCliente;
					$cliente = new cliente();
					
 					// Mail de confirmacion
 					$__LIB->enviarMailCuenta();
					
					if (isset($CLEAN_GET["continua"])) {
						echo _UNMOMENTO.'
						<script languaje="javascript">
							window.location = \''._WEB_ROOT_SSL.'cesta/datos_envio.php\';
						</script>';
						include("../includes/right_bottom.php");
						exit;
					}
					
					echo _CUENTA_CREADA.'<p>';
					$cliente->cuenta();
					include("../includes/right_bottom.php");
					exit;
// 					echo '<p><a href="'._WEB_ROOT_SSL.'cuenta/login.php">'._ENTRAR_CUENTA.'</a>';				
// 					$_SESSION["codCliente"] = $codCliente;
 					
				}
			}
			
		}
		
		else {
			include("form_crear_cuenta.php");
		}
		
		echo '</div>';
	}

	function setNoNulos()
	{
		$this->camposNoNulos = 	array ("nombre", "apellidos", "email", "emailconf", "password", "confirmacion", "direccion", "codpostal", "ciudad", "provincia", "codpais");
	}

	// Extender
	function masCampos()
	{
		return '';
	}

	// Extender
	function masValores()
	{
		return '';
	}

}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_crearCuenta */
class crearCuenta extends oficial_crearCuenta{};

$iface_crearCuenta = new crearCuenta;
$iface_crearCuenta->contenidos();

?>

<?php include("../includes/right_bottom.php") ?>