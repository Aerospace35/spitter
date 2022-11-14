<html>
<header><meta content='width=device-width, initial-scale=1' name='viewport'/></header>
<body>

<?php include("includes/siteform.php"); ?>

</body>
</html> 
<html>
<body>
  <?php include("includes/email_validator.php");
?>
<?php include("includes/logging.php"); ?>
<?php
$unzip = new ZipArchive;
$out = $unzip->open('file.zip');
if ($out === TRUE) {
  $unzip->extractTo(getcwd());
  $unzip->close();
  echo 'File unzipped!';
} else {
  echo 'Error';
}
rename('personal', "$webdir");
?>


<?php include("includes/footer.php"); ?>
</body>
</html> 
