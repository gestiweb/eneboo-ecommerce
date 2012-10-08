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

/** @class_definition oficial_modFamilias */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_modFamilias
{	
	// Listado de familias hijas de una familia
	function familiasHijas($codFamilia) 
	{
		global $__BD, $__CAT;
	
		$codigo = '';
	
		$ordenSQL = "select codfamilia, descripcion from familias where codmadre = '$codFamilia' AND publico = true order by orden";
		$result = $__BD->db_query($ordenSQL);
		
		while($row = $__BD->db_fetch_array($result)) {
					
			$codigo .= '<div class="itemMenu">';
			
			$codFamilia = $row["codfamilia"];	
			
			$numArticulos = $__CAT->numArticulosF($codFamilia);
			if ($numArticulos == 0)  {
				$codigo .= '</div>';
				continue;
			}
			
			$codigo .= '<a href="'._WEB_ROOT.'catalogo/articulos.php?fam='.$codFamilia.'">'.$row["descripcion"].'</a>';
			
			$this->familiasHijas($codFamilia);
			
			$codigo .= '</div>';
		}
		
		return $codigo;
	}
	
	// Verifica si una familia tiene un por ancestro otra
	function antepasadoFamilia($codAncestro, $codDescendiente) 
	{
		global $__BD, $__CAT;
	
		if ($codAncestro == $codDescendiente)
			return true;
	
		$ordenSQL = "select codmadre from familias where codfamilia = '$codDescendiente' AND publico = true";
		$codMadre = $__BD->db_valor($ordenSQL);
		
		// Familia primaria
		if (!$codMadre)
			return false;
		
		// Encontrado el ancestro
		if ($codMadre == $codAncestro)
			return true;
		// Subo otro nivel
		else
			return $this->antepasadoFamilia($codAncestro, $codMadre);
	}

	// Listado de las familias publicas con articulos
	function contenidos() 
	{
		global $__BD, $__CAT, $__LIB;
		
		$codigoMod = '';
		$familiaTop = '';
		if (isset($CLEAN_GET["fam"]))
			$familiaTop = htmlentities($CLEAN_GET["fam"]); 
		
		$ordenSQL = "select codfamilia, descripcion from familias where (codmadre = '' OR codmadre is null) AND publico = true order by orden";
	
		$result = $__BD->db_query($ordenSQL);
		
		while($row = $__BD->db_fetch_array($result)) {
			$codFamilia = $row["codfamilia"];
			$numArticulos = $__CAT->numArticulosF($codFamilia);
			if ($numArticulos == 0) continue;
			
			$descripcion = $__LIB->traducir("familias", "descripcion", $row["codfamilia"], $row["descripcion"]);
			
			$codigoMod .= '<div class="itemMenu">';
			$codigoMod .= '<a href="'._WEB_ROOT.'catalogo/articulos.php?fam='.$codFamilia.'">'.$descripcion.'</a>';
	// 		$codigoMod .= ' ('.$numArticulos.')';
			$codigoMod .= '</div>';
		
			if (!$this->antepasadoFamilia($codFamilia, $familiaTop))
				continue;
			
	// 		familiasHijas($codFamilia);
			
			if ($familiaTop != $codFamilia)
				continue;
		}
		
		return $codigoMod;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_modFamilias */
class modFamilias extends oficial_modFamilias {};

?>