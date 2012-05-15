<?php
header("Content-Type: text/css");	
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

/** @class_definition oficial_CSS */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_CSS
{	
	var $estilos;
	
	function setCSS()
	{
		
$this->estilos["body"] = ' 
	color:#333;
	font-family:verdana,arial;
	font-size:11px;
	margin: 0px 10px 0px 10px;
	border: 1px solid #CCC;
';

$this->estilos["pre"] = '
	color:#333;
	font-size:11px;
	font-family:verdana,arial;
';

$this->estilos["select, input, textarea, select"] = '
	color:#333;
	font-size:11px;
	font-family:verdana,arial;
	border: 1px solid #CCC;
	margin: 3px;
	padding: 2px;
';

$this->estilos["input.boton"] = '
	color:#333;
	font-size:11px;
	font-family:verdana,arial;
	border: 1px solid #CCC;
	margin: 3px;
	padding: 2px;
';

$this->estilos["input.warning, select.warning"] = '
	background-color: red;
';

$this->estilos["input.normal, select.normal"] = '
	background-color: none;
';



$this->estilos["div#barraTop"] = '
	background-color:#333;
	color:#EEE;
	height: 17px;
	padding:2px 0px 2px 0px;
';

$this->estilos["div#navTop"] = '
	float: left;
	text-align:left;
	width: 70%;
	padding: 0px 0px 0px 5px;
';

$this->estilos["div#bienvenidoTop"] = '
	float: right;
	text-align: right;
	width: 25%;
	padding: 0px 20px 0px 0px;
';


$this->estilos["div#logoTop"] = '
	color:#EEE;
	height: 56px;
	padding: 0px;
	background-image: url(images/fondo_top.png);
	background-position: right;
	background-repeat: repeat-y;
';

$this->estilos["div#imgLogoTop"] = '
	float: left;
	text-align:left;
	width: 600px;
';

$this->estilos["div#sloganLogoTop"] = '
	float: right;
	text-align: right;
	width: 250px;
	padding: 35px 20px 0px 0px;
	font-weight: bold;
';


$this->estilos["div#contenidos"] = '
	position: relative;
	margin: 20px 140px 0px 140px;
	color: #333333;
	padding: 20px;
	z-index: 13;
';

$this->estilos["div#modulosLeft"] = '
	position: absolute;
	top: 78px;
	left: 20px;
	width: 140px;
	color: #333333;
	margin-top: 40px;
	padding: 0px;
';

$this->estilos["div#modulosRight"] = '
	 position: absolute;
	 top: 78px;
	 right: 20px;
	 width: 140px;
	 color: #333333;
	 margin-top: 40px;
	 padding: 0px;
	 z-index: 1;
';




$this->estilos["div.modulosWebLeft"] = '
	border-right: 1px solid #ddd;
	background-color:#f5f5f5;
	text-align: left;
';

$this->estilos["div.modulosWebRight"] = '
	border-left: 1px solid #ddd;
	background-color:#f5f5f5;
	text-align: left;
';


$this->estilos["div.caja"] = '
	padding: 0px; 
	margin: 0px 0px 20px 0px;
';

$this->estilos["div.caja div.titulo"] = '
	background-color:#DDD;
	color:#2338AC;
	font-size: 10px;
	font-weight: bold;
	padding: 2px 5px 2px 5px; 
	margin: 0px;
	line-height:12px;
	border-width: 1px 0px 1px 0px;
	border-style: solid;
	border-color: #ccc;
';

$this->estilos["div.caja div.casillaUnica"] = '
	background-color:#DDD;
	color:#2338AC;
	font-size: 10px;
	font-weight: bold;
	padding: 2px 5px 0px 5px; 
	margin: 0px;
	border-width: 1px 0px 1px 0px;
	border-style: solid;
	border-color: #ccc;
';

$this->estilos["div.caja div.contenido"] = '
	line-height:13px;
	padding:0px;
	font-weight: normal;
';

$this->estilos["div.cajaArtMatriz"] = '
	text-align: center;
	float:left;
	padding: 0px;
	margin: 0px;
';

$this->estilos["div.innerCajaArtMatriz"] = '
	padding: 15px 5px 10px 5px;
	border-width: 0px 1px 1px 0px;
	border-color: #DDD;
	border-style: solid;
';


$this->estilos["div.cajaImagenMatriz"] = '
	text-align: center;
	float:left;
	padding: 0px;
	margin: 0px;
';

$this->estilos["div.innerCajaImagenMatriz"] = '
	padding: 15px 5px 10px 5px;
	border-width: 0px 1px 1px 0px;
	border-color: #DDD;
	border-style: solid;
	height: 150px;
';



$this->estilos["div.navBar"] = '
	width:99%;
	height: 15px;
	background-color:#EEE;
	padding: 2px 0px 2px 0px;
	margin: 5px 0px 0px 0px;
';

$this->estilos["div.navBarResultPag"] = '
	float:left;
	width:30%;
	padding: 0px 0px 0px 5px;
';

$this->estilos["div.navBarListaPag"] = '
	float:left;
	width:25%;
';

$this->estilos["div.navBarOrden"] = '
	float:left;
	width:40%;
	text-align:right;
';

$this->estilos["div.navBarResultXPag"] = '
	float:left;
	width:60%;
	text-align: left;
	padding-left: 5px;
';

$this->estilos["div.navBarDisposicion"] = '
	float:left;
	width:35%;
	text-align: right;
';


$this->estilos["div.cellMatriz"] = '
	padding: 0px;
	width: 100%;
	height: 100%;
';

$this->estilos["div.cellMatriz div.descripcion"] = '
	font-weight: bold;
	height: 40px;
	line-height: 12px;
';

$this->estilos["div.cellMatriz div.imagen"] = '
	font-weight: bold;
';

$this->estilos["div.cellMatriz div.stock"] = '
	height: 40px;
';

$this->estilos["div.cellMatriz div.precio"] = '
	font-weight: bold;
	height: 28px;
';

$this->estilos["div.cellMatriz div.venta"] = '
	height: 20px;
	padding: 10px 0px 0px 0px;
	vertical-align: top;
';



$this->estilos["div.cellLista"] = '
	clear: left;
	margin: 15px 0px 0px 0px;
	width: 100%;
	border-bottom: 1px solid #DDD;
';

$this->estilos["div.cellListaImagen"] = '
	float:left;
	font-weight: bold;
';

$this->estilos["div.cellListaImagen img"] = '
	margin: 0px 20px 0px 20px;
';

$this->estilos["div.cellListaDescripcion"] = '
	float:left;
	width: 120px;
	font-weight: bold;
	line-height: 12px;
';

$this->estilos["div.cellListaStock"] = '
	float:left;
	width: 80px;
';

$this->estilos["div.cellListaPrecio"] = '
	float:left;
	width: 90px;
	font-weight: bold;
';

$this->estilos["div.cellListaVenta"] = '
	float:left;
	width: 190px;
	text-align: center;
';



$this->estilos["div.articulo"] = '
	padding: 10px;
	position: relative;
';

$this->estilos["div.articulo div.thumb"] = '
	width: 200px;
';

$this->estilos["div.articulo div.venta"] = '
	position: absolute;
	left: 300px;
	top: 10px;
	padding: 10px;
';



$this->estilos["div.cabeceraDescCesta"] = '
	float:left;
	font-weight: bold;
	color:#2338AC;
	width: 120px;
	padding-bottom: 5px;
	margin-bottom: 5px;
	border-bottom: 1px solid #DDD;
';

$this->estilos["div.cabeceraDatoCesta"] = '
	float:left;
	font-weight: bold;
	text-align: right;
	color:#2338AC;
	padding-bottom: 5px;
	margin-bottom: 5px;
	border-bottom: 1px solid #DDD;
	width: 80px;
';

$this->estilos["div.articuloCesta"] = '
	clear:left;
	float:left;
	width: 120px;
	height: 25px;
	padding: 5px 0px 5px 0px;
	border-bottom: 1px solid #DDD;
';

$this->estilos["div.datoCesta"] = '
	float:left;
	text-align: right;
	width: 80px;
	height: 25px;
	padding: 5px 0px 5px 0px;
	border-bottom: 1px solid #DDD;
	vertical-align:top;
';

$this->estilos["div.datoCesta input"] = '
	padding: 1px 3px 1px 3px;
';

$this->estilos["div#labelTotalCesta"] = '
	clear:left;
	float:left;
	height: 25px;
	text-align: left;
	font-weight: bold;
	width: 120px;
	padding: 5px 0px 5px 0px;
	border-bottom: 1px solid #DDD;
';

$this->estilos["div#totalCesta"] = '
	float:left;
	height: 25px;
	text-align: right;
	width: 80px;
	font-weight: bold;
	padding: 5px 0px 5px 0px;
	border-bottom: 1px solid #DDD;
';


$this->estilos["div#formLogin"] = '
	position:relative;
	border: 1px solid #DDD;
	background-color:#EEE;
	width: 350px;
	height: 170px;
';

$this->estilos["div#lblTengoCuentaLogin"] = '
	position:absolute;
	top: 20px;
	left: 95px;
';

$this->estilos["div#lblEmailLogin"] = '
	position:absolute;
	top: 60px;
	left: 15px;
	font-weight: bold;
';

$this->estilos["div#emailLogin"] = '
	position:absolute;
	top: 55px;
	left: 90px;
';

$this->estilos["div#lblPassLogin"] = '
	position:absolute;
	top: 90px;
	left: 15px;
	font-weight: bold;
';

$this->estilos["div#passLogin"] = '
	position:absolute;
	top: 85px;
	left: 90px;
';

$this->estilos["div#botLogin"] = '
	position:absolute;
	top: 125px;
	left: 90px;
';

$this->estilos["div#botones"] = '
	float:left;
	text-align: left;
	width: 50%;
	padding: 10px 0px 40px 0px;
';


$this->estilos["div.labelForm"] = '
	clear:left;
	float:left;
	width: 130px;
	text-align: left;
	padding: 5px 10px 0px 0px;
	font-weight: bold;
	color: #555;
';

$this->estilos["div.datoForm"] = '
	float:left;
';




$this->estilos["div.labelListaDocFact"] = '
	float:left;
	width: 130px;
	height: 30px;
	padding-bottom: 10px;
	text-align: left;
	font-weight: bold;
	color: #555;
';

$this->estilos["div.datoListaDocFact"] = '
	float:left;
	width: 130px;
	height: 20px;
';


$this->estilos["p.separador"] = '
	clear: left;
	padding-top:20px;
';

$this->estilos[".cleaner"] = '
	clear:both;
	height:1px;
	font-size:1px;
	border:none;
	margin:0; padding:0;
	background:transparent;
';

$this->estilos[".cleanerLeft"] = '
	clear:left;
	height:1px;
	font-size:1px;
	border:none;
	margin:0; padding:0;
	background:transparent;
';


$this->estilos["div#datosEnvio div.checkEnvio"] = '
	clear:left;
	float:left;
	width:25px;
';

$this->estilos["div#datosEnvio div.labelEnvio"] = '
	float:left;
	width:250px;
';

$this->estilos["div#datosEnvio div.datoEnvio"] = '
	float:left;
	width:60px;
	text-align: right;
';

$this->estilos["div#datosEnvio div.msgEnvio"] = '
	float:left;
	width:200px;
	text-align: left;
	margin-left: 10px;
';


$this->estilos["div.formaPago"] = '
	padding: 5px;
	margin: 0px 0px 15px 0px;
	width:500px;
	border: 1px solid #DDD;
';

$this->estilos["div.formaPago div.checkPago"] = '
	clear:left;
	float:left;
	width:25px;
';

$this->estilos["div.formaPago div.labelPago"] = '
	float:left;
	font-weight: bold;
';

$this->estilos["div.formaPago div.descPago"] = '
	clear:left;
	width:90%;
	padding: 5px;
	background-color: #EEE;
';

$this->estilos["div.formaPago div.gastosPago"] = '
	clear:left;
	width:90%;
	padding: 5px;
';


$this->estilos["div.noticia"] = '
	padding: 10px 20px 2px 5px; 
	line-height:12px;
	margin: 0px;
	text-align:justify;
	vertical-align:top;
';

$this->estilos["div.noticia img"] = '
	float:right;
	padding: 0px 0px 20px 20px;
';


$this->estilos["div.flags"] = '
	padding: 4px 0px 0px 0px;
	text-align:center;
	vertical-align:middle;
	background-color:#DDD;
';


$this->estilos[".titPagina, .titPath"] = '
	font-size:13px;
	color:#2338AC;
	font-weight: bold;
	margin: 0px;
	padding: 2px 4px 2px 4px;
	text-align: left;
	background-color:#DDD;
';

$this->estilos[".subCaja"] = '
	margin: 0px;
	padding: 2px 4px 2px 4px;
	text-align: left;
	border-width: 0px 1px 1px 1px;
	border-style: solid;
	border-color: #EEE;
';

$this->estilos["div.cajaTexto"] = '
	margin: 0px;
	padding: 10px;
	text-align: justify;
	width: 550px;
';

$this->estilos["div.titApartado"] = '
	clear: left;
	color:#2338AC;
	font-weight: bold;
	padding: 20px 0px 0px 0px;
	margin: 5px 0px 10px 0px;
	text-align: left;
	border-width: 0px 0px 1px 0px;
	border-style: solid;
	border-color: #EEE;
';

$this->estilos[".titApartadoText"] = '
	color:#2338AC;
	font-weight: bold;
	background-color: #EEE;
	padding: 1px 10px 1px 10px;
';

$this->estilos[".titModulo"] = '
	color:#2338AC;
	font-size: 12px;
	font-weight: bold;
	margin: 10px 0px 0px 0px;
	padding: 0px;
	text-align: left;
	border-bottom: 1px solid #888;
';

$this->estilos[".itemMenu"] = '
	background-color:#FFF;
	border-bottom: 1px solid #DDD;
	line-height:12px;
';

$this->estilos[".itemMenu a"] = '
	margin: 0px;
	display: block;
	padding: 3px 5px 3px 5px;
';

$this->estilos[".itemMenu a:hover"] = '
	background-color: #F5F5F5;
';

$this->estilos[".itemMenuFamilia"] = '
	margin: 0px 0px 4px 0px;
';

$this->estilos["a"] = '
	text-decoration:none;
	color:#008;
';

$this->estilos["a.hover"] = '
	color:#000;
';

$this->estilos["a.botLink"] = '
	border: 1px solid #DDD;
	padding: 2px 5px 2px 5px;
	margin: 0px 10px 0px 0px;
';

$this->estilos["a.botComprar, a.botFav, a.botActualizar, a.botContinuar, a.botVolver, a.botGuardar, a.botEliminar, a.botLista"] = '
	border: 1px solid #DDD;
	padding: 3px 25px 2px 5px;
	margin: 0px 10px 0px 0px;
	background-repeat: no-repeat;
	background-position: 95% 50%;
';

$this->estilos["a.botContinuarCent, a.botVolverCent"] = '
	border: 1px solid #DDD;
	padding: 3px 10px 2px 10px;
	margin: 0px 5px 0px 0px;
	background-repeat: no-repeat;
	background-position: 50% 50%;
';

$this->estilos["a.botLink:hover, a.botComprar:hover, a.botFav:hover, a.botActualizar:hover, a.botActualizar:hover, a.botContinuar:hover, a.botContinuarCent:hover, a.botVolver:hover a.botVolverCent:hover, a.botGuardar:hover, a.botEliminar:hover, a.botLista:hover"] = '
	background-color: #EEE;
	border-color: 1px solid #555;
';

$this->estilos["a.botComprar"] = '
	background-image: url(images/cesta.png);
';

$this->estilos["a.botFav"] = '
	background-image: url(images/favoritos.png);
';

$this->estilos["a.botActualizar"] = '
	background-image: url(images/actualizar.png);
';

$this->estilos["a.botContinuar, a.botContinuarCent"] = '
	background-image: url(images/continuar.png);
';

$this->estilos["a.botVolver, a.botVolverCent"] = '
	background-image: url(images/volver.png);
';

$this->estilos["a.botGuardar"] = '
	background-image: url(images/guardar.png);
';

$this->estilos["a.botEliminar"] = '
	background-image: url(images/eliminar.png);
';

$this->estilos["a.botLista"] = '
	background-image: url(images/lista.png);
';




$this->estilos["a.inv"] = '
	color:#FFF;
';

$this->estilos["a.inv:hover"] = '
	color:#DDD;
';

$this->estilos["ul"] = '
	list-style-image:url(../../images/lista.gif)
';

$this->estilos["li"] = '
	margin-bottom:3px;
';

$this->estilos[".thumb"] = '
	margin:0px;
';


$this->estilos[".precioAnterior"] = '
	font-weight:normal;
	font-size:10px;
';

$this->estilos[".fasesPedido"] = '
	padding: 0px;
	font-weight:bold;
	border-width: 0px 0px 1px 0px;
	border-style: solid;
	border-color: #EEE;
	font-size:10px;
	color:#555;
';

$this->estilos[".seccionesCuenta"] = '
	margin: 10px 0px 20px 0px;
	padding: 0px;
	border-width: 0px 0px 1px 0px;
	border-style: solid;
	border-color: #EEE;
	font-size:10px;
	color:#555;
';

$this->estilos[".titSeccionCuenta"] = '
	color:#2338AC;
	background-color: #EEE;
	padding: 1px 10px 1px 10px;
';


$this->estilos[".imgCaja"] = '
	margin: 5px;
';

$this->estilos[".cajaImagen"] = '
	padding: 5px;
	text-align:center;
	background-color:#FFF;
';

$this->estilos[".cajaImagen img"] = '
	width: 85px;
';



$this->estilos[".msgInfo"] = '
	border: 1px solid #DDD;
	padding: 3px;
	margin: 10px 10px 10px 0px;
';

$this->estilos[".msgError"] = '
	border: 1px solid #DDD;
	padding: 3px;
	margin: 10px 10px 10px 0px;
	color: #500;
';

$this->estilos[".msgOk"] = '
	border: 1px solid #DDD;
	padding: 3px;
	margin: 10px 10px 10px 0px;
	color: #050;
';

$this->estilos[".msgIVA"] = '
	font-weight:normal;
	font-size:10px;
	color:#555;
	padding: 0px 0px 0px 5px;
';

$this->estilos["div.fotoGrande"] = '
	border: 0px solid #CCC;
	margin: 10px 10px 20px 10px;
	padding: 10px 10px 0px 10px;
';

$this->estilos["div.fotoGaleria"] = '
	margin: 0px;
	padding: 10px 10px 10px 10px;
	text-align:center;
';

$this->estilos["div.fotoGaleria img"] = '
	border: 1px solid #CCC;
	margin: 10px 0px 5px 0px;
';

$this->estilos["div.navGaleria"] = '
	text-align:left;
	padding: 4px 10px 4px 10px;
';

$this->estilos["div.selecPais"] = '
	width: 220px;
	border: 1px solid #CCC;
	background-color: #EEE;
	padding: 3px;
	margin: 3px;
';


$this->estilos[".ui-dialog div.links"] = '
	text-align: right;
	margin: 10px 20px 0px 0px;
';

$this->estilos[".ui-dialog div.img"] = '
	border: 1px solid #DDD;
';


$this->estilos[".cajaEnviarAamigo"] = '
	border: 1px solid #999;
	width: 500px;
	padding: 10px;
';

$this->estilos[".cajaEnviarAamigo input"] = '
	width: 350px;
';

$this->estilos[".cajaEnviarAamigo textarea"] = '
	width: 450px;
	height: 120px;
';

$this->estilos["span#msgDescuento"] = '
	font-weight:bold;
	padding-left: 10px;
';
}		
		
	function getCSS()
	{
		$codigo = '';
		
		foreach ($this->estilos as $sel => $estilo) {
			$codigo .= "\n";
			$codigo .= $sel.' {';
			$codigo .= $estilo;
			$codigo .= "}\n";
		}
		return $codigo;
	}
		
	function contenidos()
	{
		$this->setCSS();
		$codigo = $this->getCSS();
		
		return $codigo;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition CSS */
class CSS extends oficial_css {};

$iface_CSS = new CSS();
echo $iface_CSS->contenidos();
?>