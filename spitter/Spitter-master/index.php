
<html>
  <link rel="stylesheet" href="style.css" />
<style>
	input {
  border: solid 2px #00FF33;
  color: #00FF33;
  background-color: #000000;
  transition: border 0.3s;
  width:250px;
}</style>
<header><meta content='width=device-width, initial-scale=1' name='viewport'/></header>
<body>

Type "chat" to get the main room
<form action="#" method="post">
Destination room: <input type="text" name="room"><br>
<input type="submit" name="submit" value="Go">
</form>



</body>
</html> 
<html>
<body>
  <?php 
$room = $_POST['room'];
if(empty($room)) {
	echo '';
}
else {
	include("includes/embedhandler.html");
}
?>

<br>
<br><br>
<br>
<br>
<br>
        <?php 
	    $hm = "/var/www/html/Ads"; 
	    $hm2 = "http://brodie.loophole.site/Ads"; 
	    include "$hm/hioxRandomAd.php";
        ?>	
	<?php 
	    $hm = "/var/www/html/Ads"; 
	    $hm2 = "http://brodie.loophole.site/Ads"; 
	    include "$hm/hioxRandomAd.php";
        ?>	
	<?php 
	    $hm = "/var/www/html/Ads"; 
	    $hm2 = "http://brodie.loophole.site/Ads"; 
	    include "$hm/hioxRandomAd.php";
        ?>	
	<center><h1><a href="/Ads/New">Your Ad Here!</a></h1></center>
	<br>
	<br>
	<br>
	<br>
	<br>
	<center>Powered by <a href="life">Spitterchat</a></center>
</body>
</html> 

