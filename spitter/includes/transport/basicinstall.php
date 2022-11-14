<html>
<header><meta content='width=device-width, initial-scale=1' name='viewport'/></header>
<body>

<?php include("siteform.php"); ?>

</body>
</html> 
<html>
<body>
  <?php
$email  = $_POST['email'];
$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

if (filter_var($emailB, FILTER_VALIDATE_EMAIL) === false ||
    $emailB != $email
) {
    echo "This email adress isn't valid, or you have not inputted one.";
    exit(0);
}
?>
<?php
$webdir = $_POST['sitename'];
$webdir = preg_replace("([^\w\s\d\.\-_~,;:\[\]\(\)]|[\.]{2,})", '', $webdir);
$myfile = fopen("./" . "data/" . $webdir . ".txt", "w") or die("Unable to open file!");
$txt = $_POST['name'];
fwrite($myfile, $txt . "\n");
$txt = $_POST['email'];
fwrite($myfile, $txt . "\n");
fclose($myfile);
?>
<?php
$unzip = new ZipArchive;
$out = $unzip->open('basic.zip');
if ($out === TRUE) {
  $unzip->extractTo(getcwd());
  $unzip->close();
  echo 'File unzipped!';
} else {
  echo 'Error';
}
rename('basic', "$webdir");
?>


<br>Welcome <?php echo $_POST["name"]; ?><br>
Your site address is https://brodie.loophole.site/hosted/<?php echo $webdir; ?>
</body>
</html> 
