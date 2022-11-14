Delete Account?
<form action="#" method="POST"><input type="submit" name="delete"></form>

<?php
include '../core/init.php';
        $user_id = $_SESSION['user_id'];
        $user = User::getData($user_id);
if(isset($_POST['delete'])) {
	$user_id = $_SESSION['user_id'];
	$servername = "localhost";
    $username = "";
    $password = "";
    $dbname = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// sql to delete a record
$sql = "DELETE FROM users WHERE id='$user_id'";

if ($conn->query($sql) === TRUE) {
  echo "Account deleted successfully- you will be redirected to the landing page shortly...";
  $_SESSION['user_id'] = "";
  header( "refresh:2; url=/index.php" ); 
  header("Location: /index.php");
} else {
  echo "Error deleting record: " . $conn->error;
}

$conn->close();

};
