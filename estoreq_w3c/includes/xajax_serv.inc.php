<?php

include_once('configure_bd.php');
include_once('configure_web.php');
include_once('clases_objetos.php');
include_once('sesion.php');
include_once('libreria.php');
include_once('clases/fun_bd.php');
include_once(_DOCUMENT_ROOT.'idiomas/'.$_SESSION["idioma"].'/main.php');
include_once(_DOCUMENT_ROOT.'includes/securimage/securimage.php');
require_once('xajax_comm.inc.php');

/** @class_definition oficial_funcionesXajax */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_funcionesXajax
{	
	function contenidos()
	{
	
		function selectProvincias($datos, $tipo, $provinciaActual = '')
		{
			global $__BD, $__LIB;
		
			$objResponse = new xajaxResponse();
			
			$campo = "provincia";
			$campoPais = "codpais";
			$divProvincia = "divprovincia";
			
			if ($tipo == "env") {
				$campo = "provincia_env";
				$campoPais = "codpais_env";
				$divProvincia = "divprovincia_env";
			}
			
			$codPais = $datos[$campoPais];
			
			$codigo = '';

			$codigo .= $__LIB->selectProvincia($campo, $codPais, $provinciaActual, $datos["ambito"]);
			
			$objResponse->addAssign($divProvincia, "innerHTML", $codigo );
			
			return $objResponse;
		}
		
		
		function cargarFormasEnvio($datos)
		{
			global $__LIB;
			
			$objResponse = new xajaxResponse();
			return $objResponse;	
		}
		
		
		function cargarFormasPago($datos)
		{
			global $__LIB;
			
			$objResponse = new xajaxResponse();
			return $objResponse;	
		}


		function abrirNavFotos($id)
		{
			$objResponse = new xajaxResponse();

			$codigo = '';
			$objResponse->addScript("xajax_loadNavFotos(".$id.")");
			$objResponse->addScript("desplegarDiv()");
			return $objResponse;	
		}

		function loadNavFotos($id)
		{
			global $__BD;

			$objResponse = new xajaxResponse();

			$codigo = '';
			$linkSig = '';
			$linkAnt = '';

			$ordenSQL = "select referencia, nomfichero, orden from articulosfotos where id=$id";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_row($result);

			$referencia = $row[0];
			$orden = $row[2];

			$fichImg = _WEB_ROOT.'catalogo/img_normal/'.$referencia.'/'.$row[1];
			
			list($width, $height, $type, $attr) = getimagesize($fichImg);
			
			$marginTop = round((450 - $height) / 2);
			$style = ' style="margin-top: '.$marginTop.'px"';
			
			
			$ordenSQL = "select id, nomfichero from articulosfotos where referencia = '$referencia' and orden < $orden order by orden desc";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_row($result);
			if ($row) {
				$fichImgAnt = _DOCUMENT_ROOT.'catalogo/img_normal/'.$referencia.'/'.$row[1];
				if (file_exists($fichImgAnt))
					$linkAnt = '<a href="#" class="botVolverCent" onclick="xajax_loadNavFotos('.$row[0].')">&nbsp;</a>';
			}

			$ordenSQL = "select id, nomfichero from articulosfotos where referencia = '$referencia' and orden > $orden order by orden";
			
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_row($result);
			if ($row) {
				$fichImgSig = _DOCUMENT_ROOT.'catalogo/img_normal/'.$referencia.'/'.$row[1];
				if (file_exists($fichImgSig))
					$linkSig = '<a href="#" class="botContinuarCent" onclick="xajax_loadNavFotos('.$row[0].')">&nbsp;</a>';
			}

			$linkCerr = '<a href="javascript:cerrarDiv()" class="botLink">'._CERRAR.'</a>';
			
			$codigo .= '<div class="links">'.$linkAnt.$linkSig.$linkCerr.'</div>';
			$codigo .= '<img src="'.$fichImg.'"'.$style.'>';
			
			$objResponse->addAssign( "contenidoNavFotos", "innerHTML", $codigo );
			return $objResponse;	
		}
	

		function validarCuenta($datos)
		{
			global $__BD;

			$objResponse = new xajaxResponse();
			
			$noNulos = formularios::datosNoNulos();
			
			$noNulos = array_merge($noNulos["general"], $noNulos["dirfact"]);
			
			if (!trim($datos["codpais"]))
				$noProv = true;
			else
				$noProv = false;
			
			if (!trim($datos["codpais_env"]))
				$noProvEnv = true;
			else
				$noProvEnv = false;
			
			eqDebug::log($noNulos);
			
			foreach ($noNulos as $noNulo) {
				if ($noNulo == 'provincia' && $noProv) continue;
				if ($noNulo == 'provincia_env' && $noProvEnv) continue;
				if (!trim($datos[$noNulo]))
					$objResponse->addAssign("campo_".$noNulo, "className", 'warning');
				else
					$objResponse->addAssign("campo_".$noNulo, "className", 'normal');
			}
			
			
			eqDebug::log($datos);
			eqDebug::log($noNulos);
//			$objResponse->addAssign( "contenidoNavFotos", "innerHTML", $codigo );
			return $objResponse;	
		}
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_funcionesXajax */
class funcionesXajax extends oficial_funcionesXajax {};

$iface_funcionesXajax = new funcionesXajax;
$iface_funcionesXajax->contenidos();

$xajax->processRequests();
?>
