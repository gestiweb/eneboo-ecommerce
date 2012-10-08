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
			
		$codigoMod .= '<div id="cestaLateral">';
		$codigoMod .= $this->innerContenidos();
		$codigoMod .= '</div>';
		
/*		$codigoMod .= '<div class="itemMenu">PESO '.$cesta->peso();
		$codigoMod .= '</div>';*/
		
		return $codigoMod;
	}
	// Listado de articulos en la cesta
	function innerContenidos()
	{
		global $__BD, $__LIB, $__CAT;
	
		$codigo = '';
		
		$cesta = $_SESSION["cesta"];
		
		if (!$cesta->cestaVacia()) {
			for ($i=0; $i < $cesta->num_articulos; $i++){			
				$referencia = $cesta->codigos[$i];	
				if($referencia != 'null') {
					$row = $__BD->db_row("select descripcion, descripciondeeplink from articulos where referencia='$referencia';");
					$descripcion = $__LIB->traducir("articulos", "descripcion", $referencia, $row[0]);
					$link = $__CAT->linkArticulo($referencia, $row[1]);
					$codigo .= '<div class="itemMenu">';
					$codigo .= '<a href="'.$link.'">'.$descripcion.'</a>';
					$codigo .= '</div>';
				}
			}
		}
		
		return $codigo;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_modCesta */
class modCesta extends oficial_modCesta {};

?>
