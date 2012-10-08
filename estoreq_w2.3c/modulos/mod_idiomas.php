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

/** @class_definition oficial_modIdiomas */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_modIdiomas
{	
	// El estilo indica como se ven los idiomas disponibles
	var $estilo = 'flags'; 

	// Listado de idionas
	function contenidos()
	{
		global $__BD, $__LIB, $CLEAN_GET;
	
		$codigoMod = '';
		
		$ordenSQL = "select codidioma, nombre from idiomas where publico = true order by orden";
	
		$result = $__BD->db_query($ordenSQL);
		
		while($row = $__BD->db_fetch_array($result)) {
			$codIdioma = $row["codidioma"]; 
			
			$img = _DOCUMENT_ROOT.'idiomas/'.$codIdioma.'/images/flag.png';
			$imgW = _WEB_ROOT.'idiomas/'.$codIdioma.'/images/flag.png';
			
			if (file_exists($img))
				$codigoLang = '<img border=0 src="'.$imgW.'">';
			else
				$codigoLang = $row["nombre"];
			
			
			// Reconstruir url
			$path_parts = pathinfo($_SERVER["SCRIPT_FILENAME"]);
			$nomPHP = $path_parts["basename"];
			
			$params = '?newlang='.$codIdioma;
			if($CLEAN_GET) {
				foreach ($CLEAN_GET as $key => $value) {
					if ($key == 'newlang')
						continue;
					$params .= '&'.$key.'='.$value;
				}
			}
			$link = $nomPHP.$params;
				
			
			if ($codIdioma == $_SESSION["idioma"])
				$codigoMod .= '<b>'.$codigoLang.'</b>';
			else
				$codigoMod .= '<a href="'.$link.'">'.$codigoLang.'</a>';
		
			$codigoMod .= '&nbsp;&nbsp;';
		}
		
		return $codigoMod;
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


/** @main_class_definition oficial_modIdiomas */
class modIdiomas extends oficial_modIdiomas {};

?>