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

/** @class_definition oficial_modOfertas */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_modOfertas
{	
	// Muesta un articulo en oferta aleatorio
	function contenidos()
	{	
		global $__BD, $__LIB;
	
		$codigoMod = '';
		
		$ordenSQL = "select referencia from articulos where enoferta = true and publico=true";
		$numOfertas = $__BD->db_num_rows($ordenSQL);		
		if ($numOfertas == 0) 
			return;

		srand((double)microtime()*1000000);
		$randval = rand(0, $numOfertas - 1);
		
		$ordenSQL = "select referencia, descripcion, descripciondeeplink, pvp, codimpuesto, enoferta, pvpoferta, ivaincluido from articulos where  enoferta = true and publico=true limit 1 offset $randval";
		
		$result = $__BD->db_query($ordenSQL);
		$row = $__BD->db_fetch_array($result);
		
		$codigoMod .= '<div class="cajaImagen">';
		$codigoMod .= $__LIB->cajaImagen($row);
	 	$codigoMod .= '<br/><br/><a class="botLink" href="catalogo/articulos.php?ver=enoferta">'.strtolower(_MAS_OFERTAS).'</a>';
		$codigoMod .= '</div>';
		
		return $codigoMod;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_modOfertas */
class modOfertas extends oficial_modOfertas {};

?>