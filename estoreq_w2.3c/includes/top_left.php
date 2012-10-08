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
   	
include_once('configure_bd.php');
include_once('configure_web.php');
include_once('clases_objetos.php');
include_once('sesion.php');
include_once('opciones.php');
include_once(_DOCUMENT_ROOT.'idiomas/'.$_SESSION["idioma"].'/main.php');
include_once(_DOCUMENT_ROOT.'includes/securimage/securimage.php');

error_reporting(E_ALL);

header('Content-Type: text/html; charset='.$_SESSION["opciones"]["charset"]);


/** @class_definition oficial_topLeft */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_topLeft
{
	function contenidos()
	{
		$this->head();
		
		$codigo = '';
		$codigo .= $this->bandaSuperior();
		$codigo .= '<div id="contenidos">';
		
		echo $codigo;
	}
	
	function bandaSuperior() 
	{
		global $__LIB, $__CAT;

		$codigo = '';
		
		$codigo .= '<body>';
		
// 		$__LIB->avisoInstall();

		$codigo .= '<div id="barraTop">';

		$codigo .= '<div id="navTop">';
		$codigo .= $__LIB->navTop();
		$codigo .= '</div>';

		$codigo .= '<div id="bienvenidoTop">';
		if (isset($_SESSION["codCliente"]))
			$codigo .= strtolower(_BIENVENIDO).' <b>'.$__LIB->nombreCliente().'</b>';
		$codigo .= '</div>';

		$codigo .= '</div>';

		$codigo .= '<div id="logoTop">';

		$codigo .= '<div id="imgLogoTop">';
		$codigo .= '<a href="'._WEB_ROOT.'"><img border="0" class="logotop" src="'._WEB_ROOT.'templates/'._TEMPLATE.'/images/logotop.png"></a>';
		$codigo .= '</div>';

		$codigo .= '<div id="sloganLogoTop">';
		$codigo .= _TITULO;
		$codigo .= '</div>';

		$codigo .= '</div>';

		$codigo .= "\n\n";

		$codigo .= '<div id="modulosLeft">';
		$codigo .= $__LIB->modulosWeb(0);
		$codigo .= '</div>';
		
		$codigo .= "\n\n";

		$codigo .= '<div id="modulosRight">';
		$codigo .= $__LIB->modulosWeb(1);
		$codigo .= '</div>';
		
		$codigo .= "\n\n";

		return $codigo;
	}

	function head()
	{
		global $__LIB, $__BD;

		$ordenSQL = "select id, titulo from opcionestv";
		$row = $__BD->db_row($ordenSQL);
		
		$tituloSEO = $__LIB->traducir("opcionestv", "titulo", $row[0], $row[1]);

		define('_TITULO', $tituloSEO);

		echo '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset='.$_SESSION["opciones"]["charset"].'">
		<title>'.$__LIB->tituloPagina().'</title>
		
		<link rel="stylesheet" href="'._WEB_ROOT.'templates/'._TEMPLATE.'/estilos.css.php" type="text/css">
		<link type="text/css" href="'._WEB_ROOT.'templates/'._TEMPLATE.'/fancybox.css" rel="stylesheet" />

		<link type="text/css" href="'._WEB_ROOT.'includes/js/jquery.css" rel="stylesheet" />
		<script type="text/javascript" src="'._WEB_ROOT.'includes/js/jquery.js"></script>
		<script type="text/javascript" src="'._WEB_ROOT.'includes/js/fancybox.js"></script>
 		<script type="text/javascript" src="'._WEB_ROOT.'includes/js/jquery-ui.js"></script>
		<script type="text/javascript" src="'._WEB_ROOT.'includes/js/jquery.bgiframe.min.js"></script>

		<script language="javascript" src="'._WEB_ROOT.'includes/libreria.js"></script>';
		
		require_once( 'xajax_comm.inc.php' );
		$xajax->printJavascript( _WEB_ROOT.'includes/xajax/');
		
		echo '</head>';
	}
}
 
//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_topLeft */
class topLeft extends oficial_topLeft {};

$iface_topLeft = new topLeft;
$iface_topLeft->contenidos();

?>
