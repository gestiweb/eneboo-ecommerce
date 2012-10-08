<?php

/*
include_once('configure_bd.php');
include_once('configure_web.php');
include_once('clases_objetos.php');
include_once('sesion.php');
include_once('libreria.php');
include_once('clases/fun_bd.php');
include_once(_DOCUMENT_ROOT.'idiomas/'.$_SESSION["idioma"].'/main.php');
include_once(_DOCUMENT_ROOT.'includes/securimage/securimage.php');
*/

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
			$divProvincia = "spanProvincia";
			
			if ($tipo == "env") {
				$campo = "provincia_env";
				$campoPais = "codpais_env";
				$divProvincia = "spanProvincia_env";
			}
			
			$codPais = $datos[$campoPais];
			$ambito = '';
			if (isset($datos["ambito"]))
				$ambito = $datos["ambito"];
			
			$codigo = '';

			$codigo .= $__LIB->selectProvincia($campo, $codPais, $provinciaActual, $ambito);
			
			$objResponse->assign($divProvincia, "innerHTML", $codigo );
			
			if ($ambito == "cesta_envio") {
			
				$codigoFormasEnvio = $__LIB->formasEnvio($codPais, $provinciaActual);
				$codigoContinuar = '';
			
				if ($codigoFormasEnvio)
					$codigoContinuar = '<a class="button" href="javascript:document.datosDirEnv.submit()"><span>'._CONTINUAR.'<span></a>';
				else
					$codigoFormasEnvio = _NO_FORMAS_ENVIO;
					
				$objResponse->assign("divContinuar", "innerHTML", $codigoContinuar);
				$objResponse->assign("datosEnvio", "innerHTML", $codigoFormasEnvio);
			}
			
			if ($ambito == "cesta_pago") {
			
				$codigoFormasPago = $__LIB->formasPago($codPais, $provinciaActual);
				$codigoContinuar = '';
			
				if ($codigoFormasPago)
					$codigoContinuar = '<a class="botContinuar" href="javascript:document.datosDir.submit()">'._CONTINUAR.'</a>';
				else
					$codigoFormasPago = _NO_FORMAS_PAGO;
					
				$objResponse->assign("divContinuar", "innerHTML", $codigoContinuar);
				$objResponse->assign("datosPago", "innerHTML", $codigoFormasPago);
			}
			
			return $objResponse;
		}
		
		
		function cargarFormasEnvio($datos)
		{
			global $__LIB;
			
			$objResponse = new xajaxResponse();
			
			$codPais = $datos["codpais_env"];
			$provincia = $datos["provincia_env"];
			
			$codigoFormasEnvio = $__LIB->formasEnvio($codPais, $provincia);
			$codigoContinuar = '';
		
			if ($codigoFormasEnvio)
				$codigoContinuar = '<a class="button" href="javascript:document.datosDirEnv.submit()"><span>'._CONTINUAR.'<span></a>';
			else
				$codigoFormasEnvio = _NO_FORMAS_ENVIO;
 			
			$objResponse->assign("divContinuar", "innerHTML", $codigoContinuar);
			$objResponse->assign("datosEnvio", "innerHTML", $codigoFormasEnvio);
			return $objResponse;	
		}
		
		
		function cargarFormasPago($datos)
		{
			global $__LIB;
			
			$objResponse = new xajaxResponse();
			
			$codPais = $datos["codpais"];
			$provincia = $datos["provincia"];
			
			$codigoFormasPago = $__LIB->formasPago($codPais, $provincia);
			$codigoContinuar = '';
		
			if ($codigoFormasPago)
				$codigoContinuar = '<a class="button" href="javascript:document.datosDir.submit()"><span>'._CONTINUAR.'<span></a>';
			else
				$codigoFormasPago = _NO_FORMAS_PAGO;
 			
			$objResponse->assign("divContinuar", "innerHTML", $codigoContinuar);
			$objResponse->assign("datosPago", "innerHTML", $codigoFormasPago);
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
			
			foreach ($noNulos as $noNulo) {
				if ($noNulo == 'provincia' && $noProv) continue;
				if ($noNulo == 'provincia_env' && $noProvEnv) continue;
				if (!trim($datos[$noNulo]))
					$objResponse->assign("campo_".$noNulo, "className", 'warning');
				else
					$objResponse->assign("campo_".$noNulo, "className", 'normal');
			}
			
			
			return $objResponse;	
		}
	
		function enviarCorreoAamigo($datos)
		{
			global $__LIB;
		
			$objResponse = new xajaxResponse();
			
			$email = $datos["email"];
			$nombre = $datos["nombre"];
			$codigo = '';
			
			$objResponse->assign("avisoEnviarAamigo", "innerHTML", '');
			
			if (!$nombre || !$email) {
				$codigo .= '<div class="aviso">'._RELLENAR_TODOS_CAMPOS.'</div>';
				$objResponse->assign("avisoEnviarAamigo", "innerHTML", $codigo);
				return $objResponse;	
			}
			
			if (!$__LIB->checkEmailAddress($email)) {
				$codigo .= '<div class="aviso">'._EMAIL_NOVALIDO.'</div>';
				$objResponse->assign("avisoEnviarAamigo", "innerHTML", $codigo);
				return $objResponse;
			}
			
			$texto = $datos["texto"];
			$ref = $datos["ref"];
			$link = _WEB_ROOT.'catalogo/articulo.php?ref='.$ref;
			
			$asunto = $nombre.' '._ENVIA_RECOMENDACION;
			$codigo = $texto."\n\n".$link;
			
			$__LIB->enviarMail($email, $asunto, $codigo);
			
			$codigo = '';
			$codigo .= '<h1>'._ENVIAR_AMIGO.'</h1>';
			$codigo .= '<br/><br/>';
			$codigo .= _RECOMENDACION_ENVIADA;
			$codigo .= '<p><a href="#" onclick="$.fancybox.close();return false;" class="botLink">'._CERRAR.'</a>';

			$objResponse->assign("recomendarArticulo", "innerHTML", $codigo);
			return $objResponse;	
		}
		
		function enviarCorreoComentario($datos)
		{
			global $__LIB;
			
			$objResponse = new xajaxResponse();
			$email = $datos["email"];
			$nombre = $datos["nombre"];
			$texto = $datos["texto"];
			$ref = $datos["ref"];
			$codigo = '';
			
			if (!$texto) {
				$codigo .= '<div class="aviso">'._RELLENAR_TODOS_CAMPOS.'</div>';
				$objResponse->assign("avisoComentario", "innerHTML", $codigo);
				return $objResponse;	
			}
			
			$link = _WEB_ROOT.'catalogo/articulo.php?ref='.$ref;
			
			$asunto = _ENVIA_COMENTARIOS.' Ref. '.$ref;
			$codigo = _NOMBRE.' '.$nombre."\n\n";
			$codigo = _EMAIL.' '.$email."\n\n";
			$codigo = _COMENTARIOS.' '.$texto."\n\n";
			$codigo = $texto."\n\n".$link;
			
			$emailWM = $_SESSION["opciones"]["emailwebmaster"];
			
			$__LIB->enviarMail($emailWM, $asunto, $codigo);
			
			$codigo = '';
			$codigo .= '<h1>'._ENVIAR_COMENTARIO.'</h1>';
			$codigo .= '<br/><br/>';
			$codigo .= _COMENTARIO_ENVIADO;
			$codigo .= '<p><a href="#" onclick="$.fancybox.close();return false;" class="botLink">'._CERRAR.'</a>';
			
			$objResponse->assign("comentarArticulo", "innerHTML", $codigo);
			return $objResponse;	
		}
	

		function verificarDto($datos)
		{
			global $__LIB, $__BD, $__CAT;
			
			$objResponse = new xajaxResponse();
			
			$hoy = date("Y-m-d");
			$codDescuento = $datos["coddescuento"];
			$ordenSQL = "select * from codigosdescuento where codigo='$codDescuento' and activo=true and (caducidad is null or caducidad >= '$hoy')";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_array($result);
			
			if (!$row["id"]) {
				$objResponse->assign("msgDescuento", "innerHTML", '<span class="nok">'._DTO_NOVALIDO.'</span>');
				return $objResponse;	
			}

			if ($row["dtopor"] > 0)
				$lblDto = $row["dtopor"].'%';
			if ($row["dtolineal"] > 0)
				$lblDto = $__CAT->precioDivisa($row["dtolineal"]);
				
			$codigo = $row["descripcion"].' ('.$lblDto.')';
			$objResponse->assign("msgDescuento", "innerHTML", '<span class="ok">'.$codigo.'</span>');
			return $objResponse;	
		}
		
		function reloadCesta()
		{
			$cesta = new modCesta();
			$codigo = $cesta->innerContenidos();
			
			$objResponse = new xajaxResponse();
			$objResponse->assign("cestaLateral", "innerHTML", $codigo);
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

$xajax->processRequest();
?>
