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

require_once( '../includes/libreria/fun_bd.php' );
require_once( '../includes/configure_web.php' );
require_once("../includes/configure_bd.php");
require_once( 'top_left.php' );
require_once( '../idiomas/esp/main.php' );

$procesar = '';
if (isset($CLEAN_POST["procesar"]))
	$procesar = $CLEAN_POST["procesar"];


?>

<h1><?php echo _INSTALL_TV ?></h1>

<p>
<?php

	echo $__LIB->fasesInstalacion('datos');
	echo '<h2>'._FASES_INS_DATOS.'</h2>';
	
	echo _INS_TEXTO_3;
	
	echo '<p><a class="button" href="javascript:formVolcar.submit()"><span>'._INS_COMPROBAR_3.'</span></a>';
	
	if ($procesar) {
	
		$error = '';
		
		if (!file_exists('sql/common_'._DB_TYPE.'.sql'))
			$error = _NO_FICH_SQL.': common_'._DB_TYPE.'.sql';
		
		if (!$error)
			$__LIB->volcarBD('sql/common_'._DB_TYPE.'.sql', _DB_DATABASE, true);

		if (!$error)
			if (!$__LIB->comprobarBD(_DB_TYPE))
				$error = _ERROR_CREACION_BD;
		
		echo '<br/><br/>';
		
		echo '<h2>'._RESULTADO.'</h2>';
		if (!$error) {
			echo '<div class="msgOk">'._INS_OK_3.'</div>';
			echo '<br/><br/><a class="button" href="../index.php"><span>'._INS_SIGUIENTE_3.'</span></a>';
		}
		else
			echo '<p><div class="msgError">'.$error.'</div>';
	}
	
?>

	<form action="paso3.php" method="post" name="formVolcar">
		<input type="hidden" name="procesar" value="1">
	</form>

<?php require_once( 'right_bottom.php' );
