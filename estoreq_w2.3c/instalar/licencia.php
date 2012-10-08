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

require_once( 'top_left.php' );
require_once( '../idiomas/esp/main.php' );

?>

<div class="titPagina"><?php echo _INSTALL_TV ?></div>

<p>
<?php
	echo $__LIB->fasesInstalacion('licencia'); 
	echo '<div class="titApartado">'._FASES_INS_LICENCIA.'</div>';
	echo _INS_TEXTO_LIC
?>

<p><a class="botLink" href="paso1.php"><?php echo _SIGUIENTE_MAS ?></a>
<p>
<iframe src="gpl.html" class="licencia" scrolling="auto"></iframe>

<?php require_once( 'right_bottom.php' );
