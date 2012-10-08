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

/** @class_definition oficial_funSEO */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_funSEO
{
	public static function title()
	{
		global $CLEAN_GET, $__BD;
		
		$result = '';
		
		if (isset($CLEAN_GET["ref"])) {
			$ordenSQL = "select descripcion from articulos where referencia = '".$CLEAN_GET["ref"]."'";
			$result = $__BD->db_valor($ordenSQL);
		}
		
		if (isset($CLEAN_GET["fam"])) {
			$ordenSQL = "select descripcion from familias where codfamilia = '".$CLEAN_GET["fam"]."'";
			$result = $__BD->db_valor($ordenSQL);
		}
		
		return $result;
	}

	public static function getDL()
	{
		global $CLEAN_GET, $__BD;
		
		$result = '';
		
		if (isset($CLEAN_GET["refdl"])) {
			
			$ordenSQL = "select referencia, codfamilia from articulos where descripciondeeplink = '".$CLEAN_GET["refdl"]."'";
			$result = $__BD->db_query($ordenSQL);
			$row = $__BD->db_fetch_row($result);
			
			if (!$row) {
				$ordenSQL = "select referencia, codfamilia from articulos where descripciondeeplink = '".$CLEAN_GET["refdl"]."'";
				$result = $__BD->db_query($ordenSQL);
				$row = $__BD->db_fetch_row($result);
			}
			
			if ($row) {
				$CLEAN_GET["ref"] = $row[0];
				$CLEAN_GET["fam"] = $row[1];
			}
		}
		
		if (isset($CLEAN_GET["famdl"])) {
			
			$ordenSQL = "select codfamilia from familias where descripciondeeplink = '".$CLEAN_GET["famdl"]."'";
			$result = $__BD->db_valor($ordenSQL);
			
			if (!$result) {
				$ordenSQL = "select codfamilia from familias where descripciondeeplink = '".$CLEAN_GET["famdl"]."'";
				$result = $__BD->db_valor($ordenSQL);
			}
				
			if ($result)
				$CLEAN_GET["fam"] = $result;
		}
		
		$url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		$partes = explode(_WEB_ROOT, $url);
		$coda = $partes[1];
		$coda = preg_replace("/eng|fra|esp/", "", $coda);
		
		return $coda;
	}
}


//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_funSEO */
class funSEO extends oficial_funSEO {};
		



?>