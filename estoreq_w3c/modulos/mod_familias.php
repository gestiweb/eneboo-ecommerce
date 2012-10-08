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
		global $__BD, $__CAT, $__LIB, $CLEAN_GET;
		
		$__CAT->checkDeepLinks();
		
		$codigoMod = '';
		$familiaTop = '';
		if (isset($CLEAN_GET["fam"]))
			$familiaTop = htmlentities($CLEAN_GET["fam"]);
			
		$familiaTopDL = '';	
		if (isset($CLEAN_GET["famdl"]))
			$familiaTopDL = htmlentities($CLEAN_GET["famdl"]);
			
		$ordenSQL = "select codfamilia, descripcion, descripciondeeplink from familias where (codmadre = '' OR codmadre is null) AND publico = true order by orden";
	
		$result = $__BD->db_query($ordenSQL);
		
		while($row = $__BD->db_fetch_assoc($result)) {
			$codFamilia = $row["codfamilia"];
			$numArticulos = $__CAT->numArticulosF($codFamilia);
			if ($numArticulos == 0) continue;
			
			$descripcion = $__LIB->traducir("familias", "descripcion", $row["codfamilia"], $row["descripcion"]);
			
			$estiloMarcar = $familiaTopDL == $row["descripciondeeplink"] ? ' itemMenuActivo' : '';
			
			$codigoMod .= '<div class="itemMenu familias '.$estiloMarcar.'">';
			$codigoMod .= '<a href="'.$__CAT->linkFamilia($codFamilia, $row["descripciondeeplink"]).'">'.$descripcion.'</a>';
			$codigoMod .= '</div>';
		
			if (!$this->antepasadoFamilia($codFamilia, $familiaTop))
				continue;
			
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