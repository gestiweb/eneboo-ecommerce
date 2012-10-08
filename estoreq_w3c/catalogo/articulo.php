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
		
		$codigo = '';
		
		$referencia = '';
		if (isset($CLEAN_GET["ref"]))
			$referencia = $CLEAN_GET["ref"];
		else if(isset($CLEAN_GET["refdl"])) {
			$ordenSQL = "select referencia from articulos where descripciondeeplink = '".$CLEAN_GET["refdl"]."'";
			$referencia = $__BD->db_valor($ordenSQL);
		}
		
		$ordenSQL = "select * from articulos where referencia = '$referencia' and publico = true";
		
		$result = $__BD->db_query($ordenSQL);
		
		$row = $__BD->db_fetch_array($result);
		if (!$row) {
			$codigo .= '<h1>'._ERROR.'</h1>';
			$codigo .= '<div class="articulo">';
			$codigo .= _ARTICULO_NO_DISPONIBLE;
			$codigo .= '</div>';
			echo $codigo;
			include("../includes/right_bottom.php");
			exit;
		}
		
		
		$precio = $__CAT->precioArticulo($row, true);
		
		$descripcion = $__LIB->traducir("articulos", "descripcion", $row["referencia"], $row["descripcion"]);
		
		$codigo .= '<h1>'.$descripcion.'</h1>';
		
		$codigo .= '<div class="articulo">';
		
		$codigo .= '<div class="thumb">';
		$codigo .= $__CAT->codigoThumb($referencia, $descripcion, true, 'normal');
		$codigo .= '</div>';
		
		$codigoFS = $this->fotosSecundarias($referencia, $descripcion);
		if ($codigoFS) {
			$codigo .= '<div class="fotosSec">';
			$codigo .= $codigoFS;
			$codigo .= '</div>';
		} 
		

		$codigo .= '<div class="botones">';
		
		$codigo .= '<div id="_precio">'.$precio.'</div>';

		$datosStock = $__CAT->datosStock($row);
		if ($datosStock["venta"])
			$codigo .= $__LIB->crearBotonVenta($referencia);
		
		$codigo .= '<div class="botonesSec">';
		
		$codigo .= $__LIB->crearBotonFavoritos($referencia);
		
		$linkAmigo = $__LIB->crearBotonAmigo($referencia);
		if ($linkAmigo) {
			$codigo .= $linkAmigo;
		}
		
		$linkComentario = $__LIB->crearBotonComentario($referencia);
		if ($linkComentario) {
			$codigo .= $linkComentario;
		}
		
		if ($__LIB->esTrue($_SESSION["opciones"]["compartirarticulos"]))
			$codigo .= '<a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=250&amp;username=xa-4c3ae287271bedf0"><img src="http://s7.addthis.com/static/btn/v2/lg-share-es.gif" width="125" height="16" alt="Bookmark and Share" style="border:0"/></a><script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4c3ae287271bedf0"></script><script type="text/javascript">var addthis_config = {  ui_click: true }</script>';
		
		$codigo .= '</div>';
		$codigo .= '</div>';
	
	
		if ($datosStock["stock"])
			$codigo .= '<p>'.$datosStock["stock"].'</p>';
		
		$descPublica = $__LIB->traducir("articulos", "descpublica", $row["referencia"], $row["descpublica"]);
		$codigo .= '<p>'.nl2br($descPublica).'</p>';
		
		$codigo .= $__CAT->atributos($referencia);
		
		if ($row["codfabricante"]) {
			$ordenSQL = "select nombre from fabricantes where codfabricante = '".$row["codfabricante"]."'";
			$fabricante = $__BD->db_valor($ordenSQL);
			$codigo .= '<p>'._FABRICANTE.': <a href="'._WEB_ROOT.'catalogo/articulos.php?fab='.$row["codfabricante"].'">'.$fabricante.'</a></p>';
		}
		
		
		
		$codigo .= '<div id="codigoEnviarAamigo"></div>';
		
		$codigo .= $__CAT->accesoriosLista($referencia);
	
		$codigo .= '</div>';
		
		echo $codigo;
		
		$__LIB->controlVisitas('articulos', $referencia);
		
		echo $this->recomendar($referencia, $descripcion);
		echo $this->comentar($referencia, $descripcion);
	}


	function recomendar($referencia, $descripcion)
	{
		global $__CAT;
		
		$codigo = '';
		
		$codigo .= '<div style="display:none">';
		$codigo .= '<div id="recomendarArticulo" class="formUp">';
			
		$codigo .= '<h1>'._ENVIAR_AMIGO.' &middot; '.$descripcion.'</h1>';
		$codigo .= '<br/>';
		
		$codigo .= '<form id="enviarAamigo" action="articulo.php"><div>';
		
		$codigo .= '<div class="campoImg">';
		$codigo .= $__CAT->codigoThumb($referencia, $descripcion, false, 'thumb');
		$codigo .= '</div>';
		
		$codigo .= '<div class="campo">';
		$codigo .= '<label for="nombre">'._TUNOMBRE.' *</label>';
		$codigo .= '<input type="text" id="nombre" name="nombre"/>';
		$codigo .= '</div>';
		
		$codigo .= '<div class="campo">';
		$codigo .= '<label for="email">'._EMAIL.' *</label>';
		$codigo .= '<input type="text" id="email" name="email"/>';
		$codigo .= '</div>';
				
		$codigo .= '<div class="campoTAcaja">';
		$codigo .= '<div class="campo">';
		$codigo .= '<label for="texto">'._COMENTARIOS.'</label>';
		$codigo .= '<textarea id="texto" name="texto" rows="20" cols="30"></textarea>';
		$codigo .= '</div>';
		$codigo .= '</div>';
				
		$codigo .= '<input type="hidden" name="ref" value="'.$referencia.'"/>';
		$codigo .= '</div></form>';
		
		$codigo .= '<div id="avisoEnviarAamigo"></div>';
		
		$codigo .= '<div class="botones">';
		$codigo .= '<a class="button" href="#1" onclick="xajax_enviarCorreoAamigo(xajax.getFormValues(\'enviarAamigo\'));return false;"><span>'._ENVIAR.'</span></a>';
		$codigo .= '<a class="button" href="#" onclick="$.fancybox.close();return false;"><span>'._CANCELAR.'</span></a>';
		$codigo .= '</div>';
		
		$codigo .= '</div>';
		$codigo .= '</div>';
		return $codigo;
	}
	
	
	function comentar($referencia, $descripcion)
	{
		global $__CAT;

		$codigo = '';
		
		$codigo .= '<div style="display:none">';
		$codigo .= '<div id="comentarArticulo" class="formUp">';
		
		$codigo .= '<h1>'._ENVIAR_COMENTARIO.'</h1>';
		$codigo .= '<br/>';
		
		$codigo .= '<form id="enviarComentario" action="articulo.php"><div>';
		
		$codigo .= '<div class="campoImg">';
		$codigo .= $__CAT->codigoThumb($referencia, $descripcion, false, 'thumb');
		$codigo .= '</div>';
		
		$codigo .= '<div class="campo">';
		$codigo .= '<label for="nombre">'._TUNOMBRE.' </label>';
		$codigo .= '<input type="text" id="nombreC" name="nombre"/>';
		$codigo .= '</div>';
		
		$codigo .= '<div class="campo">';
		$codigo .= '<label for="email">'._EMAIL.' </label>';
		$codigo .= '<input type="text" id="emailC" name="email"/>';
		$codigo .= '</div>';
				
		$codigo .= '<div class="campoTAcaja">';
		$codigo .= '<div class="campo">';
		$codigo .= '<label for="texto">'._COMENTARIOS.' *</label>';
		$codigo .= '<textarea id="textoC" name="texto" rows="20" cols="30"></textarea>';
		$codigo .= '</div>';
		$codigo .= '</div>';
		
		$codigo .= '<input type="hidden" name="ref" value="'.$referencia.'"/>';
		
		$codigo .= '</div></form>';
		
		$codigo .= '<div id="avisoComentario"></div>';
		
		$codigo .= '<div class="botones">';
		$codigo .= '<a class="button" href="#" onclick="xajax_enviarCorreoComentario(xajax.getFormValues(\'enviarComentario\'));return false;"><span>'._ENVIAR.'</span></a>';
		$codigo .= '<a class="button" href="#" onclick="$.fancybox.close();return false;"><span>'._CANCELAR.'</span></a>';
		$codigo .= '</div>';

		$codigo .= '</div>';
		$codigo .= '</div>';
		
		return $codigo;
	}
	
	
	function fotosSecundarias($referencia, $descripcion)
	{
		global $__BD, $__CAT;
		
		$codigo = '';

		// Directorios para multiples imagenes
		$dirImg = _DOCUMENT_ROOT.'catalogo/img_thumb/'.$referencia;
		$dirImgN = _DOCUMENT_ROOT.'catalogo/img_normal/'.$referencia;
		if (!file_exists($dirImg))
			mkdir($dirImg, 0755);
		if (!file_exists($dirImgN))
			mkdir($dirImgN, 0755);

		$paso = 0;

		$ordenSQL = "select * from articulosfotos where referencia = '$referencia' order by orden";
		$result = $__BD->db_query($ordenSQL);
		while ($row = $__BD->db_fetch_array($result)) {
		
			if (!$paso++)
				continue;
		
			$nomFich = $row["nomfichero"];
			if (!file_exists($dirImgN.'/'.$nomFich))	
				continue;
			
			$codigo .= '<div>';
			$fichImgN = _WEB_ROOT.'catalogo/img_normal/'.$referencia.'/'.$nomFich;
			$codigoTh = '<img class="thumb" alt="'.$descripcion.'" src="'._WEB_ROOT.'catalogo/img_superthumb/'.$referencia.'/'.$nomFich.'"/>';
			$codigo .= '<a class="ampliar" rel="galeria" title="'.$descripcion.'" href="'.$fichImgN.'">'.$codigoTh.'</a>';
			$codigo .= '</div>';
		}

		return $codigo;
	}


}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////



/** @main_class_definition oficial_articulo */
class articulo extends oficial_articulo {};

$iface_articulo = new articulo;
$iface_articulo->contenidos();

?>

<?php include("../includes/right_bottom.php") ?>