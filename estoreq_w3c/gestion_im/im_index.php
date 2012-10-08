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

error_reporting(E_ALL);
ini_set('display_errors', true);

session_name('eStoreQ');
session_start();
if(!isset($_SESSION['user_id']))
{
	include_once('im_login.php');
	exit;
}

include_once('../includes/libreria/fun_debugger.php');
include_once('../includes/configure_bd.php');
include_once('../includes/libreria/fun_bd.php');
include_once('../includes/libreria/fun_seguridad.php');
include_once('includes/im_class.upload.php');

$__BD = new funBD;
$__BD->conectaBD();
$__SEC = new funSeguridad;
$CLEAN_GET = $__SEC->limpiarGET($_GET);

if (isset($CLEAN_GET["ref"]))
	$referencia = $CLEAN_GET["ref"];
else	
	$referencia = '';
	
if (isset($CLEAN_GET["numFam"]))
	$numFam = $CLEAN_GET["numFam"];
else
	$numFam = 0;

include_once('includes/im_xajax_upload.php');

$ordenSQL = "select piximages, charset from opcionestv";
$result = $__BD->db_query($ordenSQL);
$opciones = $__BD->db_fetch_array($result);

$dimThum = $opciones["piximages"];

$dim = 450;
$dimCaja = $dimThum + 20;
$yOpciones = $dimThum + 20;

$dimSuperThum = 35;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv=content-type content="text/html; charset=<? echo $opciones["piximages"]?>">
<head>
    <title>eStoreQ &middot; Gestor de im&aacute;genes</title>
    
	<link type="text/css" href="../templates/default/jquery.css" rel="stylesheet" />
	<link type="text/css" href="../templates/default/fancybox.css" rel="stylesheet" />
	<link type="text/css" href="includes/im_estilos.css" rel="stylesheet" />
	
	<style>
		#sortable {
			list-style-type: none;
			margin: 0;
			padding: 0;
		}
		#sortable li {
			position: relative;
			font-size: 0.9em;
			float: left;
			margin: 3px 3px 3px 0;
			padding: 5px;
			width: <? echo $dimCaja ?>px;
			height: <? echo $dimCaja + 10?>px;
			text-align: center;
		}
		#sortable img {
			border-width: 0px;
			margin: 0px 0px 5px 0px;
			padding: 5px;
			background-color: #FFF;
		}
		div.opciones {
			position: absolute;
			top: <? echo $yOpciones ?>px;
		}
	</style>
	
	<script type="text/javascript" src ="../includes/js/jquery.js"></script>
	<script type="text/javascript" src ="../includes/js/jquery-ui.js"></script>
	<script type="text/javascript" src ="../includes/js/fancybox.js"></script>
    
	<script type="text/javascript">
		
		$('document').ready( function() {
	
			$("#tabs").tabs();
			
			$("#sortable").sortable
			({
				opacity: 0.7,
				update: function()
				{
						disp( $("#sortable > li").get() );
				}
			});
			
			$("#accordion").accordion
			({
				collapsible: true,
				active: <? echo $numFam ?>
			});
			
			$("#sortable").disableSelection();
			$('.ampliar').fancybox();
			
		});
			
		function slideDown($targetDiv) 
		{
			$targetDiv = '#' + $targetDiv;
		
			if ($($targetDiv + ":first").is(":hidden")) {
				$($targetDiv).slideDown(150);
			}
		}
		
		function disp(lista) 
		{
			var a = [];
		 	for (var i = 0; i < lista.length; i++) {
				a.push(lista[i].id);
			}
			xajax_save_sort(a, '<? echo $referencia ?>');
		}

	</script>

	<? $xajax->printJavascript('../includes/xajax_05/'); ?>
    
</head>

<body>

<h1>eStoreQ &middot; Gestor de im&aacute;genes</h1>
<h2><a href="im_logout.php">Cerrar sesi&oacute;n</a></h2>


<?php

/** @class_definition oficial_gestorImagenes */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_gestorImagenes
{
	
	function contenidos()
	{
		global $referencia, $numFam, $__BD, $dimThum, $dimSuperThum, $dim;
	
		$this->actualizarObsoletos();
		
		$codigo = '';
	
		$pathNor = "../catalogo/img_normal/".$referencia;
		$pathThum = "../catalogo/img_thumb/".$referencia;
		$pathSuperThum = "../catalogo/img_superthumb/".$referencia;
		
		if (!file_exists($pathNor)) mkdir($pathNor);
		if (!file_exists($pathThum)) mkdir($pathThum);
		if (!file_exists($pathSuperThum)) mkdir($pathSuperThum);

		$codigo .= '<div id="column0">';
		$codigo .= $this->listaRef($referencia);
		$codigo .= '</div>';
		
		if (!$referencia) {
			$codigo .= '</body></html>';
			echo $codigo;
			exit;
		}
		
		$codigo .= '
		<div id="dialogo" title="Gestor de Im&aacute;genes de eStoreQ"></div>';
		
		$codigo .= '<div id="column1">';
		
		$codigo .= '<div id="tabs">';
		$codigo .= $this->secciones();
		
		$codigo .= $this->tabPpal($pathNor, $pathThum);
		$codigo .= $this->tabSubir();
		
		$codigo .= '</div>';
		$codigo .= '</div>';
		
		echo $codigo;
	}
	
	function secciones()
	{
		$codigo = '
			<ul>
				<li><a href="#tabs-1">Im&aacute;genes</a></li>
				<li><a href="#tabs-2">Subir im&aacute;genes</a></li>
			</ul>';
			
		return $codigo;
	}
	
	
	function tabPpal($pathNor, $pathThum)
	{
		global $referencia, $numFam, $__BD;
		
		$codigo = '';
		
		$codigo .= '<div id="tabs-1">';
		
		$codigo .= '<div id="msg"></div>';
		$codigo .= '<form name="formUploader" enctype="multipart/form-data" method="post" action="im_procesar_imagenes.php?ref='.$referencia.'&numFam='.$numFam.'">';
		
		$rand = time();
		
		
		$nomFichs = $this->leerDir($pathNor);
		
		
		$ordenSQL = "select * from articulosfotos where referencia = '$referencia' order by orden";
		$result = $__BD->db_query($ordenSQL);
		
		$codSortable = '';
		while ($row = $__BD->db_fetch_array($result)) {
			$id = $row["id"];
			$nomFich = $row["nomfichero"];
			$codSortable .='<li class="ui-state-default" id="imagen_'.$id.'">';
		
			$codSortable .='<img src="'.$pathThum.'/'.$nomFich.'?'.$rand.'">';
			
			$codSortable .='<div class="opciones">';
			
			$codSortable .='<div class="opcion" id="links_'.$id.'">';
			$codSortable .='<a href="#1" onclick="xajax_confirmar_borrar_foto('.$id.')">Eliminar</a>';
			$codSortable .='</div>';
			
			$codSortable .='<div class="opcion" id="msg_'.$id.'"></div>';
		
			$codSortable .='<div class="opcion" id="ampliar_'.$id.'">';
			$codSortable .= '<a href="'.$pathNor.'/'.$nomFich.'" class="ampliar" rel="galeria">Ampliar</a>';
			$codSortable .='</div>';
		
			$codSortable .='</div>';
			
			$codSortable .='<br class="cleaner">';
			$codSortable .='</li>';
		}
		
		if ($codSortable) {
			$codigo .= '<ul id="sortable">'.$codSortable.'</ul>';
		}
		else {
			$codigo .= 'No hay im&aacute;genes para este art&iacute;culo';
		}
		
		$codigo .= '<br class="cleaner">';
		$codigo .= '</div>';
		
		return $codigo;
	}
	
	function tabSubir()
	{
		$codigo = '';
		$codigo .= '<div id="tabs-2">';
		
		for ($i=0; $i<10; $i++) {
			$codigo .= '<input type="file" size="60" name="my_field[]" value="" /><br/>';
		}
		
		$codigo .= '<input type="hidden" name="action" value="process" />';
		$codigo .= '<p><input type="submit" name="Submit" value="SUBIR" /></p>';
		$codigo .= '</form>';
		
		$codigo .= '</div>';
		
		return $codigo;
	}
	
	function listaRef($referencia)
	{
		global $__BD;
		
	
		$codigoLeft = '';
		
		$codigoLeft .= '<div id="accordion">';
	
		$ordenSQL = "select descripcion, codfamilia from familias where publico=true order by descripcion";
		$resultF = $__BD->db_query($ordenSQL);
		
		$numFam = 0;
	
		while($rowF = $__BD->db_fetch_array($resultF)) {
			
			$codFamilia = $rowF["codfamilia"];
				
			$ordenSQL = "select referencia, descripcion from articulos where codfamilia = '$codFamilia' and publico=true order by descripcion";
			if (!$__BD->db_num_rows($ordenSQL))
				continue;
			
			$codigoLeft .= '<a class="familia" href="#">'.$rowF["descripcion"].'</a>';
			$codigoLeft .= '<div>';
	
			$result = $__BD->db_query($ordenSQL);
			
			while($row = $__BD->db_fetch_array($result)) {
				$clase = '';
				if ($referencia == $row["referencia"])
					$clase = 'class="actual"';
				$codigoLeft .= '<a '.$clase.' href="im_index.php?ref='.$row["referencia"].'&numFam='.$numFam.'">';
				$codigoLeft .= $row["descripcion"];
				$codigoLeft .= '</a>';
			}
			
			$codigoLeft .= '</div>';
			$numFam++;
		}
		
		$codigoLeft .= '</div>';
		
		return $codigoLeft;
	}
	
	
	function leerDir($dirPath)
	{
		$myDirectory = opendir($dirPath);
	
		while($entryName = readdir($myDirectory)) {
			if (substr($entryName, 0, 1) == ".") continue;
			$dirArray[] = $entryName;
		}
		
		if (!isset($dirArray))
			return false;
	
		closedir($myDirectory);
		return $dirArray;
	}
	
	/** Pasa las imagenes viejas al nuevo sistema
	*/
	function actualizarObsoletos()
	{
		global $__BD;
		
		$codigo = '';

		$ordenSQL = "select count(id) from articulosfotos";
		if ($__BD->db_valor($ordenSQL))
			return;
		
		$ordenSQL = "select piximages from opcionestv";
		$dimThum = $__BD->db_valor($ordenSQL);
		$dimSuperThum = 60;
		
		$paso = 0;
		
		$ordenSQL = "select referencia,tipoimagen from articulos order by referencia";
		$result = $__BD->db_query($ordenSQL);
		while($row = $__BD->db_fetch_array($result)) {
			
			$referencia = $row["referencia"];
			$ext = $row["tipoimagen"];
			
			$pathNor = "../catalogo/img_normal/".$referencia;
			$pathThum = "../catalogo/img_thumb/".$referencia;
			$pathSuperThum = "../catalogo/img_superthumb/".$referencia;
		
			if (!file_exists($pathNor)) mkdir($pathNor);
			if (!file_exists($pathThum)) mkdir($pathThum);
			if (!file_exists($pathSuperThum)) mkdir($pathSuperThum);
			
			$nomFich = $referencia.'.'.$ext;
			$imgNor = "../catalogo/img_normal/".$nomFich;
			if (!file_exists($imgNor)) {
				$nomFich = $referencia.'.jpg';
				$imgNor = "../catalogo/img_normal/".$nomFich;
			}
		
			if (!file_exists($imgNor))
				continue;
			
			$ordenSQL = "select id from articulosfotos where referencia='$referencia' and nomfichero='$nomFich'";
			if ($__BD->db_valor($ordenSQL))
				continue;
			
			$handle = new upload_eStoreQ($imgNor);
			
			// Foto normal
			$handle->Process($pathNor);
			
			// Thumb
			$handle->image_x = $dimThum;
			$handle->image_y = $dimThum;
			$handle->image_resize = true;
			$handle->image_ratio = true;
			$handle->file_new_name_body = $orden;
			
			$handle->Process($pathThum);
		
		
			// SuperThumb
			$handle->image_x = $dimSuperThum;
			$handle->image_y = $dimSuperThum;
			$handle->image_resize = true;
			$handle->image_ratio = true;
			$handle->file_new_name_body = $orden;
			
			$handle->Process($pathSuperThum);
		
		
			if ($handle->processed) {
					
				$ordenSQL = "update articulosfotos set orden = orden + 1 where referencia = '$referencia'";
				$__BD->db_query($ordenSQL);
					
				$ordenSQL = "insert into articulosfotos(referencia, nomfichero, orden) values ('$referencia', '$nomFich', 1)";
				$__BD->db_query($ordenSQL);
					
				$codigo .= "Fichero <b>$nomFich</b> procesado<br/>";
			}
			else {
				$codigo .= '<p>Error: ' . $handle->error . ' '.$handle->file_dst_name;
			}
			
			$paso++;
		}
	}
	
}


//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition gestorImagenes */
class gestorImagenes extends oficial_gestorImagenes {};

$iface_gestorImagenes = new gestorImagenes;
$iface_gestorImagenes->contenidos();


?> 
</body>

</html>
