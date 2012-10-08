<?php include("../includes/top_left.php") ?>

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

/** @class_definition oficial_galerias */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_galerias
{
	// Despliega las imagenes en matriz
	function imagenesMatriz($ordenSQL)
	{
		global $__BD;
	
		$numImagenes = $__BD->db_num_rows($ordenSQL);
	
		if ($numImagenes == 0)
			return '<p><div class="msgInfo">'._NO_IMAGENES.'</div><p>';
	
		$codigo = '';
			
		$idCol = 0;
		$imgXfila = $_SESSION["opciones"]["imagenesxfila"];
		$anchoCol = 100/$imgXfila;
		
		$result = $__BD->db_query($ordenSQL);

		while($row = $__BD->db_fetch_array($result)) {
			
			$baseImg = $row["fichero"];
			$fichImgN = _DOCUMENT_ROOT.'images/normales/'.$baseImg;
			if (!file_exists($fichImgN))
				continue;

			$codigo .= "\n".'<div class="cajaImagenMatriz" style="width:'.$anchoCol.'%">';
			$codigo .= '<div class="innerCajaImagenMatriz">';
			$codigo .= $this->cajaImgMatriz($row);
			$codigo .= '</div>';
			$codigo .= '</div>';
			
			$idCol++;
		}
		
		return $codigo;
	}

	function cajaImgMatriz($row)
	{	
		global $__LIB;

		$codigo = '';
		
		$codImg = $this->codigoThumb($row);
		$codigo .= '<a href="'._WEB_ROOT.'general/imagen.php?ref='.$row["codimagen"].'">'.$codImg.'</a>';
		return $codigo;
	}

	function codigoThumb($row)
	{
		global $__BD;
	
		$tipoImagen = $row[0];
		
		$codigo = "";
		$codImagen = $row["codimagen"];
		$baseImg = $row["fichero"];
		
		$fichImg = _DOCUMENT_ROOT.'images/thumbnails/'.$baseImg;
		$fichImgN = _DOCUMENT_ROOT.'images/normales/'.$baseImg;
		
		if (!file_exists($fichImgN))
			return '';
		// Si existe la grande pero no la pequena, se crea la pequena
		if (!file_exists($fichImg)) {
			$this->crearThumb($baseImg);
		}
		
		// Si se subio una imagen nueva sobre la que ya existia, se recrea la miniatura
		$fechaImagen = $row["fechamodificacion"];
		$fechaMod = filemtime ($fichImgN);
		
		if ($fechaImagen != $fechaMod) {
			$this->crearThumb($baseImg);
			$ordenSQL = "update imagenes set fechamodificacion = '$fechaMod' where codimagen = '$codImagen'";
			$__BD->db_query($ordenSQL);
		}
		
		if (file_exists($fichImg)) {
			$codigo = '<img class="thumb" src="'._WEB_ROOT.'images/thumbnails/'.$baseImg.'">';
			if (file_exists($fichImgN)) {
				$tamFoto = GetImageSize($fichImgN);
				if ($tamFoto[0] > $tamFoto[1])
					$tamFoto[1] = $tamFoto[0];
				else
					$tamFoto[0] = $tamFoto[1];
				
 				$codigo = '<a alt="'._AMPLIAR.'" href="'._WEB_ROOT.'general/imagengaleria.php?cod='.$codImagen.'">'.$codigo.'</a>';
			}
		}
		
		return $codigo;
	} 


	// Crea la imagen pequena a partir de una grande nueva
	function crearThumb($fichImagen, $pixImages = 0)
	{
		// Solo si el servidor tiene las funciones php necesarias
		if (!function_exists("imagecreatetruecolor"))
			return;
	
		$imgNor = _DOCUMENT_ROOT.'images/normales/'.$fichImagen;
		$imgPeq = _DOCUMENT_ROOT.'images/thumbnails/'.$fichImagen;
		list($width, $height) = getimagesize($imgNor);
		$path_parts = pathinfo($imgNor);
		$extension = $path_parts["extension"];
		
		if ($pixImages == 0)
			$pixImages = $_SESSION["opciones"]["piximages"];
		
		// Vertical
		if ($width < $height) {
			$new_height = $pixImages;
			$new_width = round($new_height * $width / $height);
		}
		// Horizontal o cuadrada
		else {
			$new_width = $pixImages;
			$new_height = round($new_width * $height / $width );
		}
		
		$image_p = imagecreatetruecolor($new_width, $new_height);
		
		
		switch ($extension) {
			case "PNG":
			case "png":
				$image = imagecreatefrompng($imgNor);
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				$res = imagepng($image_p, $imgPeq);
				break;
			case "JPG":
			case "jpg":
			case "jpeg":
				$image = imagecreatefromjpeg($imgNor);
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagejpeg($image_p, $imgPeq, 75);
				break;
			case "GIF":
			case "gif":
				$image = imagecreatefromgif($imgNor);
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagegif($image_p, $imgPeq);
				break;
			
			default:
				return;
		}
	}


	function contenidos()
	{
		global $__BD, $__CAT, $__LIB;
		global $CLEAN_GET;
	
		$codGaleria = '';
		if (isset($CLEAN_GET["gal"]))
			$codGaleria = $CLEAN_GET["gal"];
		
		$ordenSQL = "select titulo from galeriasimagenes where codgaleria = '$codGaleria'";
		$titulo = $__BD->db_valor($ordenSQL);
		$titPagina = $__LIB->traducir("galeriasimagenes", "titulo", $codGaleria, $titulo);
		echo '<h1>'._GALERIA.' &middot; '.$titPagina.'</h1>';
		
		// Sentencia principal
		$ordenSQL = "select * from imagenes where codgaleria = '$codGaleria' AND publico = true order by orden";
		echo $this->imagenesMatriz($ordenSQL);
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_galerias */
class galerias extends oficial_galerias {};

$iface_galerias = new galerias;
$iface_galerias->contenidos();
		
?>

<?php include("../includes/right_bottom.php") ?>