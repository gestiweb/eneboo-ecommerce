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

/** @class_definition oficial_funSeguridad */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

// Funciones de seguridad
class oficial_funSeguridad
{
	
	function limpiarPOST()
	{
		return $_POST;
		$clean = false;
	
		while (list ($clave, $taintedValor) = each ($_POST)) {
			$valor = $this->validarPost($clave, $taintedValor);
			if ($valor)
				$clean[$clave] = $valor;
		}
				
		return $clean;	
	}
	
	function limpiarGET($datos)
	{
		$clean = false;
		while (list ($clave, $taintedValor) = each ($datos)) {
			$valor = $this->validarGet($clave, $taintedValor);
			if ($valor)
				$clean[$clave] = $valor;
		}
		
		return $clean;	
	}
	
	// comprueba el correcto formato de una direccion de email
	function comprobarMail($email)
	{
		$email_pattern = '/^[^@\s<&>]+@([-a-z0-9]+\.)+[a-z]{2,}$/i';
		
		if (preg_match($email_pattern, $email))
			return true;
	
		return false;
	}
	
	
	function validarGet($clave, $taintedValor)
	{
		$maxL = 0;
		$regexp = '';
		$valores = '';
		$digit = false;
		$alnum = false;
		
		switch($clave) {
			
			case "acc":
				$valores = array("del", "add");
				$maxL = 20;
			break;
			
			case "ref":
			case "fam":
			case "refdl":
			case "famdl":
				$maxL = 120;
				$regexp = '/^[A-Za-z0-9\-]+$/';
			break;
			
			case "vista":
				$maxL = 10;
				$regexp = '/^[a-z]+$/';
			break;
			
			case "numFam":
				$regexp = '/^[0-9]{1,4}$/';
			break;
			
			case "newlang":
				$alnum = true;
				$maxL = 3;
			break;
			
			case "codigo":
				$regexp = '/^[0-9A-Z]{1,20}$/';
			break;
			
			case "orden":
				$valores = array('pvp', 'descripcion');
			break;
			
			case 'numr':
			case 'pagina':
				$digit = true;
			break;
			
			case "ok":
				$valores = array('1');
			break;
						
			case "fab":
			case "cod":
				$maxL = 20;
			break;
						
			case "ver":
				$valores = array('enoferta');
			break;
			default:
				return false;
		}
		
		
		if ($regexp) {
			if(preg_match($regexp, $taintedValor) == 0)
	    			return false;
		}
		
		if ($valores) {
			if(!in_array($taintedValor, $valores))
	    			return false;
		}
		
		if ($maxL) {
			if(strlen($taintedValor) > $maxL)
	    			return false;
		}
		
		if ($digit) {
			if (!ctype_digit($taintedValor))
				return false;
		}
		
   		return $taintedValor;
	}
	
	
	function validarPost($clave, $taintedValor)
	{
		global $__BD;
		
    		return $taintedValor;
    		
		$maxL = 0;
		$regexp = '';

		switch($clave) {
			
			case "email":
			case "emailconf":
				$maxL = 200;
				$regexp = '/^[^@\s<&>]+@([-a-z0-9]+\.)+[a-z]{2,}$/i';
			break;
			
			case "procesar":
				$regexp = '/^1$/';
			break;
			
			default:
				return false;
		}
		
		if ($regexp) {
			if(preg_match($regexp, $taintedValor) == 0)
	    			return false;
		}
		
		if ($maxL) {
			if(strlen($taintedValor) > $maxL)
	    			return false;
		}

    	return $taintedValor;
	}
	
	
	function validarDatosCuenta($tainted, $tipo, $insert = false)
	{
		global $__BD, $__LIB;

		foreach($tainted as $campo=>$val)
			$errores[$campo] = '';

		if (!$this->comprobarMail($tainted["email"]))
			$errores["email"] = _EMAIL_NOVALIDO;
		
		if ($tainted["email"] != $tainted["emailconf"])
			$errores["emailconf"] = _EMAIL_DISTINTO;
		
		if ($tainted["password"] != $tainted["confirmacion"])
			$errores["confirmacion"] = _PASSWORD_DISTINTO;
	
		$res = $this->validarCampo('password', $tainted["password"]);
		if ($res != 'ok')
			$errores["password"] = $res;
	
		// Comprobacion de email existente
		if ($insert && !$errores["email"]) {
			$result = $__BD->db_query("select email from clientes where email = '".$tainted["email"]."'");
			$row = $__BD->db_fetch_row($result);
 				if ($row[0]) $errores["email"] = _EMAIL_EXISTENTE;
		}

		$errores["telefono"] = $this->validarCampo('telefono', $tainted["telefono"]);
		$errores["fax"] = $this->validarCampo('fax', $tainted["fax"]);
		$errores["direccion"] = $this->validarCampo('direccion', $tainted["direccion"]);
		$errores["codpostal"] = $this->validarCampo('codpostal', $tainted["codpostal"]);

		$camposNoNulos = array ("nombre", "apellidos", "email", "emailconf", "password", "confirmacion", "direccion", "codpostal", "ciudad", "provincia", "codpais");
                if ($__LIB->esTrue($_SESSION["opciones"]["validarcrearcuenta"]))
			$errores["code"] = $this->validarCampo('securimage', $tainted["code"]);
		
		// Comprobacion de direccion de envio: Todos vacios o todos rellenos
		$numVacios = 0;
		$camposNoNulosEnvio = array ("direccion_env", "codpostal_env", "ciudad_env", "codpais_env", "provincia_env");
		foreach($camposNoNulosEnvio as $campo) {
			if (strlen(trim($tainted[$campo])) > 0) {
				$numVacios++;
				break;
			}
		}
		if ($numVacios)
			$camposNoNulos = array_merge($camposNoNulos, $camposNoNulosEnvio);


		foreach($camposNoNulos as $campo) {
			if (!$errores[$campo]) {
				if (!strlen(trim($tainted[$campo])))
					$errores[$campo] = _RELLENAR_CAMPO;
			}
		}
		

		$pasa = true;
		foreach($errores as $error)
			if ($error)
				$pasa = false;
		

		$result = array();
		$result["datos"] = $tainted;
		$result["errores"] = $errores;
		$result["pasa"] = $pasa;
		
		return $result;
	}
	
	
	function validarContacto($tainted, $tipo)
	{
		global $__BD, $__LIB;

		foreach($tainted as $campo=>$val)
			$errores[$campo] = '';

		if (!$this->comprobarMail($tainted["email"]))
			$errores["email"] = _EMAIL_NOVALIDO;

		$errores["nombre"] = $this->validarCampo('nombre', $tainted["nombre"]);
		
		if ($__LIB->esTrue($_SESSION["opciones"]["validarcontactar"]))
			$errores["code"] = $this->validarCampo('securimage', $tainted["code"]);
		
		$camposNoNulos = array ("nombre", "email", "texto");

		foreach($camposNoNulos as $campo) {
			if (!$errores[$campo]) {
				if (!strlen(trim($tainted[$campo])))
					$errores[$campo] = _RELLENAR_CAMPO;
			}
		}
		
		$tainted["texto"] = htmlentities($tainted["texto"], ENT_QUOTES, $_SESSION["opciones"]["charset"]);
		
		$pasa = true;
		foreach($errores as $error)
			if ($error)
				$pasa = false;

		$result = array();
		$result["datos"] = $tainted;
		$result["errores"] = $errores;
		$result["pasa"] = $pasa;
		
		return $result;
	}
	
	
	function validarRecordarContra($tainted, $tipo)
	{
		global $__BD, $__LIB;

		foreach($tainted as $campo=>$val)
			$errores[$campo] = '';

		if (!$this->comprobarMail($tainted["email"]))
			$errores["email"] = _EMAIL_NOVALIDO;

		$errores["code"] = $this->validarCampo('securimage', $tainted["code"]);
		
		$camposNoNulos = array ("email");

		foreach($camposNoNulos as $campo) {
			if (!$errores[$campo]) {
				if (!strlen(trim($tainted[$campo])))
					$errores[$campo] = _RELLENAR_CAMPO;
			}
		}
		
		$pasa = true;
		foreach($errores as $error)
			if ($error)
				$pasa = false;

		$result = array();
		$result["datos"] = $tainted;
		$result["errores"] = $errores;
		$result["pasa"] = $pasa;
		
		return $result;
	}
	
	function validarCampo($campo, $valorTainted)
	{
		$result = '';
		
		switch($campo) {
			case "nombre":
				if(strlen($valorTainted) > 200)
				if(preg_match($regexp, $valorTainted) == 0)
					$result = _ERROR_FORMAT;
			break;
			
			case "password":
				$regexp = '/^[0-9A-Za-z]{6,20}$/';
				if(preg_match($regexp, $valorTainted) == 0)
					$result = _PASSWORD_ERROR_FORMAT;
			break;
			
			case "telefono":
			case "fax":
				$regexp = '/^[0-9A-Za-z\+\(\) ]{0,20}$/';
				if(preg_match($regexp, $valorTainted) == 0)
					$result = _ERROR_FORMAT;
			break;
			
			case "direccion":
				if(strlen($valorTainted) > 200)
					$result = _ERROR_FORMAT;
			break;
			
			case "codpostal":
				$regexp = '/^[0-9]{0,20}$/';
				if(preg_match($regexp, $valorTainted) == 0)
					$result = _ERROR_FORMAT;
			break;
			
			case "securimage":
				$img = new Securimage();
				if (!$img->check($valorTainted))
					$result = _CODIGO_VALIDACION_INCORRECTO;
			break;
			
		}
		
		return $result;
	}
	
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_funSeguridad */
class funSeguridad extends oficial_funSeguridad {};

?>
