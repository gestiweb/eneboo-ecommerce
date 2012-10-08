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

$enIndex = true;
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
		
		$codigo .= '<h1>'._BIENVENIDO.'</h1>';
	
		// Texto de presentacion
		if ($__LIB->esTrue($_SESSION["opciones"]["mostrartextopre"])) {
			$ordenSQL = "select id, textopre from opcionestv";
			$row = $__BD->db_row($ordenSQL);
			$textopre = $__LIB->traducir("opcionestv", "textopre", $row[0], $row[1]);
			
			$codigo .= '<div class="subCaja">';
			$codigo .= nl2br($textopre);
			$codigo .= '</div>';
		}
		
		$codigo .= '<h2 class="titApartadoText">'._DESTACADOS.'</h2>';
		
		if (!isset($_SESSION["vista"])) $_SESSION["vista"] = 1;
		
		// Al volver al inicio las opciones se resetean
		unset($_SESSION["buscar"]);
		unset($_SESSION["orden"]);
		unset($_SESSION["fabricante"]);
		unset($_SESSION["familia"]);
		
		// articulos destacados
		$where = "publico = true AND enportada=true ORDER BY ordenportada";
		$ordenSQL = "select referencia, descripcion, descripciondeeplink, pvp, codimpuesto, stockfis, stockmin, controlstock, codplazoenvio, enoferta, pvpoferta, ivaincluido from articulos where ".$where;
 		$codigo .= $__CAT->articulos($ordenSQL);

// 		$__LIB->enviarMail('jesus@infosial.com', 'hi there', 'bokepaaasa', 'bokepaaasa <strong>my friend</strong>');
		echo $codigo;
	}
	
}
 
//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_indexPage */
class indexPage extends oficial_indexPage {};

$iface_indexPage = new indexPage;
$iface_indexPage->contenidos();

 
 	include("includes/right_bottom.php");
?>