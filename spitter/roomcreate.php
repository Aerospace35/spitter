<?php
$zip = new ZipArchive;
$res = $zip->open('/var/www/html/spitter/includes/Files.zip');
if ($res === TRUE) {
  $zip->extractTo('/var/www/html/spitter/');
  $zip->close();
  echo 'woot!';
} else {
  echo 'doh!';
};
$webdir = $_POST['name'];
rename('spitter', "$webdir");
