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

/** @class_definition oficial_noticias */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_noticias
{	
	// Muestra una noticia
	function mostrarNoticia($row, $titulo)
	{
		global $__LIB;
		
		$codigo = '';
	
		$texto = $__LIB->traducir("noticias", "texto", $row["id"], $row["texto"]);
		
		$codigo .= '<h2>'.$titulo.'</h2>';
		
		$codigo .= '<div class="noticia">';
		
		$imgNor = _DOCUMENT_ROOT.'images/noticias/'.$row["id"].'.jpg';
		$rutaImgNor = _WEB_ROOT.'images/noticias/'.$row["id"].'.jpg';
		if (file_exists($imgNor)) {
			$codigo .= '<img src="'.$rutaImgNor.'">';
		}
		
		$codigo .= '<a name="'.$row["id"].'"></a>';
		$codigo .= nl2br($texto);
		$codigo .= '<p class="separador">';
		
		if ($row["autor"])
			$codigo .= _AUTOR.': <b>'.$row["autor"].'</b>';
		if ($row["fecha"])
			$codigo .= '&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$row["fecha"].'</b>';
		

		$codigo .= '</div>';
		
		return $codigo;
	}
	
	// Procesa la imagen de la noticia ajustandola al size adecuado
	function procesarImagen($id)
	{
		if (!function_exists("imagecreatetruecolor"))
			return;
		
		$imgNor = _DOCUMENT_ROOT.'images/noticias/'.$id.'.jpg';
		if (!file_exists($imgNor))
			return;
		
		list($width, $height) = getimagesize($imgNor);
		$path_parts = pathinfo($imgNor);
		$extension = $path_parts["extension"];
		$pixImages = 250;
		
		// Vertical
		if ($width < $height) {
			if ($height <= $pixImages)
				return;
			$new_height = $pixImages;
			$new_width = round($new_height * $width / $height);
		}
		// Horizontal o cuadrada
		else {
			if ($width <= $pixImages)
				return;
			$new_width = $pixImages;
			$new_height = round($new_width * $height / $width );
		}
		
		$image_p = imagecreatetruecolor($new_width, $new_height);
		
		switch ($extension) {
			case "png":
				$image = imagecreatefrompng($imgNor);
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagepng($image_p, $imgNor);
				break;
			case "jpg":
			case "jpeg":
				$image = imagecreatefromjpeg($imgNor);
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagejpeg($image_p, $imgNor, 70);
				break;
			case "gif":
				$image = imagecreatefromgif($imgNor);
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagegif($image_p, $imgNor);
				break;
			
			default:
				return;
		}
		
	}
	
	function contenidos() 
	{
		global $__BD, $__LIB, $CLEAN_GET;
		
		echo '<h1>'._NOTICIAS.'</h1>';
		echo '<div class="cajaTexto">';
		
		$idNoticia = '';
		if (isset($CLEAN_GET["id"]))
			$idNoticia = $CLEAN_GET["id"];
		
		$hoy = date("Y-m-d", time());
		
		if ($idNoticia)
			$ordenSQL = "select id, titulo, texto, fecha, autor from noticias where publico = true and id = $idNoticia";
		else
			$ordenSQL = "select id, titulo, texto, fecha, autor from noticias where publico = true and fechalimite > '$hoy' order by fecha desc";
		
		$result = $__BD->db_query($ordenSQL);
		
		$menu = '';
		$noticias = '';
		
		while ($row = $__BD->db_fetch_array($result)) {	
			$id = $row[0];
			$this->procesarImagen($id);
			$titulo = $__LIB->traducir("noticias", "titulo", $id, $row["titulo"]);
			$menu .= '<a href="noticias.php#'.$id.'">'.$titulo.'</a><br/>';
			$noticias .= $this->mostrarNoticia($row, $titulo);
		}
		
		if (!$idNoticia)
			echo $menu;
		
		echo $noticias;
	
		if ($idNoticia)
			echo '<p class="separador"><a class="botLista" href="noticias.php">'._MAS_NOTICIAS.'</a>';
		
		echo '</div>';
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_noticias */
class noticias extends oficial_noticias {};

$iface_noticias = new noticias;
$iface_noticias->contenidos();

?>


<?php include("../includes/right_bottom.php") ?>