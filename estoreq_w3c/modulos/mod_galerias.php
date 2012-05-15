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

/** @class_definition oficial_modGalerias */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_modGalerias
{	
	// Listado de las galerias de imagenes
	function contenidos() 
	{
		global $__BD, $__CAT, $__LIB;
		
		$codigoMod = '';
		$familiaTop = '';
		if (isset($CLEAN_GET["fam"]))
			$familiaTop = htmlentities($CLEAN_GET["fam"]); 
		
		$ordenSQL = "select * from galeriasimagenes where publico = true order by orden";
	
		$result = $__BD->db_query($ordenSQL);
		
		while($row = $__BD->db_fetch_array($result)) {
			$codGaleria = $row["codgaleria"];			
			$titulo = $__LIB->traducir("galeriasimagenes", "titulo", $row["codgaleria"], $row["titulo"]);
			
			$codigoMod .= '<div class="itemMenu">';
			$codigoMod .= '<a href="'._WEB_ROOT.'general/galeria.php?gal='.$codGaleria.'">'.$titulo.'</a>';
			$codigoMod .= '</div>';
		}
		
		return $codigoMod;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition modGalerias */
class modGalerias extends oficial_modGalerias {};

?>