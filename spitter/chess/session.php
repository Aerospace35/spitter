<?php /* Start PHP session. */ 
/* If your server uses load balancing or anything that causes session variables to 
 * be mixed up you can provide a fixed location (physical directory) here. Must 
 * have full execute/read/write access! */ 
//session_save_path( "/home/allusers/myself/htdocs/tmp" );
session_set_cookie_params(10800); /* three hours */
session_start();

/* XXX Allow changing theme via URL since there is no other way right now */
if (isset($_GET['theme'])) {
	$theme=$_GET['theme'];
	if (preg_match('/[^a-z0-9_\-]/i',$theme))
		$theme='default';
	$_SESSION['theme'] = $theme;
} else
	$theme=$_SESSION['theme'];

/* Remember build time start time stamp. Good place here since this script is
 * included by any other else first (used in renderPageEnd) */
$btstart=microtime(true);
?>
