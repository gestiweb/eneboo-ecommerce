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

/** @class_definition oficial_rightBottom */
//////////////////////////////////////////////////////////////////
//// OFICIAL /////////////////////////////////////////////////////

class oficial_rightBottom
{
	function contenidos()
	{

		global $__LIB;

		echo	'</div><!-- end content -->
		
				</div><!-- end inner -->
		
				<br class="cleaner"/>
		
			</div><!-- end outer -->
			<div id="footer">'.$__LIB->pie().'</div>
		
		</div><!-- end container -->
		
		<div id="outerFooter"></div>
		
		<!--	<br class="cleaner"/>
			</div>-->
		
		
		
		</body>
		</html>';
	}
}
//// OFICIAL /////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

/** @main_class_definition rightBottom */
class rightBottom extends oficial_rightBottom {};

$iface_rightBottom = new rightBottom;
$iface_rightBottom->contenidos();

?>