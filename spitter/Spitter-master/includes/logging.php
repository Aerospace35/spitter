<?php
$webdir = $_POST['sitename'];
$domain_before = $_POST['domain'];
$domain = $domain_before . "----";
$webdir = preg_replace("([^\w\s\d\.\-_~,;:\[\]\(\)]|[\.]{2,})", '', $webdir);
$myfile = fopen("./" . "data/" . $domain . $webdir . ".txt", "w") or die("Unable to open file!");
$txt = $_POST['name'];
fwrite($myfile, $txt . "\n");
$txt = $_POST['email'];
fwrite($myfile, $txt . "\n");
fclose($myfile);
?>
