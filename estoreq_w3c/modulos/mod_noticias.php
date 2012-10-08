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

/** @class_definition oficial_modNoticias */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_modNoticias
{	
	// Muesta los registros de noticias activas
	function contenidos()
	{
		global $__BD, $__LIB;
	
		$codigoMod = '';
		
		$hoy = date("Y-m-d", time());
		
		$ordenSQL = "select id, titulo, fecha from noticias where publico = true and fechalimite > '$hoy' order by fecha desc";
		$result = $__BD->db_query($ordenSQL);
		
	
		while($row = $__BD->db_fetch_array($result)) {
			$titulo = $__LIB->traducir("noticias", "titulo", $row["id"], $row["titulo"]);
			$codigoMod .= '<div class="itemMenu">';
			$codigoMod .= '<a href="'._WEB_ROOT.'general/noticias.php?id='.$row["id"].'">'.$titulo.'</a>';
			$codigoMod .= '</div>';
		}
		
		return $codigoMod;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_modNoticias */
class modNoticias extends oficial_modNoticias {};

?>