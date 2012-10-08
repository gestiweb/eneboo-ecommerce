<?php

include_once('../includes/libreria/fun_debugger.php');
include_once('../includes/configure_bd.php');
include_once('../includes/libreria/fun_bd.php');

$__BD = new funBD;
$__BD->conectaBD();
		
// $ordenSQL = "select max(hora) from transportersql where hora <> 'NULL'";
// $hora = $__BD->db_valor($ordenSQL);

// $ordenSQL = "select tabla, lineas from transportersql where hora = '$hora'";

$codigo = '';

$ordenSQL = "select * from modificadossql";
$result = $__BD->db_query($ordenSQL);

$now = date("H:i:s");


while($row = $__BD->db_fetch_assoc($result)) {
	
	$tabla = $row['tabla'];
	$campoClave = $row['campoclave'];
	$valorClave = $row['valorclave'];
	$valores = $row['valores'];
	
	
	$ordenSQL = "select $campoClave from $tabla where $campoClave = '$valorClave'";
	$existe = $__BD->db_valor($ordenSQL);
	if (!$existe) {
		$ordenSQL = "insert into $tabla ($campoClave) values ('$valorClave')";
		$__BD->db_query($ordenSQL);
	}
	
	$ordenSQL = "update $tabla set $valores where $campoClave = '$valorClave'";
//  	echo $ordenSQL;
	$ok = $__BD->db_query($ordenSQL);
	
	if ($ok) {
 		$ordenSQL = "delete from modificadossql where id=".$row["id"];
 		$__BD->db_query($ordenSQL);
	}
	
	$ok = $ok ? ' OK' : ' ERROR';

	$codigo .= $tabla.' &gt; '.$valorClave.': '.$ok.'<br/>';
	
// 	echo '<br/><br/><br/>';
	
	flush();
}

if (!$codigo) {
	$codigo = 'No hay datos para actualizar';
	$timer = true;
}
else
	$timer = false;

?>
<html>
<head>
<title> Actualizacion eStoreQ </title>
<style>
	body {
		margin: 0; padding: 0;
		font: 13px "Trebuchet MS", verdana, arial;
	}
	.central {
		width: 800px;
		margin: 50px auto;
	}
	button {
		padding: 5px;
		font-size: 1.2em;
	}
	.boton {
		margin: 0 0 10px 0;
	}
</style>
		
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		
</head>

<body>
<div class="central">
	<h1> Actualizacion <?php echo $now ?></h1>
	<div class="boton datos">
		<button onclick="refrescar()">Actualizar</button>
	</div>
	<div class="datos">
	<?php echo $codigo ?>
	</div>
</div>
		
<script type="text/javascript">
	function refrescar() {
		$('h1').text("actualizando...");
		$('.datos').hide();
		window.location.reload()
	}
	$(document).ready(function() {
//		setTimeout("refrescar()", 10000);
	})
</script>
</body>

</html>