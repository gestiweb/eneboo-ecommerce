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

/** @class_definition oficial_faq */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_faq
{	
	// Muestra las preguntas frecuentes
	function contenidos() 
	{
		global $CLEAN_POST, $__LIB, $__BD;

		echo '<a name="home"></a>';
		
		echo '<h1>'._FAQ.'</h1>';
		echo '<div class="cajaTexto">';
		
		if (!$__LIB->esTrue($_SESSION["opciones"]["activarfaq"])) {
			echo _SECCION_NO_DISPONIBLE;
			include("../includes/right_bottom.php");
		}	
			
		$ordenSQL = "select id, pregunta, respuesta from faqs where publico = true order by orden";
		
		$result = $__BD->db_query($ordenSQL);
		
		$menu = '';
		$texto = '';
		
		while ($row = $__BD->db_fetch_array($result)) {	
			$pregunta = $__LIB->traducir("faqs", "pregunta", $row["id"], $row["pregunta"]);
			$respuesta = $__LIB->traducir("faqs", "respuesta", $row["id"], $row["respuesta"]);
			$menu .= '<a href="general/faq.php#'.$row[0].'">'.$pregunta.'</a><br/>';
			$texto .= '<a name="'.$row[0].'"></a>';
			$texto .= '<h2>'.$pregunta.'</h2>';
			$texto .= nl2br($respuesta);
			$texto .= '<p><br/><a class="botLink" href="general/faq.php#home">'._INICIO.'</a></p>';
		}
		
		echo $menu;
		echo $texto;
		
		echo '</div>';
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_faq */
class faq extends oficial_faq {};

$iface_faq = new faq;
$iface_faq->contenidos();

?>


<?php include("../includes/right_bottom.php") ?>