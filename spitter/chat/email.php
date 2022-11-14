<?php // the message
$msg = "New Spitterchat Message: http://spitter.c1.biz";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

// send email
$email = file_get_contents('/var/www/html/spitter/chat/email.txt');
mail("$email","Spitterchat Notification",$msg);
