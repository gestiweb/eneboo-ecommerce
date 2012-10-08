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

error_reporting(E_USER_NOTICE);

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
			
		$nextUrl = 'crear_pedido.php';
		$codigo = '<a class="botContinuar" href="'.$nextUrl.'">'._CREAR_PEDIDO.'</a>';		
		
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
		
		$codigo = '<p><br><br>'.$codigo;
		return $codigo;
	}


	// Muestra un resumen de todos los datos del pedido previo a la cofirmacion del cliente
	function contenidos() 
	{	
		global $__BD, $__CAT, $__LIB, $__CLI;
		global $CLEAN_GET, $CLEAN_POST;
		
		$__LIB->comprobarCliente(true);
		
		echo '<div class="titPagina">'._CREAR_PEDIDO.'</div>';
		echo '<div class="cajaTexto">';
	
		echo $__LIB->fasesPedido('confirmacion');
		
		if (!isset($_SESSION["pedido"])) {
			echo '<a href="'._WEB_ROOT.'cuenta/login.php">'._PEDIDO_INCORRECTO.'</a>';
			include("../includes/right_bottom.php");
			exit;
		}
		
		$error = $__LIB->comprobarPedido($_SESSION["pedido"]["datosEnv"], $CLEAN_POST);	
		if ($error) {
			echo '<div class="msgError">'.$error.'</div>';
			echo '<a href="javascript:history.go(-1)">'._VOLVER.'</a>';
			include("../includes/right_bottom.php");
			exit;
		}
		
		// Los datos de pago vienen del formulario anterior
		$_SESSION["pedido"]["datosPag"] = $CLEAN_POST;
			
		echo '<p>'._CONFIRME_PEDIDO;
		
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
		echo '<div class="titApartado"><span class="titApartadoText">'._PEDIDO.'</span></div>';
		$_SESSION["cesta"]->imprime_cesta_pedido($datos["codenvio"], $codPago);
		
		//2. Datos de envio
		echo '<div class="titApartado"><span class="titApartadoText">'._DATOS_ENVIO.'</span></div>';
		
		echo '<p><b>'._FORMA_ENVIO.'</b><br>';
		echo $formaEnvio;
		
		$datos = $_SESSION["pedido"]["datosEnv"];
		$pais = $__BD->db_valor("select nombre from paises where codpais = '".$datos["codpais_env"]."'");
		
		echo '<p><b>'._DIRECCION.'</b><br>';
		echo $datos["nombre_env"].' '.$datos["apellidos_env"].'<br>';
		if (strlen(trim($datos["empresa_env"])) > 0)
			echo $datos["empresa_env"].'<br>';
		echo $datos["direccion_env"].'<br>';
		echo $datos["codpostal_env"].' '.$datos["ciudad_env"].'<br>';
		echo $datos["provincia_env"].'<br>';
		echo $pais;
		
		
		// 3. Datos de Pago
		echo '<div class="titApartado"><span class="titApartadoText">'._DATOS_PAGO.'</span></div>';
		
		echo '<p><b>'._FORMA_PAGO.'</b><br>';
		echo $formaPago;
		
		echo '<p><b>'._DIRECCION.'</b><br>';
		
		$datos = $_SESSION["pedido"]["datosPag"];
		$pais = $__BD->db_valor("select nombre from paises where codpais = '".$datos["codpais"]."'");
		echo $datos["nombre"].' '.$datos["apellidos"].'<br>';
		
		if ($__LIB->esTrue($_SESSION["opciones"]["solicitarnif"])) {
			echo _NIF.' '.$datos["nif"].'<br>';
		}
		
		if (strlen(trim($datos["empresa"])) > 0)
			echo $datos["empresa"].'<br>';
		echo $datos["direccion"].'<br>';
		echo $datos["codpostal"].' '.$datos["ciudad"].'<br>';
		echo $datos["provincia"].'<br>';
		echo $pais;
		
		$nextUrl = 'crear_pedido.php';
		
		echo $this->botonProcesar($codPago);																	
		
		echo '</div>';
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