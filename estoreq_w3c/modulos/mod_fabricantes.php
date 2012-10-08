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

/** @class_definition oficial_modFabricantes */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_modFabricantes
{	
	// Combo con el listado de fabricantes
	function contenidos()
	{
		global $__BD, $__LIB;
	
		$codigoMod = '';
		$hayAlgo = false;
		
		$fabricanteSelec = '';	
		if (isset($_SESSION["fabricante"]))
			$fabricanteSelec = $_SESSION["fabricante"];	
		
		$ordenSQL = "select codfabricante, nombre from fabricantes where publico = true order by nombre";
		$result = $__BD->db_query($ordenSQL);
		
		$codigoMod .= '<form action="catalogo/articulos.php" method="get">';
		$codigoMod .= '<div>';
		$codigoMod .= '<select name="fab" class="fabricantes" onchange="this.form.submit();">';
	
		$codigoMod .= '<option></option>';
		while($row = $__BD->db_fetch_array($result)) {
			
			$fabricante = $row["codfabricante"];
			
			$ordenSQL = "select count(referencia) from articulos where publico = true and codfabricante = '$fabricante'";
			if ($__BD->db_valor($ordenSQL) == 0)
				continue;
			
			$hayAlgo = true;
			
			$codigoMod .= '<option value="'.$fabricante.'"';
			if ($fabricante == $fabricanteSelec)
				$codigoMod .= ' selected';
			$codigoMod .= '>';
			$codigoMod .= $row["nombre"];
			$codigoMod .= '</option>';
		}
		$codigoMod .= '</select>';
		$codigoMod .= '</div>';
		$codigoMod .= '</form>';
		
		if (!$hayAlgo)
			return;
		
		return $codigoMod;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_modFabricantes */
class modFabricantes extends oficial_modFabricantes {};

?>