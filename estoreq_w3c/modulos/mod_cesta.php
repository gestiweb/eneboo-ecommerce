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

/** @class_definition oficial_modCesta */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_modCesta
{	
	// Listado de articulos en la cesta
	function contenidos()
	{
		global $__BD, $__LIB;
	
		$codigoMod = '';
		
		if (!isset($_SESSION["cesta"]))
			return;
			
		$cesta = $_SESSION["cesta"];
		
		if (!$cesta->cestaVacia()) {
			for ($i=0; $i < $cesta->num_articulos; $i++){			
				$referencia = $cesta->codigos[$i];			
				if($referencia != 'null') {								
					$descripcion = $__BD->db_valor("select descripcion from articulos where referencia='$referencia';");	
					$descripcion = $__LIB->traducir("articulos", "descripcion", $referencia, $descripcion);
					
					$codigoMod .= '<div class="itemMenu">';
					$codigoMod .= '<a href="'._WEB_ROOT.'catalogo/articulo.php?ref='.$referencia.'">'.$descripcion.'</a>';
					$codigoMod .= '</div>';
				}
			}
		}
		
		return $codigoMod;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_modCesta */
class modCesta extends oficial_modCesta {};

?>
