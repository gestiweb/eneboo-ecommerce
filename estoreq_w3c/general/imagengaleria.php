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


/** @class_definition oficial_imagenGalerias */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_imagenGalerias
{
	function contenidos()
	{
		global $__BD, $__CAT, $__LIB;
		global $CLEAN_GET;
	
		$codImagen = '';
		if (isset($CLEAN_GET["cod"]))
			$codImagen = $CLEAN_GET["cod"];
		

		$ordenSQL = "select * from imagenes where codimagen = '$codImagen' and publico = true";
		$result = $__BD->db_query($ordenSQL);
		$row = $__BD->db_fetch_array($result);

		$baseImg = $row["fichero"];
		$fichImg = _DOCUMENT_ROOT.'images/normales/'.$baseImg;

		if (!file_exists($fichImg))
			 die ("No existe esta imagen");



		
		// NavegaciÛn
		$codGaleria = $row["codgaleria"];

		$ordenSQL = "select titulo from galeriasimagenes where codgaleria = '$codGaleria'";
		$titGaleria = $__BD->db_valor($ordenSQL);
		$titGaleria = $__LIB->traducir("galeriasimagenes", "titulo", $codGaleria, $titGaleria);

		$titImagen = $__LIB->traducir("imagenes", "titulo", $codImagen, $row["titulo"]);

		$ordenSQL = "select codimagen from imagenes where codgaleria = '".$codGaleria."' and publico = true and orden > ".$row["orden"]." order by orden";
		$imgSig = $__BD->db_valor($ordenSQL);

		$ordenSQL = "select codimagen from imagenes where codgaleria = '".$codGaleria."' and publico = true and orden < ".$row["orden"]." order by orden desc";
		$imgAnt = $__BD->db_valor($ordenSQL);


		echo '<h1>'._GALERIA.' &middot; '.$titGaleria.'</h1>';
		
		echo '<div class="fotoGaleria">';


		echo '<div class="navGaleria">';
		if ($imgAnt)
			echo '<a alt="'._AMPLIAR.'" href="'._WEB_ROOT.'general/imagengaleria.php?cod='.$imgAnt.'">&lt;&lt;</a>';
		else
			echo '&lt;&lt;';
		echo '&nbsp;&nbsp;&nbsp;';
		if ($imgSig)
			echo '<a alt="'._AMPLIAR.'" href="'._WEB_ROOT.'general/imagengaleria.php?cod='.$imgSig.'">&gt;&gt;</a>';
		else
			echo '&gt;&gt;';

		echo '&nbsp;&nbsp;&nbsp;';

		echo '<a alt="'._GALERIA.'" href="'._WEB_ROOT.'general/galeria.php?gal='.$codGaleria.'">'.$titGaleria.'</a>';
		echo '&nbsp;&nbsp;&middot&nbsp;&nbsp;';
		echo $titImagen;

		echo '</div>';

		echo '<img src="'._WEB_ROOT.'images/normales/'.$baseImg.'">';

		echo '<div class="pieImagen">';
		if ($row["descripcion"]) {
			$titPagina = $__LIB->traducir("imagenes", "descripcion", $codImagen, $row["descripcion"]);
			echo $row["descripcion"];
		}
		echo '</div>';	

		echo '</div>';
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition imagenGalerias */
class imagenGalerias extends oficial_imagenGalerias {};

$iface_imagenGalerias = new imagenGalerias;
$iface_imagenGalerias->contenidos();
		
?>

<?php include("../includes/right_bottom.php") ?>