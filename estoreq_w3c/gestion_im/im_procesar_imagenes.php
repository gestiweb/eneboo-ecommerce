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

/*** begin the session ***/
session_name('eStoreQ');
session_start();
if(!isset($_SESSION['user_id']))
{
	include_once('im_login.php');
	exit;
}

include_once('../includes/configure_bd.php');
include_once('../includes/libreria/fun_bd.php');

$__BD = new funBD;
$__BD->conectaBD();

include_once('includes/im_class.upload.php');
include_once('includes/im_xajax_upload.php');

$numFam = 0;
if (isset($_GET["numFam"]))
	$numFam = $_GET["numFam"];

$referencia = '';
if (isset($_GET["ref"]))
	$referencia = $_GET["ref"];
	
$ordenSQL = "select piximages, charset from opcionestv";
$result = $__BD->db_query($ordenSQL);
$opciones = $__BD->db_fetch_array($result);

$dimThum = $opciones["piximages"];

$dim = 400;
$dimCaja = $dimThum + 50;
$yOpciones = $dimThum + 20;

$dimSuperThum = 80;
$dimMedX = 400;
$dimMedY = 300;

$urlOK = "Location: im_index.php?ref=$referencia&numFam=$numFam";
$urlNOK = $urlOK."&result=nok";

// header($urlOK);


/** @class_definition oficial_gestorImagenes */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_gestorImagenes
{
	
	function contenidos()
	{
		global $referencia, $urlOK, $urlNOK, $__BD, $dimThum, $dimSuperThum, $dim, $dimMedX, $dimMedY;
	
		$codigo = '';
	
		$pathNor = "../catalogo/img_normal/".$referencia;
		$pathMed = "../catalogo/img_mediana/".$referencia;
		$pathThum = "../catalogo/img_thumb/".$referencia;
		$pathSuperThum = "../catalogo/img_superthumb/".$referencia;
		
		if (!file_exists($pathNor)) mkdir($pathNor);
		if (!file_exists($pathMed)) mkdir($pathMed);
		if (!file_exists($pathThum)) mkdir($pathThum);
		if (!file_exists($pathSuperThum)) mkdir($pathSuperThum);
		
		
		// we first include the upload class, as we will need it here to deal with the uploaded file
		
		if (!isset($_POST['action'])) {
			header($urlOK);
			exit;
		}
		
		$nomFichs = $this->leerDir($pathNor);
		$paso = 1;
		if ($nomFichs)
			$paso += count($nomFichs);
		
		$files = array();
		foreach ($_FILES['my_field'] as $k => $l) {
			foreach ($l as $i => $v) {
				if (!array_key_exists($i, $files)) 
					$files[$i] = array();
				$files[$i][$k] = $v;
			}
		}
		
		// now we can loop through $files, and feed each element to the class
	
		$ordenSQL = "select max(id) from articulosfotos";
		$orden = $__BD->db_valor($ordenSQL);
		$orden++;
	
		foreach ($files as $file) {
		
			$handle = new upload_eStoreQ($file);
			
			if (!$handle->uploaded)
				continue;
	
			$orden++;
			$handle->image_x = $dim;
			$handle->image_y = $dim;
			$handle->image_resize = true;
			$handle->image_ratio_no_zoom_in = true;
			$handle->file_new_name_body = $orden;
	
			// Foto normal
			$handle->Process($pathNor);
			
			
			
			// Mediana
			$handle->image_x = $dimMedX;
			$handle->image_y = $dimMedY;
			$handle->image_resize = true;
			$handle->image_ratio_no_zoom_in = true;
			$handle->file_new_name_body = $orden;
		
			$handle->Process($pathMed);
			
	
	
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
				$nomFich = $handle->file_dst_name;
				$ordenSQL = "insert into articulosfotos(referencia, nomfichero, orden, id) values ('$referencia', '$nomFich', $orden, $orden)";
				$result = $__BD->db_query($ordenSQL);
				header($urlOK);
			}
			else {
				header($urlNOK);
			}
		}
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
