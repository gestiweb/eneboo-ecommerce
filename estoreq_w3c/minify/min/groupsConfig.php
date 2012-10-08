<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/

include_once('../../includes/configure_web.php');

return array(
    'js' => array(
			_DOCUMENT_ROOT.'includes/js/jquery.js',
			_DOCUMENT_ROOT.'includes/js/fancybox.js',
			_DOCUMENT_ROOT.'includes/libreria.js'
		)
);