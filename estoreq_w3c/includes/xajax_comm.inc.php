<?php

require_once( 'xajax/xajax.inc.php' );

$xajax = new xajax( _WEB_ROOT.'includes/xajax_serv.inc.php' );

/** @class_definition oficial_declareFuncionesXajax */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_declareFuncionesXajax
{	
	function contenidos()
	{
		$numFunciones = 0;

		$funciones[$numFunciones++] = "selectProvincias";
		$funciones[$numFunciones++] = "cargarFormasEnvio";
		$funciones[$numFunciones++] = "cargarFormasPago";
		$funciones[$numFunciones++] = "abrirNavFotos";
		$funciones[$numFunciones++] = "loadNavFotos";
		$funciones[$numFunciones++] = "validarCuenta";

		return $funciones;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_declareFuncionesXajax */
class declareFuncionesXajax extends oficial_declareFuncionesXajax {};

$iface_declareFuncionesXajax = new declareFuncionesXajax;
$funciones = $iface_declareFuncionesXajax->contenidos();

if ($funciones)
	foreach($funciones as $funcion)
		$xajax->registerFunction($funcion);
?>