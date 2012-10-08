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
   	
include("includes/top_left.php");


/** @class_definition oficial_indexPage */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_indexPage
{
	/** Obtiene y muestra la informacion de un indexPage */
	function contenidos() 
	{
		global $__LIB, $__CAT, $__BD;

		$codigo = '';
		$codigo .= '<div class="titPagina">'._BIENVENIDO.'</div>';
	
		// Texto de presentacion
		if ($__LIB->esTrue($_SESSION["opciones"]["mostrartextopre"])) {
			$ordenSQL = "select id, textopre from opcionestv";
			$row = $__BD->db_row($ordenSQL);
			$textopre = $__LIB->traducir("opcionestv", "textopre", $row[0], $row[1]);
			
			$codigo .= '<div class="subCaja">';
			$codigo .= nl2br($textopre);
			$codigo .= '</div>';
		}
		
		$codigo .= '<div class="titApartado"><span class="titApartadoText">'._DESTACADOS.'</span></div>';
		
		if (!isset($_SESSION["vista"])) $_SESSION["vista"] = 1;
		
		// Al volver al inicio las opciones se resetean
		unset($_SESSION["buscar"]);
		unset($_SESSION["orden"]);
		unset($_SESSION["fabricante"]);
		unset($_SESSION["familia"]);
		
		// articulos destacados
		$where = "publico = true AND enportada=true ORDER BY ordenportada";
		$ordenSQL = "select referencia, descripcion, pvp, codimpuesto, stockfis, stockmin, controlstock, codplazoenvio, enoferta, pvpoferta, ivaincluido from articulos where ".$where;
 		$codigo .= $__CAT->articulos($ordenSQL);

		echo $codigo;
	}
}
 
//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @class_definition pcrednet_indexPage */
//////////////////////////////////////////////////////////////////
//// PC REDNET /////////////////////////////////////////////////////

class pcrednet_indexPage extends oficial_indexPage
{
	/** Obtiene y muestra la informacion de un indexPage */
	function contenidos() 
	{
		global $__LIB, $__CAT;

		$codigo = '';
		$codigo .= '<div class="titPagina">'._BIENVENIDO.'</div>';
	
		// Texto de presentacion
		if ($__LIB->esTrue($_SESSION["opciones"]["mostrartextopre"])) {
			$codigo .= '<div class="subCaja">';
			$codigo .= nl2br($_SESSION["opciones"]["textopre"]);
			$codigo .= '</div>';
		}
		
		$codigo .= '<div class="titApartado"><span class="titApartadoText">'._DESTACADOS.'</span></div>';
		
		if (!isset($_SESSION["vista"])) $_SESSION["vista"] = 1;
		
		// Al volver al inicio las opciones se resetean
		unset($_SESSION["buscar"]);
		unset($_SESSION["orden"]);
		unset($_SESSION["fabricante"]);
		unset($_SESSION["familia"]);
		
		// articulos destacados
		$where = "publico = true AND obsoleto = false AND enportada=true ORDER BY ordenportada";
		$ordenSQL = "select referencia, descripcion, pvp, codimpuesto, stockfis, stockmin, controlstock, codplazoenvio, enoferta, pvpoferta, ivaincluido from articulos where ".$where;
		$codigo .= $__CAT->articulos($ordenSQL);

		echo $codigo;
	}
}

//// PC REDNET /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
	
/** @main_class_definition oficial_indexPage */
class indexPage extends pcrednet_indexPage{};

$iface_indexPage = new indexPage;
$iface_indexPage->contenidos();

 
 	include("includes/right_bottom.php");
?>