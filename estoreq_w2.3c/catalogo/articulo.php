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

//error_reporting(E_ALL);

/** @class_definition oficial_articulo */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_articulo
{
	/** Obtiene y muestra la informacion de un articulos */
	function contenidos()
	{
		global $__BD, $__CAT, $__LIB;
		global $CLEAN_GET;
		
		$referencia = '';
		if (isset($CLEAN_GET["ref"]))
			$referencia = $CLEAN_GET["ref"];
		
		$ordenSQL = "select * from articulos where referencia = '$referencia' and publico = true";
		
		$result = $__BD->db_query($ordenSQL);
		
		$row = $__BD->db_fetch_array($result);
		if (!$row) {
			echo '<div class="titPagina">'._ERROR.'</div>';
			echo '<div class="articulo">';
			echo _ARTICULO_NO_DISPONIBLE;
			echo '</div>';
			include("../includes/right_bottom.php");
		}
		
		
		$precio = $__CAT->precioArticulo($row, true);
		
		$descripcion = $__LIB->traducir("articulos", "descripcion", $row["referencia"], $row["descripcion"]);
		
		echo '<div class="titPagina">'.$descripcion.'</div>';
		
		echo '<div class="articulo">';
		
		echo '<div class="thumb">';
		echo $__CAT->codigoThumb($referencia, true);
		echo '</div>';
		
		echo '<p><div id="_precio"><b>'.$precio.'</b></div>';


		echo '<div class="venta">';
		$datosStock = $__CAT->datosStock($row);
		if ($datosStock["venta"])
			echo $__LIB->crearBotonVenta($referencia);
		echo '&nbsp;&nbsp;'.$__LIB->crearBotonFavoritos($referencia);
		echo '</div>';
	
	
		if ($datosStock["stock"])
			echo '<p>'.$datosStock["stock"].'</p>';
		
		$descPublica = $__LIB->traducir("articulos", "descpublica", $row["referencia"], $row["descpublica"]);
		echo '<p>'.nl2br($descPublica);
		
		echo $__CAT->atributos($referencia);
		
		if ($row["codfabricante"]) {
			$ordenSQL = "select nombre from fabricantes where codfabricante = '".$row["codfabricante"]."'";
			$fabricante = $__BD->db_valor($ordenSQL);
			echo '<p>'._FABRICANTE.': <a href="'._WEB_ROOT.'catalogo/articulos.php?fab='.$row["codfabricante"].'">'.$fabricante.'</a>';
		}
		
		
		echo '<div id="codigoEnviarAamigo"></div>';
		
		echo $__CAT->accesoriosLista($referencia);
	
		echo '</div>';
		
		$__LIB->controlVisitas('articulos', $referencia);
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////



/** @class_definition pcrednet_articulo */
//////////////////////////////////////////////////////////////////
//// PC REDNET /////////////////////////////////////////////////////

class pcrednet_articulo extends oficial_articulo
{
	/** Obtiene y muestra la informacion de un articulos */
	function contenidos() {
		
		global $__BD;
		global $CLEAN_GET;
		
		$referencia = '';
		if (isset($CLEAN_GET["ref"]))
			$referencia = $CLEAN_GET["ref"];
		
		$ordenSQL = "select * from articulos where referencia = '$referencia' and publico = true and obsoleto = false";
		$result = $__BD->db_query($ordenSQL);
		$row = $__BD->db_fetch_array($result);
		if (!$row) {
			echo '<div class="titPagina">'._ERROR.'</div>';
			echo '<div class="articulo">';
			echo _ARTICULO_NO_DISPONIBLE;
			echo '</div>';
			return;
		}

		return parent::contenidos();
	}
}

//// PC REDNET /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_articulo */
class articulo extends pcrednet_articulo{};

$iface_articulo = new articulo;
$iface_articulo->contenidos();

?>

<?php include("../includes/right_bottom.php") ?>