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

// 	error_reporting(E_USER_NOTICE);

/** @class_definition oficial_contactar */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_contactar
{	
	// Muestra el formulario de contacto
	function contenidos() 
	{
		global $CLEAN_POST, $__SEC, $__LIB;
	
		$destino = $_SESSION["opciones"]["emailcontacto"];
		if (!$destino) {
			echo _ERROR_CONTACTAR;
			include("../includes/right_bottom.php");
			exit;
		}
	
		$error = '';
		$nombre = '';
		$email = '';
		$texto = '';
		
		$esComentario = '';
		if (isset($CLEAN_POST["esComentario"])) { 
			
			$esComentario = $CLEAN_POST["esComentario"]; 
			
			$email = $CLEAN_POST["email"];
			$nombre = $CLEAN_POST["nombre"];
			
			$texto = _NOMBRE.': '.$nombre."\n";
			$texto .= _EMAIL.': '.$email."\n\n";
			
			$textoPost = $CLEAN_POST["texto"]; 
			$texto .= $CLEAN_POST["texto"];
			
			if (!$email || !$nombre || !$textoPost)
				$error = _RELLENAR_TODOS_CAMPOS;
				
			if (!$error) {
				if (!$__SEC->comprobarMail($email))
					$error = _EMAIL_NOVALIDO;
			}
			
/*  			$img = new Securimage();
  			$valid = $img->check($CLEAN_POST['code']);
  			if (!$valid)
				$error = _CODIGO_VALIDACION_INCORRECTO;*/
			
			
			if (!$error) {	
				$titulo = _MSG_CONTACTO;
				$__LIB->enviarMail($destino, $titulo, $texto);
			}
		}	
		
		echo '<div class="titPagina">'._CONTACTAR.'</div>';


		echo '<div class="cajaTexto">';

		if ($esComentario && !$error) {
			echo _CONSULTA_ENVIADA;
			include("../includes/right_bottom.php");
			exit;
		}
		
		if ($error) {
			echo '<div class="msgError">'.$error.'</div>';
		}
		
		echo _INTRO_CONTACTAR;
	
		echo '<p>';

		echo '<form name="formComentario" method="post" action="contactar.php">';

		echo _NOMBRE.' *<br><input type="text" name="nombre" value="'.$nombre.'" size=50>';

		echo '<p>';
		echo _EMAIL.' *<br><input type="text" name="email" value="'.$email.'" size=50>';
	
		echo '<p>';
		echo _COMENTARIOS.' *<br><textarea name="texto" rows="14" cols="80">'.$CLEAN_POST["texto"].'</textarea>';
		
		
/*		echo '<p>';
		echo _CODIGO_VALIDACION.' *<br/>';
		echo '<img src="'._WEB_ROOT.'includes/securimage/securimage_show.php?sid='.md5(uniqid(time())).'" id="securimage">';

		echo '<br/><input type="text" name="code" />';*/
		
		
		echo '<p>(*) '._CAMPOS_OBLIGATORIOS;
		
		echo '<p><br><a class="botLink" href="javascript:document.formComentario.submit()">'._ENVIAR.'</a>';
		echo '<input type="hidden" name="esComentario" value="si">';
		echo '</form>';

		echo '</div>';
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