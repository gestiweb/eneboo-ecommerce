<?php 
	
	include_once('../includes/configure_bd.php');
	include_once('../includes/configure_web.php');
	include_once('../includes/clases_objetos.php');
	include_once('../includes/sesion.php');
	include_once('../includes/opciones.php');
	include_once(_DOCUMENT_ROOT.'idiomas/'.$_SESSION["idioma"].'/main.php');

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

/** @class_definition oficial_imagen */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_imagen
{
	var $datosImagen;
	
	function datos()
	{
		global $__BD, $CLEAN_GET;
		
		$referencia = "";
		if (isset($CLEAN_GET["ref"]))
			$referencia = $CLEAN_GET["ref"];	
		
		$ordenSQL = "select descripcion, tipoimagen from articulos where referencia = '$referencia'";
		
		$row = $__BD->db_row($ordenSQL);
		$this->datosImagen["descripcion"] = $row[0];
		$tipoImagen = $row[1];
		
		$this->datosImagen["ruta"] = '../catalogo/img_normal/'.$referencia.'.'.$tipoImagen;
		if (!file_exists($this->datosImagen["ruta"])) die ("No existe esta imagen");
	}
	
	function contenidos()
	{
		$codigo = '';
		
		$this->datos();
		
		$codigo .= '
		<html>
		<head>
		<title>'.$this->datosImagen["descripcion"].'</title>
		<link rel="stylesheet" href="'._WEB_ROOT.'templates/'._TEMPLATE.'/estilos.css" type="text/css">
		</head>
		<body>
		
			<div class="fotoGrande">
		
			<img src="'.$this->datosImagen["ruta"].'">
			<p>'.$this->datosImagen["descripcion"].'</p>
	
			</div>
			
			<a class="botLink" href="javascript:window.close()">'._CERRAR_VENTANA.'</a>
			<p class="separador">
			
		</body>
		</html>';
		
		echo $codigo;
	}
	
	
	
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_imagen */

class imagen extends oficial_imagen {};
$iface_imagen = new imagen;
$iface_imagen->contenidos();


?>