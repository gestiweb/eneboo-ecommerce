<?

$debugger = 0;

$firePHPfile = '/home/lorena/funcional/FirePHPCore/FirePHP.class.php';
if (!file_exists($firePHPfile))
	$firePHPfile = '/Library/WebServer/Documents/FirePHPCore/FirePHP.class.php';

if (file_exists($firePHPfile)) {
	include_once($firePHPfile);
	$debugger = 1;
	ob_start();
	$fireDeb = FirePHP::getInstance(true);
	ini_set('display_errors', true);
	error_reporting(E_ALL);
}
else 
	ini_set('display_errors', false);

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

eqDebug::log('Init');

?>