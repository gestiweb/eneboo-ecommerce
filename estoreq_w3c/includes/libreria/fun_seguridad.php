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

// error_reporting(0);

// Funciones de seguridad
class oficial_funSeguridad
{
	
	// limpia de caracteres peligrosos y etiquetas html
	function limpiarPOST()
	{
		$clean = false;
	
		while (list ($clave, $valor) = each ($_POST))
			$clean[$clave] = addslashes(htmlspecialchars($valor));
				
		return $clean;	
	}
	
	// limpia de caracteres peligrosos y etiquetas html
	function limpiarGET($datos)
	{
		$clean = false;
		while (list ($clave, $valor) = each ($datos))
			$clean[$clave] = addslashes(htmlspecialchars($valor));
		
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
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_funSeguridad */
class funSeguridad extends oficial_funSeguridad {};

?>