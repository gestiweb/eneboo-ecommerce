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

/** @class_definition oficial_modInfogeneral */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_modInfogeneral
{	
	// Muesta el listado de entradas de informacion general
	function contenidos()
	{
		global $__BD, $__LIB;
	
		$codigoMod = '';
		
		$lastmonth = mktime(0,0,0,date("m"),date("d")-42,  date("Y"));
		$fechaLimite = date("Y-m-d", $lastmonth);
		
		$ordenSQL = "select codigo, titulo from infogeneral where publico = true order by orden";
		$result = $__BD->db_query($ordenSQL);
		
	
		while($row = $__BD->db_fetch_array($result)) {
			$titulo = $__LIB->traducir("infogeneral", "titulo", $row["codigo"], $row["titulo"]);
			$codigoMod .= '<div class="itemMenu">';
			$codigoMod .= '<a href="general/infogeneral.php?cod='.$row["codigo"].'">'.$titulo.'</a>';
			$codigoMod .= '</div>';
		}
		
		// FAQs
		if ($__LIB->esTrue($_SESSION["opciones"]["activarfaq"])) {
			$codigoMod .= '<div class="itemMenu">';
			$codigoMod .= '<a href="general/faq.php">'._FAQ.'</a>';
			$codigoMod .= '</div>';
		}
		return $codigoMod;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_modInfogeneral */
class modInfogeneral extends oficial_modInfogeneral {};

?>