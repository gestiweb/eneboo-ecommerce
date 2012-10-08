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

/** @class_definition oficial_contactar */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_contactar
{	
	// Muestra el formulario de contacto
	function contenidos() 
	{
		global $CLEAN_POST, $CLEAN_GET, $__SEC, $__LIB;
	
		$destino = $_SESSION["opciones"]["emailcontacto"];
		if (!$destino) {
			echo _ERROR_CONTACTAR;
			include("../includes/right_bottom.php");
			exit;
		}
	
		$codigo = '';
		
		$valores = array(); $errores = array();
		$pasa = false;
		$nombre = '';
		$email = '';
		$texto = '';
		$textoPost = '';
		
		$esComentario = '';
		
		
		if (isset($CLEAN_POST["esComentario"])) { 
			
			$validacion = $__SEC->validarContacto($CLEAN_POST, "datosCuenta");
			$CLEAN_POST = $validacion["datos"];
			$errores = $validacion["errores"];
			$pasa = $validacion["pasa"];
			$esComentario = true;
			
			$email = $CLEAN_POST["email"];
			$nombre = $CLEAN_POST["nombre"];
			
			$texto = _NOMBRE.': '.$nombre."\n";
			$texto .= _EMAIL.': '.$email."\n\n";
			
			$textoPost = $CLEAN_POST["texto"]; 
			$texto .= $CLEAN_POST["texto"];
			
			if ($pasa) {	
				$titulo = _MSG_CONTACTO;
				$__LIB->enviarMail($destino, $titulo, $texto);
			}
		}	
		
		$codigo .= '<h1>'._CONTACTAR.'</h1>';


		$codigo .= '<div class="cajaTexto">';

		if (isset($CLEAN_GET['ok'])) {
			$codigo .= _CONSULTA_ENVIADA;
			$codigo .= '</div>';
			echo $codigo;
			include("../includes/right_bottom.php");
			exit;
		}
		
		if ($esComentario && $pasa) {
 			echo '<script type="text/javascript">window.location = "'._WEB_ROOT_SSL_L.'general/contactar.php"</script>';
 			exit;
		}
		
		$codigo .= _INTRO_CONTACTAR;
	
		$codigo .= '<form method="post" action="'._WEB_ROOT_L.'general/contactar.php"><div>';
		
		$codigo .= '<br/><br/>';
		
 		$codigo .= formularios::contactar($CLEAN_POST, $errores);
		
 		if ($__LIB->esTrue($_SESSION["opciones"]["validarcontactar"]))
 			$codigo .= formularios::codigoValidacion(_CODIGO_VALIDACION, $errores);
		
 		$codigo .= formularios::botEnviar();
		
		$codigo .= '<input name="esComentario" type="hidden" value="1"/>';
		$codigo .= '</div></form>';

		$codigo .= '</div>';
		
		echo $codigo;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_contactar */
class contactar extends oficial_contactar {};

$iface_contactar = new contactar;
$iface_contactar->contenidos();

?>


<?php include("../includes/right_bottom.php") ?>