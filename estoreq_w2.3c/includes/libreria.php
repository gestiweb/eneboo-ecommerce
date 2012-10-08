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

		$codigo .= '<a class="inv" href="'._WEB_ROOT.'">'.strtolower(_INICIO).'</a>';
		$codigo .= '&nbsp;&middot;&nbsp;';
		$codigo .= '<a class="inv" href="'._WEB_ROOT_SSL.'cuenta/login.php">'.strtolower(_MI_CUENTA).'</a>';
		$codigo .= '&nbsp;&middot;&nbsp;';
		$codigo .= '<a class="inv" href="'._WEB_ROOT.'general/cesta.php">'.strtolower(_VER_CESTA).'</a>';
		$codigo .= '&nbsp;&middot;&nbsp;';
		$codigo .= '<a class="inv" href="'._WEB_ROOT_SSL.'cuenta/favoritos.php">'.strtolower(_FAVORITOS).'</a>';
		$codigo .= '&nbsp;&middot;&nbsp;';
		$codigo .= '<a class="inv" href="'._WEB_ROOT.'general/contactar.php">'.strtolower(_CONTACTAR).'</a>';
		
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
		$codigo .= '<select id="campo_'.$nomSelect.'" name="'.$nomSelect.'" onChange="xajax_selectProvincias(xajax.getFormValues(\''.$formName.'\'), \''.$tipo.'\')">';
		
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
			return '<div class="selecPais">'._SELEC_PAIS.'</div>';
		
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
		
			$onChange = '';
			
			if ($ambito == "cesta_envio")
				$onChange = ' onChange="xajax_cargarFormasEnvio(xajax.getFormValues(\'datosDirEnv\'))"';
				
			if ($ambito == "cesta_pago")
				$onChange = ' onChange="xajax_cargarFormasPago(xajax.getFormValues(\'datosDir\'))"';
		
			$listProvincias = '<option value="">-- '._SELECCIONAR.' --</option>'.$listProvincias;
			$codigo .= '<select id="campo_'.$nomCampo.'" name="'.$nomCampo.'" '.$onChange.'>';
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
		
		$ordenSQL = "select *, pvpunitarioiva as pvp from $tablaL where $campoId = $id";
		$datosLin = $__BD->db_query($ordenSQL);
		
		$codigo .= '<div class="titApartado">'.constant(strtoupper('_'.$tipoDoc)).' '.$datos["codigo"].'</div>';
		
		$codigo .= '<p><b>'._FECHA.'</b>: '.date("d-m-Y", strtotime($datos["fecha"]));
		
		if ($tipoDoc == 'pedido') {
			$codigo .= '<br><b>'._ENVIADO.'</b>: '.$datos["servido"];
			if ($datos["servido"] <> 'No') {	
				$fechaEnvio = date("d-m-Y", strtotime($datos["fechasalida"]));
				if ($fechaEnvio > '01-01-1970')
					$codigo .= '&nbsp;&nbsp;&nbsp;&nbsp;<b>'._FECHA_ENVIO.'</b>: '.date("d-m-Y", strtotime($datos[6]));
			}
		}
		
		
		$codigo .= '<div>';
		$codigo .= '<div class="cabeceraDescCesta">'._ARTICULO.'</div>';
		if ($__LIB->esTrue($_SESSION["opciones"]["desglosariva"])) {
			$codigo .= '<div class="cabeceraDatoCesta">'._PRECIO.'</div>';
			$codigo .= '<div class="cabeceraDatoCesta">'._IVA.'</div>';
		}
		else
			$codigo .= '<div class="cabeceraDatoCesta">'._PRECIO.'</div>';
			
		$codigo .= '<div class="cabeceraDatoCesta">'._CANTIDAD.'</div>';
		$codigo .= '<div class="cabeceraDatoCesta">'._IMPORTE.'</div>';
		$codigo .= '</div>';

		while ($row = $__BD->db_fetch_array($datosLin)) {
			
			$descripcion = $this->traducir("articulos", "descripcion", $row["referencia"], $row["descripcion"]);
			
			$precio = $__CAT->precioNeto($row, false, true, true);
			$impuesto = $__CAT->impuestoArticulo($row, true);
			$pvpUnitario = $precio + $impuesto;
			$pvpTotal = $pvpUnitario * $row["cantidad"];
		
			$codigo .= '<div>';
			$codigo .= '<div class="articuloCesta">'.$descripcion.'</div>';
			
			if ($this->esTrue($_SESSION["opciones"]["desglosariva"])) {
				$codigo .= '<div class="datoCesta">'.$__CAT->precioDivisa($precio).'</div>';				
				$codigo .= '<div class="datoCesta">'.$__CAT->precioDivisa($impuesto).'</div>';
			}
			else 
				$codigo .= '<div class="datoCesta">'.$__CAT->precioDivisa($precio + $impuesto).'</div>';
			
			$codigo .= '<div class="datoCesta">'.round($row["cantidad"]).'</div>';
			$codigo .= '<div class="datoCesta">'.$__CAT->precioDivisa($pvpTotal).'</div>';
			$codigo .= '</div>';
		}
		
		$codigo .= '<div>';
		$codigo .= '<div id="labelTotalCesta">'._TOTAL.'</div>';

		$codigo .= '<div class="datoCesta">&nbsp;</div>';
		if ($this->esTrue($_SESSION["opciones"]["desglosariva"]))
			$codigo .= '<div class="datoCesta">&nbsp;</div>';

		$codigo .= '<div class="datoCesta">&nbsp;</div>';

		$codigo .= '<div id="totalCesta">'.$__CAT->precioDivisa($datos["total"]).'</div>';
		$codigo .= '<div>';

		
		$pais = $__BD->db_valor("select nombre from paises where codpais = '".$datos["codpais"]."'");
		$paisEnv = $__BD->db_valor("select nombre from paises where codpais = '".$datos["codpaisenv"]."'");
		
		$formaPago = $__BD->db_valor("select descripcion from formaspago where codpago = '".$datos["codpago"]."'");
		$formaEnvio = $__BD->db_valor("select descripcion from formasenvio where codenvio = '".$datos["codenvio"]."'");
		
		// Envio
		$codigoEnvio = '';
		$codigoEnvio .= '<div class="titApartado">'._DATOS_ENVIO.'</div>';
		
		if ($formaEnvio) {
			$codigoEnvio .= '<p><b>'._FORMA_ENVIO.'</b><br>';
			$codigoEnvio .= $formaEnvio;
			$codigoEnvio .= '<p><b>'._DIRECCION.'</b>';
		}	
		
		$codigoEnvio .= '&nbsp;<br>';
		$codigoEnvio .= $datos["nombreenv"].' '.$datos["apellidosenv"];
		if ($datos["empresaenv"]) {
			$codigoEnvio .= '<br>';
			$codigoEnvio .= $datos["empresaenv"];
		}
		$codigoEnvio .= '<p>';
		$codigoEnvio .= $datos["direccionenv"];
		$codigoEnvio .= '<br>';
		$codigoEnvio .= $datos["codpostalenv"].' '.$datos["ciudadenv"].'&nbsp;&nbsp;'.$datos["provinciaenv"];
		$codigoEnvio .= '<br>';
		$codigoEnvio .= $paisEnv;
			
		if ($mostrarEnvio)
 			$codigo .= $codigoEnvio;
		
		// Pago
		$codigoPago = '';
		$codigoPago .= '<div class="titApartado">'._DATOS_PAGO.'</div>';
		
		if ($formaEnvio) {
			$codigoPago .= '<p><b>'._FORMA_PAGO.'</b><br>';
			$codigoPago .= $formaPago;
			$codigoPago .= '<p><b>'._DIRECCION.'</b>';
		}	
		
		$codigoPago .= '&nbsp;<br>';
		$codigoPago .= $datos["nombre"].' '.$datos["apellidos"];
		if ($datos["empresa"]) {
			$codigoPago .= '<br>';
			$codigoPago .= $datos["empresa"];
		}
		$codigoPago .= '<p>';
		$codigoPago .= $datos["direccion"];
		$codigoPago .= '<br>';
		$codigoPago .= $datos["codpostal"].' '.$datos["ciudad"].'&nbsp;&nbsp;'.$datos["provincia"];
		$codigoPago .= '<br>';
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
 		$this->enviarMail($email, $titulo, $texto);

		$emailWM = $_SESSION["opciones"]["emailwebmaster"];
		$this->enviarMail($emailWM, $titulo, $texto);
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
	
	// Envia un mail con formato HTML
    function enviarMail($destino, $titulo, $texto)
    {
        global $__BD;

        $codigoCSS = '';
        $fichCSS = _DOCUMENT_ROOT.'templates/'._TEMPLATE.'/mail.css';
        if (file_exists($fichCSS))
            $codigoCSS = file_get_contents($fichCSS);

        $codigo = '<html><head>'."\n";
        $codigo .= '<style type="text/css">'.$codigoCSS.'</style>'."\n";
        $codigo .= '</head><body>'."\n";

        $codigo .= nl2br($texto);

        $codigo .= '</body></html>';

        global $CONFIG_MAILER, $CONFIG_SMTPAUTH, $CONFIG_SMTPUSER,$CONFIG_SMTPPASS, $CONFIG_SMTPHOST,$CONFIG_SENDMAIL;
        require_once("phpmailer/class.phpmailer.php");
        $mail = new PHPMailer();

         $mail->PluginDir = _DOCUMENT_ROOT.'includes/phpmailer/';
         $mail->SetLanguage( 'en', _DOCUMENT_ROOT.'includes/phpmailer/language/' );
         $mail->CharSet = $_SESSION["opciones"]["charset"];
         $mail->IsMail();
         $mail->From = $_SESSION["opciones"]["emailfrom"];
         $mail->Sender = $_SESSION["opciones"]["emailfrom"];
        $mail->FromName = $_SESSION["opciones"]["nombrefrom"];
        $mail->AddReplyTo = $_SESSION["opciones"]["emailfrom"];
        $mail->Mailer     = $CONFIG_MAILER;

        // Add smtp values if needed
        if ( $CONFIG_MAILER == 'smtp' ) {
            $mail->SMTPAuth = $CONFIG_SMTPAUTH;
            $mail->Username = $CONFIG_SMTPUSER;
            $mail->Password = $CONFIG_SMTPPASS;
            $mail->Host     = $CONFIG_SMTPHOST;
        } else
            // Set sendmail path
            if ( $CONFIG_MAILER == 'sendmail' ) {
                if (isset($CONFIG_SENDMAIL))
                    $mail->Sendmail = $CONFIG_SENDMAIL;
            }

        $mail->AddAddress( $destino );
        $mail->Subject = $titulo;
        $mail->Body = $codigo;
        $mail->IsHTML(true);

        $mail->Send();
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
				$codigo .= $titulo.'<br>';
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
		
		$codigo = '';
		$codigo .= '<a href="'._WEB_ROOT.'catalogo/articulo.php?ref='.$row["referencia"].'">';
		$codigo .= $descripcion;
		$codigo .= '</a>';
		
		$codigo .= '<div class="imgCaja">';
		$codigo .= '<a href="'._WEB_ROOT.'catalogo/articulo.php?ref='.$row["referencia"].'">';
		$codigo .= $__CAT->codigoThumb($row["referencia"]);
		$codigo .= '</a>';
		$codigo .= '</div>';
		$codigo .= $precio;
		
		
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
				echo '<script languaje="javascript">document.location=\''._WEB_ROOT_SSL.'cuenta/login.php?continua=pedido\'</script>';
			}
		
			echo '<div class="titPagina">'._TIT_DEBES_LOGIN.'</div>';
			echo '<div class="cajaTexto">';
			echo _DEBES_LOGIN;
			echo '<p><br>';
			echo '<a class="botLink" href="'._WEB_ROOT_SSL.'cuenta/login.php">'._MI_CUENTA.'</a>&nbsp;';

			if (!$this->esTrue($_SESSION["opciones"]["noautoaccount"]))
				echo '<a class="botLink" href="'._WEB_ROOT_SSL.'cuenta/crear_cuenta.php">'._CREAR_CUENTA.'</a>';
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
		
		$params = '?acc=add&ref='.$referencia;
		$codigo .= '<a class="botComprar" href="'._WEB_ROOT.'general/cesta.php'.$params.'">';
		$codigo .= _ADD_CART;
		$codigo .= '</a>';
	
		if($nomPHP == 'favoritos.php') {
			$params = '?acc=del&ref='.$referencia;
		
			$codigo .= '<p><a class="botEliminar" href="'._WEB_ROOT_SSL.'cuenta/favoritos.php'.$params.'">';
			$codigo .= _ELIMINAR;
			$codigo .= '</a> ';
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
			$params = '?acc=del&ref='.$referencia;
			$codigo = '<a class="botFav" href="'._WEB_ROOT_SSL.'cuenta/favoritos.php'.$params.'">';
			$codigo .= _ELIMINAR;
			$codigo .= '</a>';
		}
		else {
			$params = '?acc=add&ref='.$referencia;
			$codigo = '<a class="botFav" href="'._WEB_ROOT_SSL.'cuenta/favoritos.php'.$params.'">';
			$codigo .= _ADD_FAVORITOS;
			$codigo .= '</a>';
		}
		
	
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
		$noNulos = array ("codenvio", "direccion_env", "codpostal_env", "ciudad_env", "provincia_env", "codpais_env", "nombre_env", "apellidos_env");
		$error = "";
		while (list ($clave, $campo) = each ($noNulos)) {
			if (strlen(trim($datosEnv[$campo])) == 0) {
				$error = _RELLENAR_TODOS_DATOS_ENVIO;
			}
		}
		
		// Comprobacion de datos de pago
		if ($datosPag) {
			$noNulos = array ("codpago", "direccion", "codpostal", "ciudad", "provincia", "codpais", "nombre", "apellidos");
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
			$msg .= _NO_CODPAGO.'<br>'; 
	
		// Debe existir una forma de envio
		$ordenSQL = "select codenvio from formasenvio where activo = true";
		if (!$__BD->db_valor($ordenSQL))
			$msg .= _NO_CODENVIO.'<br>'; 
	
		// El directorio de instalacion debe eliminarse
		if (file_exists(_DOCUMENT_ROOT.'/instalar'))
			$msg .= _AVISO_INSTALL.'<br>'; 
	
		if (!$msg)
			return;

		$codigo = '';
		$codigo .= '<div class="msgError">';
		$codigo .= _ATENCION.'<br>'.$msg;
		$codigo .= '</div>';
		
		echo $codigo;
	}
	
	function tituloPagina()
	{
		global $CLEAN_GET, $__BD;
		
		$titulo = '';
		$titulo .= _TITULO;
		
		if (isset($CLEAN_GET["ref"])) {
			$referencia = $CLEAN_GET["ref"];
			$titulo .= ' . '.$__BD->db_valor("select descripcion from articulos where referencia = '$referencia'");
		}
		
		if (isset($CLEAN_GET["fam"])) {
			$familia = $CLEAN_GET["fam"];
			$titulo .= ' . '.$__BD->db_valor("select descripcion from familias where codfamilia = '$familia'");
		}
		
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


	function formasEnvio($codPais, $provincia = '')
	{
		global $__BD, $__LIB, $__CLI, $__CAT;
		
		$codigo = '';
		
		// Forma de envio
		$ordenSQL = "select codenvio, descripcion, pvp, codimpuesto, ivaincluido from formasenvio where activo = true";
		
		$result = $__BD->db_query($ordenSQL);
		$paso = 0;	
		
		while ($row = $__BD->db_fetch_array($result)) {
		
			$descripcion = $__LIB->traducir("formasenvio", "descripcion", $row["codenvio"], $row["descripcion"]);
	
			if ($__LIB->envioGratuito($row["codenvio"])) {
				$txPrecio = $__CAT->precioDivisa(0);
				$txMsg = '('.$__LIB->msgEnvioGratuito().')';
			}
			else {
// 				$precio = $row["pvp"];
				$txPrecio = $__CAT->precioDivisa($__CAT->precioArticulo($row, false, true, true));		
				$txMsg = '';
			}
			
			$codigo .= '<div class="checkEnvio"><input type="radio" name="codenvio" value="'.$row["codenvio"].'"';
			if ($paso++ == 0)
				$codigo .= ' checked';
			$codigo .= '></div>';
			$codigo .= '<div class="labelEnvio">'.$descripcion.'</div>';
			$codigo .= '<div class="datoEnvio">'.$txPrecio.'</div>';
			$codigo .= '<div class="msgEnvio">'.$txMsg.'</div>';
			$codigo .= '<br/>';
		}
		
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
			$codigo .= '<input type="radio" name="codpago" value="'.$codPago.'"';
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
				$codigo .= _AVISO_GASTOS_PAGO.' '.$row["gastos"].'%';
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
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
			return false;
		}
		// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
				return false;
			}
		}
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
					return false;
				}
			}
		}
		return true;
	}	
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_funLibreria */
class funLibreria extends oficial_funLibreria {};

?>