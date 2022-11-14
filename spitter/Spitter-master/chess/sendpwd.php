<?php /* Lost password page */
include 'io.php';
include 'render.php';

/* Theme is set to default before login; since this script is called when
 * login fails, we have to set theme to default here, too. */
$theme='default';

/* Handle posted user id and email address. */
$uid = "";
$email = "";
$errmsg = "";
if (isset($_POST['uid']) || isset($_POST['email'])) {
	$uid = $_POST['uid'];
	$email = $_POST['email'];
	if (empty($uid) || empty($email))
		$errmsg = "Please provide user name and email address!";
	else if (ioLoadUserEmailAddress($uid) != $email)
		$errmsg = "User name and email address do not match,<br>please contact administrator!";
	else {
		/* mail password */
		$pwd = ioLoadUserPassword($uid);
		$url = dirname(getGameURL("dummy"));
		mail($email, "[OCC] Your Account Info",
				"Dear $uid,\n\nyour password is: $pwd\n\n".
				"Login at: $url", $mail_header);
		/* render page */
		renderPageBegin(null,null,null,null);
		echo '<p><b>Password Recovery</b></p>';
		echo "<p>Password has been sent to $email.</p>";
		echo '[ <a href="index.php">Back To Login</a> ]';
		renderPageEnd(null);
		exit;
	}
}

/* ---------- Lost Password page ---------- */
renderPageBegin(null,null,null,null);
echo '<p><b>Password Recovery</b></p>';
if (empty($errmsg))
	echo '<p>Please provide your user name and e-mail address for password recovery.<br><FONT class="warning"><b>Warning: Password will be sent by e-mail in plain text!</b></FONT></p>';
else 
	echo '<p><FONT class="warning"><b>'.$errmsg.'</b></FONT></p>';
echo '<TABLE border=0><TR><TD align="center">';
echo '<FORM method="POST"><DIV align="right">';
echo 'Username: &nbsp;&nbsp;&nbsp;&nbsp;';
echo '<INPUT type="text" size=20 name="uid" value="'.$uid.'"><BR><BR>';
echo 'E-Mail Address: &nbsp;&nbsp;&nbsp;&nbsp;';
echo '<INPUT type="text" size=20 name="email" value="'.$email.'">';
echo '</DIV><BR><INPUT type="submit" value="Send Password"></FORM>';
echo '[ <a href="index.php">Back To Login</a> ]';
echo '</TD></TR></TABLE>';
renderPageEnd(null);

?>
