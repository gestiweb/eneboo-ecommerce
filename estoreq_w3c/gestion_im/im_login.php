<html>
<head>
<title>eStoreQ . Gestor de Im&aacute;genes</title>

<style>

	body {
		text-align:center;
	}

	div.form {
		width:  300px;
		text-align: center;
		width: 400px;
		border: 1px solid #444;
		padding: 0 0 20px 0;
		font-family:verdana;
		font-size: 11px;
		color: #333;
		margin: 130px auto;
		background: url(includes/im_snow.png) no-repeat right;
	}
	
	div.titForm {
		background-color: #444;
		color: #FFF;
		font-size: 1.2em;
		padding: 5px 0;
		margin-bottom: 20px;
	}
	
	div.error {
		text-align:center;
		padding: 5px;
		margin-top: 10px;
		color: #990000;
		font-weight: bold;
	}
</style>

<LINK type="text/css" href="includes/im_estilos.css" rel="StyleSheet">

</head>
<body>

<form action="im_login_submit.php" method="post">

<div class="form">

<div class="titForm">
Gestor de im&aacute;genes de eStoreQ	
</div>

<p>
<label for="img_password">Password</label>
<input type="password" id="img_password" name="img_password" value="" maxlength="20" />
</p>

<p><input type="submit" value="Acceder" /> </p>

<?php if (isset($error))
	echo '<div class="error">'.$error.'</div>';
?>

</div>

</form>


</body>
</html>