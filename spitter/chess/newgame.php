<?php /* Start game page. */
include 'verifysession.php';
include 'io.php';
include 'render.php';

/* If color and opponent is submitted create new game and redirect to board page. */
if (isset($_POST['color']) && isset($_POST['opponent'])) { 
	if ($_POST['color']=='w') {
		$white=$uid;
		$black=$_POST['opponent'];
		$comment=null;
	} else {
		$white=$_POST['opponent'];
		$black=$uid;
		$comment=$_POST['comment'];
	}
	$gid=ioCreateGame($white,$black,$comment);
	
	/* send notification about new game, if player is black, for white
	 * player first move will be sent as notification */
	if ($_POST['color']=='b') {
		$email=ioLoadUserEmailAddress($white);
		if ($email) {
			$message="Dear $white,\n\n".
					"$black has started a game.\n\n".
					"Comment:\n$comment\n\n".
					"It is your turn now!\n\n".
					"Enter the game:\n".getGameURL($gid);
			mail($email,"[SpitterChess] New Game: $white vs $black",
						$message, $mail_header);
		}
	}
	
	header('location:board.php?gid='.$gid);
}

/* JS confirm dialog */
echo '<script language="Javascript">function confirm_new() {';
echo 'return confirm("Are you sure you want to open a new game?");';
echo '}</script>';

renderPageBegin('SpitterChess - New Game',null,array(
	'My Games'=>'index.php',
	'Help'=>'help.php',
	'Logout'=>'logout.php'),
	'New Game');
?>
<div class=inlineblock>
<FORM onSubmit="return confirm_new();" action="newgame.php" method="POST">
<TABLE border=0>
<TR><TD>Color:</TD><TD align=right>
<SELECT name="color">
  <OPTION value="w">White</OPTION>
  <OPTION value="b">Black</OPTION>
</SELECT>
</TD></TR>
<TR><TD>Opponent:</TD><TD align=right>
<SELECT name="opponent">
<?php
$users=ioLoadUserList();
foreach($users as $usr)
	if ($usr!=$uid)
		echo '<OPTION value="'.$usr.'">'.$usr.'</OPTION>';
?>
</SELECT>
</TD></TR>
<TR><TD colspan=2>Comment (if Black):</TD></TR>
<TR><TD colspan=2><TEXTAREA cols=30 rows=3 name="comment"></TEXTAREA>
</TD></TR>
<TR><TD colspan=2 align="center">
<INPUT type="submit" name="submit" value="Open New Game">
</TD></TR>
</TABLE>
</FORM>
</div>

<?php
renderPageEnd(null);
?>
