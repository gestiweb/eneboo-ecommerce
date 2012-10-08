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

/** @class_definition oficial_salirSesion */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_salirSesion
{
	// Se cierra la sesion del cliente
	function contenidos() 
	{
		global $CLEAN_POST;
	
		echo '<h1>'._MI_CUENTA.'</h1>';
		
		echo '<div class="cajaTexto">';

		$salir = '';
		if (isset($CLEAN_POST["salir"]))
			$salir = $CLEAN_POST["salir"];
		
		if ($salir) {
			unset($_SESSION["codCliente"]);
			unset($_SESSION["key"]);
			unset($_SESSION["cesta"]);
			$_SESSION["cesta"] = new cesta();
			
			echo _UNMOMENTO.'
			<script languaje="javascript">
				window.location = \''._WEB_ROOT.'\';
			</script>';
			include("../includes/right_bottom.php");
			exit;
		}
		else {
			echo _CONFIRM_SALIR;
			echo '
				<form action="cuenta/salir_sesion.php" method="post"><div>
				<input size="30" type="hidden" name="salir" value="1"/>
				<br/><br/>
				<button type="submit" value="'._SALIR_SESION.'" class="submitBtn"><span>'._SALIR_SESION.'</span></button>
				<a class="button" href="javascript:history.go(-1)"><span>'._VOLVER.'</span></a>
				</div></form>';
		}
		
		echo '</div>';
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_salirSesion */
class salirSesion extends oficial_salirSesion {};

$iface_salirSesion = new salirSesion;
$iface_salirSesion->contenidos();

?>

<?php include("../includes/right_bottom.php") ?>