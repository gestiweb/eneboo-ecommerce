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

<h1><?php echo _INSTALL_TV ?></h1>

<br/><br/>

<?php
	echo $__LIB->fasesInstalacion('licencia'); 
	echo '<h2>'._FASES_INS_LICENCIA.'</h2>';
	echo _INS_TEXTO_LIC
?>

<br/><br/>
<a class="button" href="paso1.php"><span><?php echo _SIGUIENTE_MAS ?></span></a>
<br/><br/>
			
<div class="licencia">
<div class="innerLicencia">
	<?php readfile('gpl.html') ?>
</div>
</div>

<?php require_once( 'right_bottom.php' );
