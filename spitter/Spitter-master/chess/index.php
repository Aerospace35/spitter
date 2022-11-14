<header><meta content='width=device-width, initial-scale=1' name='viewport'/></header>
<style>body { 
	background-color: black 
	}</style>
<?php /* Login/main page */
include 'session.php';
include 'render.php';
include 'io.php';

/* Set message to be displayed on login screen for general information of users. */
$releaseinfo='';

/* ---------- Login ---------- */
$uid=$_SESSION['uid'];
if (empty($uid) && isset($_POST)) {
	$uid=$_POST['uid'];
	$pwd=$_POST['pwd'];
	$storedpwd=ioLoadUserPassword($uid);
	if (!empty($uid) || !empty($pwd)) {
		$result='ok';
		if (empty($uid))
			$result='Username is missing!';
		else if (empty($pwd))
			$result='Password is missing!';
		else if ($storedpwd==null || $storedpwd!=$pwd)
			$result='Invalid username or password!';
		if ($result=='ok') {
			$_SESSION['uid']=$uid;
			$_SESSION['filter_name']='Open Games';
			$_SESSION['filter_loc']='opengames';
			$_SESSION['filter_plyr']=$uid;
			$_SESSION['filter_clr']=null;
			$_SESSION['filter_opp']=null;
			$_SESSION['allow_newgame']=1;
			$_SESSION['theme']=ioLoadUserTheme($uid);
			$theme=$_SESSION['theme'];
			$_SESSION['noteskey']=crypt($pwd,'noteskey');
			ioUpdateLoginHistory($uid);
		} else {
			$uid=null;
			$errmsg=$result;
		}
	}
}

/* ---------- Login page ---------- */
if (empty($uid)) {
	$theme='default';
	renderPageBegin(null,null,null,null);
	if (!empty($errmsg)) {
		echo '<b class="warning">'.$errmsg.'</b><br>';
		echo '<a href="sendpwd.php">Lost your password?</a><br><br>';
	}
	echo '<div class=inlineblock>';
	echo '<FORM method="POST"><DIV align="right">';
	echo 'Username: &nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<INPUT type="text" size=20 name="uid" value=""><BR><BR>';
	echo 'Password: &nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<INPUT type="password" size=20 name="pwd" value="">';
	echo '</DIV><BR><INPUT type="submit" value="Login"></FORM>';
	echo '<FONT class="warning">(Cookies must be enabled to login.)</FONT>';
	echo '</div>';
	if ($releaseinfo)
		echo '<P class="releaseInfo">'.$releaseinfo.'</P>';
	renderPageEnd(null);
	exit;
}

/* ---------- Main page ---------- */

/* Render page */
renderPageBegin('SpitterChess - Overview',null,array(
	'My Games'=>'index.php?mygames=1',
	'Search'=>'search.php',
	'Rankings'=>'rankings.php',
	'Help'=>'help.php',
	'Logout'=>'logout.php'),
	null);

/* Show login info + stats */
$stats=ioLoadUserStats($uid);
$gamecount=$stats['wins']+$stats['draws']+$stats['losses'];
echo '<p>Hello <b>'.$uid.'</b>! </p>';
echo '<span class=textBox>';
echo $gamecount.' games total, ';
echo $stats['wins'].' wins, '.$stats['draws'].' draws, ';
echo $stats['losses'].' losses';
echo '</span>';

/* Update filter for game list and load infos. */
if (isset($_POST) && isset($_POST['search'])) {
	$_SESSION['allow_newgame']=0;
	$_SESSION['filter_name']='Search Results';
	$_SESSION['filter_loc']=$_POST['location'];
	if ($_POST['player']=='anyplayer')
		$_SESSION['filter_plyr']=null;
	else
		$_SESSION['filter_plyr']=$_POST['player'];
	if ($_POST['color']=='anycolor')
		$_SESSION['filter_clr']=null;
	else
		$_SESSION['filter_clr']=$_POST['color'];
	if ($_POST['opponent']=='anyplayer')
		$_SESSION['filter_opp']=null;
	else
		$_SESSION['filter_opp']=$_POST['opponent'];
} else if (isset($_GET) && isset($_GET['mygames'])) {
	$_SESSION['allow_newgame']=1;
	$_SESSION['filter_name']='Open Games';
	$_SESSION['filter_loc']='opengames';
	$_SESSION['filter_plyr']=$uid;
	$_SESSION['filter_clr']=null;
	$_SESSION['filter_opp']=null;
}
$list=ioLoadGameInfoList($_SESSION['filter_loc'],$_SESSION['filter_plyr'],
				$_SESSION['filter_clr'],$_SESSION['filter_opp']);
/* Add mark whether user may move in game */
foreach ($list as $key=>$info)
	if ($_SESSION['filter_loc']=='opengames' &&
			(($info['white']==$uid && $info['curplyr']=='w') ||
			($info['black']==$uid && $info['curplyr']=='b')))
		$list[$key]['p_maymove']=1;

/* If open games are displayed sort games where user may move to top. */
if ($_SESSION['filter_name']=='Open Games') {
	$greenlist=array();
	$redlist=array();
	$i=0;
	$j=count($list); /* required for unique keys for union */
	foreach ($list as $info)
		if ($info['p_maymove'])
			$greenlist[$i++]=$info;
		else
			$redlist[$j++]=$info;
	$list=$greenlist+$redlist;
}

/* Display list */
echo '<P><B>'.count($list).' '.$_SESSION['filter_name'].'</B></P>';
if (count($list)>0) {
	echo '<div class=inlineblock>';
	echo '<TABLE cellpadding=0 border=0 cellspacing=5 class="textBox">';
	echo '<TR><TD></TD>';
	echo '<TD><B>White&nbsp;&nbsp;&nbsp;&nbsp;</B></TD>';
	echo '<TD><B>Black&nbsp;&nbsp;&nbsp;&nbsp;</B></TD>';
	echo '<TD><B>Moves&nbsp;&nbsp;&nbsp;&nbsp;</B></TD>';
	echo '<TD><B class=pconly>Starting Date&nbsp;&nbsp;&nbsp;&nbsp;</B></TD>';
	echo '<TD><B class=pconly>Last Move On&nbsp;&nbsp;&nbsp;&nbsp;</B></TD>';
	echo '<TD></TD></TR>';
	foreach ($list as $info) {
		if ($info['p_maymove'])
			$mark='greenmark.png';
		else
			$mark='redmark.png';
		$startdate=date('M jS Y H:i',$info['ts_start']);
		$lastdate=date('M jS Y H:i',$info['ts_last']);
		echo '<TR><TD class=tdmarker><IMG alt="" class=marker src="images/'.$theme.'/'.$mark.'"></TD>';
		if ($info['curstate']=='w' || $info['curstate']=='-')
			echo '<TD><U>'.$info['white'].'</U></TD>';
		else
			echo '<TD>'.$info['white'].'</TD>';
		if ($info['curstate']=='b' || $info['curstate']=='-')
			echo '<TD><U>'.$info['black'].'</U></TD>';
		else
			echo '<TD>'.$info['black'].'</TD>';
		echo '<TD>'.$info['curmove'].'</TD>';
		echo '<TD><span class=pconly>'.$startdate.'&nbsp;&nbsp;</span></TD>';
		echo '<TD><span class=pconly>'.$lastdate.'&nbsp;&nbsp;</span></TD>';
		echo '<TD><A href="board.php?gid='.$info['gid'].'">';
		echo 'View</A></TD></TR>';
	}
	echo '</TABLE>';
	echo '</div>';
}
if ($_SESSION['allow_newgame'])
	echo '<P>[ <A href="newgame.php">New Game</A> ]</P>';

renderPageEnd(null);
?>
