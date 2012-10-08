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

/** @no_class */
   	
   	ini_set('display_errors', false);
   	
   	include('libreria.php');
   	$__LIB = new funLibreria;
   	
   	include('../includes/libreria/fun_seguridad.php');
   	$__SEC = new funSeguridad;
   	$CLEAN_GET = $__SEC->limpiarGET($_GET);
   	$CLEAN_POST = $__SEC->limpiarPOST();

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Tienda Virtual eStoreQ</title>
<link rel="stylesheet" href="../templates/default/estilos.css" type="text/css">
<link rel="stylesheet" href="estilos.css" type="text/css">
</head>
<body>

<table class="mainTable" cellspacing=0 cellpadding=0 width="90%" height="100%" align="center">

<tr><td colspan="2" height="35">

<table cellspacing=0 cellpadding=0 class="mainTop" width="100%" height="100%">
	<tr><td class="logo" width="100">
	
		<img class="logotop" src="../templates/default/images/logotop.png"></a>
	
	</td></tr>
</table>

</td></tr>

<tr><td colspan="2">

	<table class="cuerpo" cellspacing=0 cellpadding=0 width="100%" height="100%"><tr>
	<td class="contenidos" valign="top">
