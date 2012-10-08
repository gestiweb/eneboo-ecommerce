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

$CONFIG_MAILER = 'mail'; // 'mail','smtp' o 'sendmail'
$CONFIG_SMTPAUTH = '';
$CONFIG_SMTPUSER = '';
$CONFIG_SMTPPASS = '';
$CONFIG_SMTPHOST = ''; 
$CONFIG_SMTPPORT = 25;
$CONFIG_SMTPSSL = ''; // 'ssl'
$CONFIG_SENDMAIL = '/usr/sbin/sendmail -bs';

include_once('libreria.php');
include_once('libreria/fun_bd.php');
include_once('libreria/fun_catalogo.php');
include_once('libreria/fun_cliente.php');
include_once('libreria/fun_cesta.php');
include_once('libreria/fun_seguridad.php');
include_once('libreria/fun_formularios.php');
include_once('libreria/fun_seo.php');
include_once('libreria/fun_cache.php');
require_once 'swiftmailer/swift_required.php';

// Se incluyen los ficheros php de los modulos
$ruta = _DOCUMENT_ROOT.'modulos';

if (file_exists($ruta)) {
	$handle=opendir($ruta); 
	$numFich=0;
	$noMatch = 0;
	
	$allowable = array (
		'php'
	);
	
	while ($file = readdir($handle)) {
		$format = substr( $file, -3 );
		foreach( $allowable as $ext ) {
		if ( strcasecmp( $format, $ext ) == 0 )
			include($ruta.'/'.$file);
		}
	}
	closedir($handle);
}


// Declaracion de objetos que se usaran en global
$__LIB = new funLibreria;
$__BD = new funBD;
$__CAT = new funCatalogo;
$__SEC = new funSeguridad;

// Procesado de seguridad de parametros
$CLEAN_GET = $__SEC->limpiarGET($_GET);
$CLEAN_POST = $__SEC->limpiarPOST($_POST);

?>