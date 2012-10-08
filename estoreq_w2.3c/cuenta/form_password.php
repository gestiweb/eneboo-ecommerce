<?php
/** @no_class */
?>

<form name="datosPassword" action="<?php echo $destino?>" method="post">

	<div class="labelForm"><?php echo _NEW_PASSWORD?></div>
	<div class="datoForm"><input size="20" type="password" name="password" maxlength="40">*</div>

	<div class="labelForm"><?php echo _CONFIRM_PASSWORD?></div>
	<div class="datoForm"><input size="20" type="password" name="confirmacion" maxlength="40">*</div>

<input size="30" type="hidden" name="procesarPassword" value="1">

<p style="clear: left; padding-top:30px"/><a class="botGuardar" href="javascript:document.datosPassword.submit()"><?php echo _ENVIAR ?></a>

</form>
