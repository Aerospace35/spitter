<?php /* Kill session and go to login page. */
include 'session.php';
session_destroy();
header('location:index.php');
?>
