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
	function total() 
	{
		global $__BD, $__CAT;
		
		if ($this->cestaVacia())
			return false;
		
		$total = 0;
		$totalIVA = 0;
		$totalNeto = 0;
		
		for ($i=0; $i < $this->num_articulos; $i++){
			
			$referencia = $this->codigos[$i];
			
			if($referencia!='null'){
								
				$result = $__BD->db_query("select pvp, codimpuesto, enoferta, pvpoferta from articulos where referencia='$referencia'");	
				$row = $__BD->db_fetch_array($result);
				$precio = $__CAT->precioArticulo($row);
				$cantidad = $this->cantidades[$i];
				$importe = $precio * $cantidad;
 				$total += $importe;
			}
		}
		return $total;
	}
	
	

	// Imprime la cesta
	function imprime_cesta() 
	{
		global $__BD, $__CAT, $__LIB;
		
		$this->actualizarCesta();
			
		if ($this->cestaVacia())
			return false;
		
		echo '<form name="cesta" action="cesta.php" method="post">';
		
		echo '<div>';
		echo '<div class="cabeceraDescCesta">'._ARTICULO.'</div>';
				
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])) {
					echo '<div class="cabeceraDatoCesta">'._PRECIO.'</div>';
					echo '<div class="cabeceraDatoCesta">'._IVA.'</div>';
				}
				else
					echo '<div class="cabeceraDatoCesta">'._PRECIO.'</div>';
			
		echo '<div class="cabeceraDatoCesta">'._CANTIDAD.'</div>
				<div class="cabeceraDatoCesta">'._IMPORTE.'</div>
				<div class="cabeceraDatoCesta">'._ELIMINAR.'</div>';
		
		echo '</div>';
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
				
				$cantidad = $this->cantidades[$i];				
				$importe = ($precio + $impuesto) * $cantidad;
				
				$total += $importe;
				
				echo '<div>';
				echo '<div class="articuloCesta"><a href="'._WEB_ROOT.'/catalogo/articulo.php?ref='.$referencia.'">'.$descripcion.'</a></div>';
				
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])) {
					echo '<div class="datoCesta">'.$__CAT->precioDivisa($precio).'</div>';				
					echo '<div class="datoCesta">'.$__CAT->precioDivisa($impuesto).'</div>';
				}
				else 
					echo '<div class="datoCesta">'.$__CAT->precioDivisa($precio + $impuesto).'</div>';
				
				echo '<div class="datoCesta">';
				echo '<input type="text" size="3" name="cantidad_'.$i.'" value="'.$cantidad.'">';
				echo '</div>';
				echo '<div class="datoCesta">'.$__CAT->precioDivisa($importe).'</div>';
				echo '<div class="datoCesta">';
				echo '<input type="checkbox" name="eliminar_'.$i.'">';
				echo '</div>';
				
				echo '</div>';
			}
		}
		
		echo '<div id="labelTotalCesta">'._TOTAL.'</div>';;
		echo '<div class="datoCesta">&nbsp;</div>';
		echo '<div class="datoCesta">&nbsp;</div>';
		if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
			echo '<div class="datoCesta">&nbsp;</div>';
		echo '<div id="totalCesta">'.$__CAT->precioDivisa($total).'</div>';
		echo '<div class="datoCesta">&nbsp;</div>';
		
		echo '<p class="separador">';
		echo '<input type="hidden" name="actualizar" value="1">';
		
		echo '</form>';
		
		$linkCrearPedido = '<a class="botContinuar" href="'._WEB_ROOT_SSL.'cesta/datos_envio.php">'._CREAR_PEDIDO.'</a>';
		
		echo '<p class="separador">';
		
		echo '<div id="botones">';
 		echo '<a class="botActualizar" href="javascript:document.cesta.submit()">'._ACTUALIZAR.'</a>';
 		echo $linkCrearPedido;
		echo '</div>';
		
		echo '<br class="cleaner"/>';

		return true;
	}
	
	// Imprime la cesta para el resumen o confirmacion de un pedido
	function imprime_cesta_pedido($codEnvio = false, $codPago = false) 
	{
		global $__BD, $__LIB, $__CAT;
		
		if ($this->cestaVacia())
			return false;
		
		echo '<div class="cabeceraDescCesta">'._ARTICULO.'</div>';
				
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])) {
					echo '<div class="cabeceraDatoCesta">'._PRECIO.'</div>';
					echo '<div class="cabeceraDatoCesta">'._IVA.'</div>';
				}
				else
					echo '<div class="cabeceraDatoCesta">'._PRECIO.'</div>';
			
		echo '<div class="cabeceraDatoCesta">'._CANTIDAD.'</div>
				<div class="cabeceraDatoCesta">'._IMPORTE.'</div>';
		

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
				
				echo '<div class="articuloCesta"><a href="'._WEB_ROOT.'/catalogo/articulo.php?ref='.$referencia.'">'.$descripcion.'</a></div>';
				
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])) {
					echo '<div class="datoCesta">'.$__CAT->precioDivisa($precio).'</div>';				
					echo '<div class="datoCesta">'.$__CAT->precioDivisa($impuesto).'</div>';
				}
				else 
					echo '<div class="datoCesta">'.$__CAT->precioDivisa($precio + $impuesto).'</div>';
				
				echo '<div class="datoCesta">'.$cantidad.'</div>';
				echo '<div class="datoCesta">'.$__CAT->precioDivisa($importe).'</div>';
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
				
				echo '<div class="articuloCesta">'._DTO.' '.$row["codigo"].' ('.$lblDto.')</div>';
				echo '<div class="datoCesta">&nbsp;</div>';				
				echo '<div class="datoCesta">&nbsp;</div>';				
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
					echo '<div class="datoCesta">&nbsp;</div>';				
				echo '<div class="datoCesta">'.$__CAT->precioDivisa($precio).'</div>';				
			
				$total += $precio;
			}
		}
		
		if ($codEnvio) {
			$result = $__BD->db_query("select * from formasenvio where codenvio = '".$codEnvio."'");
			$row = $__BD->db_fetch_array($result);
			
			if ($__LIB->envioGratuito($codEnvio))
				$precio = 0;
			else {
				$precio = $row["pvp"];
			}
			
			echo '<div class="articuloCesta">'._GASTOS_ENVIO.'</div>';
			echo '<div class="datoCesta">&nbsp;</div>';				
			echo '<div class="datoCesta">&nbsp;</div>';				
			if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
				echo '<div class="datoCesta">&nbsp;</div>';				
			echo '<div class="datoCesta">'.$__CAT->precioDivisa($precio).'</div>';				

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
				
				echo '<div class="articuloCesta">'._GASTOS_PAGO.'</div>';
				echo '<div class="datoCesta">&nbsp;</div>';				
				echo '<div class="datoCesta">&nbsp;</div>';				
				if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
					echo '<div class="datoCesta">&nbsp;</div>';				
				echo '<div class="datoCesta">'.$__CAT->precioDivisa($precio).'</div>';				
			
				$total += $precio;
			}			
			
		}
		
		echo '<div id="labelTotalCesta">'._TOTAL.'</div>';;
		echo '<div class="datoCesta">&nbsp;</div>';				
		echo '<div class="datoCesta">&nbsp;</div>';				
		if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"]))
			echo '<div class="datoCesta">&nbsp;</div>';				
		echo '<div id="totalCesta">'.$__CAT->precioDivisa($total).'</div>';

		return true;
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