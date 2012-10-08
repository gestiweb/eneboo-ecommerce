<?php

/*** begin our session ***/
session_name('eStoreQ');
session_start();
if(isset( $_SESSION['user_id'] ))
{
	echo '<script type="text/javascript">
			<!--
			window.location = "im_index.php"
			//-->
			</script>';
	$message = '<a href="im_index.php">Acceso</a>';
}
elseif (ctype_alnum($_POST['img_password']) != true)
{
       $error = "Password incorrecto";
}
else
{
		include_once('../includes/configure_bd.php');
		include_once('../includes/libreria/fun_bd.php');
		
		$__BD = new funBD;
		$__BD->conectaBD();
		
        $ordenSQL = "select managerpass from opcionestv";
        $managerpass = $__BD->db_valor($ordenSQL);

        if(!$managerpass)
        {
            $error = 'Password incorrecto';
        }
        /*** if we do have a result, all is well ***/
        else if ($managerpass == sha1($_POST['img_password']))
        {
                $_SESSION['user_id'] = sha1(time());
                
                echo '<script type="text/javascript">
						<!--
 						window.location = "im_index.php"
						//-->
						</script>';
                $message = '<a href="im_index.php">Acceso</a>';
        }
        else
	       $error = "Password incorrecto";
}
	
	if ($error) {
		include_once('im_login.php');
	}
	
// 	echo $error;
?>
