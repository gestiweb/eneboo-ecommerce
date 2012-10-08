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

/** @class_definition oficial_modNovedad */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_modNovedad
{	
	// Muesta aleatoriamente un articulo nuevo
	function contenidos()
	{
		global $__BD, $__LIB;
	
		$codigoMod = '';
		
		$lastmonth = mktime(0,0,0,date("m"),date("d")-42,  date("Y"));
		$fechaLimite = date("Y-m-d", $lastmonth);
		
		$ordenSQL = "select referencia from articulos where fechapub > '$fechaLimite' and publico=true";
		
		$numNovedades = $__BD->db_num_rows($ordenSQL);
		if ($numNovedades == 0)
			return;
		
		srand((double)microtime()*1000000);
		$randval = rand(0, $numNovedades - 1);
		
		$ordenSQL = "select referencia, descripcion, descripciondeeplink, pvp, codimpuesto, enoferta, pvpoferta, ivaincluido from articulos where fechapub > '$fechaLimite' and publico=true limit 1 offset $randval";
		
		$result = $__BD->db_query($ordenSQL);
		$row = $__BD->db_fetch_array($result);
		
		$codigoMod .= '<div class="cajaImagen">';
		$codigoMod .= $__LIB->cajaImagen($row);
		$codigoMod .= '</div>';
		return $codigoMod;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_modNovedad */
class modNovedad extends oficial_modNovedad {};

?>