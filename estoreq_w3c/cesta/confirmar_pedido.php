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

/** @class_definition oficial_confirmarPedido */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_confirmarPedido
{

	/** Crea el boton de pago para redireccionar a la pasarela, si es el caso
	*/
	function botonProcesar($codPago)
	{
		global $__LIB;
			
		$nextUrl = _WEB_ROOT_SSL_L.'cesta/crear_pedido.php';
		$codigo = '<a class="button" href="'.$nextUrl.'"><span>'._CREAR_PEDIDO.'</span></a>';
		$error = '';
		
		// Si existe el fichero datos.php dentro del directorio de forma de pago
		if (file_exists(_DOCUMENT_ROOT.'cesta/sistpago/'.$codPago.'/datos.php')) {
			
			include(_DOCUMENT_ROOT.'cesta/sistpago/'.$codPago.'/datos.php');
			$className = $codPago.'Datos';
			
			if (class_exists($className)) {
				
				$iface_pago = new $className;
				$codigo = $iface_pago->codigoProcesar();
				
			}
			else
				$error = _ERROR_PAGO;
		}
		
		if ($error) {
			echo '<div class="msgError">'.$error.'</div>'; 
			echo '</div>';
			include("../includes/right_bottom.php");
			exit;
		}
		
		$codigo = '<p><br/><br/>'.$codigo;
		return $codigo;
	}


	// Muestra un resumen de todos los datos del pedido previo a la cofirmacion del cliente
	function contenidos() 
	{	
		global $__BD, $__CAT, $__LIB, $__CLI;
		global $CLEAN_GET, $CLEAN_POST;
		
		$__LIB->comprobarCliente(true);
		
		$codigo = '';
		$codigo .= '<h1>'._CREAR_PEDIDO.'</h1>';
		$codigo .= '<div class="cajaTexto">';
	
		$codigo .= $__LIB->fasesPedido('confirmacion');
		
		if (!isset($_SESSION["pedido"])) {
			$codigo .= '<a href="'._WEB_ROOT_SSL_L.'cuenta/login.php">'._PEDIDO_INCORRECTO.'</a>';
			include("../includes/right_bottom.php");
			exit;
		}
		
		
		if (isset($CLEAN_POST["contacto"])) {
			
			$error = $__LIB->comprobarPedido($_SESSION["pedido"]["datosEnv"], $CLEAN_POST);	
			if ($error) {
				$codigo .= '<div class="msgError">'.$error.'</div>';
				$codigo .= '<a href="javascript:history.go(-1)">'._VOLVER.'</a>';
				echo $codigo;
				include("../includes/right_bottom.php");
				exit;
			}
			
			// Los datos de pago vienen del formulario anterior
			$_SESSION["pedido"]["datosPag"] = $CLEAN_POST;
			
			// Validado el post, cargamos la misma pagina sin el mismo (para evitar repetir el POST)
			echo '<script type="text/javascript">window.location = "'._WEB_ROOT_SSL_L.'cesta/confirmar_pedido.php"</script>';
			exit;
		}
		
			
		$codigo .= '<p>'._CONFIRME_PEDIDO;

		// Los datos de envio vienen de la sesion
		$datos = $_SESSION["pedido"]["datosEnv"];		
		
		// Forma de envio
		$result = $__BD->db_query("select descripcion, pvp, codimpuesto from formasenvio where codenvio = '".$datos["codenvio"]."'");
		$row = $__BD->db_fetch_array($result);
		$formaEnvio = $__LIB->traducir("formasenvio", "descripcion", $datos["codenvio"], $row["descripcion"]);
		
		// Total
		$total = $_SESSION["cesta"]->total();
		
		// Forma de pago
		$datosPago = $_SESSION["pedido"]["datosPag"];
		
		$ordenSQL = "select codpago, descripcion, descripcionlarga from formaspago where codpago = '".$datosPago["codpago"]."'";
		$result = $__BD->db_query($ordenSQL);
		$row = $__BD->db_fetch_array($result);
		$codPago = $datosPago["codpago"];
		$formaPago = $__LIB->traducir("formaspago", "descripcion", $datosPago["codpago"], $row["descripcion"]);
		$descPago = $__LIB->traducir("formaspago", "descripcionlarga", $datosPago["codpago"], $row["descripcionlarga"]);
		$paso = 0;

									
		// 1. Impresion del Pedido
		$codigo .= '<h2><span class="titApartadoText">'._PEDIDO.'</span></h2>';
		$codigo .= $_SESSION["cesta"]->imprime_cesta_pedido($datos["codenvio"], $codPago);
		
		$codigo .= '<br/>';
		$codigo .= '<a href="'._WEB_ROOT_SSL_L.'general/cesta.php">'._MODIFICAR.'</a>';
		$codigo .= '<br/>';
		$codigo .= '<br/>';
		
		
		//2. Datos de envio
		$codigo .= '<h2><span class="titApartadoText">'._DATOS_ENVIO.'</span></h2>';
		
		$codigo .= '<p><b>'._FORMA_ENVIO.'</b><br/>';
		$codigo .= $formaEnvio;
		
		$datos = $_SESSION["pedido"]["datosEnv"];
		$pais = $__BD->db_valor("select nombre from paises where codpais = '".$datos["codpais_env"]."'");
		
		$codigo .= '<p><b>'._DIRECCION.'</b><br/>';
		$codigo .= $datos["contacto"].' '.$datos["apellidos"].'<br/>';
		if (strlen(trim($datos["empresa"])) > 0)
			$codigo .= $datos["empresa"].'<br/>';
		$codigo .= $datos["direccion_env"].'<br/>';
		$codigo .= $datos["codpostal_env"].' '.$datos["ciudad_env"].'<br/>';
		$codigo .= $datos["provincia_env"].'<br/>';
		$codigo .= $pais;
		
		$codigo .= '<br/><br/>';
		$codigo .= '<a href="'._WEB_ROOT_SSL_L.'cesta/datos_envio.php">'._MODIFICAR.'</a>';
		
		
		
		// 3. Datos de Pago
		$codigo .= '<h2><span class="titApartadoText">'._DATOS_PAGO.'</span></h2>';
		
		$codigo .= '<p><b>'._FORMA_PAGO.'</b><br/>';
		$codigo .= $formaPago;
		
		$codigo .= '<p><b>'._DIRECCION.'</b><br/>';
		
		$datos = $_SESSION["pedido"]["datosPag"];
		$pais = $__BD->db_valor("select nombre from paises where codpais = '".$datos["codpais"]."'");
		$codigo .= $datos["contacto"].' '.$datos["apellidos"].'<br/>';
		
		if ($__LIB->esTrue($_SESSION["opciones"]["solicitarnif"])) {
			$codigo .= _NIF.' '.$datos["nif"].'<br/>';
		}
		
		if (strlen(trim($datos["empresa"])) > 0)
			$codigo .= $datos["empresa"].'<br/>';
		$codigo .= $datos["direccion"].'<br/>';
		$codigo .= $datos["codpostal"].' '.$datos["ciudad"].'<br/>';
		$codigo .= $datos["provincia"].'<br/>';
		$codigo .= $pais;
		
		$codigo .= '<br/><br/>';
		$codigo .= '<a href="'._WEB_ROOT_SSL_L.'cesta/datos_pago.php">'._MODIFICAR.'</a>';
		
		
		$nextUrl = 'crear_pedido.php';
		
		$codigo .= $this->botonProcesar($codPago);																	
		
		$codigo .= '</div>';
		
		echo $codigo;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_confirmarPedido */
class confirmarPedido extends oficial_confirmarPedido {};

$iface_confirmarPedido = new confirmarPedido;
$iface_confirmarPedido->contenidos();

?>

<?php include("../includes/right_bottom.php") ?>