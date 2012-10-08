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

/** @class_definition oficial_formularios */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_formularios 
{
	public static function bloqueCampo($nombre, $label, $valor, $errores, $campoDirecto = '', $requerido = '*', $textarea = false, $pass = false) 
	{
		$codigo = '';
		
		if ($textarea)
			$codigo .= '<div class="campoTA">';
		
		$codigo .= '<div class="campo">';
		
		$codigo .= '<label for="campo_'.$nombre.'">'.$label.' '.$requerido.'</label>';
		
		$type = $pass ? 'password' : 'text';
		
		if ($campoDirecto)
			$codigo .= $campoDirecto;
		else {
			if ($textarea)
				$codigo .= '<textarea rows="10" cols="10" name="'.$nombre.'" id="campo_'.$nombre.'">'.$valor.'</textarea>';
			else
				$codigo .= '<input type="'.$type.'" name="'.$nombre.'" id="campo_'.$nombre.'" value="'.$valor.'"/>';
		}
		
		if (isset($errores[$nombre]))
			$codigo .= '<span class="errorForm">'.$errores[$nombre].'</span>';
		
		$codigo .= '</div>';
		
		if ($textarea)
			$codigo .= '</div>';
		
		return $codigo;
	}
	
	public static function dirFact($datosDir, $formName = '', $errores = array())
	{	
		global $__LIB;

		$codigo = '';

		$codigo .= formularios::bloqueCampo('direccion', _DIRECCION, $datosDir["direccion"], $errores);
		$codigo .= formularios::bloqueCampo('codpostal', _CODPOSTAL, $datosDir["codpostal"], $errores);
		$codigo .= formularios::bloqueCampo('ciudad', _POBLACION, $datosDir["ciudad"], $errores);
		
		$campoDirecto = $__LIB->selectPais($formName, "", $datosDir["codpais"]);
		$codigo .= formularios::bloqueCampo('codpais', _PAIS, '', $errores, $campoDirecto);
		
		$campoDirecto = '<span id="spanProvincia">'.$__LIB->selectProvincia("provincia", $datosDir["codpais"], $datosDir["provincia"], "").'</span>';
		$codigo .= formularios::bloqueCampo('provincia', _PROVINCIA, '', $errores, $campoDirecto);

		return $codigo;
	}

	public static function dirEnv($datosDir, $formName = '', $errores = array(), $suf = '', $ambito = '')
	{	
		global $__LIB;

		$codigo = '';
		
		$codigo .= formularios::bloqueCampo('direccion_env', _DIRECCION, $datosDir["direccion$suf"], $errores);
		$codigo .= formularios::bloqueCampo('codpostal_env', _CODPOSTAL, $datosDir["codpostal$suf"], $errores);
		$codigo .= formularios::bloqueCampo('ciudad_env', _POBLACION, $datosDir["ciudad$suf"], $errores);
		
		$campoDirecto = $__LIB->selectPais($formName, "env", $datosDir["codpais$suf"]);
		$codigo .= formularios::bloqueCampo('codpais_env', _PAIS, '', $errores, $campoDirecto);
		
		$campoDirecto = '<span id="spanProvincia_env">'.$__LIB->selectProvincia("provincia_env", $datosDir["codpais$suf"], $datosDir["provincia$suf"], $ambito).'</span>';
		$codigo .= formularios::bloqueCampo('provincia_env', _PROVINCIA, '', $errores, $campoDirecto);

		return $codigo;
	}
	

	public static function nombre($datos, $nif = false, $errores = '')
	{	
		global $__LIB;

		$codigo = '';
		
		$codigo .= formularios::bloqueCampo('contacto', _NOMBRE, $datos["contacto"], $errores);
		$codigo .= formularios::bloqueCampo('apellidos', _APELLIDOS, $datos["apellidos"], $errores);
		$codigo .= formularios::bloqueCampo('empresa', _EMPRESA, $datos["empresa"], $errores, '', '&nbsp;');
			
		if ($nif)
			$codigo .= formularios::bloqueCampo('nif', _NIF, '', $errores, '');

		return $codigo;
	}
	
	

	public static function nombreEnv($datos, $errores = array())
	{	
		global $__LIB;

		$codigo = '';
		
		$empresa = '';
		if ($__LIB->esTrue($datos["esempresa"])) 
			$empresa = $datos["nombre"];
		
		$codigo .= formularios::bloqueCampo('nombre_env', _NOMBRE, $datos["contacto"], $errores);
		$codigo .= formularios::bloqueCampo('apellidos_env', _APELLIDOS, $datos["apellidos"], $errores);
		$codigo .= formularios::bloqueCampo('empresa_env', _EMPRESA, $empresa, $errores);
			
		return $codigo;
	}
	
	
	

	public static function nuevaCuentaGeneral($datos, $errores)
	{
		$codigo = '';
		
		$codigo .= formularios::bloqueCampo('email', _EMAIL, $datos["email"], $errores);
		$codigo .= formularios::bloqueCampo('emailconf', _EMAILCONF, $datos["emailconf"], $errores);
		$codigo .= formularios::bloqueCampo('password', _PASSWORD, $datos["password"], $errores,'', '*', false, true);
		$codigo .= formularios::bloqueCampo('confirmacion', _CONFIRM_PASSWORD, $datos["confirmacion"], $errores,'', '*', false, true);
			
		return $codigo;
	}
	
	public static function nuevaCuentaPersonal($datos, $errores)
	{	
		$codigo = '';
		
		$codigo .= formularios::bloqueCampo('nombre', _NOMBRE, $datos["nombre"], $errores);
		$codigo .= formularios::bloqueCampo('apellidos', _APELLIDOS, $datos["apellidos"], $errores);
		$codigo .= formularios::bloqueCampo('telefono', _TELEFONO, $datos["telefono"], $errores,'', '');
		$codigo .= formularios::bloqueCampo('fax', _FAX, $datos["fax"], $errores,'', '');
		$codigo .= formularios::bloqueCampo('empresa', _EMPRESA, $datos["empresa"], $errores,'', '');
		
		return $codigo;
	}

	public static function editarCuentaPersonal($datos, $errores = array())
	{	
		global $__LIB;
	
		$codigo = '';

		$nombre = '';
		if ($__LIB->esTrue($datos["esempresa"])) 
			$nombre = $datos["nombre"];
		
		$email = '<input name="email" disabled="disabled" value="'.$datos["email"].'"/>';
		
		$codigo .= formularios::bloqueCampo('email', _EMAIL, $datos["email"], $errores, $email);
		$codigo .= formularios::bloqueCampo('contacto', _NOMBRE, $datos["contacto"], $errores);
		$codigo .= formularios::bloqueCampo('apellidos', _APELLIDOS, $datos["apellidos"], $errores);
		$codigo .= formularios::bloqueCampo('telefono1', _TELEFONO, $datos["telefono1"], $errores,'', '');
		$codigo .= formularios::bloqueCampo('fax', _FAX, $datos["fax"], $errores,'', '');
		$codigo .= formularios::bloqueCampo('nombre', _EMPRESA, $nombre, $errores,'', '');
		
		return $codigo;
	}

	
	public static function editarPassword($errores = array())
	{
		$codigo = '';
	
		$campoDirecto = '<input type="password" name="password", id="campo_password">';
		$codigo .= formularios::bloqueCampo('password', _PASSWORD, '', $errores, $campoDirecto);
		
		$campoDirecto = '<input type="password" name="confirmacion", id="campo_confimacion">';
		$codigo .= formularios::bloqueCampo('confirmacion', _CONFIRM_PASSWORD, '', $errores, $campoDirecto);
			
		return $codigo;
	}
	

	
	public static function contactar($valores, $errores)
	{
		$codigo = '';
	
		if ($valores) {
			$nombre = $valores["nombre"];
			$email = $valores["email"];
			$texto = $valores["texto"];
		}
		else {
			$nombre = '';
			$email = '';
			$texto = '';
		}
		
		$codigo .= formularios::bloqueCampo('nombre', _NOMBRE, $nombre, $errores);
		$codigo .= formularios::bloqueCampo('email', _EMAIL, $email, $errores);
		$codigo .= formularios::bloqueCampo('texto', _COMENTARIOS, $texto, $errores, '', '*', true);
			
		return $codigo;
	}
	

	
	
	public static function recordarContra($valores, $errores)
	{
		$codigo = '';
		if ($valores)
			$email = $valores["email"];
		else
			$email = '';
		
		$codigo .= formularios::bloqueCampo('email', _EMAIL, $email, $errores);
		$codigo .= formularios::codigoValidacion(_CODIGO_VALIDACION, $errores);
			
		return $codigo;
	}
	

	
	
	public static function datosNoNulos()
	{
		$datos = array(
			'general' => array('email', 'emailconf', 'nombre', 'apellidos', 'password', 'confirmacion'),
			'dirfact' => array('direccion', 'codpostal', 'ciudad', 'codpais', 'provincia'),
			'direnv' => array('direccion_env', 'codpostal_env', 'ciudad_env', 'codpais_env', 'provincia_env')
		);
		
		return $datos;
	}
	
	public static function botEnviar($label = _ENVIAR)
	{
		$codigo = '';
		$codigo .= '<div class="campoEnviar">';
		$codigo .= '<label for="submit">&nbsp;</label>';
		$codigo .= '<button type="submit" value="'.$label.'" class="submitBtn"><span>'.$label.'</span></button>';
		$codigo .= '</div>';
		
		return $codigo;
	}
	
	public static function codigoValidacion($label, $errores)
	{
		$codigo = '';
		$codigo .= '<div class="validacion">';
		$codigo .= '<div class="campo">';
		$codigo .= '<label for="campo_code">'.$label.' *</label>';
			
		$codigo .= '<img src="'._WEB_ROOT.'includes/securimage/securimage_show.php?sid='.md5(uniqid(time())).'" alt="securimage" id="securimage"/>';
		$codigo .= '<br/><input type="text" name="code" id="campo_code"/>';
		if (isset($errores["code"]))
			$codigo .= '<span class="errorForm">'.$errores["code"].'</span>';
		$codigo .= '<br/><br/><a href="#" onclick="reloadSecurimage(\''._WEB_ROOT.'\'); return false;">'._RELOAD_SECURIMAGE.'</a>';
		$codigo .= '</div>';
		$codigo .= '</div>';
		
		return $codigo;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_formularios */
class formularios extends oficial_formularios {}

?>