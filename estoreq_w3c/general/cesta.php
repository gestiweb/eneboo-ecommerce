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

/** @class_definition oficial_verCesta */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_verCesta
{	
	function contenidos() 
	{
		global $CLEAN_GET, $__LIB;
		
		$codigo = '';
		echo '<h1>'._MI_CESTA.'</h1>';
		echo '<div class="cajaTexto">';
	
		if ($__LIB->esTrue($_SESSION["opciones"]["noautoaccount"]) && !isset($_SESSION["codCliente"])) {
			echo _DEBES_LOGIN;
			echo '<br/><br/>';
			echo '<a class="button" href="'._WEB_ROOT_SSL.'cuenta/login.php"><span>'._MI_CUENTA.'</span></a>';
			echo '</div>';
			return;
		}

		$ref = '';
		$accion = '';
		
		if (isset($CLEAN_GET["ref"]))
			$ref = $CLEAN_GET["ref"];
			
		if (isset($CLEAN_GET["acc"]))
			$accion = $CLEAN_GET["acc"];
		
		if ($accion == "add") {	
			$_SESSION["cesta"]->introduce_articulo($ref);
		}
		
		if ($accion == "del") {	
			$_SESSION["cesta"]->elimina_articulo($ref);
		}
		
		$tieneAlgo = $_SESSION["cesta"]->imprime_cesta();
	
		echo $this->jsReloadCesta();
		
		if (!$tieneAlgo)
			echo _CESTA_VACIA;

		echo '</div>';
		
		echo $codigo;
	}
	
	function jsReloadCesta()
	{
		$codigo = '<script type="text/javascript">$(document).ready( function() { xajax_reloadCesta() })</script>';
		return $codigo;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_verCesta */
class verCesta extends oficial_verCesta {};

$iface_verCesta = new verCesta;
$iface_verCesta->contenidos();

?>


<?php include("../includes/right_bottom.php") ?>