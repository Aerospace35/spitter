
<html>
	<style>body{margin:0; padding: 0; font-family: "VT323"; font-size: 18px; background: #000000; overflow: hidden;}
*{font-family: "VT323"; font-size: 18px; color:#00FF33; }

</style>
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

<form action="#" method="post">
Destination room: <input type="text" name="room"><br>
<input type="submit" name="submit" value="Create">
</form>


</body>
</html> 
<html>
<body>
  <?php 
$room = $_POST['room'];

if ($room = '') {
  echo '';
  } else {
    echo '<iframe width="100%" height="100%" src="<?php echo "$room" . "/" . "index" . ".php"; ?>"></iframe>';

 }
?>
</body>
</html> 
