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

/** @class_definition oficial_infoGeneral */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_infoGeneral
{	
	// Muestra la informacion general
	function contenidos() 
	{
		global $CLEAN_GET, $__LIB, $__BD;

		$codigo = "";
		if (isset($CLEAN_GET["cod"]))
			$codigo = $CLEAN_GET["cod"];	
		
		$ordenSQL = "select titulo, texto from infogeneral where publico = true and codigo = '$codigo'";
		$row = $__BD->db_row($ordenSQL);
	
		if (!$row) {
			echo _ERROR_INFO_GENERAL;
			include("../includes/right_bottom.php");
			exit;
		}
		
		$titulo = $__LIB->traducir("infogeneral", "titulo", $codigo, $row[0]);
		$texto = $__LIB->traducir("infogeneral", "texto", $codigo, $row[1]);
		
		echo '<h1>'.$titulo.'</h1>';
		
		echo '<div class="cajaTexto">';
		echo nl2br($texto);
		echo '</div>';
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_infoGeneral */
class infoGeneral extends oficial_infoGeneral {};

$iface_infoGeneral = new infoGeneral;
$iface_infoGeneral->contenidos();

?>


<?php include("../includes/right_bottom.php") ?>