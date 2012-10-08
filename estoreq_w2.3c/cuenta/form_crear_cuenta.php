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
	function contenidos($continua) 
	{
		global $CLEAN_POST;
		$formName = "crearCuenta";
	
		$dirFact[0] = $CLEAN_POST["direccion"];
		$dirFact[1] = $CLEAN_POST["codpostal"];
		$dirFact[2] = $CLEAN_POST["ciudad"];
		$dirFact[3] = $CLEAN_POST["provincia"];
		$dirFact[4] = $CLEAN_POST["codpais"];
		
		$dirEnv[0] = $CLEAN_POST["direccion_env"];
		$dirEnv[1] = $CLEAN_POST["codpostal_env"];
		$dirEnv[2] = $CLEAN_POST["ciudad_env"];
		if (isset($CLEAN_POST["provincia_env"]))
			$dirEnv[3] = $CLEAN_POST["provincia_env"];
		else
			$dirEnv[3] = '';
		$dirEnv[4] = $CLEAN_POST["codpais_env"];
		
		$codigo = '';
		$codigo .= '<form name="'.$formName.'" id="'.$formName.'" action="crear_cuenta.php'.$continua.'" method="post">';
		
		$codigo .= '<div class="titApartado">'._DATOS_CUENTA.'</div>';
		$codigo .= formularios::nuevaCuentaGeneral($CLEAN_POST);
		
		$codigo .= '<div class="titApartado">'._PERSONAL.'</div>';
		$codigo .= formularios::nuevaCuentaPersonal($CLEAN_POST);
		
		$codigo .= '<div class="titApartado">'._DIRECCION_FACT.'</div>';
		$codigo .= formularios::dirFact($dirFact, 'crearCuenta');
		
		$codigo .= '<div class="titApartado">'._DIRECCION_ENV.' ('._AVISO_DIRECCION_ENV.')</div>';
		$codigo .= formularios::dirEnv($dirEnv, 'crearCuenta');
		
		$codigo .= $this->masDatos();
		
		$codigo .= '<input type="hidden" name="procesar" value="1">';
		$codigo .= '</form>';
	
		$codigo .= '<p style="clear:both; padding-top:40px">';
		$codigo .= '<a class="botLink" href="javascript:document.crearCuenta.submit()">'._CREAR_CUENTA.'</a>';
		//$codigo .= '<a class="botLink" href="#" onclick="xajax_validarCuenta(xajax.getFormValues(\'crearCuenta\'))">'._CREAR_CUENTA.'</a>';
		$codigo .= '</p>';
		
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
$iface_formCrearCuenta->contenidos($continua);