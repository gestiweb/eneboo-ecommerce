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

/** @class_definition oficial_articulos */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_articulos
{

	function unsetTodos()
	{
		unset($_SESSION["buscar"]);
// 		unset($_SESSION["orden"]);
		unset($_SESSION["fabricante"]);
		unset($_SESSION["familia"]);
		unset($_SESSION["ver"]);
	}
	
	// Realiza la consulta a base de datos sobre los artículos, la lanza y los muestra
	function contenidos() 
	{
		global $__BD, $__CAT, $__LIB;
		global $CLEAN_GET, $CLEAN_POST;
	
		$titPagina = '';	
		
		// Vista en matriz o lista
		$vista = '';
		if (isset($CLEAN_GET["vista"]))
			$vista = $CLEAN_GET["vista"];
		
		if ($vista == "lista") {
			$_SESSION["vista"] = 0;
			$_SESSION["numresults"] = 10;
		}
		if ($vista == "matriz")	{
			$_SESSION["vista"] = 1;
			$_SESSION["numresults"] = 2 * $_SESSION["opciones"]["articulosxfila"];
		}
		
		// Registros por pagina
		if (isset($CLEAN_GET["numr"])) $_SESSION["numresults"] = $CLEAN_GET["numr"];
		
		if (!isset($_SESSION["numresults"])) $_SESSION["numresults"] = 10;
			
		$where = 'a.publico = true';
		
		
		// Familia
		$codFamilia = '';
		if (isset($CLEAN_GET["fam"]))
			$codFamilia = $CLEAN_GET["fam"];
		else if(isset($CLEAN_GET["famdl"])) {
			$ordenSQL = "select codfamilia from familias where descripciondeeplink = '".$CLEAN_GET["famdl"]."'";
			$codFamilia = $__BD->db_valor($ordenSQL);
		}
		
		if ($codFamilia) {
			$this->unsetTodos();
			$_SESSION["familia"] = $codFamilia;
		}
		
		
		// Busqueda. Desde el formulario
		$buscar = 0;
		if (isset($CLEAN_POST["buscar"])) $buscar = 1;
		if ($buscar == 1) {
		
			$this->unsetTodos();
			$palabras = addslashes($CLEAN_POST["palabras"]);
			$like = $__CAT->likeBuscar($palabras);
		
 			if ($like)
				$_SESSION["buscar"] = $like;
			else
				$_SESSION["buscar"] = "1=0";
			
			echo '<script type="text/javascript">window.location = "'._WEB_ROOT_SSL_L.'catalogo/articulos.php"</script>';
			exit;
		}
		
		
		// Fabricante
		if (isset($CLEAN_GET["fab"])) {
			$this->unsetTodos();
			$_SESSION["fabricante"] = $CLEAN_GET["fab"];
		}
		
		// Ver: oferta, novedades...
		if (isset($CLEAN_GET["ver"])) {
			$this->unsetTodos();
			$_SESSION["ver"] = $CLEAN_GET["ver"];
		}
		
		
		
		
		// Busqueda. Desde una pagina anterior
		if (isset($_SESSION["buscar"])) {
			$titPagina = _RESULT_BUSQUEDA;
			$where .= ' AND ('.$_SESSION["buscar"].')';
		}
		
		// Fabricante
		if (isset($_SESSION["fabricante"])) {
			$fabricante = $_SESSION["fabricante"];
			$where .= " AND a.codfabricante = '$fabricante'";
			$ordenSQL = "select nombre from fabricantes where codfabricante = '$fabricante'";
			$nombre = $__BD->db_valor($ordenSQL);		
			$titPagina = _PRODUCTOS_DE.' '.$nombre;
		}
		
		// Ver: Ofertas, novedades
		if (isset($_SESSION["ver"])) {
			
			switch($_SESSION["ver"]) {
				case "enoferta":
					$titPagina = _EN_OFERTA;
					$where .= ' AND (a.enoferta = true)';
				break;
			}
			
		}
		
		
		if (isset($_SESSION["familia"])) {
		
			$codFamilia = $_SESSION["familia"];
			$titPagina = $__CAT->rastro($codFamilia);
			
			$where .= " AND (a.codfamilia = '$codFamilia'";	 	
			$where = $__CAT->whereFamiliasHijas($codFamilia, $where);
			$where .= ')';
			
			// Reseteamos los criterios de busqueda
			unset($_SESSION["buscar"]);
		} 
		
		// Control de orden
		$orderBy = '';
		if (isset($CLEAN_GET["orden"]))
			$_SESSION["orden"] = $CLEAN_GET["orden"];
			
		
		// Control de orden y sentencia en funcion del idioma
		if (isset($_SESSION["orden"])) {
				$orderBy .= ' order by a.'.$_SESSION["orden"];
		}
		
		if (isset($CLEAN_GET["tipoord"])) {
			if ($CLEAN_GET["tipoord"] == 'desc')
				$orderBy .= ' desc';
		}
		
		$where .= $this->masWhere();
		$where .= $orderBy;

		echo $this->mostrarArticulos($codFamilia, $where, $titPagina);
	
		$__LIB->controlVisitas('familias', $codFamilia);
	}

	function mostrarArticulos($codFamilia, $where, $titPagina)
	{
		global $__BD, $__CAT;

		$codigo = '';

		// Sentencia principal	
		$ordenSQL = "select a.* from articulos a where ".$where;
			
		$totalArticulos = $__BD->db_num_rows($ordenSQL);
		
		$navPaginas = $__CAT->navPaginas($ordenSQL, "top");
		$navPaginasB = $__CAT->navPaginas($ordenSQL);
		$ordenSQL .= $__CAT->wherePagina($ordenSQL);
		
		
/*		$cache = new JG_Cache();
		
		$keyCache = $ordenSQL;
		
		$data = $cache->get($keyCache);
		
		if ($data === FALSE) {}
		else
			return $data;
		
		echo 'cacheando';*/
		
		$codigo .= '<h1>'.$titPagina.'</h1>';
		
		$subFamilias = '';
		if (isset($_SESSION["familia"]))
// 			$subFamilias = $__CAT->familiasMatriz($codFamilia);
			$subFamilias = $__CAT->listaFamiliasHijas($codFamilia);
		if ($subFamilias) {
			$codigo .= $subFamilias;
// 			$codigo .= '<br class="cleaner"/>';
// 			return $codigo;
		}
		
		
		$codigo .= $navPaginas;
		// Llamada principal	
		$codigo .= $__CAT->articulos($ordenSQL);
		$codigo .= '<br class="cleaner"/>';
		$codigo .= $navPaginasB;
		$codigo .= $__CAT->navOpciones($totalArticulos);

// 		$cache->set($keyCache, $codigo);
		
		return $codigo;
	}


	// Para sobreescribir
	function masWhere()
	{
		return "";
	}
}

//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition oficial_articulos */
class articulos extends oficial_articulos {};

$iface_articulos = new articulos;
$iface_articulos->contenidos();
		
?>

<?php include("../includes/right_bottom.php") ?>