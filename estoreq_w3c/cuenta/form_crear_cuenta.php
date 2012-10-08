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

/** @class_definition oficial_formCrearCuenta */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_formCrearCuenta
{
	function contenidos($continua, $errores) 
	{
		global $CLEAN_POST, $__LIB;

		$formName = "crearCuenta";
	
		$dirEnv["direccion"] = $CLEAN_POST["direccion_env"];
		$dirEnv["codpostal"] = $CLEAN_POST["codpostal_env"];
		$dirEnv["ciudad"] = $CLEAN_POST["ciudad_env"];
		if (isset($CLEAN_POST["provincia_env"]))
			$dirEnv["provincia"] = $CLEAN_POST["provincia_env"];
		else
			$dirEnv["ciudad"] = '';
		$dirEnv["codpais"] = $CLEAN_POST["codpais_env"];
		
		$codigo = '';
		$codigo .= '<form name="'.$formName.'" id="'.$formName.'" action="cuenta/crear_cuenta.php'.$continua.'" method="post">';
		
		$codigo .= '<h2>'._DATOS_CUENTA.'</h2>';
		$codigo .= formularios::nuevaCuentaGeneral($CLEAN_POST, $errores);
		
		$codigo .= '<h2>'._PERSONAL.'</h2>';
		$codigo .= formularios::nuevaCuentaPersonal($CLEAN_POST, $errores);
		
		$codigo .= '<h2>'._DIRECCION_FACT.'</h2>';
		$codigo .= formularios::dirFact($CLEAN_POST, 'crearCuenta', $errores);
		
		$codigo .= '<h2>'._DIRECCION_ENV.' ('._AVISO_DIRECCION_ENV.')</h2>';
		$codigo .= formularios::dirEnv($dirEnv, 'crearCuenta', $errores);
		
		$codigo .= $this->masDatos();
		
		$codigo .= '<input type="hidden" name="procesar" value="1">';
		
		if ($__LIB->esTrue($_SESSION["opciones"]["validarcrearcuenta"]))
 			$codigo .= formularios::codigoValidacion(_CODIGO_VALIDACION, $errores);
		
		$codigo .= formularios::botEnviar(_CREAR_CUENTA);
		
		$codigo .= '</form>';
		
		echo $codigo;
	}
	
	// Extender
	function masDatos()
	{
		return '';
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_formCrearCuenta */
class formCrearCuenta extends oficial_formCrearCuenta{};

$iface_formCrearCuenta = new formCrearCuenta();
$iface_formCrearCuenta->contenidos($continua, $errores);