<?php
	define('_IMG_ROOT', '../catalogo/');

	include_once("../includes/xajax_05/xajax_core/xajax.inc.php");
	$xajax = new xajax();


	$xajax->registerFunction('confirmar_borrar_foto');
	function confirmar_borrar_foto($id)
	{	
		$objResponse = new xajaxResponse();
		
		$codigo = '&iquest;Seguro?&nbsp;&nbsp;';
		$codigo .= '<a href="#" onclick="xajax_borrar_foto('.$id.')"> S&Iacute; </a>';
		$codigo .= ' <a href="#" onclick="xajax_cancelar_borrar_foto('.$id.')"> NO </a>';
		
		$objResponse->assign('msg_'.$id, 'innerHTML', $codigo);
		$objResponse->assign('links_'.$id, 'style.display', 'none');
		$objResponse->assign('ampliar_'.$id, 'style.display', 'none');
		
		return $objResponse;
	}


	$xajax->registerFunction('cancelar_borrar_foto');
	function cancelar_borrar_foto($id)
	{	
		$objResponse = new xajaxResponse();
		
		$objResponse->assign('links_'.$id, 'style.display', 'inline');
		$objResponse->assign('ampliar_'.$id, 'style.display', 'inline');
		$objResponse->assign('msg_'.$id, 'innerHTML', '');
		
		return $objResponse;
	}



	$xajax->registerFunction('borrar_foto');
	function borrar_foto($id)
	{	
		global $__BD;
	
		$objResponse = new xajaxResponse();
		
		$codigo = '';
		
		$ordenSQL = "select referencia,nomfichero from articulosfotos where id=$id";
		$result = $__BD->db_query($ordenSQL);
		$row = $__BD->db_fetch_row($result);

		
		$referencia = $row[0];
		$nomFich = $row[1];
		$fichNor = _IMG_ROOT.'img_normal/'.$referencia.'/'.$nomFich;
		$fichThum = _IMG_ROOT.'img_thumb/'.$referencia.'/'.$nomFich;
		$fichMed = _IMG_ROOT.'img_mediana/'.$referencia.'/'.$nomFich;
		$fichSuperThum = _IMG_ROOT.'img_superthumb/'.$referencia.'/'.$nomFich;
		
		if (file_exists($fichThum))
			unlink($fichThum);

		if (file_exists($fichNor))
			unlink($fichNor);

		if (file_exists($fichMed))
			unlink($fichMed);

		if (file_exists($fichSuperThum)) {
			unlink($fichSuperThum);
		}
		
		$ordenSQL = "delete from articulosfotos where id=$id";
		$__BD->db_query($ordenSQL);
		
		$objResponse->remove('imagen_'.$id);

		return $objResponse;
	}


	$xajax->registerFunction('save_sort');
	function save_sort($arraySort)
	{	
		global $__BD;

		$objResponse = new xajaxResponse();
		
		$orden = 1;
		foreach($arraySort as $idImg) {
			$partes = split('_', $idImg);
			$id = $partes[1];
			$ordenSQL = "update articulosfotos set orden = $orden where id=$id";
			$__BD->db_query($ordenSQL);
			$orden++;
		}
		
		return $objResponse;
	}

	$xajax->processRequest();
?>