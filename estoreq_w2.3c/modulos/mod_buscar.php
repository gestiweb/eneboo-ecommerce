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

/** @class_definition oficial_modBuscar */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_modBuscar
{	
	// Formulario de busqueda
	function contenidos()
	{
		$codigoMod = '
		<form name="buscar" method="post" action="'._WEB_ROOT.'catalogo/articulos.php">
		<input type="text" name="palabras" size="10" maxlength="100"> <a href="javascript:buscar.submit()"><img border="0" src="'._WEB_ROOT.'images/buscar.png"></a><input type="hidden" name="buscar" value="1"></form>';
	
		return $codigoMod;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_modBuscar */
class modBuscar extends oficial_modBuscar {};

?>
