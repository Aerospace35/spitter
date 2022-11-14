<html>
  <link rel="stylesheet" href="style.css" />
<style>
	input {
  border: solid 2px #00FF33;
  color: #00FF33;
  font-family: "VT323"; font-size: 18px;
  background-color: #000000;
  transition: border 0.3s;
  width:250px;
}</style>
<header><meta content='width=device-width, initial-scale=1' name='viewport'/></header>
<body>

<?php include("includes/siteform.php"); ?>

</body>
</html> 
<html>
<body>
  <?php 
$webdir = $_POST['name'];
?>

<?php
$unzip = new ZipArchive;
$out = $unzip->open('includes/Files.zip');
if ($out === TRUE) {
  $unzip->extractTo(getcwd());
  $unzip->close();
  echo 'File unzipped!';
} else {
  echo 'Error';
}
rename('spitter', "$webdir");
?>


<?php include("includes/footer.php"); ?>
</body>
</html> 
