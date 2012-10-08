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

/** @class_definition oficial_modMicuenta */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_modMicuenta
{	
	// Muesta las opciones de cuenta
	function contenidos()
	{
		$codigoMod = '';
		
		if (isset($_SESSION["codCliente"])) {
			$codigoMod .= '<a href="'._WEB_ROOT.'cuenta/micuenta.php">'._MI_CUENTA.'</a>';
			$codigoMod .= '<br/><a href="'._WEB_ROOT.'cuenta/salir_sesion.php">'._SALIR.'</a>';
		}
		else {
			$codigoMod .= '<a href="'._WEB_ROOT.'cuenta/login.php">'._ENTRAR.'</a>';
			$codigoMod .= '<br/><a href="'._WEB_ROOT.'cuenta/crear_cuenta.php">'._CREAR_CUENTA.'</a>';
		}
		return $codigoMod;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_modMicuenta */
class modMicuenta extends oficial_modMicuenta {};

?>
