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

/** @class_definition oficial_sesion */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_sesion
{	
	function contenidos() 
	{
		global $__CLI, $__BD;
		
 		session_name('eStoreQ');
		session_start();
		
		if (!isset($_SESSION['initiated']))
		{
			session_regenerate_id();
			$_SESSION['initiated'] = true;
		}
		
		if (!isset($_SESSION["cesta"])) {
			$_SESSION["cesta"] = new cesta();
		}
		
		if (!isset($_SESSION["idioma"])) {
			$_SESSION["idioma"] = "esp";
		}
	
		if (isset($_SESSION["codCliente"]))
			$__CLI = new cliente();
	
 		$__BD->conectaBD();
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition sesion */
class sesion extends oficial_sesion {};

$iface_sesion = new sesion;
$iface_sesion->contenidos();

?>