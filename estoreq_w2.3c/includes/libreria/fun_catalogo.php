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

/** @class_definition oficial_funCatalogo */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

// Funciones del catalogo
class oficial_funCatalogo
{

	// Funcion principal de entrada. Llama a otras en base al tipo de vista
	function articulos($ordenSQL)
	{
		if ($_SESSION["vista"] == 1)
			return $this->articulosMatriz($ordenSQL);
		else
			return $this->articulosLista($ordenSQL);
	}
	
	// Despliega los articulos en lista
	function articulosLista($ordenSQL)
	{
		global $__BD;
	
		if ($__BD->db_num_rows($ordenSQL) == 0)
			return '<p><div class="msgInfo">'._NO_ARTICULOS.'</div><p>';
	
		$codigo = '';
		
		$result = $__BD->db_query($ordenSQL);
		while($row = $__BD->db_fetch_array($result)) {
			$codigo .= $this->cajaArtLista($row);
		}
		
		return $codigo;
	}
	
	function articulosMatriz($ordenSQL)
	{
		global $__BD;
	
		$numArticulos = $__BD->db_num_rows($ordenSQL);
	
		if ($numArticulos == 0)
			return '<div class="msgInfo">'._NO_ARTICULOS.'</div>';
	
		$codigo = '';
			
		$idCol = 0;
		$artXfila = $_SESSION["opciones"]["articulosxfila"];
		$anchoCol = 100/$artXfila;
		
		$result = $__BD->db_query($ordenSQL);

		while($row = $__BD->db_fetch_array($result)) {
			
			$codigo .= "\n".'<div class="cajaArtMatriz" style="width:'.$anchoCol.'%">';
			$codigo .= '<div class="innerCajaArtMatriz">';
			$codigo .= $this->cajaArtMatriz($row);
			$codigo .= '</div>';
			$codigo .= '</div>';
			
			$idCol++;
		}
		
		return $codigo;
	}
	
	// Devuelve la clausula sql para la seleccion por pagina
	function wherePagina($ordenSQL)
	{
		$registrosXpag = $_SESSION["numresults"];
		
		$pagina = 0;
		if (isset($_GET["pag"]))
			$pagina = htmlentities($_GET["pag"]);	
		
		if (!$pagina)
			$pagina = 1;
		$primerRegistro = ($pagina - 1) * $registrosXpag;
		$limit = ' limit '.$registrosXpag.' offset '.$primerRegistro;
		
		return $limit;
	}
		
	// Links de navegacion por las paginas
	function navPaginas($ordenSQL)
	{
		global $__BD;
	
		$path_parts = pathinfo($_SERVER["SCRIPT_FILENAME"]);
		$nomPHP = $path_parts["basename"];
		
		$registrosXpag = $_SESSION["numresults"];
		
		$pagina = 0;
		$linkAnt = '';
		$linkSig = '';
		
		if ( isset($_GET["pag"]) && intval($_GET["pag"]) )
			$pagina = $_GET["pag"];
		
		if (!$pagina)
			$pagina = 1;
		
		$numRegistros = $__BD->db_num_rows($ordenSQL);
		$numPaginas = ceil($numRegistros / $registrosXpag);
		
		if ($pagina > 1) {
			$pagAnt = $pagina - 1;
			$linkAnt = $nomPHP.'?pag='.$pagAnt;
			$linkAnt = '<a href="'.$linkAnt.'">'._ANTERIOR.'</a>';
		}
		if ($pagina < $numPaginas) {
			$pagSig = $pagina + 1;
			$linkSig = $nomPHP.'?pag='.$pagSig;
			$linkSig = '<a href="'.$linkSig.'">'._SIGUIENTE.'</a>';
		}
		
		
		// Resumen de resultados
		$artInicial = ($pagina - 1) * $registrosXpag + 1;
		$artFinal = $pagina * $registrosXpag;
		if ($pagina == $numPaginas)	$artFinal = $numRegistros;
				
		if ($numRegistros > 0) 
  			$resultadosPagina = _ARTICULOS.' '.$artInicial.'-'.$artFinal.' '._DE.' '.$numRegistros;
  		else
			$resultadosPagina = _ARTICULOS;
		
		// Lista de paginas
		$indGrupoPag = ceil($pagina/_NUM_PAG_GRUPO);
		$numGrupos = ceil($numPaginas/_NUM_PAG_GRUPO);
		$inicioLista = ($indGrupoPag - 1) * _NUM_PAG_GRUPO + 1;
		$finLista = $indGrupoPag * _NUM_PAG_GRUPO;
		if ($finLista > $numPaginas)
			$finLista = $numPaginas;
		
		$listaPaginas = "";
		
		if ($indGrupoPag > 1) { // Link de grupo anteriores <<
			$pagGrupoAnterior = ($indGrupoPag - 1) * _NUM_PAG_GRUPO;
			$linkPag = $nomPHP.'?pag='.$pagGrupoAnterior;
			$listaPaginas .= '<a href="'.$linkPag.'">&lt;&lt;</a> ';
		}
			
		for ($i = $inicioLista; $i <= $finLista; $i++) {
		
			if ($i == $pagina)
				$listaPaginas .= '<b>'.$i.'</b> ';
			else {
				$linkPag = $nomPHP.'?pag='.$i;
				$listaPaginas .= '<a href="'.$linkPag.'">'.$i.'</a> ';
			}
		}
		
		if ($indGrupoPag < $numGrupos) { // Link de grupo siguientes >>
			$pagGrupoSiguiente = $indGrupoPag * _NUM_PAG_GRUPO + 1;
			$linkPag = $nomPHP.'?pag='.$pagGrupoSiguiente;
			$listaPaginas .= '<a href="'.$linkPag.'">&gt;&gt;</a> ';
		}
			
		
		$codigo = '';

		$codigo .= '<div class="navBar">';
		
		$codigo .= '<div class="navBarResultPag">'.$resultadosPagina.'</div>';
		
		$codigo .= '<div class="navBarListaPag">';
		if ($numPaginas > 1)
			$codigo .= $linkAnt.' '.$listaPaginas.' '.$linkSig;
		$codigo .= '&nbsp;</div>';
		
		$codigo .= '<div class="navBarOrden">';
		
		$codigo .= _ORDENAR_POR.'&nbsp; ';
	
		$orden = '';
		if (isset($_SESSION["orden"]))
			$orden = $_SESSION["orden"];
	
		if ($orden != 'pvp')
			$codigo .= '<a href="'.$nomPHP.'?ord=pvp">'.strtolower(_PRECIO).'</a>';
		else
			$codigo .= '<b>'.strtolower(_PRECIO).'</b>';
			
		// El orden por descripcion solo para el idioma por defecto
		if ($_SESSION["opciones"]["codidiomadefecto"] == $_SESSION["idioma"]) { 
			$codigo .= '&nbsp;<b>&middot;</b>&nbsp;';
			if ($orden != 'descripcion')
				$codigo .= '<a href="'.$nomPHP.'?ord=descripcion">'.strtolower(_ARTICULO).'</a>';
			else
				$codigo .= '<b>'.strtolower(_ARTICULO).'</b>';
		}
				
		$codigo .= '</div>';
		
		$codigo .= $this->masOpcionesNav();

		$codigo .= '</div>';
		
		return $codigo;
	}
	
	// Para agregar en extensiones
	function masOpcionesNav()
	{
		return '';
	}
	
	// Links de opciones
	function navOpciones($totalArticulos)
	{
		$path_parts = pathinfo($_SERVER["SCRIPT_FILENAME"]);
		$nomPHP = $path_parts["basename"];
		
		$codigo = '';
		$codigo .= '<div class="navBar">';
		$codigo .= '<div class="navBarResultXPag">';
		
		$resultadosPP = _RESULTADOS_X_PAGINA.'&nbsp; ';
		
		// Matriz
		if ($_SESSION["vista"] == 1)
			$numResults = array(2, 3, 6, 10, 20, 50);	
		// Lista
		else
			$numResults = array(10, 20, 30, 50, 100);	
		
		$mostrarPP = false;
		while (list ($clave, $val) = each ($numResults)) {
			
			if ($_SESSION["vista"] == 1)
				$val = $val * $_SESSION["opciones"]["articulosxfila"];

			if ($clave > 0) {
				$mostrarPP = true;
				$resultadosPP .= ' <b>&middot;</b> ';
			}
			
			if ($val != $_SESSION["numresults"])
				$resultadosPP .= '<a href="'.$nomPHP.'?numr='.$val.'">'.$val.'</a>';
			else
				$resultadosPP .= '<b>'.$val.'</b>';

			if ($val >= $totalArticulos)
				break;
		}

		// Si no hay solo una pagina
		if ($mostrarPP)
			$codigo .= $resultadosPP;
			
		$codigo .= '&nbsp;</div>';
		$codigo .= '<div class="navBarDisposicion">'._DISPOSICION_EN.' ';
		
		if ($_SESSION["vista"] != 0)
			$codigo .= '<a href="'.$nomPHP.'?vista=lista">'._LISTA.'</a>';
		else
			$codigo .= '<b>'._LISTA.'</b>';
			
		$codigo .= '&nbsp;<b>&middot;</b>&nbsp;';
		
		if ($_SESSION["vista"] != 1)
			$codigo .= '<a href="'.$nomPHP.'?vista=matriz">'._MATRIZ.'</a>';
		else
			$codigo .= '<b>'._MATRIZ.'</b>';
		
		$codigo .= '</div>';
		$codigo .= '</div>';
		
		return $codigo;
	}
	
	// Despliega los accesorios en lista
	function accesoriosLista($referencia)
	{
		global $__BD;
	
		$codigo = '';
	
		$ordenSQL = "SELECT acc.referenciaacc as ref
						FROM articulos art INNER JOIN accesoriosart acc 
						ON art.referencia = acc.referenciappal
						WHERE art.referencia = '$referencia' AND acc.publico = true
						ORDER BY acc.orden";
		
		$resultAcc = $__BD->db_query($ordenSQL);
		while($rowAcc = $__BD->db_fetch_array($resultAcc)) {		
			$ordenSQL = "select referencia, descripcion, pvp, descpublica, fechapub, codimpuesto, codfabricante, controlstock, enoferta, codplazoenvio from articulos where referencia = '".$rowAcc["ref"]."'";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_array($result);
			$codigo .= $this->cajaArtLista($row);
		}
		
		if ($codigo) {
			$encabezado = '<div class="titApartado"><span class="titApartadoText">'._ACCESORIOS.'</span></div>';
			$codigo = $encabezado.$codigo;
			return $codigo;
		}
		
		return '';
	}
	
	// HTML de una celda de lista
	function cajaArtLista($row)
	{
		global $__LIB;
		
		$precio = $this->precioArticulo($row, true);
		$codImg = $this->codigoThumb($row["referencia"]);
		
		$altoCaja = $_SESSION["opciones"]["piximages"] + 10;
		
		$descripcion = $__LIB->traducir("articulos", "descripcion", $row["referencia"], $row["descripcion"]);
		
		$codigo = "\n".'<div class="cellLista">';

		$codigo .= '<div class="cellListaImagen"><a href="'._WEB_ROOT.'catalogo/articulo.php?ref='.$row["referencia"].'">'.$codImg.'</a></div>';
		$codigo .= '<div class="cellListaDescripcion"><a href="'._WEB_ROOT.'catalogo/articulo.php?ref='.$row["referencia"].'">'.$descripcion.'</a></div>';
		$codigo .= '<div class="cellListaPrecio">'.$precio.'</div>';
		
		// Datos de stock
		$datosStock = $this->datosStock($row);
 		$codigo .= '<div class="cellListaStock">'.$datosStock["stock"].'&nbsp;</div>';
		
		// Boton de venta
 		$codigo .= '<div class="cellListaVenta">';
 		if ($datosStock["venta"])
 			$codigo .= $__LIB->crearBotonVenta($row["referencia"]);
 		$codigo .= '&nbsp;</div>';
		
 		$codigo .= '<br class="cleanerLeft"/>';
		$codigo .= '</div>';
		
		return $codigo;
	}
	
	// HTML de una celda de matriz
	function cajaArtMatriz($row)
	{	
		global $__LIB;
		
		$precio = $this->precioArticulo($row, true);
		$codImg = $this->codigoThumb($row["referencia"]);
		$altoCeldaImg = $_SESSION["opciones"]["piximages"] + 10;
		
		$descripcion = $__LIB->traducir("articulos", "descripcion", $row["referencia"], $row["descripcion"]);
		
		$codigo = '<div class="cellMatriz">';
		
		// Descripcion
		$codigo .= '<div class="descripcion">';
		$codigo .= '<a href="'._WEB_ROOT.'catalogo/articulo.php?ref='.$row["referencia"].'">'.$descripcion.'</a>';
		$codigo .= '</div>';
		
		// Imagen
		$codigo .= '<div class="imagen" style="height:'.$altoCeldaImg.'px">';
		$codigo .= '<a href="'._WEB_ROOT.'catalogo/articulo.php?ref='.$row["referencia"].'">'.$codImg.'</a>';
		$codigo .= '</div>';
		
		// Precio
		$codigo .= '<div class="precio">';
		$codigo .= $precio;
		$codigo .= '</div>';
		
		// Datos de stock
		$codigo .= '<div class="stock" valign="top">';
		$datosStock = $this->datosStock($row);
		$botonVenta = $datosStock["venta"];
		if ($datosStock["stock"])
			$codigo .= $datosStock["stock"];
		$codigo .= '</div>';
		
		// Boton de venta y favoritos
		$codigo .= '<div class="venta">';
		if ($datosStock["venta"])
			$codigo .= $__LIB->crearBotonVenta($row["referencia"]);
		$codigo .= '</div>';
		
		$codigo .= '</div>';
	
		return $codigo;
	}
	
	// Datos del stock y plazo de envio de un articulo
	function datosStock($row)
	{
		global $__BD, $__LIB;
	
		$datos["stock"] = "";
		$datos["venta"] = true;
		
		// Stocks
		if (!$__LIB->esTrue($row["controlstock"])) {
			if ($row["stockfis"] <= $row["stockmin"]) {
				$datos["stock"] = _AGOTADO;
				$datos["venta"] = false;
			}
			else {
				$datos["stock"] = _EN_STOCK;
			}
		} else
			$datos["stock"] = _EN_STOCK;
		
		if (!$__LIB->esTrue($_SESSION["opciones"]["mostrarstock"]))
			$datos["stock"] = "";
		
		// Plazo de envio
		if ($row["codplazoenvio"]  &&  $__LIB->esTrue($_SESSION["opciones"]["mostrarplazoenvio"])) {
			$codigo = "";
			$ordenSQL = "select plazo, unidades from plazosenvio where codplazo = '".$row["codplazoenvio"]."'";
			$row = $__BD->db_row($ordenSQL);
			if ($row[0]) {
				$unidades = strtoupper('_'.$row[1]);
				if (defined($unidades)) {
					$unidades = constant($unidades);
				}
				$codigo .= _ENVIO.': '.$row[0].' '.$unidades;
			}
				
			$codigo;
			
			if ($datos["stock"])
				$datos["stock"] .= '<br>';
				
			$datos["stock"] .= $codigo;
		}
				
		return $datos;
	}
	
	
	// Numero de articulos por familia
	function numArticulosF($codFamilia) 
	{
		global $__BD;
	
		$where = " where (codfamilia = '$codFamilia'";	 	
		$where = $this->whereFamiliasHijas($codFamilia, $where);
		$where .= ')';
		$ordenSQL = "select count(referencia) from articulos $where and publico = true";
		return $__BD->db_valor($ordenSQL);
	}
	
	// HTML de la miniatura de la imagen
	function codigoThumb($referencia, $link = false)
	{
		global $__BD;
		
		$ordenSQL = "select tipoimagen, fechaimagen from articulos where referencia = '$referencia'";

		$row = $__BD->db_row($ordenSQL);
		$tipoImagen = $row[0];
		
		// Si se sube directamente o por FTP
		if (!$tipoImagen)
			$tipoImagen = 'jpg';
		
		$baseImg = $referencia.'.'.$tipoImagen;

		$fichImg = _DOCUMENT_ROOT.'catalogo/img_thumb/'.$baseImg;
		$fichImgN = _DOCUMENT_ROOT.'catalogo/img_normal/'.$baseImg;
		
		if (!file_exists($fichImgN))
			return '';
		// Si existe la grande pero no la pequena, se crea la pequena
		if (!file_exists($fichImg)) {
			$this->crearThumb($baseImg);
		}
		
		// Si se subio una imagen nueva sobre la que ya existia, se recrea la miniatura
		$fechaImagen = $row[1];
		$fechaMod = filemtime ($fichImgN);
		if ($fechaImagen != $fechaMod) {
			$this->crearThumb($baseImg);

			$ordenSQL = "update articulos set fechaimagen = '$fechaMod' where referencia = '$referencia'";
			$__BD->db_query($ordenSQL);
		}
		
		$codigo = $this->contenidoCodigoThumb($referencia, $baseImg, $fichImg, $fichImgN, $link);
		
		return $codigo;
	} 


	function contenidoCodigoThumb($referencia, $baseImg, $fichImg, $fichImgN, $link)
	{
		$codigo = '';
		if (file_exists($fichImg)) {
			$codigoTh = '<img class="thumb" border="0" src="'._WEB_ROOT.'catalogo/img_thumb/'.$baseImg.'">';
			// Si hay que poner link a foto grande
			
			if ($link && file_exists($fichImgN)) {
				$fichImgN = _WEB_ROOT.'catalogo/img_normal/'.$baseImg;
				$codigo .= '<a class="ampliar" href="'.$fichImgN.'">'.$codigoTh.'</a>';
			}
			else
				$codigo = $codigoTh;
		}
		
		return $codigo;
	}


	// Crea la imagen pequena a partir de una grande nueva
	function crearThumb($fichImagen, $pixImages = 0, $codFoto = '')
	{
		// Solo si el servidor tiene las funciones php necesarias
		if (!function_exists("imagecreatetruecolor"))
			return;
		
		$imgNor = _DOCUMENT_ROOT.'catalogo/img_normal/'.$fichImagen;
		$imgPeq = _DOCUMENT_ROOT.'catalogo/img_thumb/'.$fichImagen;
		list($width, $height) = getimagesize($imgNor);
		$path_parts = pathinfo($imgNor);
		$extension = $path_parts["extension"];
		
		if ($pixImages == 0)
			$pixImages = $_SESSION["opciones"]["piximages"];

		// Miniatura de secundaria?
		if ($codFoto)
			$pixImages = _PIX_FOTO_SEC;
		
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
			case "png":
				$image = imagecreatefrompng($imgNor);
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				$res = imagepng($image_p, $imgPeq);
				break;
			case "jpg":
			case "jpeg":
				$image = imagecreatefromjpeg($imgNor);
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagejpeg($image_p, $imgPeq, 75);
				break;
			case "gif":
				$image = imagecreatefromgif($imgNor);
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagegif($image_p, $imgPeq);
				break;
			
			default:
				return;
		}
	}
	
	// Devuelve la clausula where de una familia y sus familias hijas. Recursiva
	function whereFamiliasHijas($codFamilia, $where)
	{
		global $__BD;
	
		$ordenSQL = "select codfamilia from familias where codmadre = '$codFamilia' AND publico = true";
		$result = $__BD->db_query($ordenSQL);
			
		while($row = $__BD->db_fetch_array($result)) {
			$codFamilia = $row["codfamilia"];
			$where .= " OR codfamilia = '$codFamilia'";
			$where = $this->whereFamiliasHijas($codFamilia, $where);
		}
		return $where;
	}
		
	// Devuelve un listado de los atributos del artículo
	function atributos($referencia)
	{
		global $__BD, $__LIB;
	
		$atributos = '';
	
		$ordenSQL = "select atributos.codatributo as codatributo, atributos.nombre as nombre, atributosart.valor as valor, atributosart.id as id
						from atributos INNER JOIN atributosart 
						ON atributos.codatributo = atributosart.codatributo
						WHERE atributosart.referencia = '$referencia'
						AND atributos.publico = true ORDER BY atributos.orden";
						
		$result = $__BD->db_query($ordenSQL);
			
		while($row = $__BD->db_fetch_array($result)) {
			$nombre = $__LIB->traducir("atributos", "nombre", $row["codatributo"], $row["nombre"]);
			$valor = $__LIB->traducir("atributosart", "valor", $row["id"], $row["valor"]);
			$atributos .= '<b>'.$nombre.'</b>: '.$valor.'<br>';
		}
		
		if ($atributos)
			$atributos = '<p>'.$atributos;
			
		return $atributos;
	}
	
	// Devuelve el % de iva de un impuesto
	function selectIVA($codImpuesto)
	{
		global $__BD;
	
		$iva = $__BD->db_valor("select iva from impuestos where codimpuesto = '$codImpuesto'");	
		if (!$iva) $iva = 0;
		return $iva;
	}
	
	// Devuelve el precio del articulo aplicando el impuesto correspondiente
	// Opcionalmente le da formato con simbolo de moneda o descuento por oferta
	function precioArticulo($row, $formato = false, $precioOferta = true, $precioFinal = false)
	{
		global $__LIB;
		
		if ($__LIB->esTrue($_SESSION["opciones"]["preciossolousuarios"]) && !isset($_SESSION["codCliente"]))
			return '';

		$codImpuesto = $row["codimpuesto"];
		$precio = $row["pvp"];
		$precioAnterior = $row["pvp"];
			
		if (!$precioFinal)
			$precio = $this->aplicarTarifa($precio, $row["referencia"]);
		
		$enOferta = false;
		if ($__LIB->esTrue($row["enoferta"]) && $row["pvpoferta"] > 0)
			$enOferta = true;
		
		if ($enOferta)
			$precio = $row["pvpoferta"];
		
		$ivaIncluido = false;
		if (isset($row["ivaincluido"]))
			$ivaIncluido = $row["ivaincluido"]; 
		
		// Con IVA no incluido
		if ($__LIB->esTrue($_SESSION["opciones"]["impincluidos"]) && !$__LIB->esTrue($ivaIncluido)) {
			$iva = $this->selectIVA($codImpuesto);
			$precio = $precio + $precio * $iva / 100;
			$precioAnterior = $precioAnterior + $precioAnterior * $iva / 100;
		}
		
		// Con IVA incluido
		if (!$__LIB->esTrue($_SESSION["opciones"]["impincluidos"]) && $__LIB->esTrue($ivaIncluido)) {
			$iva = $this->selectIVA($codImpuesto);
			$precio = $precio - $precio * $iva / 100;
			$precioAnterior = $precioAnterior - $precioAnterior * $iva / 100;
		}
		
		if (!$formato)
			return $precio;
		
		// Con oferta
		if ($precioOferta && $enOferta) {
			$descuento = round(($precioAnterior - $precio) * 100 / $precioAnterior);
			$precio = $this->precioDivisa($precio);
			if (!$__LIB->esTrue($_SESSION["opciones"]["impincluidos"]))
				$precio .= ' <span class="msgIVA">'._IVA_NO_INCLUIDO.'</span>';
			
			$precioAnterior = $this->precioDivisa($precioAnterior);
			$precio .= '<br><span class="precioAnterior">'._ANTES.': '.$precioAnterior;
			if ($_SESSION["vista"] == 0) $precio .= '<br>';
			$precio .= ' '._DTO.': '.$descuento.'%';
			$precio .= '</span>';
		}
		else {
			$precio = $this->precioDivisa($precio);		
			if (!$__LIB->esTrue($_SESSION["opciones"]["impincluidos"]))
				$precio .= ' <span class="msgIVA">'._IVA_NO_INCLUIDO.'</span>';
		}
		
		return $precio;
	}
	
	
	// Devuelve el precio neto del articulo
	function precioNeto($row, $formato = false, $precioOferta = true, $precioFinal = false)
	{
		global $__LIB;
		
		$codImpuesto = $row["codimpuesto"];
		$iva = $this->selectIVA($codImpuesto);
		$precio = $row["pvp"];
		$precioAnterior = $row["pvp"];
		
		if (!$precioFinal)
			$precio = $this->aplicarTarifa($precio, $row["referencia"]);
		
		// Oferta
		if (isset($row["enoferta"])) {
			$enOferta = false;
			if ($__LIB->esTrue($row["enoferta"]) && $row["pvpoferta"] > 0)
				$enOferta = true;
			
			if ($enOferta)
				$precio = $row["pvpoferta"];
		}
		
		// Si el precio lleva IVA incluido lo restamos
		if ($__LIB->esTrue($row["ivaincluido"])) {
			$precio = $precio / (1 + $iva / 100);
			$precioAnterior = $precioAnterior - $precioAnterior * $iva / 100;
		}
		
		if ($formato)
			return formatoPrecio($precio, $precioAnterior, $enOferta);
			
		return $precio;
	}
	
	// Devuelve el precio + impuestos del articulo
	function precioImpuestos($row, $formato = false, $precioOferta = true)
	{
		global $__LIB;
		
		$codImpuesto = $row["codimpuesto"];
		$iva = $this->selectIVA($codImpuesto);
		$precio = $row["pvp"];
		$precioAnterior = $row["pvp"];
			
		$precio = $this->aplicarTarifa($precio, $row["referencia"]);
		
		// Oferta
		$enOferta = false;
		if ($__LIB->esTrue($row["enoferta"]) && $row["pvpoferta"] > 0)
			$enOferta = true;
		
		if ($enOferta)
			$precio = $row["pvpoferta"];
		
		$precio += impuestoArticulo($row);
		
		if ($formato)
			return formatoPrecio($precio, $precioAnterior, $enOferta);
			
		return $precio;
	}
	
	// Devuelve el precio modificado con su tarifa si corresponde
	function aplicarTarifa($precio, $referencia = '')
	{
		global $__BD, $__LIB;
		$preciosXtarifa = $_SESSION["opciones"]["preciosportarifas"];

		if (!$__LIB->esTrue($preciosXtarifa))
			return $precio;
	
		if (!isset($_SESSION["codCliente"]))
			return $precio;

		$ordenSQL = "select g.codtarifa from gruposclientes g
				inner join clientes c on g.codgrupo = c.codgrupo
				where c.codcliente = '".$_SESSION["codCliente"]."'";

		$codTarifa = $__BD->db_valor($ordenSQL);
		if (!$codTarifa)
			return $precio;

		// Precio por art�?culo y tarifa?
		if ($referencia) {
			$ordenSQL = "select pvp from articulostarifas where codtarifa = '$codTarifa' and referencia = '$referencia'" ;
			$precioTarifa = $__BD->db_valor($ordenSQL);
			if ($precioTarifa) {
				return $precioTarifa;
			}
		}

		// Precio por tarifa?
		$ordenSQL = "select incporcentual, inclineal from tarifas where codtarifa='$codTarifa'";
		$result = $__BD->db_query($ordenSQL);
		$row = $__BD->db_fetch_row($result);
		
		$incPor = $row[0]; 
		$incLin = $row[1]; 

		$precio = $precio + $precio * $incPor / 100 + $incLin; 
		
		return $precio;
	}
	
	// Devuelve el importe en impuesto de un articulo
	function impuestoArticulo($row, $precioFinal = false)
	{
		global $__LIB;
		
		$precio = $row["pvp"];
		if (!$precioFinal)
			$precio = $this->aplicarTarifa($precio, $row["referencia"]);
		
		// Oferta ?
		$enOferta = false;
		if (isset($row["enoferta"])) {
			if ($__LIB->esTrue($row["enoferta"]) && $row["pvpoferta"] > 0)
				$enOferta = true;
			
			if ($enOferta)
				$precio = $row["pvpoferta"];
			
			if ($__LIB->esTrue($row["enoferta"]) && $row["pvpoferta"] > 0)
				$precio = $row["pvpoferta"];
		}
		
		$iva = $this->selectIVA($row["codimpuesto"]);
		
		// Si el precio no lleva IVA incluido
		if (!$__LIB->esTrue($row["ivaincluido"]))
			$impuesto = $precio * $iva / 100;
		else
			$impuesto = $precio - $precio / (1 + $iva / 100);
		
		return $impuesto;
	}
	
	// Da formato al precio, incluido el simbolo de la divisa
	function formatoPrecio($precio, $precioAnterior, $enOferta)
	{
		global $__LIB;
		
		if ($precioOferta && $enOferta) {
			$descuento = round(($precioAnterior - $precio) * 100 / $precioAnterior);
			$precio = $this->precioDivisa($precio);
			if (!$__LIB->esTrue($_SESSION["opciones"]["impincluidos"]))
				$precio .= ' <span class="msgIVA">'._IVA_NO_INCLUIDO.'</span>';
			
			$precioAnterior = $this->precioDivisa($precioAnterior);
			$precio .= '<br><span class="precioAnterior">'._ANTES.': '.$precioAnterior;
			if ($_SESSION["vista"] == 0) $precio .= '<br>';
			$precio .= ' '._DTO.': '.$descuento.'%';
			$precio .= '</span>';
		}
		else {
			$precio = $this->precioDivisa($precio);		
			if (!$__LIB->esTrue($_SESSION["opciones"]["impincluidos"]))
				$precio .= ' <span class="msgIVA">'._IVA_NO_INCLUIDO.'</span>';
		}
		
		return $precio;
	}
	
	
	// Da al precio el simbolo de la divisa
	function precioDivisa($precio)
	{
		$precio = number_format($precio,2,",",".");
		$simbolo = "";
		
		switch($_SESSION["divisa"]) {
			case "EUR":
				$precio .= "&euro;";
			break;
			case "USD":
				$precio = "$".$precio;
			break;
			default:
				$precio .= "&euro;";
		}
			
		return $precio;
	}
	
	
	
	
	
	// Rastro de familias antecedentes de una familia
	function rastro($codFamilia, $rastro = "")
	{
		global $__BD, $__LIB;
	
		$ordenSQL = "select codmadre, descripcion from familias where codfamilia = '$codFamilia'";
		$row = $__BD->db_row($ordenSQL);
		$codMadre = $row[0];
		
		$descripcion = $__LIB->traducir("familias", "descripcion", $codFamilia, $row[1]);
		
		$link = '';
		
		if ($rastro)
			$link .= '<a href="articulos.php?fam='.$codFamilia.'">';
		
		$link .= $descripcion;
		
		if ($rastro) {
			$link .= '</a>';
			$link .= '&nbsp;<b>&middot;</b>&nbsp;';
		}
		
		$rastro = $link.$rastro;
		
		if ($codMadre)
			$rastro = $this->rastro($codMadre, $rastro);
		
		return $rastro;
	}
	
	// Lista de familias hijas de una familia
	function listaFamiliasHijas($codFamilia)
	{
		global $__BD, $__LIB;
	
		$codigo = '';
		$ordenSQL = "select codfamilia, descripcion from familias where codmadre = '$codFamilia' AND publico = true order by orden";
		
		$result = $__BD->db_query($ordenSQL);
		while($row = $__BD->db_fetch_array($result)) {
			
			$descripcion = $__LIB->traducir("familias", "descripcion", $row["codfamilia"], $row["descripcion"]);
			
			$numArticulos = $this->numArticulosF($row["codfamilia"]);
			if ($numArticulos == 0) continue;
			
			if ($codigo)
				$codigo .= ' &middot; ';
				
			$codigo .= '<a href="articulos.php?fam='.$row["codfamilia"].'">';
			$codigo .= $descripcion;
			$codigo .= '</a>';
		}
		
		if ($codigo)
			$codigo = '<div class="subCaja">'.$codigo.'</div>';
	
		return $codigo;
	}
	
	// Clausula like para las queries de busqueda
	function likeBuscar($palabras) 
	{
		global $__BD;
	
		if (!$palabras) 
			return false;
		
		$like = '(';
		$likeD = '(';
		$likeT = '(';
		
		$arrayPalabras = split(" ",$palabras);
		for ($i = 0; $i < count($arrayPalabras); $i++) {
			if ($i > 0) {
				$like .= " AND";
				$likeD .= " AND";
				$likeT .= " AND";
			}
			$like .= " lower(descripcion) LIKE '%".strtolower($arrayPalabras[$i])."%'";
			$likeD .= " lower(a.descripcion) LIKE '%".strtolower($arrayPalabras[$i])."%'";
			$likeT .= " lower(t.traduccion) LIKE '%".strtolower($arrayPalabras[$i])."%'";
		}
		
		$like .= ')';
		$likeD .= ')';
		$likeT .= ')';
		
		// Si es idioma por defecto
		if ($_SESSION["idioma"] == $_SESSION["opciones"]["codidiomadefecto"]) {
			if ($i == 0)
				return false;
			return $like;
		}
		
		$ordenSQL = 'select a.referencia as ref, a.descripcion, t.traduccion from articulos a left outer join traducciones t on a.referencia=t.idcampo where ((t.codidioma=\''.$_SESSION["idioma"].'\' AND t.tabla=\'articulos\' and t.campo=\'descripcion\' and '.$likeT.') or (t.tabla is null and '.$likeD.')) and a.publico=true';
				
		$result = $__BD->db_query($ordenSQL);
		$paso = 0;
		$lista = '';
		while($row = $__BD->db_fetch_array($result)) {
			if ($paso++ > 0) $lista .= ",";
			$lista .= '\''.$row["ref"].'\'';
		}
		
		if ($paso == 0)
			return false;
		
		$like = 'referencia IN ('.$lista.')';
		return $like;	
	}


}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @class_definition pcrednet_funCatalogo */
//////////////////////////////////////////////////////////////////
//// PC REDNET /////////////////////////////////////////////////////

class pcrednet_funCatalogo extends oficial_funCatalogo
{
	// Devuelve el precio modificado con su tarifa si corresponde
	// Si no, busca la tarifa general web
	function aplicarTarifa($precio, $referencia)
	{
		global $__BD, $__LIB;
		$preciosXtarifa = $_SESSION["opciones"]["preciosportarifas"];

		if ($__LIB->esTrue($preciosXtarifa) && isset($_SESSION["codCliente"]))  {
			$ordenSQL = "
					select t.incporcentual, t.inclineal from
					tarifas t inner join
					gruposclientes g on t.codtarifa = g.codtarifa inner join
					clientes c on g.codgrupo = c.codgrupo
					where c.codcliente = '".$_SESSION["codCliente"]."'";
	
			if ($__BD->db_num_rows($ordenSQL) > 0)
				return parent::aplicarTarifa($precio);
		}

		$codTarifaWeb = $_SESSION["opciones"]["codtarifaweb"];
		if (!$__LIB->esTrue($codTarifaWeb))
			return $precio;

		// Precio por artículo y tarifa?
		if ($referencia) {
			$ordenSQL = "select pvp from articulostarifas where codtarifa = '$codTarifaWeb' and referencia = '$referencia'" ;
			$precioTarifa = $__BD->db_valor($ordenSQL);
			if ($precioTarifa) {
				return $precioTarifa;
			}
		}

		$ordenSQL = "select incporcentual, inclineal from tarifas where codtarifa = '$codTarifaWeb'";
		if ($__BD->db_num_rows($ordenSQL) == 0)
			return $precio;

		$result = $__BD->db_query($ordenSQL);
		$row = $__BD->db_fetch_row($result);
		
		$incPor = $row[0];
		$incLin = $row[1];

		$precio = $precio + $precio * $incPor / 100 + $incLin; 
		
		return $precio;
	}

	// Numero de articulos por familia
	function numArticulosF($codFamilia) 
	{
		global $__BD;
	
		$where = " where (codfamilia = '$codFamilia'";	 	
		$where = $this->whereFamiliasHijas($codFamilia, $where);
		$where .= ')';
		$ordenSQL = "select count(referencia) from articulos $where and publico = true and obsoleto = false";
		return $__BD->db_valor($ordenSQL);
	}

}

//// PC REDNET /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
	
/** @main_class_definition oficial_funCatalogo */
class funCatalogo extends pcrednet_funCatalogo{};

?>
