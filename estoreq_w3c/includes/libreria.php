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

/** @class_definition oficial_funLibreria */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

// Funciones generales
class oficial_funLibreria
{

	// Crea el codigo para el combo-box de los paises de la base de datos
	function navTop()
	{
		$codigo = '';
		
		$codigo .= '<a class="inv" href="">'.strtolower(_INICIO).'</a>';
		$codigo .= '&nbsp;&bull;&nbsp;';
		$codigo .= '<a class="inv" href="'._WEB_ROOT_SSL_L.'cuenta/login.php">'.strtolower(_MI_CUENTA).'</a>';
		$codigo .= '&nbsp;&bull;&nbsp;';
		$codigo .= '<a class="inv" href="'._WEB_ROOT_L.'general/cesta.php">'.strtolower(_VER_CESTA).'</a>';
		$codigo .= '&nbsp;&bull;&nbsp;';
		$codigo .= '<a class="inv" href="'._WEB_ROOT_SSL_L.'cuenta/favoritos.php">'.strtolower(_FAVORITOS).'</a>';
		$codigo .= '&nbsp;&bull;&nbsp;';
		$codigo .= '<a class="inv" href="'._WEB_ROOT_L.'general/contactar.php">'.strtolower(_CONTACTAR).'</a>';

		return $codigo;
	}

	// Crea el codigo para el combo-box de los paises de la base de datos
	// tipo: facturacion o envio
	function selectPais($formName, $tipo, $paisActual = "")
	{
		global $__BD;

		$nomSelect = "codpais";
		if ($tipo == "env")
		$nomSelect = "codpais_env";

		$codigo = "";
		$codigo .= '<select id="campo_'.$nomSelect.'" name="'.$nomSelect.'" onchange="xajax_selectProvincias(xajax.getFormValues(\''.$formName.'\'), \''.$tipo.'\'); return false;">';

		$codigo .= '<option value="">-- '._SELECCIONAR.' --</option>';

		$ordenSQL = "select codpais, nombre from paises order by nombre";
		//		$ordenSQL = "select codpais, nombre from paises where codzona is not null order by nombre";
		$result = $__BD->db_query($ordenSQL);

		while($pais = $__BD->db_fetch_array($result)) {

			$codigo .= '<option ';
			if ($pais["codpais"] == $paisActual) $codigo .= 'selected ';
			$codigo .= 'value="'.$pais["codpais"].'">'.$pais["nombre"];
			$codigo .= '</option>';
		}

		$codigo .= '</select>';

		return $codigo;
	}

	function selectProvincia($nomCampo, $codPais, $provinciaActual = "", $ambito = "")
	{
		global $__BD;

		if (!$codPais)
		return '<input type="text" disabled="disables" value="'._SELEC_PAIS.'"/>';

		$codigo = '';
		$listProvincias = '';

		$ordenSQL = "select idprovincia, provincia from provincias where codpais='$codPais' order by provincia";
		$result = $__BD->db_query($ordenSQL);

		while($provincia = $__BD->db_fetch_array($result)) {
			$listProvincias .= '<option ';
			if (strtolower($provincia["provincia"]) == strtolower($provinciaActual)) $listProvincias .= 'selected ';
			$listProvincias .= 'value="'.$provincia["provincia"].'">'.$provincia["provincia"];
			$listProvincias .= '</option>';
		}

		// Lista
		if ($listProvincias) {

			$onchange = '';

			if ($ambito == "cesta_envio")
			$onchange = ' onchange="xajax_cargarFormasEnvio(xajax.getFormValues(\'datosDirEnv\'))"';

			if ($ambito == "cesta_pago")
			$onchange = ' onchange="xajax_cargarFormasPago(xajax.getFormValues(\'datosDir\'))"';

			$listProvincias = '<option value="">-- '._SELECCIONAR.' --</option>'.$listProvincias;
			$codigo .= '<select id="campo_'.$nomCampo.'" name="'.$nomCampo.'" '.$onchange.'>';
			$codigo .= $listProvincias;
			$codigo .= '</select>';
		}
		// Input normal
		else
		$codigo .= '<input input size="30" type="text" id="campo_'.$nomCampo.'" name="'.$nomCampo.'" value="'.$provinciaActual.'">';

		return $codigo;
	}

	// Rellena de ceros a la izqierda un string
	function cerosIzquierda($numero, $totalCifras)
	{
		$ret = $numero;
		$numCeros = $totalCifras - strlen($numero);
		for ($i = 0 ; $i < $numCeros; $i++)
		$ret = "0".$ret;
		return $ret;
	}

	// Imprime un pedido, albaran, etc
	function imprimirDocFacturacion_old($tipoDoc, $id)
	{
		global $__BD, $__CAT, $__LIB;

		$codigo = "";

		switch ($tipoDoc) {
			case "pedido":
				$tabla = "pedidoscli";
				$tablaL = "lineaspedidoscli";
				$campoId = "idpedido";
				$mostrarEnvio = 1;
				$mostrarPago = 1;
				break;
			case "albaran":
				$tabla = "albaranescli";
				$tablaL = "lineasalbaranescli";
				$campoId = "idalbaran";
				$mostrarEnvio = 1;
				$mostrarPago = 1;
				break;
			case "factura":
				$tabla = "facturascli";
				$tablaL = "lineasfacturascli";
				$campoId = "idfactura";
				$mostrarEnvio = 0;
				$mostrarPago = 1;
				break;
		}

		$ordenSQL = "select * from $tabla where $campoId = $id";
		$result = $__BD->db_query($ordenSQL);
		$datos = $__BD->db_fetch_array($result);

		$ordenSQL = "select *, pvpunitarioiva as pvp from $tablaL where $campoId = $id";
		$datosLin = $__BD->db_query($ordenSQL);

		$codigo .= '<h2 class="top">'.constant(strtoupper('_'.$tipoDoc)).' '.$datos["codigo"].'</h2>';

		$codigo .= '<p><b>'._FECHA.'</b>: '.date("d-m-Y", strtotime($datos["fecha"]));

		if ($tipoDoc == 'pedido') {
			$codigo .= '<br/><b>'._ENVIADO.'</b>: '.$datos["servido"];
			if ($datos["servido"] <> 'No') {
				$fechaEnvio = date("d-m-Y", strtotime($datos["fechasalida"]));
				if ($fechaEnvio > '01-01-1970')
				$codigo .= '&nbsp;&nbsp;&nbsp;&nbsp;<b>'._FECHA_ENVIO.'</b>: '.date("d-m-Y", strtotime($datos[6]));
			}
		}

		$codigo .='<br/><br/>';

		$codigo .= '<table class="cesta" cellspacing="5">';
		
		$codigo .= '<tr>';
		$codigo .= '<th align="left" class="desc">'._ARTICULO.'</th>';
				
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])) {
					$codigo .= '<th align="left">'._PRECIO.'</th>';
					$codigo .= '<th align="left">'._IVA.'</th>';
				}
				else
					$codigo .= '<th align="left">'._PRECIO.'</th>';
			
		$codigo .= '<th align="left">'._CANTIDAD.'</th>
					<th align="left">'._IMPORTE.'</th>';
		
		$codigo .= '</tr>';

		while ($row = $__BD->db_fetch_array($datosLin)) {

			$descripcion = $this->traducir("articulos", "descripcion", $row["referencia"], $row["descripcion"]);

			$precio = $__CAT->precioNeto($row, false, true, true);
			$impuesto = $__CAT->impuestoArticulo($row, true);
			$pvpUnitario = $precio + $impuesto;
			$pvpTotal = $pvpUnitario * $row["cantidad"];

			$codigo .= '<tr">';
			$codigo .= '<td class="desc">'.$descripcion.'</td>';

			if ($this->esTrue($_SESSION["opciones"]["desglosariva"])) {
				$codigo .= '<td>'.$__CAT->precioDivisa($precio).'</td>';				
				$codigo .= '<td>'.$__CAT->precioDivisa($impuesto).'</td>';
			}
			else
				$codigo .= '<td>'.$__CAT->precioDivisa($precio + $impuesto).'</td>';

			$codigo .= '<td>'.round($row["cantidad"]).'</td>';
			$codigo .= '<td>'.$__CAT->precioDivisa($pvpTotal).'</td>';
			$codigo .= '</tr>';
		}

		$codigo .= '<tr class="totales">';
		$codigo .= '<td colspan="3" class="total">'._TOTAL.'</td>';;
		if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
			$codigo .= '<td>&nbsp;</td>';
		$codigo .= '<td>'.$__CAT->precioDivisa($datos["total"]).'</td>';
		$codigo .= '</tr>'
				;
		$codigo .= '</table>';
		


		$pais = $__BD->db_valor("select nombre from paises where codpais = '".$datos["codpais"]."'");
		$paisEnv = $__BD->db_valor("select nombre from paises where codpais = '".$datos["codpaisenv"]."'");

		$formaPago = $__BD->db_valor("select descripcion from formaspago where codpago = '".$datos["codpago"]."'");
		$formaEnvio = $__BD->db_valor("select descripcion from formasenvio where codenvio = '".$datos["codenvio"]."'");

		// Envio
		$codigoEnvio = '';
		$codigoEnvio .='<br/><br/>';
		$codigoEnvio .= '<h2>'._DATOS_ENVIO.'</h2>';

		if ($formaEnvio) {
			$codigoEnvio .= '<p><b>'._FORMA_ENVIO.'</b><br/>';
			$codigoEnvio .= $formaEnvio;
			$codigoEnvio .= '<p><b>'._DIRECCION.'</b>';
		}

		$codigoEnvio .= '&nbsp;<br/>';
		$codigoEnvio .= $datos["nombreenv"].' '.$datos["apellidosenv"];
		if ($datos["empresaenv"]) {
			$codigoEnvio .= '<br/>';
			$codigoEnvio .= $datos["empresaenv"];
		}
		$codigoEnvio .= '<p>';
		$codigoEnvio .= $datos["direccionenv"];
		$codigoEnvio .= '<br/>';
		$codigoEnvio .= $datos["codpostalenv"].' '.$datos["ciudadenv"].'&nbsp;&nbsp;'.$datos["provinciaenv"];
		$codigoEnvio .= '<br/>';
		$codigoEnvio .= $paisEnv;
			
		if ($mostrarEnvio)
		$codigo .= $codigoEnvio;

		// Pago
		$codigoPago = '';
		$codigoEnvio .='<br/><br/>';
		$codigoPago .= '<h2>'._DATOS_PAGO.'</h2>';

		if ($formaEnvio) {
			$codigoPago .= '<p><b>'._FORMA_PAGO.'</b><br/>';
			$codigoPago .= $formaPago;
			$codigoPago .= '<p><b>'._DIRECCION.'</b>';
		}

		$codigoPago .= '&nbsp;<br/>';
		$codigoPago .= $datos["nombre"].' '.$datos["apellidos"];
		if ($datos["empresa"]) {
			$codigoPago .= '<br/>';
			$codigoPago .= $datos["empresa"];
		}
		$codigoPago .= '<p>';
		$codigoPago .= $datos["direccion"];
		$codigoPago .= '<br/>';
		$codigoPago .= $datos["codpostal"].' '.$datos["ciudad"].'&nbsp;&nbsp;'.$datos["provincia"];
		$codigoPago .= '<br/>';
		$codigoPago .= $pais;

		if ($mostrarPago)
		$codigo .= $codigoPago;

		return $codigo;
	}

	// Imprime un pedido, albaran, etc
	function imprimirDocFacturacion($tipoDoc, $id)
	{
		global $__BD, $__CAT, $__LIB;

		$codigo = "";

		switch ($tipoDoc) {
			case "pedido":
				$tabla = "pedidoscli";
				$tablaL = "lineaspedidoscli";
				$campoId = "idpedido";
				$mostrarEnvio = 1;
				$mostrarPago = 1;
				break;
			case "albaran":
				$tabla = "albaranescli";
				$tablaL = "lineasalbaranescli";
				$campoId = "idalbaran";
				$mostrarEnvio = 1;
				$mostrarPago = 1;
				break;
			case "factura":
				$tabla = "facturascli";
				$tablaL = "lineasfacturascli";
				$campoId = "idfactura";
				$mostrarEnvio = 0;
				$mostrarPago = 1;
				break;
		}

		$ordenSQL = "select * from $tabla where $campoId = $id";
		$result = $__BD->db_query($ordenSQL);
		$datos = $__BD->db_fetch_array($result);

		$ordenSQL = "select * from $tablaL where $campoId = $id";
		$datosLin = $__BD->db_query($ordenSQL);

		$codigo .= '<h2 class="top">'.constant(strtoupper('_'.$tipoDoc)).' '.$datos["codigo"].'</h2>';

		$codigo .= '<p><b>'._FECHA.'</b>: '.date("d-m-Y", strtotime($datos["fecha"]));

		if ($tipoDoc == 'pedido') {
			$codigo .= '<br/><b>'._ENVIADO.'</b>: '.$datos["servido"];
			if ($datos["servido"] <> 'No') {
				$fechaEnvio = date("d-m-Y", strtotime($datos["fechasalida"]));
				if ($fechaEnvio > '01-01-1970')
				$codigo .= '&nbsp;&nbsp;&nbsp;&nbsp;<b>'._FECHA_ENVIO.'</b>: '.date("d-m-Y", strtotime($datos[6]));
			}
		}

		$codigo .='<br/><br/>';

		$codigo .= '<table class="cesta" cellspacing="5">';
		
		$codigo .= '<tr>';
		$codigo .= '<th align="left" class="desc">'._ARTICULO.'</th>';
				
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])) {
 					$codigo .= '<th align="left">'._PRECIO.'</th>';
 					$codigo .= '<th align="left">'._IVA.'</th>';
 				}
 				else
		$codigo .= '<th align="left">'._PRECIO.'</th>';
			
		$codigo .= '<th align="left">'._CANTIDAD.'</th>
				<th align="left">'._IMPORTE.'</th>';
		
		$codigo .= '</tr>';
	
		$arrayImpuestos;
		$arrayImpuestos[18] = 0;
		$arrayImpuestos[8] = 0;
		$arrayImpuestos[4] = 0;
		while ($row = $__BD->db_fetch_array($datosLin)) {

			$descripcion = $this->traducir("articulos", "descripcion", $row["referencia"], $row["descripcion"]);

// 			$precio = $__CAT->precioNeto($row, false, true, true);
// 			$impuesto = $__CAT->impuestoArticulo($row, true);
// 			$pvpUnitario = $precio + $impuesto;
// 			$pvpTotal = $pvpUnitario * $row["cantidad"];
			if(isset($row["iva"]) && $row["iva"] != "0") {
				$arrayImpuestos[$row["iva"]] += $row["pvptotal"];
			}

			$codigo .= '<tr">';
			$codigo .= '<td class="desc">'.$descripcion.'</td>';

			if ($this->esTrue($_SESSION["opciones"]["desglosariva"])) {
				$codigo .= '<td>'.$__CAT->precioDivisa($row["pvpunitario"]).'</td>';	
			}
			else
				$codigo .= '<td>'.$__CAT->precioDivisa($row["pvpunitarioiva"]).'</td>';

			$codigo .= '<td>'.round($row["cantidad"]).'</td>';
			if ($this->esTrue($_SESSION["opciones"]["desglosariva"])) {
				$codigo .= '<td>'.$__CAT->precioDivisa($row["pvptotal"]).'</td>';	
			}
			else
				$codigo .= '<td>'.$__CAT->precioDivisa($row["pvptotaliva"]).'</td>';
			
			$codigo .= '</tr>';
		}
		
		if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])){
			$codigo .= '<tr class="subtotales">';
			$codigo .= '<td colspan="3" class="txtsubtotal">'._NETO.'</td>';
			
			$codigo .= '<td>'.$__CAT->precioDivisa($datos["neto"]).'</td>';
			$codigo .= '</tr>';

			if($arrayImpuestos["18"] != 0) {
				$codigo .= '<tr><td colspan="3" class="txtsubtotal">IVA 18%</td>';
				$iva = $arrayImpuestos["18"]* 18 / 100;
				$codigo .= '<td>'.$__CAT->precioDivisa($iva).'</td></tr>';
			}
			if($arrayImpuestos["8"] != 0) {
				$codigo .= '<tr><td colspan="3" class="txtsubtotal">IVA 8%</<td>';
				$iva = $arrayImpuestos["8"]* 8 / 100;
				$codigo .= '<td>'.$__CAT->precioDivisa($iva).'</td></tr>';
			}
			if($arrayImpuestos["4"] != 0) {
				$codigo .= '<tr><td colspan="3" class="txtsubtotal">IVA 4%</td>';;
				$iva = $arrayImpuestos["4"]* 4 / 100;
				$codigo .= '<td>'.$__CAT->precioDivisa($iva).'</td></tr>';
			}
		}
	
		$codigo .= '<tr class="totales">';
		$codigo .= '<td colspan="3" class="total">'._TOTAL.'</td>';;
		$codigo .= '<td>'.$__CAT->precioDivisa($datos["total"]).'</td>';
		$codigo .= '</tr>'
				;
		$codigo .= '</table>';
		


		$pais = $__BD->db_valor("select nombre from paises where codpais = '".$datos["codpais"]."'");
		$paisEnv = $__BD->db_valor("select nombre from paises where codpais = '".$datos["codpaisenv"]."'");

		$formaPago = $__BD->db_valor("select descripcion from formaspago where codpago = '".$datos["codpago"]."'");
		$formaEnvio = $__BD->db_valor("select descripcion from formasenvio where codenvio = '".$datos["codenvio"]."'");

		// Envio
		$codigoEnvio = '';
		$codigoEnvio .='<br/><br/>';
		$codigoEnvio .= '<h2>'._DATOS_ENVIO.'</h2>';

		if ($formaEnvio) {
			$codigoEnvio .= '<p><b>'._FORMA_ENVIO.'</b><br/>';
			$codigoEnvio .= $formaEnvio;
			$codigoEnvio .= '<p><b>'._DIRECCION.'</b>';
		}

		$codigoEnvio .= '&nbsp;<br/>';
		$codigoEnvio .= $datos["nombreenv"].' '.$datos["apellidosenv"];
		if ($datos["empresaenv"]) {
			$codigoEnvio .= '<br/>';
			$codigoEnvio .= $datos["empresaenv"];
		}
		$codigoEnvio .= '<p>';
		$codigoEnvio .= $datos["direccionenv"];
		$codigoEnvio .= '<br/>';
		$codigoEnvio .= $datos["codpostalenv"].' '.$datos["ciudadenv"].'&nbsp;&nbsp;'.$datos["provinciaenv"];
		$codigoEnvio .= '<br/>';
		$codigoEnvio .= $paisEnv;
			
		if ($mostrarEnvio)
		$codigo .= $codigoEnvio;

		// Pago
		$codigoPago = '';
		$codigoEnvio .='<br/><br/>';
		$codigoPago .= '<h2>'._DATOS_PAGO.'</h2>';

		if ($formaEnvio) {
			$codigoPago .= '<p><b>'._FORMA_PAGO.'</b><br/>';
			$codigoPago .= $formaPago;
			$codigoPago .= '<p><b>'._DIRECCION.'</b>';
		}

		$codigoPago .= '&nbsp;<br/>';
		$codigoPago .= $datos["nombre"].' '.$datos["apellidos"];
		if ($datos["empresa"]) {
			$codigoPago .= '<br/>';
			$codigoPago .= $datos["empresa"];
		}
		$codigoPago .= '<p>';
		$codigoPago .= $datos["direccion"];
		$codigoPago .= '<br/>';
		$codigoPago .= $datos["codpostal"].' '.$datos["ciudad"].'&nbsp;&nbsp;'.$datos["provincia"];
		$codigoPago .= '<br/>';
		$codigoPago .= $pais;

		if ($mostrarPago)
		$codigo .= $codigoPago;

		return $codigo;
	}

	// Prepara un pedido para enviarlo por mail
	function enviarMailPedido($id)
	{
		global $__BD;

		$texto = $this->imprimirDocFacturacion("pedido", $id);

		$email = $__BD->db_valor("select email from clientes where codcliente = '".$_SESSION["codCliente"]."'");
		$codPedido = $__BD->db_valor("select codigo from pedidoscli where idpedido = $id");
		$titulo = $_SESSION["opciones"]["titulo"].' - '._PEDIDO.' '.$codPedido;
		$this->enviarMail($email, $titulo, $texto, $texto);

		$emailWM = $_SESSION["opciones"]["emailwebmaster"];
		$this->enviarMail($emailWM, $titulo, $texto, $texto);
	}

	// Prepara los datos de una nueva cuenta para enviarlos por mail
	function enviarMailCuenta()
	{
		global $__BD;

		$texto = _TEXTO_CUENTA_CREADA;
		$texto .= '<p><a href="'._WEB_ROOT_SSL.'cuenta/login.php">'._WEB_ROOT_SSL.'cuenta/login.php</a>';
		$email = $__BD->db_valor("select email from clientes where codcliente = '".$_SESSION["codCliente"]."'");
		$titulo = _CUENTA_CREADA_MAIL;

		$this->enviarMail($email, $titulo, $texto);
	}

	// Genera un password aleatorio
	function generarPassword($longitud)
	{
		global $__BD;

		if(!is_numeric($longitud) || $longitud <= 0)
		$longitud = 8;

		if($longitud > 50)
		$longitud = 50;

		$password = '';
		$caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		mt_srand(microtime() * 1000000);
		for($i = 0; $i < $longitud; $i++)
		{
			$key = mt_rand(0,strlen($caracteres)-1);
			$password = $password . $caracteres{$key};
		}
		return $password;
	}

	function enviarMail($destino, $titulo, $texto, $textoHTML = '')
	{
		global $CONFIG_MAILER, $CONFIG_SMTPUSER, $CONFIG_SMTPPASS, $CONFIG_SMTPHOST, $CONFIG_SMTPPORT, $CONFIG_SMTPSSL, $CONFIG_SENDMAIL;
		
		switch($CONFIG_MAILER) {
			case 'mail':
				$transport = Swift_MailTransport::newInstance();
			break;
			case 'sendmail':
				$transport = Swift_SendmailTransport::newInstance($CONFIG_SENDMAIL);
			break;
			case 'smtp':
				$transport = Swift_SmtpTransport::newInstance($CONFIG_SMTPHOST, $CONFIG_SMTPPORT);
				if ($CONFIG_SMTPUSER) {
					$transport->setUsername($CONFIG_SMTPUSER);
					$transport->setPassword($CONFIG_SMTPPASS);
				}
				if ($CONFIG_SMTPSSL)
					$transport->setEncryption($CONFIG_SMTPSSL);
			break;
		}
		
		
		$mailer = Swift_Mailer::newInstance($transport);		
		
		$message = Swift_Message::newInstance()
			->setSubject($titulo)
			->setFrom(array($_SESSION["opciones"]["emailfrom"] => $_SESSION["opciones"]["nombrefrom"]))
			->setTo(array($destino))
			->setBody($texto, 'text/html')
			;
		
		$message->setCharset($_SESSION["opciones"]["charset"]);
		
// 		if ($textoHTML)	$message->addPart($textoHTML, 'text/html');
		
		$result = $mailer->send($message);
	}
	
	
	// Devuelve el HTML de una caja lateral
	function caja($titulo, $contenido, $estilo = '', $casillaUnica = false)
	{
		if (!$estilo)
		$estilo = 'caja';
			
		$codigo = '';
		$codigo .= '<div class="'.$estilo.'">';

		if ($this->esTrue($casillaUnica)) {
			$codigo .= '<div class="casillaUnica">';
			if ($titulo)
			$codigo .= $titulo.'<br/>';
			$codigo .= $contenido;
			$codigo .= '</div>';
		}
		else {
			if ($titulo) {
				$codigo .= '<div class="titulo">';
				$codigo .= $titulo;
				$codigo .= '</div>';
			}

			$codigo .= '<div class="contenido">';
			$codigo .= $contenido;
			$codigo .= '</div>';
		}

		$codigo .= '</div >';
		return $codigo;
	}

	// Devuelve el HTML de una caja lateral que incluye una imagen
	function cajaImagen($row)
	{
		global $__CAT;

		$precio = $__CAT->precioArticulo($row, true, false);
		$descripcion = $this->traducir("articulos", "descripcion", $row["referencia"], $row["descripcion"]);

		$link = $__CAT->linkArticulo($row["referencia"], $row["descripciondeeplink"]);

		$codigo = '';
		
		$codigo .= '<a class="titulo" href="'.$link.'">';
		$codigo .= $descripcion;
		$codigo .= '</a>';
		
		$codigo .= '<div class="imgCaja">';
		$codigo .= '<a href="'.$link.'">';
		$codigo .= $__CAT->codigoThumb($row["referencia"], $descripcion);
		$codigo .= '</a>';
		$codigo .= '</div>';
		
		$codigo .= '<div class="precio">'.$precio.'</div>';


		return $codigo;
	}

	// Devuelve el HTML del pie de pagina establecido en las opciones generales
	function pie()
	{
		global $__BD, $__LIB;

		$codigo = '';

		if ($this->esTrue($_SESSION["opciones"]["mostrartextopie"])) {

			$ordenSQL = "select id, textopie from opcionestv";
			$row = $__BD->db_row($ordenSQL);
			$textopie = $__LIB->traducir("opcionestv", "textopie", $row[0], $row[1]);

			$codigo .= '<div id="textoPie">';
			$codigo .= nl2br($textopie);
			$codigo .= '</div>';
		}

		return $codigo;
	}


	// Comprueba si el cliente esta logeado
	function comprobarCliente($desviar = false)
	{
		global $__BD, $__LIB;

		$valido = false;

		$keySesion = '-1';
		if (isset($_SESSION["key"]))
		$keySesion = $_SESSION["key"];

		if (isset($_SESSION["codCliente"])) {
			$ordenSQL = "select codcliente from clientes where codcliente = '".$_SESSION["codCliente"]."' AND sessionid = '$keySesion'";
			$valido = $__BD->db_valor($ordenSQL);
		}

		if (!$valido && $desviar) {

			// Si vengo de la cesta, desvia al login
			$nomPHP = basename($_SERVER["SCRIPT_FILENAME"]);
			if ($nomPHP == "datos_envio.php") {
				echo '<script languaje="javascript">document.location=\'cuenta/login.php?continua=pedido\'</script>';
			}

			echo '<h1>'._TIT_DEBES_LOGIN.'</h1>';
			echo '<div class="cajaTexto">';
			echo _DEBES_LOGIN;
			echo '<p><br/>';
			echo '<a class="button" href="cuenta/login.php"><span>'._MI_CUENTA.'</span></a>';

			if (!$this->esTrue($_SESSION["opciones"]["noautoaccount"]))
			echo '<a class="button" href="cuenta/crear_cuenta.php"><span>'._CREAR_CUENTA.'</span></a>';
			echo '</div>';
			include("../includes/right_bottom.php");
			exit;
		}

		return $valido;
	}


	// Introduce en la tabla de clientes el ID de sesion
	function altaSesion($codCliente)
	{
		global $__BD;

		$keySesion = $_SESSION["key"];
		$ordenSQL = "update clientes set sessionid = '$keySesion' where codcliente = '$codCliente'";

		$__BD->db_query($ordenSQL);
	}


	// Devuelve el nombre de un cliente
	function nombreCliente()
	{
		global $__BD;

		$codCliente = $_SESSION["codCliente"];
		if (!$codCliente)
		return;

		$ordenSQL = "select nombre, contacto from clientes where codcliente = '$codCliente'";
		$result = $__BD->db_query($ordenSQL);
		$row = $__BD->db_fetch_row($result);

		if ($row[1])
		return $row[1];

		return $row[0];
	}


	// Imprime la fase por la que pasa el pedido
	function fasesPedido($fase)
	{
		$fases = array('envio','pago','confirmacion','creado');

		$codigo = '<div class="fasesPedido">';
		while (list ($clave, $val) = each ($fases)) {

			$paso = $clave + 1;
			$titulo = strtoupper('_FASES_PEDIDO_'.$val);
			if (defined($titulo)) {
				$titulo = constant($titulo);
			}
			$titulo = $paso.'. '.$titulo;

			if ($clave > 0)
			$codigo .= ' <b>&middot;</b> ';

			if ($val == $fase)
			$codigo .= '<span class="titApartadoText">'.$titulo.'</span>';
			else
			$codigo .= $titulo;
		}
		$codigo .= '</div>';

		return $codigo;
	}

	// Funcion para homogeneizar las distintas bases de datos
	function esTrue($valor)
	{
		if ($valor == 'f' || $valor == '0'  || !$valor )
		return false;

		return true;
	}

	// Devuelve la cadena traducida para un campo de una tabla. Si no hay traduccion se devuelve el original
	function traducir($tabla, $campo, $idCampo, $valorDefecto)
	{
		global $__BD;

		$idioma = $_SESSION["idioma"];

		$traduccion = $__BD->db_valor("select traduccion from traducciones where tabla='$tabla' and campo='$campo' and idcampo='$idCampo' and codidioma='$idioma'");
		if (!$traduccion)
		return $valorDefecto;
			
		return $traduccion;
	}

	// Cambio de idioma desde el modulo lateral
	function cambiarIdioma($idioma)
	{
		global $__BD;

		$ordenSQL = "select codidioma from idiomas where codidioma = '$idioma' and publico = true";
		if ($__BD->db_valor($ordenSQL))
		$_SESSION["idioma"] = $idioma;
	}

	// Devuelve el HTML de los modulos laterales de la posicion indicada (derecha o izquierda)
	function modulosWeb($posicion)
	{
		global $__BD, $__CAT;

		$codigo = '';
		$ordenSQL = "select codigo, titulo, tipo, html, mostrartitulo, casillaunica from modulosweb where posicion='$posicion' and publico=true order by orden";

		$numModulos = $__BD->db_num_rows($ordenSQL);
		if ($numModulos == 0)
		return '';

		$resultMod = $__BD->db_query($ordenSQL);
		while($rowMod = $__BD->db_fetch_array($resultMod)) {

			$codigoMod = '';

			// puede venir de $fichero
			$estiloCaja = '';
			// Codigo HTML
			if ($rowMod["tipo"] == 0) {
				$codigoMod = $rowMod["html"];
			}
			// Fichero php
			else {
				if (class_exists($rowMod["codigo"])) {
					$modulo = new $rowMod["codigo"];
					$codigoMod = $modulo->contenidos();
					if (isset($modulo->estilo))
					$estiloCaja = $modulo->estilo;
				}
			}

			if ($this->esTrue($rowMod["mostrartitulo"]))
				$tituloMod = $this->traducir("modulosweb", "titulo", $rowMod["codigo"], $rowMod["titulo"]);
			else
				$tituloMod = '';

			if ($codigoMod)
			$codigo .= $this->caja($tituloMod, $codigoMod, $estiloCaja, $rowMod["casillaunica"]);
		}

		$claseModulos = "Left";
		if ($posicion == 1)
		$claseModulos = "Right";
		if ($codigo) {
			$codigo = '<div class="modulosWeb'.$claseModulos.'">'.$codigo.'</div>';
			return $codigo;
		}

		return '';
	}

	// Crea los botones de venta y favoritos
	function crearBotones($referencia)
	{
		$codigo = crearBotonVenta($referencia).' '.crearBotonFavoritos($referencia);

		return $codigo;
	}

	// Crea el boton de venta de una referencia
	function crearBotonVenta($referencia)
	{
		global $__LIB;

		$codigo = '';

		if ($__LIB->esTrue($_SESSION["opciones"]["noautoaccount"]) && !isset($_SESSION["codCliente"]))
		return;

		if ($__LIB->esTrue($_SESSION["opciones"]["preciossolousuarios"]) && !isset($_SESSION["codCliente"]))
		return '';

		$path_parts = pathinfo($_SERVER["SCRIPT_FILENAME"]);
		$nomPHP = $path_parts["basename"];

		$params = '?acc=add&amp;ref='.$referencia;
		$codigo .= '<a class="button" href="general/cesta.php'.$params.'">';
		$codigo .= '<span>'._ADD_CART.' </span>';
		$codigo .= '</a>';

		if($nomPHP == 'favoritos.php') {
			$params = '?acc=del&amp;ref='.$referencia;

			$codigo .= '<p><a href="cuenta/favoritos.php'.$params.'">';
			$codigo .= '<span>'._ELIMINAR.'</span>';
			$codigo .= '</a></p>';
		}

		return $codigo;
	}

	// Crea el boton de agregar a favoritos de una referencia
	function crearBotonFavoritos($referencia)
	{
		$path_parts = pathinfo($_SERVER["SCRIPT_FILENAME"]);
		$nomPHP = $path_parts["basename"];

		// Si estamos en favoritos, boton de eliminar
		if($nomPHP == 'favoritos.php') {
			$params = '?acc=del&amp;ref='.$referencia;
			$codigo = '<a href="'._WEB_ROOT_SSL_L.'cuenta/favoritos.php'.$params.'">';
			$codigo .= '<span>'._ELIMINAR.'</span>';
			$codigo .= '</a>';
		}
		else {
			$params = '?acc=add&amp;ref='.$referencia;
			$codigo = '<a href="'._WEB_ROOT_SSL_L.'cuenta/favoritos.php'.$params.'">';
			$codigo .= '<span>'._ADD_FAVORITOS.'</span>';
			$codigo .= '</a>';
		}


		return $codigo;
	}

	function crearBotonAmigo($referencia)
	{
		global $__LIB;

		if (!$__LIB->esTrue($_SESSION["opciones"]["enviaraunamigo"]))
		return '';
			
		$codigo = '<a class="ampliarRec" href="#recomendarArticulo">';
		$codigo .= '<span>'._ENVIAR_AMIGO.'</span>';
		$codigo .= '</a>';

		return $codigo;
	}

	function crearBotonComentario($referencia)
	{
		global $__LIB;

		if (!$__LIB->esTrue($_SESSION["opciones"]["enviarcomentario"]))
		return '';
			
		$codigo = '<a class="ampliarRec" href="#comentarArticulo">';
		$codigo .= '<span>'._ENVIAR_COMENTARIO.'</span>';
		$codigo .= '</a>';

		return $codigo;
	}

	// Comprueba si existe una forma de envio gratuita en las opciones generales
	function envioGratuito($codEnvio)
	{
		global $__BD;
			
		if (!$this->esTrue($_SESSION["opciones"]["enviogratis"]))
		return false;

		if ($codEnvio != $_SESSION["opciones"]["codenviogratis"])
		return false;
			
		$codEnvioGratis = $_SESSION["opciones"]["codenviogratis"];
		$ordenSQL = "select codenvio from formasenvio where codenvio = '$codEnvio'";
		if (!$__BD->db_valor($ordenSQL))
		return false;
			
		if ($_SESSION["opciones"]["enviogratisdesde"] > $_SESSION["cesta"]->total())
		return false;

		return true;
	}

	// Mensaje de envio gratuito
	function msgEnvioGratuito()
	{
		global $__CAT;

		$precio = $_SESSION["opciones"]["enviogratisdesde"];
		$codigo = _ENVIO_GRATIS_DESDE.' '.$__CAT->precioDivisa($precio);

		return $codigo;
	}

	// Comprueba que todos los datos de pago y envio son correctos en un pedido
	function comprobarPedido($datosEnv, $datosPag)
	{
		// Comprobacion de datos de envio
		$noNulos = array ("codenvio", "direccion_env", "codpostal_env", "ciudad_env", "provincia_env", "codpais_env", "contacto", "apellidos");
		$error = "";
		while (list ($clave, $campo) = each ($noNulos)) {
			if (strlen(trim($datosEnv[$campo])) == 0) {
				$error = _RELLENAR_TODOS_DATOS_ENVIO;
			}
		}

		// Comprobacion de datos de pago
		if ($datosPag) {
			$noNulos = array ("codpago", "direccion", "codpostal", "ciudad", "provincia", "codpais", "contacto", "apellidos");
			$error = "";
			while (list ($clave, $campo) = each ($noNulos)) {
				if (strlen(trim($datosPag[$campo])) == 0) {
					echo $campo;
					$error = _RELLENAR_TODOS_DATOS_PAGO;
				}
			}

			// NIF, si es obligatorio
			if ($this->esTrue($_SESSION["opciones"]["solicitarnif"])) {
				if (strlen(trim($datosPag["nif"])) == 0)
				$error = _RELLENAR_TODOS_DATOS_PAGO;
			}
		}

		return $error;
	}

	// Mensajes y comprobaciones de aviso al finalizar la instalacion
	function avisoInstall()
	{
		global $__BD;

		$msg = '';

		// Debe existir una forma de pago
		$ordenSQL = "select codpago from formaspago where activo = true";
		if (!$__BD->db_valor($ordenSQL))
		$msg .= _NO_CODPAGO.'<br/>';

		// Debe existir una forma de envio
		$ordenSQL = "select codenvio from formasenvio where activo = true";
		if (!$__BD->db_valor($ordenSQL))
		$msg .= _NO_CODENVIO.'<br/>';

		// El directorio de instalacion debe eliminarse
		if (file_exists(_DOCUMENT_ROOT.'/instalar'))
		$msg .= _AVISO_INSTALL.'<br/>';

		if (!$msg)
		return;

		$codigo = '';
		$codigo .= '<div style="text-align:center; color:red; padding: 10px">';
		$codigo .= _ATENCION.'<br/>'.$msg;
		$codigo .= '</div>';

		echo $codigo;
	}

	function tituloPagina()
	{
		global $CLEAN_GET, $__BD;

		$titulo = '';

		$referencia = '';
		if (isset($CLEAN_GET["ref"]))
		$referencia = $CLEAN_GET["ref"];
		else if(isset($CLEAN_GET["refdl"])) {
			$ordenSQL = "select referencia from articulos where descripciondeeplink = '".$CLEAN_GET["refdl"]."'";
			$referencia = $__BD->db_valor($ordenSQL);
		}

		if ($referencia)
		$titulo = $__BD->db_valor("select descripcion from articulos where referencia = '$referencia'");


		$codFamilia = '';
		if (isset($CLEAN_GET["fam"]))
		$codFamilia = $CLEAN_GET["fam"];
		else if(isset($CLEAN_GET["famdl"])) {
			$ordenSQL = "select codfamilia from familias where descripciondeeplink = '".$CLEAN_GET["famdl"]."'";
			$codFamilia = $__BD->db_valor($ordenSQL);
		}

		if ($codFamilia)
		$titulo = $__BD->db_valor("select descripcion from familias where codfamilia = '$codFamilia'");

		if ($titulo) $titulo = ' | '.$titulo;
		$titulo = _TITULO.$titulo;
		return $titulo;
	}

	function controlVisitas($tabla, $campo)
	{
		global $__BD;

		if (!$campo)
		return;

		$codCliente = '';
		if (isset($_SESSION["codCliente"]))
		$codCliente = $_SESSION["codCliente"];

		$ordenSQL = "select visitas from visitasweb where codcliente = '$codCliente' AND tabla = '$tabla' AND campo = '$campo'";
		$visitas = $__BD->db_valor($ordenSQL);

		if ($visitas) {
			$visitas++;
			$ordenSQL = "update visitasweb set visitas=$visitas, modificado = true where codcliente = '$codCliente' AND tabla = '$tabla' AND campo = '$campo'";
		}
		else
		$ordenSQL = "insert into visitasweb (tabla, campo, codcliente, visitas, modificado) values ('$tabla', '$campo', '$codCliente', 1, true)";

		$__BD->db_query($ordenSQL);
	}


	function zonaEnvio($codPais, $provincia)
	{
		global $__BD;

		// Provincia?
		$ordenSQL = "select codzona from provincias where codpais = '$codPais' and provincia = '$provincia'";
		$codZona = $__BD->db_valor($ordenSQL);

		// Pais?
		if (!$codZona) {
			$ordenSQL = "select codzona from paises where codpais = '$codPais'";
			$codZona = $__BD->db_valor($ordenSQL);
		}

		if (!$codZona)
		return '';

		return $codZona;
	}


	function precioEnvio($row, $codPeso, $codZona)
	{
		global $__BD, $__LIB;

		// 1. Por zonas?
		if (!$__LIB->esTrue($row["controlporzonas"]))
		return $row["pvp"];

		$codEnvio = $row["codenvio"];


		// Datos generales de zona
		$ordenSQL = "select codzona,pvp from zonasformasenvio where codzona = '$codZona' and codenvio ='$codEnvio'";
		$result = $__BD->db_query($ordenSQL);
		$rowZona = $__BD->db_fetch_array($result);

		// Zona excluida?
		if (!$rowZona["codzona"])
		return -1;


		// Existe precio por peso?
		$ordenSQL = "select pvp from costesenvio where codpeso = '$codPeso' and codenvio = '$codEnvio' AND codzona = '$codZona'";
		$precio = $__BD->db_valor($ordenSQL);
		if ($precio)
		return $precio;

		// Existe precio general de la zona?
		if ($rowZona["pvp"] > 0)
		return $rowZona["pvp"];

		// No hay precio especial, se devuelve el precio original
		return $row["precio"];
	}

	function formasEnvio($codPais, $provincia = '')
	{
		global $__BD, $__LIB, $__CLI, $__CAT;

		$codigo = '';

		// Forma de envio
		$codPeso = $_SESSION["cesta"]->intervaloPeso();
		$codZona = $this->zonaEnvio($codPais, $provincia);

				if ($__LIB->envioGratuito()) {
				$codEnvio = $_SESSION["opciones"]["codenviogratis"];
			$ordenSQL = "select codenvio, descripcion, pvp, codimpuesto, ivaincluido from formasenvio where codenvio = '$codEnvio'";
				}
			else
		$ordenSQL = "select codenvio, descripcion, pvp, codimpuesto, ivaincluido, controlporzonas from formasenvio where activo = true";

		$result = $__BD->db_query($ordenSQL);
		$paso = 0;

		while ($row = $__BD->db_fetch_array($result)) {

			$descripcion = $__LIB->traducir("formasenvio", "descripcion", $row["codenvio"], $row["descripcion"]);

			if ($__LIB->envioGratuito($row["codenvio"])) {
				$txPrecio = $__CAT->precioDivisa(0);
				$txMsg = '('.$__LIB->msgEnvioGratuito().')';
			}
			else {
				$precio = $this->precioEnvio($row, $codPeso, $codZona);

				if ($precio == -1) // Zona excluida?
				continue;

				if ($precio)
				$row["pvp"] = $precio;

				$txPrecio = $__CAT->precioDivisa($__CAT->precioArticulo($row, false, true, true));
				$txMsg = '';
			}

			$codigo .= '<tr>';
			
			$codigo .= '<td class="check"><input class="rdo" type="radio" name="codenvio" value="'.$row["codenvio"].'"';
			if ($paso++ == 0)
			$codigo .= ' checked';
			$codigo .= '></td>';
			$codigo .= '<td class="label">'.$descripcion.'</td>';
			$codigo .= '<td class="datoEnvio">'.$txPrecio.'</td>';
			$codigo .= '<td class="msgEnvio">'.$txMsg.'</td>';
		
			$codigo .= '</tr>';
		}

		if ($codigo)
			$codigo = '<table class="datosEnvio">'.$codigo.'</table>';
		
		
		return $codigo;
	}


	function enZonaPago($codZona, $codPago)
	{
		global $__BD, $__LIB;

		$ordenSQL = "select id from zonasformaspago where codzona='$codZona' and codpago='$codPago'";
		if ($__LIB->esTrue($__BD->db_valor($ordenSQL)))
		return true;

		return false;
	}

	function formasPago($codPais, $provincia = '')
	{
		global $__BD, $__LIB, $__CLI, $__CAT;

		$codigo = '';
		$codZona = $this->zonaEnvio($codPais, $provincia);

		$ordenSQL = "select * from formaspago where activo = true order by orden";
		$result = $__BD->db_query($ordenSQL);
		$paso = 0;

		while ($row = $__BD->db_fetch_array($result)) {
				
			$codPago = $row["codpago"];
			if ($__LIB->esTrue($row["controlporzonas"]))
			if (!$this->enZonaPago($codZona, $codPago))
			continue;
				
			$descripcion = $__LIB->traducir("formaspago", "descripcion", $codPago, $row["descripcion"]);;
			$descLarga = $__LIB->traducir("formaspago", "descripcionlarga", $codPago, $row["descripcionlarga"]);
				
			$codigo .= '<div class="formaPago">';

			$codigo .= '<div class="checkPago">';
			$codigo .= '<input type="radio" class="rdo" name="codpago" value="'.$codPago.'"';
			if ($paso++ == 0)
			$codigo .= ' checked';
			$codigo .= '></div>';

			$codigo .= '<div class="labelPago">';
			$codigo .= $descripcion;
			$codigo .= '</div>';
				
			if (strlen(trim($descLarga)) > 0) {
				$codigo .= '<div class="descPago">';
				$codigo .= nl2br($descLarga);
				$codigo .= '</div>';
			}
				
			if ($__LIB->esTrue($row["gastos"])) {
				$codigo .= '<div class="gastosPago">';
				$codigo .= _AVISO_GASTOS_PAGO.' '.number_format($row["gastos"],2).'%';
				$codigo .= '</div>';
			}
				
			if ($__LIB->esTrue($row["gastosfijo"])) {
				$codigo .= '<div class="gastosPago">';
				$codigo .= _AVISO_GASTOS_PAGO.' '.$__CAT->precioDivisa($row["gastosfijo"]);
				$codigo .= '</div>';
			}
				
			$codigo .= '<br class="cleanerLeft"/>';
			$codigo .= '</div>';
		}

		return $codigo;
	}

	function checkEmailAddress($email)
	{
		// First, we check that there's one @ symbol, and that the lengths are right
		$email_pattern = '/^[^@\s<&>]+@([-a-z0-9]+\.)+[a-z]{2,}$/i';

		if (!preg_match($email_pattern, $email))
		return false;

		return true;
	}

	function cleanURL($string)
	{
		$charSet = $_SESSION["opciones"]["charset"];
		$string = preg_replace("`\[.*\]`U","",$string);
		$string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i','-',$string);
		$string = htmlentities($string, ENT_COMPAT, $charSet);
		$string = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","\\1", $string );
		$string = preg_replace( array("`[^a-z0-9]`i","`[-]+`") , "-", $string);
		return strtolower(trim($string, '-'));
	}


	function scriptGA()
	{
		$code = $_SESSION["opciones"]["codigoanalytics"];

		if (!$code)
		return '';

		$codigo = "
		<script type=\"text/javascript\">
		
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '$code']);
		_gaq.push(['_trackPageview']);
		
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
		
		</script>"."\n";

		return $codigo;
	}

}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_funLibreria */
class funLibreria extends oficial_funLibreria {};

?>
