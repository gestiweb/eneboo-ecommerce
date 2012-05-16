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

/** @no_class */

// Cargamos todas las opciones
$ordenSQL = "select * from opcionestv";
$result = $__BD->db_query($ordenSQL);
$_SESSION["opciones"] = $__BD->db_fetch_array($result);


// Vista
if (!isset($_SESSION["vista"])) {
	switch($_SESSION["opciones"]["vistacatalogo"]) {
		case "Matriz":
			$_SESSION["vista"] = 1;
		break;
		case "Lista":
			$_SESSION["vista"] = 0;
		break;
		default:
			$_SESSION["vista"] = 1;
	}
}


// Divisa
if (!isset($_SESSION["divisa"])) {
	$_SESSION["divisa"] = $__BD->db_valor("select coddivisa from empresa");
}


// Idioma
if (isset($CLEAN_GET["newlang"])) {
	$__LIB->cambiarIdioma(htmlentities($CLEAN_GET["newlang"]));
} 


if (!isset($_SESSION["idioma"]))
	$_SESSION["idioma"] = $_SESSION["opciones"]["codidiomadefecto"];


// Plantilla
$template = $_SESSION["opciones"]["plantilla"];
if (!$template || !file_exists(_DOCUMENT_ROOT.'/templates/'.$template))
	$template = 'default';

define('_TEMPLATE', $template);

?>