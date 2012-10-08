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
	public static function dirFact($datosDir, $formName = '')
	{	
		global $__LIB;

		$codigo = '';

		$codigo .= '
			<div class="labelForm">'._DIRECCION.' *</div>
			<div class="datoForm"><input size="30" type="text" name="direccion" id="campo_direccion" value="'.$datosDir[0].'"></div>

			<div class="labelForm">'._CODPOSTAL.' *</div>
			<div class="datoForm"><input size="30" type="text" name="codpostal" id="campo_codpostal" value="'.$datosDir[1].'"></div>

			<div class="labelForm">'._POBLACION.' *</div>
			<div class="datoForm"><input size="30" type="text" name="ciudad" id="campo_ciudad" value="'.$datosDir[2].'"></div>

			<div class="labelForm">'._PAIS.' *</div>
			<div class="datoForm">'.$__LIB->selectPais($formName, "", $datosDir[4]).'</div>

			<div class="labelForm">'._PROVINCIA.' *</div>
			<div class="datoForm" id="divprovincia">'.$__LIB->selectProvincia("provincia", $datosDir[4], $datosDir[3], "").'</div>';


		return $codigo;
	}

	public static function dirEnv($datosDir, $formName = '')
	{	
		global $__LIB;

		$codigo = '';

		$codigo .= '
			<div class="labelForm">'._DIRECCION.'</div>
			<div class="datoForm"><input size="30" type="text" name="direccion_env" id="campo_direccion_env" value="'.$datosDir[0].'"></div>
			
			<div class="labelForm">'._CODPOSTAL.'</div>
			<div class="datoForm"><input size="30" type="text" name="codpostal_env" id="campo_codpostal_env"  value="'.$datosDir[1].'"></div>
			
			<div class="labelForm">'._POBLACION.'</div>
			<div class="datoForm"><input size="30" type="text" name="ciudad_env" id="campo_ciudad_env" value="'.$datosDir[2].'"></div>
			
			<div class="labelForm">'._PAIS.'</div>
			<div class="datoForm">'.$__LIB->selectPais($formName, "env", $datosDir[4]).'</div>

			<div class="labelForm">'._PROVINCIA.'</div>
			<div class="datoForm" id="divprovincia_env">'.$__LIB->selectProvincia("provincia_env", $datosDir[4], $datosDir[3], "").'</div>';

		return $codigo;
	}
	

	public static function nombre($datosPer, $nif = false)
	{	
		global $__LIB;

		$codigo = '';
		$empresa = '';
		if ($__LIB->esTrue($datosPer[6]))
			$empresa = $datosPer[3];

		$codigo .= '
			<div class="labelForm">'._NOMBRE.'</div>
			<div class="datoForm"><input type="text" name="nombre" id="campo_nombre" value="'.$datosPer[1].'"></div>
		
			<div class="labelForm">'._APELLIDOS.'</div>
			<div class="datoForm"><input type="text" name="apellidos" id="campo_apellidos" value="'.$datosPer[2].'"></div>
		
			<div class="labelForm">'._EMPRESA.'</div>
			<div class="datoForm"><input size="30" type="text" name="empresa" id="campo_empresa" value="'.$empresa.'">&nbsp;</div>';
			
		if ($nif) {
			$codigo .= '
				<div class="labelForm">'._NIF.'</div>
				<div class="datoForm"><input type="text" name="nif" id="campo_nif">&nbsp;</div>';
		}

		return $codigo;
	}
	
	

	public static function nombreEnv($datosPer)
	{	
		global $__LIB;

		$codigo = '';
		$empresa = '';
		if ($__LIB->esTrue($datosPer[6]))
			$empresa = $datosPer[3];

		$codigo .= '
			<div class="labelForm">'._NOMBRE.' *</div>
			<div class="datoForm"><input type="text" name="nombre_env" value="'.$datosPer[1].'"></div>
		
			<div class="labelForm">'._APELLIDOS.' *</div>
			<div class="datoForm"><input type="text" name="apellidos_env" value="'.$datosPer[2].'"></div>
		
			<div class="labelForm">'._EMPRESA.'</div>
			<div class="datoForm"><input size="30" type="text" name="empresa_env" value="'.$empresa.'">&nbsp;</div>';
			
		return $codigo;
	}
	
	
	

	public static function nuevaCuentaGeneral($datos)
	{
		$codigo = '';
	
			$codigo .= '
			<div class="labelForm">'._EMAIL.' *</div>
			<div class="datoForm"><input type="text" name="email" id="campo_email" size="30" value="'.$datos["email"].'"></div>
		
			<div class="labelForm">'._EMAILCONF.' *</div>
			<div class="datoForm"><input type="text" name="emailconf" id="campo_emailconf" size="30" value="'.$datos["emailconf"].'"></div>
		
			<div class="labelForm">'._PASSWORD.' *</div>
			<div class="datoForm"><input type="password" name="password" id="campo_password" size="15" maxlength="40"></div>
		
			<div class="labelForm">'._CONFIRM_PASSWORD.' *</div>
			<div class="datoForm"><input type="password" name="confirmacion" id="campo_confirmacion" size="15" maxlength="40"></div>';
			
		return $codigo;
	}
	
	public static function nuevaCuentaPersonal($datos)
	{	
		$codigo = '';
		
		$codigo .= '		
			<div class="labelForm">'._NOMBRE.' *</div>
			<div class="datoForm"><input type="text" name="nombre" id="campo_nombre" size="30" value="'.$datos["nombre"].'"></div>
		
		
			<div class="labelForm">'._APELLIDOS.' *</div>
			<div class="datoForm"><input type="text" name="apellidos" id="campo_apellidos" size="30" value="'.$datos["apellidos"].'"></div>
		
		
			<div class="labelForm">'._TELEFONO.' </div>
			<div class="datoForm"><input type="text" name="telefono" id="campo_telefono" size="20" value="'.$datos["telefono"].'">&nbsp;</div>
		
		
			<div class="labelForm">'._FAX.'</div>
			<div class="datoForm"><input type="text" name="fax" id="campo_fax" size="20" value="'.$datos["fax"].'">&nbsp;</div>
		
		
			<div class="labelForm">'._EMPRESA.'</div>
			<div class="datoForm"><input type="text" name="empresa" id="campo_empresa" size="30" value="'.$datos["empresa"].'">&nbsp;</div>';
		
		return $codigo;
	}

	public static function editarCuentaPersonal($datos)
	{	
		global $__LIB;
	
		$codigo = '';

		$nombre = '';
		if ($__LIB->esTrue($datos[6])) 
			$nombre = $datos[3];
				eqDebug::log($datos);
				eqDebug::log($nombre);
		
		$codigo .= '		
			<div class="labelForm">'._EMAIL.'</div>
			<div class="datoForm">&nbsp;&nbsp;<b>'.$datos[0].'</b></div>
			<div style="width:1px;"></div>
		
			<div class="labelForm">'._NOMBRE.'</div>
			<div class="datoForm"><input size="30" type="text" name="contacto" value="'.$datos[1].'">*</div>
		
			<div class="labelForm">'._APELLIDOS.'</div>
			<div class="datoForm"><input size="30" type="text" name="apellidos" value="'.$datos[2].'">*</div>
		
			<div class="labelForm">'._EMPRESA.'</div>
			<div class="datoForm"><input size="30" type="text" name="nombre" value="'.$nombre.'"></div>
			
			<div class="labelForm">'._TELEFONO.'</div>
			<div class="datoForm"><input size="30" type="text" name="telefono1" value="'.$datos[4].'">&nbsp;</div>
		
			<div class="labelForm">'._FAX.'</div>
			<div class="datoForm"><input size="30" type="text" name="fax" value="'.$datos[5].'">&nbsp;</div>
			
			<input size="30" type="hidden" name="procesarDatos" value="1">';
		
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
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_formularios */
class formularios extends oficial_formularios {}

?>