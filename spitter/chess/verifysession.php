<?php /* Verify running session (and return to login page if not logged in). */
include 'session.php';
if (isset($_SESSION['uid']))
	$uid=$_SESSION['uid'];
else
	header('location:index.php');
?>
