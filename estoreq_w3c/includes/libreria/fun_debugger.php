<?

$debugger = 0;

$firePHPfile = '/home/jrodriguez/funcional/FirePHPCore/FirePHP.class.php';
if (file_exists($firePHPfile)) {
	include_once($firePHPfile);
	$debugger = 1;
	ob_start();
	$fireDeb = FirePHP::getInstance(true);
} 

class eqDebug
{
	public static function log($msg)
	{
		global $debugger;
		
		if (!$debugger)
			return;
		
		global $fireDeb;
		$fireDeb->log($msg);
	}
}

/*
eqDebug::log('Init');
*/
?>