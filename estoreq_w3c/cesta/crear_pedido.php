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

/** @class_definition oficial_crearPedido */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_crearPedido
{
	// Inserta las lineas del pedido
	function insertarLineas($idPedido)
	{
		global $__BD, $__CAT, $__LIB;
			
		$cesta = $_SESSION["cesta"];
		
		$totalPedido = 0;
		$arrayImpuestos;
		$arrayImpuestos["18"] = 0;
		$arrayImpuestos["8"] = 0;
		$arrayImpuestos["4"] = 0;
		$totales;
		$totales["coniva"] = 0;
		$totales["siniva"] = 0;

		for ($i=0; $i < $cesta->num_articulos; $i++) {
		
			$referencia = $cesta->codigos[$i];
			$cantidad = $cesta->cantidades[$i];
			$ordenSQL = "select * from articulos where referencia='$referencia'";
			$gastosEnvio = "false";
			if ($referencia == 'null')
				continue;
		
			$result = $__BD->db_query($ordenSQL);	
			$row = $__BD->db_fetch_array($result);
			
			$descripcion = $cesta->descripcionLinea($row, $i);
			$codImpuesto = $row["codimpuesto"];
			$iva = $__CAT->selectIVA($codImpuesto);
			$pvps = $__CAT->pvp($row);
			$ivaIncluido = "false";
			$netoSinDto;
			if ($__LIB->esTrue($row["ivaincluido"])) {
				$ivaIncluido = "true";
				$netoSinDto["siniva"] = ($pvps["coniva"] * $cantidad) / (1 + $iva / 100);
				$netoSinDto["coniva"] = $pvps["coniva"] * $cantidad;
			}
			else {
				$ivaIncluido = "false";
				$netoSinDto["siniva"] = $pvps["siniva"] * $cantidad;
				$netoSinDto["coniva"] = ($pvps["siniva"] * $cantidad) * (1 + $iva / 100);
			}
	
			$netoSinDto["siniva"] = number_format($netoSinDto["siniva"],2,".","");
			$netoSinDto["coniva"] = number_format($netoSinDto["coniva"],2,".","");

			$totales["siniva"] += $netoSinDto["siniva"];
			$totales["coniva"] += $netoSinDto["coniva"];

			$arrayImpuestos[$iva] += $netoSinDto["siniva"];
			
			$linea = array (
				"idpedido" => $idPedido,
				"referencia" => "'$referencia'",
				"descripcion" => "'".$descripcion."'",
				"cantidad" => "'".$cantidad."'",
				"pvpsindto" => "'".$netoSinDto["siniva"]."'",
				"pvpunitario" => "'".$pvps["siniva"]."'",
				"pvpunitarioiva" => "'".$pvps["coniva"]."'",
				"ivaincluido" =>"'".$ivaIncluido."'",
				"pvptotal" => "'".$netoSinDto["siniva"]."'",
				"codimpuesto" => "'".$row["codimpuesto"]."'",
				"iva" => "'".$iva."'",
				"totalenalbaran" => 0,
				"dtolineal" => 0,
				"dtopor" => 0,
				"gastosenvio" => "'".$gastosEnvio."'",
				//"pvpsindtoiva" => "'".$netoSinDto["coniva"]."'",
				//"pvptotaliva" => "'".$netoSinDto["coniva"]."'"
			);
									
			$linea = $this->editarLinea($linea);

			if (!$this->insertarLinea($linea, $i))
				return false;
		}
		
		$this->lineaDescuento($idPedido, $totales["siniva"]);
		$totalPedido += $this->lineaGastosEnvio($idPedido);
		$this->lineaGastosPago($idPedido, $totales["siniva"]);
		
		$result = $this->calcularTotales($idPedido);
		return $result;
	}

	function insertarLineas_old($idPedido)
	{
		global $__BD, $__CAT, $__LIB;
			
		$cesta = $_SESSION["cesta"];
		
		$totalPedido = 0;
	
		for ($i=0; $i < $cesta->num_articulos; $i++) {
		
			$referencia = $cesta->codigos[$i];
			$cantidad = $cesta->cantidades[$i];
			$ordenSQL = "select * from articulos where referencia='$referencia'";
			$gastosEnvio = "false";
			if ($referencia == 'null')
				continue;
		
			$result = $__BD->db_query($ordenSQL);	
			$row = $__BD->db_fetch_array($result);
			
			$descripcion = $cesta->descripcionLinea($row, $i);
			$pvpUnitario = $cesta->netoLinea($row, $i);
			$porIVA = $__CAT->selectIVA($row["codimpuesto"]);
			
			$pvpNominal = $pvpUnitario * $cantidad;
			$pvpTotal = $pvpNominal;
			
			$totalPedido += $pvpTotal + $pvpTotal * $porIVA / 100;
			
			$linea = array (
				"idpedido" => $idPedido,
				"referencia" => "'$referencia'",
				"descripcion" => "'".$descripcion."'",
				"cantidad" => "'".$cantidad."'",
				"pvpsindto" => "'".$pvpNominal."'",
				"pvpunitario" => "'".$pvpUnitario."'",
				"pvpunitarioiva" => "'".$pvpUnitario."'",
				"ivaincluido" => "false",
				"pvptotal" => "'".$pvpTotal."'",
				"codimpuesto" => "'".$row["codimpuesto"]."'",
				"iva" => "'".$porIVA."'",
				"totalenalbaran" => 0,
				"dtolineal" => 0,
				"dtopor" => 0,
				"gastosenvio" => "'".$gastosEnvio."'"
			);
									
			$linea = $this->editarLinea($linea);

			if (!$this->insertarLinea($linea, $i))
				return false;
		}
		
		$this->lineaDescuento($idPedido, $totalPedido);
		$totalPedido += $this->lineaGastosEnvio($idPedido);
		$this->lineaGastosPago($idPedido, $totalPedido);
		
		$result = $this->calcularTotales($idPedido);
		return $result;
	}

	// Inserta una línea en la base de datos
	// Numlinea es necesario para extender 
	function insertarLinea($linea, $numLinea = 0) 
	{
		global $__BD;
	
			$ordenSQL1 = "insert into lineaspedidoscli (";
			$ordenSQL2 = ") values (";
			while (list ($campo, $valor) = each ($linea)) {
				if ($campo != "idpedido") {
					$ordenSQL1 .= ', ';
					$ordenSQL2 .= ', ';
				}
				$ordenSQL1 .= $campo;
				$ordenSQL2 .= $valor;
			}
			
			$ordenSQL1 .= ',idlinea';
			
			$intentos = 0;
			$result = false;
			
			// Hasta 10 intentos
			while(!$result) {
			
				if ($intentos++ == 10)
					break;
			
				// Id linea
				$idLinea = $__BD->nextId("lineaspedidoscli", "idlinea");;
				$ordenSQL = $ordenSQL1.$ordenSQL2.','.$idLinea.')';
				$result = $__BD->db_query($ordenSQL);
				
				if (!$result) usleep(100);
			}
			
			return $result;
	}


	// Inserta una linea de descuento
	function lineaDescuento($idPedido, $totalPedido)
	{
			global $__CAT, $__BD, $__LIB;
	
			if (!$__LIB->esTrue($_SESSION["opciones"]["activarcoddescuento"]))
				return;
				
			$hoy = date("Y-m-d");
			$codDescuento = $_SESSION["pedido"]["datosEnv"]["coddescuento"];
			$ordenSQL = "select * from codigosdescuento where codigo='$codDescuento' and activo=true and (caducidad is null or caducidad >= '$hoy')";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_array($result);
			
			if (!$row["id"])
				return;
			
			$pvpUnitario = 0 - $totalPedido * $row["dtopor"] / 100 - $row["dtolineal"];
			
			if ($row["dtopor"] > 0)
				$lblDto = $row["dtopor"].'%';
			if ($row["dtolineal"] > 0)
				$lblDto = $__CAT->precioDivisa($row["dtolineal"]);

			$descripcion = _DTO.' '.$row["codigo"].' ('.$lblDto.')';
			
			$linea = array (
				"idpedido" => $idPedido,
				"referencia" => "''",
				"descripcion" => "'".$descripcion."'",
				"cantidad" => 1,
				"pvpsindto" => "'".$pvpUnitario."'",
				"pvpunitario" => "'".$pvpUnitario."'",
				"pvpunitarioiva" => "'".$pvpUnitario."'",
				"ivaincluido" => "false",
				"pvptotal" => "'".$pvpUnitario."'",
				"codimpuesto" => "''",
				"iva" => 0,
				"totalenalbaran" => 0,
				"dtolineal" => 0,
				"dtopor" => 0,
				"gastosenvio" => "false",
				//"pvpsindtoiva" => "'".$pvpUnitario."'",
				//"pvptotaliva" => "'".$pvpUnitario."'"
			);
	
		if (!$this->insertarLinea($linea))
			return false;
	}
	
	
	// Inserta una linea de gastos de pago si procede
	function lineaGastosPago($idPedido, $netoPedido)
	{
			global $__CAT, $__BD, $__LIB;
	
			$codPago = $_SESSION["pedido"]["datosPag"]["codpago"];
	
			$ordenSQL = "select * from formaspago where codpago = '".$codPago."'";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_array($result);

			$codImpuesto = $row["codimpuesto"];
			$iva = $__CAT->selectIVA($codImpuesto);

			$gastos = 0;
		
			$ivaIncluido = "false";
			if ($__LIB->esTrue($row["ivaincluido"]))
				$ivaIncluido = "true";

			if ($__LIB->esTrue($row["gastosfijo"])) {
				if ($__LIB->esTrue($row["ivaincluido"])) {
					$gastos = $row["gastosfijo"]/ (1 + $iva / 100);
				}
				else {
					$gastos = $row["gastosfijo"];
				}
			}
			
			if (!$__LIB->esTrue($row["gastos"]) && $gastos == 0)
				return 0;

			$gastos += $netoPedido * $row["gastos"] / 100;
			$gastosConIva = $gastos  * (1 + $iva / 100);

			$descripcion = _GASTOS_PAGO;
			
			$linea = array (
				"idpedido" => $idPedido,
				"referencia" => "''",
				"descripcion" => "'".$descripcion."'",
				"cantidad" => 1,
				"pvpsindto" => "'".$gastos."'",
				"pvpunitario" => "'".$gastos."'",
				"pvpunitarioiva" => "'".$gastosConIva."'",
				"ivaincluido" => "'".$ivaIncluido."'",
				"pvptotal" => "'".$gastos."'",
				"codimpuesto" => "'".$row["codimpuesto"]."'",
				"iva" => "'".$iva."'",
				"totalenalbaran" => 0,
				"dtolineal" => 0,
				"dtopor" => 0,
				"gastosenvio" => "false",
				//"pvpsindtoiva" => "'".$gastosConIva."'",
				//"pvptotaliva" => "'".$gastosConIva."'"
			);
	
		if (!$this->insertarLinea($linea))
			return false;
	}
	
	
	// Inserta una linea de gastos de pago si procede
	function lineaGastosEnvio($idPedido)
	{
			global $__CAT, $__BD, $__LIB;
	
			$codEnvio = $_SESSION["pedido"]["datosEnv"]["codenvio"];
			
			if ($__LIB->envioGratuito($codEnvio))
				$pvp = 0;
			else {
				$codPeso = $_SESSION["cesta"]->intervaloPeso();
				$codZona = $__LIB->zonaEnvio($_SESSION["pedido"]["datosEnv"]["codpais_env"], $_SESSION["pedido"]["datosEnv"]["provincia_env"]);
				$ordenSQL = "select * from formasenvio where codenvio = '$codEnvio'";
				$result = $__BD->db_query($ordenSQL);
				$row = $__BD->db_fetch_array($result);
				$pvp = $__LIB->precioEnvio($row, $codPeso, $codZona);
			}
			
			$row["pvp"] = $pvp;
			
			if (!$__LIB->esTrue($row["pvp"]))
				return 0;

			$pvpUnitario = $__CAT->precioNeto($row, false, true, true);
			$porIVA = $__CAT->selectIVA($row["codimpuesto"]);
			
			$linea = array (
				"idpedido" => $idPedido,
				"referencia" => "''",
				"descripcion" => "'".$row["descripcion"]."'",
				"cantidad" => 1,
				"pvpsindto" => "'".$pvpUnitario."'",
				"pvpunitario" => "'".$pvpUnitario."'",
				"pvpunitarioiva" => "'".$pvpUnitario."'",
				"ivaincluido" => "false",
				"pvptotal" => "'".$pvpUnitario."'",
				"codimpuesto" => "'".$row["codimpuesto"]."'",
				"iva" => "'".$porIVA."'",
				"totalenalbaran" => 0,
				"dtolineal" => 0,
				"dtopor" => 0,
				"gastosenvio" => "false",
				//"pvpsindtoiva" => "'".$pvpUnitario."'",
				//"pvptotaliva" => "'".$pvpUnitario."'",
			);
	
		if (!$this->insertarLinea($linea))
			return 0;
	
		return $pvpUnitario + $pvpUnitario * $porIVA / 100;;
	}
	
	
	
	// Establece los totales del pedido (neto, iva y total)
	function calcularTotales($idPedido)
	{
		global $__BD;
			
		$ordenSQL = "select * from lineaspedidoscli where idpedido = $idPedido"; 
		$result = $__BD->db_query($ordenSQL);
		
		$neto = 0;
		$iva = 0;
		while($row = $__BD->db_fetch_array($result)) {
			$neto += $row["pvptotal"];
			$iva += $row["pvptotal"] * $row["iva"] / 100;
		}
		
		$total = $neto + $iva;
		
		$ordenSQL = "update pedidoscli set neto='$neto', totaliva='$iva', total='$total' where idpedido = $idPedido";
		$result = $__BD->db_query($ordenSQL);
		return $result;
	}
	
	// Verifica si se ha hecho el pago via una pasarela y devuelve el Id de transaccion proporcionado por ella
	function datosTransaccion()
	{
		global $__LIB;
	
		$codPago = $_SESSION["pedido"]["datosPag"]["codpago"];
		$pagado = false;
		$error = '';
		
		if (file_exists(_DOCUMENT_ROOT.'cesta/sistpago/'.$codPago.'/datos.php')) {
			
			include(_DOCUMENT_ROOT.'cesta/sistpago/'.$codPago.'/datos.php');
			$className = $codPago.'Datos';
			
			if (class_exists($className)) {
				
				$iface_pago = new $className;
				$datosTransaccion = $iface_pago->datosTransaccion();
				
				if (isset($datosTransaccion["error"]))
					$error = $datosTransaccion["error"];
				
				if (isset($datosTransaccion["tid"]))
					$transactionID = $datosTransaccion["tid"];
				
				if (isset($datosTransaccion["pagado"]))
					$pagado = $datosTransaccion["pagado"];
			}
			else 
				$error = _ERROR_PAGO;
		
			if (!$pagado)
				$error = _ERROR_PAGO;
		}
		
		else {
			$result["pagado"] = "false";
			$result["tid"] = "";
			return $result;
		} 
		
		if ($error) {
			echo '<div class="msgError">'.$error.'</div>'; 
			echo '</div>';
			include("../includes/right_bottom.php");
			exit;
		}
		
		$result["pagado"] = "true";
		$result["tid"] = $transactionID;
		
		return $result;
	}

	// Verifica que en la tabla de secuencias ejercicios al menos hay un registro para el ejercicio y la serie actuales
	function secuenciasEjercicios($ejercicio, $serie)
	{
		global $__BD;
		
		$ordenSQL = "select id from secuenciasejercicios where codejercicio = '$ejercicio' and codserie = '$serie'";
		$id = $__BD->db_valor($ordenSQL);
		if (!$id) {
			$ordenSQL = "select max(id) from secuenciasejercicios";
			$id = $__BD->db_valor($ordenSQL);
			if ($id)
				$id++;
			else
				$id = 1;
				
			$ordenSQL = "insert into secuenciasejercicios 
							(id, codserie, codejercicio, npedidoprov, nalbaranprov, nfacturaprov, npresupuestocli, npedidocli, nalbarancli, nfacturacli)
							values ($id, '$serie', '$ejercicio', 0,0,0,0,0,0,0)";
			$__BD->db_query($ordenSQL);
		}
	}
	
	// Crea el pedido
	function contenidos() 
	{	
		global $__BD, $__CAT, $__LIB, $__CLI;
		global $CLEAN_GET, $CLEAN_POST;
		
		$__LIB->comprobarCliente(true);
	
		echo '<h1>'._CREAR_PEDIDO.'</h1>';
		echo '<div class="cajaTexto">';

		$cesta = $_SESSION["cesta"];
		if ($cesta->cestaVacia()) {
			echo _AVISO_PEDIDO_CREADO.'<p>';
			echo '<a class="botLista" href="'._WEB_ROOT_SSL_L.'cuenta/pedidos.php">'._LISTA_PEDIDOS.'</a>';
			echo '</div>';
			include("../includes/right_bottom.php");
			exit;
		}
		
		if (!isset($_SESSION["pedido"])) {
			echo '<a href="'._WEB_ROOT_L.'cuenta/login.php">'._PEDIDO_INCORRECTO.'</a>';
			echo '</div>';
			include("../includes/right_bottom.php");
			exit;
		}
		
		// Comprobar los datos del pedido
		$error = $__LIB->comprobarPedido($_SESSION["pedido"]["datosEnv"], $_SESSION["pedido"]["datosPag"]);
		if ($error) {
			echo '<div class="msgError">'.$error.'</div>'; 
			echo '</div>';
			include("../includes/right_bottom.php");
			exit;
		}
		
		// Datos de pago (si viene de una pasarela)
		$datosTransaccion = $this->datosTransaccion();
		$pagado = $datosTransaccion["pagado"];
		$transactionID = $datosTransaccion["tid"]; 
		
		
		// Crear pedido
			
		$codPago = $_SESSION["pedido"]["datosPag"]["codpago"];
		$codCliente = $_SESSION["codCliente"];
		
		// Datos del cliente
		$datosCli = $__BD->db_row("select nombre from clientes where codcliente = '$codCliente'");
		// Datos generales
		$datosGen = $__BD->db_row("select codserie, coddivisa, codalmacen from empresa");
		
		$fecha = date("Y-m-d", time());
		
		$nif = '...';
		if (isset($_SESSION["pedido"]["datosPag"]["nif"]))
			$nif = $_SESSION["pedido"]["datosPag"]["nif"];
		
		$ejercicio = $_SESSION["opciones"]["codejercicio"];
		$serie = $datosGen[0];
		
		// Verificar la tabla de secuencias ejercicios
		$this->secuenciasEjercicios($ejercicio, $serie);
		
		$pedido = array (
			"codejercicio" => "'$ejercicio'",
			"codserie" => "'$serie'",
			"codpago" => "'".$codPago."'",
			"codenvio" => "'".$_SESSION["pedido"]["datosEnv"]["codenvio"]."'",
			"coddivisa" => "'$datosGen[1]'",
			"codalmacen" => "'$datosGen[2]'",
			"codcliente" => "'$codCliente'",
			"nombrecliente" => "'$datosCli[0]'",
			"cifnif" =>  "'$nif'",
			
			"direccion" => "'".$_SESSION["pedido"]["datosPag"]["direccion"]."'",
			"codpostal" => "'".$_SESSION["pedido"]["datosPag"]["codpostal"]."'",
			"ciudad" => "'".$_SESSION["pedido"]["datosPag"]["ciudad"]."'",
			"provincia" => "'".$_SESSION["pedido"]["datosPag"]["provincia"]."'",
			"codpais" => "'".$_SESSION["pedido"]["datosPag"]["codpais"]."'",
			"nombre" => "'".$_SESSION["pedido"]["datosPag"]["contacto"]."'",
			"apellidos" => "'".$_SESSION["pedido"]["datosPag"]["apellidos"]."'",
			"empresa" => "'".$_SESSION["pedido"]["datosPag"]["empresa"]."'",
			
			"direccionenv" => "'".$_SESSION["pedido"]["datosEnv"]["direccion_env"]."'",
			"codpostalenv" => "'".$_SESSION["pedido"]["datosEnv"]["codpostal_env"]."'",
			"ciudadenv" => "'".$_SESSION["pedido"]["datosEnv"]["ciudad_env"]."'",
			"provinciaenv" => "'".$_SESSION["pedido"]["datosEnv"]["provincia_env"]."'",
			"codpaisenv" => "'".$_SESSION["pedido"]["datosEnv"]["codpais_env"]."'",
			"nombreenv" => "'".$_SESSION["pedido"]["datosEnv"]["contacto"]."'",
			"apellidosenv" => "'".$_SESSION["pedido"]["datosEnv"]["apellidos"]."'",
			"empresaenv" => "'".$_SESSION["pedido"]["datosEnv"]["empresa"]."'",
			
			"fecha" => "'$fecha'",
			"fechasalida" => "'$fecha'",
			"editable" => "true",
			"pedidoweb" => "true",
			"modificado" => "true",
			"tasaconv" => 1,
			"recfinanciero" => 0,
			"servido" => "'No'",
		
			"totaliva" => 0,
			"totalirpf" => 0,
			"totaleuros" => 0,
			"irpf" => 0,
			"neto" => 0,
			"total" => 0,

			"pagado" => $pagado,
			"transactionid" => "'$transactionID'"
		);

		$pedido = $this->editarPedido($pedido);
		
		// Intentamos crear el pedido. 10 intentos
		$intentos = 0;
		$result = false;
		while(!$result) {
			
			if ($intentos++ == 10)
				break;
		
			// ID del pedido
			$id = $__BD->nextId("pedidoscli", "idpedido");
			
			$codigo = '';
			
			// Creamos el codigo a partir del id. 10 intentos
			// Codigo = ejercicio + serie + W + id 
			$intentosSeq = 0;
			while(!$codigo) {
				$numero = $id;
				
				settype($numero,"string");
				$numero = $__LIB->cerosIzquierda($numero, 6);
				$numero = 'W'.$numero;
				
				$codigo = $__LIB->cerosIzquierda($ejercicio, 4).$__LIB->cerosIzquierda($serie, 2).$numero;
				
				// Control de duplicados
				if ($__BD->db_valor("select idpedido from pedidoscli where codigo = '$codigo'"))
					$codigo = '';
					
				if ($intentosSeq++ == 10)
					break;
			}
			
			reset($pedido);
			$pedido["idpedido"] = $id;
			$pedido["codigo"] = "'$codigo'";
			$pedido["numero"] = "'$numero'";
			
			$ordenSQL1 = "insert into pedidoscli (";
			$ordenSQL2 = ") values (";
			$paso = 0;
			while (list ($campo, $valor) = each ($pedido)) {
				if ($paso++ > 0) {
					$ordenSQL1 .= ', ';
					$ordenSQL2 .= ', ';
				}
				$ordenSQL1 .= $campo;
				$ordenSQL2 .= $valor;
			}
			
			$ordenSQL = $ordenSQL1.$ordenSQL2.')';
			$result = $__BD->db_query($ordenSQL);
			
			// Si falla esperamos 3 decimas de segundo y reintentamos
			if (!$result)
				usleep(300);
		}
		
		// Insertar lineas
		if ($result)
			$result = $this->insertarLineas($id);		
		
		if ($result) {
			
			$fichSisPago = _DOCUMENT_ROOT.'cesta/sistpago/'.strtolower($codPago).'.php';
			
			// Forma de pago aparte, se redirecciona
			if (file_exists($fichSisPago)) {
				include($fichSisPago);
				include("../includes/right_bottom.php");
				echo '</div>';
			}
		
			// Pedido creado, no pagado
			else {
				echo $__LIB->fasesPedido('creado');
				echo '<p>';
				echo '<div class="msgInfo">'._PEDIDO_CORRECTO.'</div>';
				echo $__LIB->imprimirDocFacturacion("pedido", $id);		
   				echo $__LIB->enviarMailPedido($id);
				echo '<p class="separador"><br/><a class="botLista" href="'._WEB_ROOT_SSL_L.'cuenta/pedidos.php">'._LISTA_PEDIDOS.'</a>';
			}
		}
		else {
			echo _PEDIDO_INCORRECTO;
			echo '<p><br/><a class="botLista" href="'._WEB_ROOT_L.'general/contactar.php">'. _CONTACTAR.'</a>';
		}
			
		// Vaciar la cesta y el pedido
   	   	unset($_SESSION["pedido"]);
   	   	unset($_SESSION["cesta"]);
  	   	$_SESSION["cesta"] = new cesta();
	
		echo '</div>';
	}
	
	// Para extender
	function editarPedido($pedido)
	{
		return $pedido;
	} 
	
	// Para extender
	function editarLinea($linea)
	{
		return $linea;
	} 
	
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_crearPedido */
class crearPedido extends oficial_crearPedido {};

$iface_crearPedido = new crearPedido;
$iface_crearPedido->contenidos();

?>

<?php include("../includes/right_bottom.php") ?>
