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
		<form id="buscar" method="post" action="catalogo/articulos.php">
		<div>
		<input type="text" name="palabras" size="10"/>
		<input type="hidden" name="buscar" value="1"/>
		<button type="submit" class="buscar"><span></span></button>
		</div>
		</form>';
		
		return $codigoMod;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_modBuscar */
class modBuscar extends oficial_modBuscar {};

?>
