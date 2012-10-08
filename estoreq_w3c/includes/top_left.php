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

include_once('libreria/fun_debugger.php');
   
include_once('configure_bd.php');
include_once('configure_web.php');
include_once('clases_objetos.php');
include_once('sesion.php');
include_once('opciones.php');
include_once(_DOCUMENT_ROOT.'idiomas/'.$_SESSION["idioma"].'/main.php');
include_once(_DOCUMENT_ROOT.'includes/securimage/securimage.php');

include_once( 'xajax_comm.inc.php' );
include_once( 'xajax_serv.inc.php' );

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
		
		echo $codigo;
	}
	
	function bandaSuperior() 
	{
		global $__LIB, $__CAT, $__BD, $enIndex;

		$codigo = '';
		
		$codigo .= '<body>';
		
//  		$__LIB->avisoInstall();

		$codigo .= '<div id="container" >';
		$codigo .= '<div id="banner">';

		$titulo = $_SESSION["opciones"]["titulo"];
		
		$codigo .= '<div id="logoTop">';
		$codigo .= '<h1><a href="'._WEB_ROOT_L.'">'.$titulo.'</a></h1>';
		$codigo .= '</div>';

 		$codigo .= '<div id="navTop">';
 		$codigo .= $__LIB->navTop();
		
 		$codigo .= '<div id="bienvenidoTop">';
 		if (isset($_SESSION["codCliente"]))
			$codigo .= strtolower(_BIENVENIDO).' <b>'.$__LIB->nombreCliente().'</b>';
		$codigo .= '</div>';

 		$codigo .= '</div>';

 		if ($enIndex)
	 		$codigo .= $__CAT->stylishSlider();
 		
		$codigo .= '</div>';
		
 		$modsLeft = $__LIB->modulosWeb(0); 
		$modsRight = $__LIB->modulosWeb(1);
		
		$classOuter = '';
		if ($modsLeft)
			$classOuter .= 'outerLeft ';
		if ($modsRight)
			$classOuter .= 'outerRight ';
		if ($modsLeft || $modsRight)
			$classOuter = ' class="'.$classOuter.'"';
		
		$codigo .= '<div id="outer"'.$classOuter.'>';
		$codigo .= '<div id="inner">';
		
		$codigo .= "\n\n";

		if ($modsLeft) {
			$codigo .= '<div id="left">';
			$codigo .= $modsLeft;
			$codigo .= '</div>';
		}
		
		
		if ($modsRight) {
			$codigo .= '<div id="right">';
			$codigo .= $modsRight;
			$codigo .= '</div>';
		}
		
		$codigo .= '<div id="content">';

		return $codigo;
	}

	function head()
	{
		global $__LIB, $__BD, $xajax, $CLEAN_GET;

		$ordenSQL = "select id, tituloseo, descripcionseo from opcionestv";
		$row = $__BD->db_row($ordenSQL);
		
		$tituloSEO = $__LIB->traducir("opcionestv", "tituloseo", $row[0], $row[1]);
		$descripcionSEO = $__LIB->traducir("opcionestv", "descripcionseo", $row[0], $row[2]);

		define('_TITULO', $tituloSEO);
// 		define('_DESCRIPCION_SEO', $tituloSEO);

		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset='.$_SESSION["opciones"]["charset"].'"/>
<title>'.$__LIB->tituloPagina().'</title>
<meta name="description" content="'.$descripcionSEO.'"/>
<base href="'._WEB_ROOT_L.'" />
<link rel="stylesheet" href="'._WEB_ROOT.'templates/'._TEMPLATE.'/estilos.css" type="text/css"/>
<link type="text/css" href="'._WEB_ROOT.'templates/'._TEMPLATE.'/fancybox.css" rel="stylesheet"/>
<script type="text/javascript" src="'._WEB_ROOT.'includes/js/jquery.js"></script>
<script type="text/javascript" src="'._WEB_ROOT.'includes/js/fancybox.js"></script>
<script type="text/javascript" src="'._WEB_ROOT.'includes/libreria.js"></script>';

// echo '<script type="text/javascript" src="'._WEB_ROOT.'minify/min/g=js"></script>';

		$xajax->printJavascript( _WEB_ROOT.'includes/xajax_05/');
		
		echo $__LIB->scriptGA();
		
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