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

/** @class_definition oficial_cesta */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

// Clase de funcionalidades de la cesta
class oficial_cesta 
{
	var $codigos;
	var $cantidades;
	var $num_articulos;
	var $total;

	function oficial_cesta() 
	{
		$this->num_articulos = 0;
	}
	
	// Introduce un articulo
	function introduce_articulo($cod_prod) 
	{
 		if ($this->enCesta($cod_prod))	{
 			echo '<p><span class="msgInfo">'._EN_CESTA.'</span>';
		}
		else {
			$this->codigos[$this->num_articulos] = $cod_prod;
			$this->cantidades[$this->num_articulos] = 1;
			$this->num_articulos++;
		}
	}

	// Actualiza la cesta cuando ha habido un cambio de cantidad o un borrado
	function actualizarCesta() 
	{
		global $CLEAN_POST;
	
		if (isset($CLEAN_POST["actualizar"]))
			if ($CLEAN_POST["actualizar"] != 1)
				return;
			
		for ($i=0; $i < $this->num_articulos; $i++) {
			
			// Borrado
			if (isset($CLEAN_POST["eliminar_".$i]))
				if ($CLEAN_POST["eliminar_".$i] == 'on')
 					$this->elimina_articulo($i);
			
			// Cantidad
			if (isset($CLEAN_POST["cantidad_".$i])) {
				$cantidad = (int) $CLEAN_POST["cantidad_".$i];
				if ($cantidad) {
					$this->cantidades[$i] = $cantidad;
				}
				else
					$this->cantidades[$i] = 1;
			}
		}
	}

	// Devuelve el importe total
	function total($codPago = '', $codEnvio = '') 
	{
		global $__BD, $__LIB, $__CAT;
		
		if ($this->cestaVacia())
			return 0;
		
		$total = 0;
		for ($i=0; $i < $this->num_articulos; $i++){
			
			$referencia = $this->codigos[$i];
			
			if($referencia!='null'){
	
				$cantidad = $this->cantidades[$i];
				$ordenSQL = "select * from articulos where referencia='$referencia'";
				$result = $__BD->db_query($ordenSQL);	
				$row = $__BD->db_fetch_array($result);
	
				$codImpuesto = $row["codimpuesto"];
				$iva = $__CAT->selectIVA($codImpuesto);
				$pvps = $__CAT->pvp($row);
				$netoSinDto;
				if ($__LIB->esTrue($row["ivaincluido"])) {
					$netoSinDto = $pvps["coniva"] * $cantidad;
				}
				else {
					$netoSinDto = ($pvps["siniva"] * $cantidad) * (1 + $iva / 100);
				}
		
				$netoSinDto = number_format($netoSinDto,2,".","");
				
				$total += $netoSinDto;
			}
		}
	
		if ($__LIB->esTrue($_SESSION["opciones"]["activarcoddescuento"])) {
			$hoy = date("Y-m-d");
			$codDescuento = $_SESSION["pedido"]["datosEnv"]["coddescuento"];
			$ordenSQL = "select * from codigosdescuento where codigo='$codDescuento' and activo=true and (caducidad is null or caducidad >= '$hoy')";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_array($result);
			
			if ($row["id"]) {
				$precio = 0 - $total * $row["dtopor"] / 100 - $row["dtolineal"];
				$total += $precio;
			}
		}

		if ($codEnvio) {
			$result = $__BD->db_query("select * from formasenvio where codenvio = '".$codEnvio."'");
			$row = $__BD->db_fetch_array($result);
			
			if ($__LIB->envioGratuito($codEnvio))
				$precio = 0;
			else {
				$codPeso = $this->intervaloPeso();
				$codZona = $__LIB->zonaEnvio($_SESSION["pedido"]["datosEnv"]["codpais_env"], $_SESSION["pedido"]["datosEnv"]["provincia_env"]);
				$precio = $__LIB->precioEnvio($row, $codPeso, $codZona);
			}

			$total += $precio;
		}

		if ($codPago) {	
		
			$ordenSQL = "select * from formaspago where codpago = '".$codPago."'";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_array($result);
			
			$gastos = 0;
			if ($__LIB->esTrue($row["gastosfijo"]))
				$gastos = $row["gastosfijo"];
			
			if ($__LIB->esTrue($row["gastos"]) || $gastos > 0) {

				$gastos += $total * $row["gastos"] / 100;
				$row["pvp"] = $gastos;
				
				$precio = $__CAT->precioArticulo($row, false, true, true);

				$total += $precio;
			}
			
		}

		return $total;
	}
	
	// Devuelve el importe total
	function total_old($codPago = '', $codEnvio = '') 
	{
		global $__BD, $__LIB, $__CAT;
		
		if ($this->cestaVacia())
			return 0;
		
		$total = 0;
		
		for ($i=0; $i < $this->num_articulos; $i++){
			
			$referencia = $this->codigos[$i];
			
			if($referencia!='null'){
								
				$result = $__BD->db_query("select * from articulos where referencia='$referencia'");	
				$row = $__BD->db_fetch_array($result);
				
				$descripcion = $this->descripcionLinea($row, $i);
				$precio = $this->netoLinea($row, $i);
				$impuesto = $this->impuestoLinea($row, $i);
				
				$cantidad = $this->cantidades[$i];				
				$importe = ($precio + $impuesto) * $cantidad;
				
				$total += $importe;
			}
		}
		
		if ($__LIB->esTrue($_SESSION["opciones"]["activarcoddescuento"])) {
			$hoy = date("Y-m-d");
			$codDescuento = $_SESSION["pedido"]["datosEnv"]["coddescuento"];
			$ordenSQL = "select * from codigosdescuento where codigo='$codDescuento' and activo=true and (caducidad is null or caducidad >= '$hoy')";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_array($result);
			
			if ($row["id"]) {
				$precio = 0 - $total * $row["dtopor"] / 100 - $row["dtolineal"];
				$total += $precio;
			}
		}
		
		if ($codEnvio) {
			$result = $__BD->db_query("select * from formasenvio where codenvio = '".$codEnvio."'");
			$row = $__BD->db_fetch_array($result);
			
			if ($__LIB->envioGratuito($codEnvio))
				$precio = 0;
			else {
				$codPeso = $this->intervaloPeso();
				$codZona = $__LIB->zonaEnvio($_SESSION["pedido"]["datosEnv"]["codpais_env"], $_SESSION["pedido"]["datosEnv"]["provincia_env"]);
				$precio = $__LIB->precioEnvio($row, $codPeso, $codZona);
			}

			$total += $precio;
		}

		if ($codPago) {	
		
			$ordenSQL = "select * from formaspago where codpago = '".$codPago."'";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_array($result);
			
			$gastos = 0;
			if ($__LIB->esTrue($row["gastosfijo"]))
				$gastos = $row["gastosfijo"];
			
			if ($__LIB->esTrue($row["gastos"]) || $gastos > 0) {

				$gastos += $total * $row["gastos"] / 100;			
				$row["pvp"] = $gastos;
				
				$precio = $__CAT->precioArticulo($row, false, true, true);
				$total += $precio;
			}			
			
		}

		return $total;
	}

	// Devuelve el peso total de la cesta
	function peso() 
	{
		global $__BD;
		
		if ($this->cestaVacia())
			return 0;
		
		$pesoTotal = 0;
		
		for ($i=0; $i < $this->num_articulos; $i++) {
			
			$referencia = $this->codigos[$i];
			
			if($referencia!='null'){
				$peso = $__BD->db_valor("select peso from articulos where referencia='$referencia'");	
				$cantidad = $this->cantidades[$i];
				$pesoTotal += $peso * $cantidad;
			}
		}
		
		return $pesoTotal;
	}
	

	// Devuelve el peso total de la cesta
	function intervaloPeso() 
	{
		global $__BD;
		
		if ($this->cestaVacia())
			return 0;
		
		$pesoTotal = $this->peso();
		$pesoTotal /= 1000;
		
		$ordenSQL = "select codigo from intervalospesos where desde <= $pesoTotal and hasta >= $pesoTotal";
		$ivPeso = $__BD->db_valor($ordenSQL);
		
		return $ivPeso;
	}
	
	// Imprime la cesta
	function imprime_cesta_old() 
	{
		global $__BD, $__CAT, $__LIB;
		
		$this->actualizarCesta();
			
		if ($this->cestaVacia())
			return false;
		
		$codigo = '';
		
		$codigo .= '<form action="general/cesta.php" method="post">';
		
		$codigo .= '<table class="cesta" cellspacing="0">';
		
		$codigo .= '<tr>';
		$codigo .= '<th class="desc">'._ARTICULO.'</th>';
				
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])) {
					$codigo .= '<th>'._PRECIO.'</th>';
					$codigo .= '<th>'._IVA.'</th>';
				}
				else
					$codigo .= '<th>'._PRECIO.'</th>';
			
		$codigo .= '<th>'._CANTIDAD.'</th>
				<th>'._IMPORTE.'</th>
				<th>'._ELIMINAR.'</th>';
		
		$codigo .= '</tr>';
		$total = 0;
		$totalIVA = 0;
		$totalNeto = 0;
		
		for ($i=0; $i < $this->num_articulos; $i++){
			$referencia = $this->codigos[$i];
			
			
			if($referencia != 'null'){
								
				$result = $__BD->db_query("select * from articulos where referencia='$referencia'");	
				$row = $__BD->db_fetch_array($result);
				
				$descripcion = $this->descripcionLinea($row, $i);
				$precio = $this->netoLinea($row, $i);
				$impuesto = $this->impuestoLinea($row, $i);
				$link = $__CAT->linkArticulo($row["referencia"], $row["descripciondeeplink"]);
				
				$cantidad = $this->cantidades[$i];				
				$importe = ($precio + $impuesto) * $cantidad;
				
				$total += $importe;
				
				$codigo .= '<tr>';
				$codigo .= '<td class="desc"><a href="'.$link.'">'.$descripcion.'</a></td>';
				
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])) {
					$codigo .= '<td>'.$__CAT->precioDivisa($precio).'</td>';				
					$codigo .= '<td>'.$__CAT->precioDivisa($impuesto).'</td>';
				}
				else 
					$codigo .= '<td>'.$__CAT->precioDivisa($precio + $impuesto).'</td>';
				
				$codigo .= '<td>';
				$codigo .= '<input type="text" size="3" name="cantidad_'.$i.'" value="'.$cantidad.'"/>';
				$codigo .= '</td>';
				$codigo .= '<td>'.$__CAT->precioDivisa($importe).'</td>';
				$codigo .= '<td>';
				$codigo .= '<input type="checkbox" class="rdo" name="eliminar_'.$i.'"/>';
				$codigo .= '</td>';
				
				$codigo .= '</tr>';
			}
		}
		
		$codigo .= '<tr class="totales">';
		$codigo .= '<td colspan="3" class="total">'._TOTAL.'</td>';;
		if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
			$codigo .= '<td>&nbsp;</td>';
		$codigo .= '<td>'.$__CAT->precioDivisa($total).'</td>';
		$codigo .= '<td>&nbsp;</td>';
		$codigo .= '</tr>';
		$codigo .= '</table>';
		
		$linkCrearPedido = '<a class="button" href="cesta/datos_envio.php"><span>'._CREAR_PEDIDO.'</span></a>';
		
		$codigo .= '<div id="botones">';
 		$codigo .= '<br/><br/>';
		$codigo .= '<input type="hidden" name="actualizar" value="1"/>';
		$codigo .= '<button type="submit" value="'._ACTUALIZAR.'" class="submitBtn"><span>'._ACTUALIZAR.'</span></button>';
 		$codigo .= $linkCrearPedido;
		$codigo .= '</div>';
		
		$codigo .= '</form>';
		
		echo $codigo;

		return true;
	}
	
	// Imprime la cesta
	function imprime_cesta() 
	{
		global $__BD, $__CAT, $__LIB;
		
		$this->actualizarCesta();
			
		if ($this->cestaVacia())
			return false;
		
		$codigo = '';
		
		$codigo .= '<form action="general/cesta.php" method="post">';
		
		$codigo .= '<table class="cesta" cellspacing="0">';
		
		$codigo .= '<tr>';
		$codigo .= '<th class="desc">'._ARTICULO.'</th>';
		$codigo .= '<th>'._PRECIO.'</th>';
		$codigo .= '<th>'._CANTIDAD.'</th>
				<th>'._IMPORTE.'</th>
				<th>'._ELIMINAR.'</th>';
		
		$codigo .= '</tr>';
		$totalIVA = 0;
		$totalNeto = 0;
		$arrayImpuestos;
		$arrayImpuestos["18"] = 0;
		$arrayImpuestos["8"] = 0;
		$arrayImpuestos["4"] = 0;
		$totales;
		$totales["coniva"] = 0;
		$totales["siniva"] = 0;

		for ($i=0; $i < $this->num_articulos; $i++){
			$referencia = $this->codigos[$i];
			
			
			if($referencia != 'null'){
								
				$result = $__BD->db_query("select * from articulos where referencia='$referencia'");	
				$row = $__BD->db_fetch_array($result);
				
				$descripcion = $this->descripcionLinea($row, $i);
				$link = $__CAT->linkArticulo($row["referencia"], $row["descripciondeeplink"]);
				$cantidad = $this->cantidades[$i];

				$codImpuesto = $row["codimpuesto"];
				$iva = $__CAT->selectIVA($codImpuesto);
				$pvps = $this->pvp($row, $i);

				$netoSinDto;
				 if ($__LIB->esTrue($row["ivaincluido"])) {
					$netoSinDto["siniva"] = ($pvps["coniva"] * $cantidad) / (1 + $iva / 100);
					$netoSinDto["coniva"] = $pvps["coniva"] * $cantidad;
				}
				else {
					$netoSinDto["siniva"] = $pvps["siniva"] * $cantidad;
					$netoSinDto["coniva"] = ($pvps["siniva"] * $cantidad) * (1 + $iva / 100);
				}
		
				$netoSinDto["siniva"] = number_format($netoSinDto["siniva"],2,".","");
				$netoSinDto["coniva"] = number_format($netoSinDto["coniva"],2,".","");

				$totales["siniva"] += $netoSinDto["siniva"];
				$totales["coniva"] += $netoSinDto["coniva"];

				if($iva > 0)
					$arrayImpuestos[$iva] += $netoSinDto["siniva"];

				$codigo .= '<tr>';
				$codigo .= '<td class="desc"><a href="'.$link.'">'.$descripcion.'</a></td>';

				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])) {
					$codigo .= '<td>'.$__CAT->precioDivisa($pvps["siniva"]).'</td>';
				}
				else {
					$codigo .= '<td>'.$__CAT->precioDivisa($pvps["coniva"]).'</td>';
				}

				$codigo .= '<td>';
				$codigo .= '<input type="text" size="3" name="cantidad_'.$i.'" value="'.$cantidad.'"/>';
				$codigo .= '</td>';
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
					$codigo .= '<td>'.$__CAT->precioDivisa($netoSinDto["siniva"]).'</td>';
				else
					$codigo .= '<td>'.$__CAT->precioDivisa($netoSinDto["coniva"]).'</td>';
				$codigo .= '<td>';
				$codigo .= '<input type="checkbox" class="rdo" name="eliminar_'.$i.'"/>';
				$codigo .= '</td>';
				
				$codigo .= '</tr>';
			}
		}
	
		if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])){
			$codigo .= '<tr class="subtotales">';
			$codigo .= '<td colspan="3" class="txtsubtotal">'._NETO.'</td>';
			
			$codigo .= '<td>'.$__CAT->precioDivisa($totales["siniva"]).'</td>';
			$codigo .= '<td>&nbsp;</td></tr>';

			if($arrayImpuestos["18"] != 0) {
				$codigo .= '<tr><td colspan="3" class="txtsubtotal">IVA 18%</td>';
				$iva = $arrayImpuestos["18"]* 18 / 100;
				$codigo .= '<td>'.$__CAT->precioDivisa($iva).'</td></tr>';
			}
			if($arrayImpuestos["8"] != 0) {
				$codigo .= '<tr><td colspan="3" class="txtsubtotal">IVA 8%</<td>';;
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
		$codigo .= '<td>'.$__CAT->precioDivisa($totales["coniva"]).'</td>';
		$codigo .= '<td>&nbsp;</td>';
		$codigo .= '</tr>';
		$codigo .= '</table>';
		
		$linkCrearPedido = '<a class="button" href="cesta/datos_envio.php"><span>'._CREAR_PEDIDO.'</span></a>';
		
		$codigo .= '<div id="botones">';
 		$codigo .= '<br/><br/>';
		$codigo .= '<input type="hidden" name="actualizar" value="1"/>';
		$codigo .= '<button type="submit" value="'._ACTUALIZAR.'" class="submitBtn"><span>'._ACTUALIZAR.'</span></button>';
 		$codigo .= $linkCrearPedido;
		$codigo .= '</div>';
		
		$codigo .= '</form>';
		
		echo $codigo;

		return true;
	}
	
	// Imprime la cesta para el resumen o confirmacion de un pedido
	function imprime_cesta_pedido($codEnvio = false, $codPago = false) 
	{
		global $__BD, $__LIB, $__CAT;
		
		if ($this->cestaVacia())
			return false;
		
		$codigo = '';
		
		$codigo .= '<table class="cesta" cellspacing="0">';
		
		$codigo .= '<tr>';
		$codigo .= '<th class="desc">'._ARTICULO.'</th>';
		$codigo .= '<th>'._PRECIO.'</th>';
		$codigo .= '<th>'._CANTIDAD.'</th>
				<th>'._IMPORTE.'</th>';
		
		$codigo .= '</tr>';
		$totalIVA = 0;
		$totalNeto = 0;
		$arrayImpuestos;
		$arrayImpuestos["18"] = 0;
		$arrayImpuestos["8"] = 0;
		$arrayImpuestos["4"] = 0;
		$totales;
		$totales["coniva"] = 0;
		$totales["siniva"] = 0;

		for ($i=0; $i < $this->num_articulos; $i++){
			$referencia = $this->codigos[$i];
			
			
			if($referencia != 'null'){
								
				$result = $__BD->db_query("select * from articulos where referencia='$referencia'");	
				$row = $__BD->db_fetch_array($result);
				
				$descripcion = $this->descripcionLinea($row, $i);
				$link = $__CAT->linkArticulo($row["referencia"], $row["descripciondeeplink"]);
				$cantidad = $this->cantidades[$i];

				$codImpuesto = $row["codimpuesto"];
				$iva = $__CAT->selectIVA($codImpuesto);
				$pvps = $this->pvp($row, $i);

				$netoSinDto;
				 if ($__LIB->esTrue($row["ivaincluido"])) {
					$netoSinDto["siniva"] = ($pvps["coniva"] * $cantidad) / (1 + $iva / 100);
					$netoSinDto["coniva"] = $pvps["coniva"] * $cantidad;
				}
				else {
					$netoSinDto["siniva"] = $pvps["siniva"] * $cantidad;
					$netoSinDto["coniva"] = ($pvps["siniva"] * $cantidad) * (1 + $iva / 100);
				}
		
				$netoSinDto["siniva"] = number_format($netoSinDto["siniva"],2,".","");
				$netoSinDto["coniva"] = number_format($netoSinDto["coniva"],2,".","");

				$totales["siniva"] += $netoSinDto["siniva"];
				$totales["coniva"] += $netoSinDto["coniva"];

				if($iva > 0)
					$arrayImpuestos[$iva] += $netoSinDto["siniva"];

				$codigo .= '<tr>';
				$codigo .= '<td class="desc"><a href="'.$link.'">'.$descripcion.'</a></td>';

				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])) {
					$codigo .= '<td>'.$__CAT->precioDivisa($pvps["siniva"]).'</td>';
				}
				else {
					$codigo .= '<td>'.$__CAT->precioDivisa($pvps["coniva"]).'</td>';
				}

				$codigo .= '<td>';
				$codigo .= '<input type="text" size="3" name="cantidad_'.$i.'" value="'.$cantidad.'"/>';
				$codigo .= '</td>';
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
					$codigo .= '<td>'.$__CAT->precioDivisa($netoSinDto["siniva"]).'</td>';
				else
					$codigo .= '<td>'.$__CAT->precioDivisa($netoSinDto["coniva"]).'</td>';

				$codigo .= '</tr>';
			}
		}

		$descuento = 0;
		if ($__LIB->esTrue($_SESSION["opciones"]["activarcoddescuento"])) {
			$hoy = date("Y-m-d");
			$codDescuento = $_SESSION["pedido"]["datosEnv"]["coddescuento"];
			$ordenSQL = "select * from codigosdescuento where codigo='$codDescuento' and activo=true and (caducidad is null or caducidad >= '$hoy')";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_array($result);
			
			if ($row["id"]) {
				if ($row["dtopor"] > 0)
					$lblDto = $row["dtopor"].'%';
				if ($row["dtolineal"] > 0)
					$lblDto = $__CAT->precioDivisa($row["dtolineal"]);
				
				$descuento = 0 - $totales["coniva"] * $row["dtopor"] / 100 - $row["dtolineal"];
				
				$codigo .= '<tr>';
				$codigo .= '<td class="desc">'._DTO.' '.$row["codigo"].' ('.$lblDto.')</div>';
				$codigo .= '<td colspan="2"></td>';
			
				$codigo .= '<td>'.$__CAT->precioDivisa($descuento).'</td>';
				$codigo .= '</tr>';
			
				$totales["coniva"] += $descuento;
				$totales["siniva"] += $descuento;
			}
		}
		
		$gastosEnvio = 0;
		if ($codEnvio) {
			$result = $__BD->db_query("select * from formasenvio where codenvio = '".$codEnvio."'");
			$row = $__BD->db_fetch_array($result);
			if ($__LIB->envioGratuito($codEnvio))
				$precio = 0;
			else {
				$codPeso = $this->intervaloPeso();
				$codZona = $__LIB->zonaEnvio($_SESSION["pedido"]["datosEnv"]["codpais_env"], $_SESSION["pedido"]["datosEnv"]["provincia_env"]);
				$gastosEnvio = $__LIB->precioEnvio($row, $codPeso, $codZona);
			}
 			
			$codigo .= '<tr>';
			$codigo .= '<td class="desc">'._GASTOS_ENVIO.'</td>';
			$codigo .= '<td colspan="2">&nbsp;</td>';
			$codigo .= '<td>'.$__CAT->precioDivisa($gastosEnvio).'</td>';
			$codigo .= '</tr>';

			$totales["siniva"] += $gastosEnvio;
			$totales["coniva"] += $gastosEnvio;
		}

		$gastosPago = 0;
		if ($codPago) {	
			$ordenSQL = "select * from formaspago where codpago = '".$codPago."'";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_array($result);
			
			$codImpuesto = $row["codimpuesto"];
			$iva = $__CAT->selectIVA($codImpuesto);

			$gastos;
			$gastos["coniva"] = 0;
			$gastos["siniva"] = 0;
			if ($__LIB->esTrue($row["gastosfijo"])) {
				if ($__LIB->esTrue($row["ivaincluido"])) {
					$gastos["coniva"] = $row["gastosfijo"];
					$gastos["siniva"] = $row["gastosfijo"]/ (1 + $iva / 100);
				}
				else {
					$gastos["coniva"] = $row["gastosfijo"] * (1 + $iva / 100);
					$gastos["siniva"] = $row["gastosfijo"];
				}
			}
			
			if ($__LIB->esTrue($row["gastos"]) || $gastos > 0) {
				$gastos["coniva"] += $totales["coniva"] * $row["gastos"] / 100;
				$gastos["siniva"] += $totales["siniva"] * $row["gastos"] / 100;
			}
	
			if($iva > 0)
				$arrayImpuestos[$iva] += $gastos["siniva"];

			$codigo .= '<tr>';
			$codigo .= '<td class="desc">'._GASTOS_PAGO.'</td>';
			$codigo .= '<td colspan="2">&nbsp;</td>';	
			if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
				$codigo .= '<td>'.$__CAT->precioDivisa($gastos["siniva"]).'</td>';
			else
				$codigo .= '<td>'.$__CAT->precioDivisa($gastos["coniva"]).'</td>';
			$codigo .= '</tr>';
		
			$totales["siniva"] += $gastos["siniva"];
			$totales["coniva"] += $gastos["coniva"];
		}

		if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])){
			$codigo .= '<tr class="subtotales">';
			$codigo .= '<td colspan="3" class="txtsubtotal">'._NETO.'</td>';
			
			$codigo .= '<td>'.$__CAT->precioDivisa($totales["siniva"]).'</td>';
			$codigo .= '</tr>';

			if($arrayImpuestos["18"] != 0) {
				$codigo .= '<tr><td colspan="3" class="txtsubtotal">IVA 18%</td>';
				$iva = $arrayImpuestos["18"]* 18 / 100;
				$codigo .= '<td>'.$__CAT->precioDivisa($iva).'</td></tr>';
			}
			if($arrayImpuestos["8"] != 0) {
				$codigo .= '<tr><td colspan="3" class="txtsubtotal">IVA 8%</<td>';;
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
		$codigo .= '<td>'.$__CAT->precioDivisa($totales["coniva"]).'</td>';
		$codigo .= '</tr>';
		$codigo .= '</table>';

		return $codigo;
	}

	// Imprime la cesta para el resumen o confirmacion de un pedido
	function imprime_cesta_pedido_old($codEnvio = false, $codPago = false) 
	{
		global $__BD, $__LIB, $__CAT;
		
		if ($this->cestaVacia())
			return false;
		
		$codigo = '';
		
		$codigo .= '<table class="cesta" cellspacing="0">';
		
		$codigo .= '<tr>';
		$codigo .= '<th class="desc">'._ARTICULO.'</th>';
				
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])) {
					$codigo .= '<th>'._PRECIO.'</th>';
					$codigo .= '<th>'._IVA.'</th>';
				}
				else
					$codigo .= '<th>'._PRECIO.'</th>';
			
		$codigo .= '<th>'._CANTIDAD.'</th>
				<th>'._IMPORTE.'</th>';
		
		$codigo .= '</tr>';
		$total = 0;
		$totalIVA = 0;
		$totalNeto = 0;
		
		for ($i=0; $i < $this->num_articulos; $i++){
			$referencia = $this->codigos[$i];
			
			
			if($referencia != 'null'){
								
				$result = $__BD->db_query("select * from articulos where referencia='$referencia'");	
				$row = $__BD->db_fetch_array($result);
				
				$descripcion = $this->descripcionLinea($row, $i);
				$precio = $this->netoLinea($row, $i);
				$impuesto = $this->impuestoLinea($row, $i);
				$link = $__CAT->linkArticulo($row["referencia"], $row["descripciondeeplink"]);
				
				$cantidad = $this->cantidades[$i];				
				$importe = ($precio + $impuesto) * $cantidad;
				
				$total += $importe;
				
				$codigo .= '<tr>';
				$codigo .= '<td class="desc"><a href="'.$link.'">'.$descripcion.'</a></td>';
				
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])) {
					$codigo .= '<td>'.$__CAT->precioDivisa($precio).'</td>';				
					$codigo .= '<td>'.$__CAT->precioDivisa($impuesto).'</td>';
				}
				else 
					$codigo .= '<td>'.$__CAT->precioDivisa($precio + $impuesto).'</td>';
				
				$codigo .= '<td>';
				$codigo .= $cantidad;
				$codigo .= '</td>';
				$codigo .= '<td>'.$__CAT->precioDivisa($importe).'</td>';
				
				$codigo .= '</tr>';
			}
		}
		
		
		if ($__LIB->esTrue($_SESSION["opciones"]["activarcoddescuento"])) {
			$hoy = date("Y-m-d");
			$codDescuento = $_SESSION["pedido"]["datosEnv"]["coddescuento"];
			$ordenSQL = "select * from codigosdescuento where codigo='$codDescuento' and activo=true and (caducidad is null or caducidad >= '$hoy')";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_array($result);
			
			if ($row["id"]) {
				if ($row["dtopor"] > 0)
					$lblDto = $row["dtopor"].'%';
				if ($row["dtolineal"] > 0)
					$lblDto = $__CAT->precioDivisa($row["dtolineal"]);
				
				$precio = 0 - $total * $row["dtopor"] / 100 - $row["dtolineal"];
				
				$codigo .= '<tr>';
				$codigo .= '<td class="desc">'._DTO.' '.$row["codigo"].' ('.$lblDto.')</div>';
				$codigo .= '<td colspan="2"></td>';
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
					$codigo .= '<td>&nbsp;</td>';				
				$codigo .= '<td>'.$__CAT->precioDivisa($precio).'</td>';
				$codigo .= '</tr>';
			
				$total += $precio;
			}
		}
		
		
		if ($codEnvio) {
			$result = $__BD->db_query("select * from formasenvio where codenvio = '".$codEnvio."'");
			$row = $__BD->db_fetch_array($result);
			if ($__LIB->envioGratuito($codEnvio))
				$precio = 0;
			else {
				$codPeso = $this->intervaloPeso();
				$codZona = $__LIB->zonaEnvio($_SESSION["pedido"]["datosEnv"]["codpais_env"], $_SESSION["pedido"]["datosEnv"]["provincia_env"]);
				$precio = $__LIB->precioEnvio($row, $codPeso, $codZona);
			}
			
			$codigo .= '<tr>';
			$codigo .= '<td class="desc">'._GASTOS_ENVIO.'</td>';
			$codigo .= '<td colspan="2">&nbsp;</td>';				
			if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
				$codigo .= '<td>&nbsp;</td>';				
			$codigo .= '<td>'.$__CAT->precioDivisa($precio).'</td>';
			$codigo .= '</tr>';

			$total += $precio;
		}

		if ($codPago) {
			$ordenSQL = "select * from formaspago where codpago = '".$codPago."'";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_array($result);
			
			$gastos = 0;
			if ($__LIB->esTrue($row["gastosfijo"]))
				$gastos = $row["gastosfijo"];
			
			if ($__LIB->esTrue($row["gastos"]) || $gastos > 0) {

				$gastos += $total * $row["gastos"] / 100;			
				$row["pvp"] = $gastos;
				
				$precio = $__CAT->precioArticulo($row, false, true, true);
				
				$codigo .= '<tr>';
				$codigo .= '<td class="desc">'._GASTOS_PAGO.'</td>';
				$codigo .= '<td colspan="2">&nbsp;</td>';				
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
					$codigo .= '<td>&nbsp;</td>';				
				$codigo .= '<td>'.$__CAT->precioDivisa($precio).'</td>';
				$codigo .= '</tr>';
			
				$total += $precio;
			}			
			
		}
		
		
		
		$codigo .= '<tr class="totales">';
		$codigo .= '<td colspan="3" class="total">'._TOTAL.'</td>';;
		if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
			$codigo .= '<td>&nbsp;</td>';
		$codigo .= '<td>'.$__CAT->precioDivisa($total).'</td>';
		$codigo .= '</tr>';
		$codigo .= '</table>';

		return $codigo;
	}
	

	// Elimina un articulo
	function elimina_articulo($linea)
	{
 		$this->codigos[$linea] = 'null';
	}
	
	// Verifica si la cesta esta vacia
	function cestaVacia() 
	{
		for ($i=0; $i < $this->num_articulos; $i++) {
			if($this->codigos[$i]!='null')
				return false;
		}
		return true;
	}
	
	// Verifica si una referencia ya esta en la cesta
	function enCesta($referencia) 
	{
		for ($i=0;$i<$this->num_articulos;$i++) {
			if ($this->codigos[$i] == $referencia) return true;
		}
		return false;
	}
	
	// Devuelve un Array con las referencias de los articulos de la cesta
	function lista_articulos() 
	{
		$lista = '';
		for ($i=0;$i<$this->num_articulos;$i++){
			if($this->codigos[$i]!='null'){
				$lista .= ' ['.$this->codigos[$i].']';
			}
		}
		return $lista;
	}
	
	// Para personalizar en extensiones
	function descripcionLinea($row, $linea) 
	{
		global $__LIB;
	
		$descripcion = $__LIB->traducir("articulos", "descripcion", $row["referencia"], $row["descripcion"]);
		return $descripcion;
	}
	
	// Para personalizar en extensiones
	function netoLinea($row, $linea) 
	{
		global $__CAT;
	
		$precio = $__CAT->precioNeto($row);
		return $precio;
	}
	
	// Para personalizar en extensiones
	function pvp($row, $linea) 
	{
		global $__CAT;
	
		$precio = $__CAT->pvp($row);
		return $precio;
	}

	// Para personalizar en extensiones
	function impuestoLinea($row, $linea) 
	{
		global $__CAT;
	
		$impuesto = $__CAT->impuestoArticulo($row);
		return $impuesto;
	}
	
	
} 

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_cesta */
class cesta extends oficial_cesta 
{
	function cesta() 
	{
		$this->num_articulos = 0;
	}
}

?>