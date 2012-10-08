<?php
/** @no_class */
?>
<div action="<?php echo $destino?>" method="post"><div>

	<div class="labelForm"><?php echo _EMAIL?></div>
	<div class="datoForm">&nbsp;&nbsp;<b><?php echo $datos[0]?></b></div>
	<div style="width:1px;"></div>

	<div class="labelForm"><?php echo _NOMBRE?></div>
	<div class="datoForm"><input size="30" type="text" name="contacto" value="<?php echo $datos[1]?>"/>*</div>

	<div class="labelForm"><?php echo _APELLIDOS?></div>
	<div class="datoForm"><input size="30" type="text" name="apellidos" value="<?php echo $datos[2]?>"/>*</div>

	<div class="labelForm"><?php echo _EMPRESA?></div>
	<div class="datoForm"><input size="30" type="text" name="nombre"<?php if ($__LIB->esTrue($datos[6])) echo 'value="'.$datos[3].'"'?>/></div>
	
	<div class="labelForm"><?php echo _TELEFONO?></div>
	<div class="datoForm"><input size="30" type="text" name="telefono1" value="<?php echo $datos[4]?>"/>&nbsp;</div>

	<div class="labelForm"><?php echo _FAX?></div>
	<div class="datoForm"><input size="30" type="text" name="fax" value="<?php echo $datos[5]?>"/>&nbsp;</div>

<input size="30" type="hidden" name="procesarDatos" value="1">

<p class="separador"/><a class="botGuardar" href="javascript:document.datosCuenta.submit()"><?php echo _ENVIAR ?></a>

</div></form>
