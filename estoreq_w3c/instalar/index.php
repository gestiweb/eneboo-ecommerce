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

require_once( 'top_left.php' );
require_once( '../idiomas/esp/main.php' );

?>

<h1><?php echo _INSTALL_TV ?></h1>

<p>

<?php
	echo $__LIB->fasesInstalacion('previo');
	echo '<h2>'._FASES_INS_PREVIO.'</h2>';

	echo _INS_TEXTO_0;
?>

<p><br/>
	<a class="button" href="javascript:window.location=window.location"><span><?php echo _COMPROBAR_NUEVO ?></span></a>
	<a class="button" href="licencia.php"><span><?php echo _SIGUIENTE_MAS ?></span></a>
<p/>

<br/>		
		
<h2><?php echo _RECURSOS_SISTEMA ?></h2>

<?php echo _RECURSOS_SISTEMA_TEXT ?>

<br/><br/>

<table class="datos">

<tr>
	<th><?php echo _RECURSO ?></th>
	<th><?php echo _DISPONIBLE ?></th>
</tr>

<?php
	$funcionesPHP = array(
		array ('MySQL', 'mysql_connect', true),
		array ('PostgreSQL', 'pg_connect', true),
		array (_SOPORTE_IMG, 'imagecopyresampled', true),
	);
?>


<?php
	echo $__LIB->versionPHP('4.1');
	foreach ($funcionesPHP as $funcion) {
		echo $__LIB->funcionPHP($funcion);
	}
?>

</table>


<h2><?php echo _DIRECT_SISTEMA ?></h2>

<?php echo _DIRECT_SISTEMA_TEXT ?>

<br/><br/>

<table class="datos">
<tr>
	<th><?php echo _DIRECTIVA ?></th>
	<th><?php echo _RECOMENDADO ?></th>
	<th><?php echo _ACTUAL ?></th>
</tr>

<?php
$settings = array(
	array ('safe_mode','OFF'),
	array ('register_globals','OFF'),
	array ('session.auto_start','OFF'),
);

foreach ($settings as $sett) {
	echo $__LIB->settingsPHP($sett);
}

?>

</table>


<h2><?php echo _PERMISOS_DISCO ?></h2>
<?php echo _PERMISOS_DISCO_TEXT ?>

<br/><br/>

<table class="datos">
<tr>
	<th><?php echo _DIRECTORIO ?></th>
	<th><?php echo _PERMISO_ESC ?></th>
</tr>
<?php
	
$dirs = array(
	'includes',
	'includes/configure_web.php',
	'includes/configure_bd.php',
	'catalogo/img_normal',
	'catalogo/img_mediana',
	'catalogo/img_thumb',
	'catalogo/img_superthumb',
	'images/noticias'
); 

foreach ($dirs as $dir) {
	echo $__LIB->permisoEscritura($dir);
}

?>
</table>

<?php require_once( 'right_bottom.php' );
