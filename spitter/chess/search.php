<?php /* Search page */
include 'verifysession.php';
include 'render.php';
include 'io.php';

$users=ioLoadUserList();

renderPageBegin('OCC - Search',null,array(
	'Overview'=>'index.php',
	'Rankings'=>'rankings.php',
	'Help'=>'help.php',
	'Logout'=>'logout.php'),
	'Search Games');

echo '<div class=inlineblock>';
echo '<FORM method="post" action="index.php"><TABLE border=0>';
echo '<TR><TD>Location:</TD>';
echo '<TD align="right"><SELECT name="location">';
$sel=($_SESSION['filter_loc']=='opengames')?'selected':'';
echo '<OPTION '.$sel.' value="opengames">Open Games</OPTION>';
$sel=($_SESSION['filter_loc']=='archive')?'selected':'';
echo '<OPTION '.$sel.' value="archive">Archive</OPTION>';
echo '</SELECT></TD></TR>';
echo '<TR><TD>Player:</TD>';
echo '<TD align="right"><SELECT name="player">';
echo '<OPTION value="anyplayer">Any Player</OPTION>';
foreach ($users as $usr) {
	$sel=($_SESSION['filter_plyr']==$usr)?'selected':'';
	echo '<OPTION '.$sel.' value="'.$usr.'">'.$usr.'</OPTION>';
}
echo '</SELECT></TD></TR>';
echo '<TR><TD>Player Color:&nbsp;&nbsp;&nbsp;&nbsp;</TD>';
echo '<TD align="right"><SELECT name="color">';
echo '  <OPTION value="anycolor">Any Color</OPTION>';
$sel=($_SESSION['filter_clr']=='w')?'selected':'';
echo '  <OPTION '.$sel.' value="w">White</OPTION>';
$sel=($_SESSION['filter_clr']=='b')?'selected':'';
echo '  <OPTION '.$sel.' value="b">Black</OPTION>';
echo '</SELECT></TD></TR>';
echo '<TR><TD>Opponent:&nbsp;&nbsp;&nbsp;&nbsp;</TD>';
echo '<TD align="right"><SELECT name="opponent">';
echo '<OPTION value="anyplayer">Any Player</OPTION>';
foreach ($users as $usr) {
	$sel=($_SESSION['filter_opp']==$usr)?'selected':'';
	echo '<OPTION '.$sel.' value="'.$usr.'">'.$usr.'</OPTION>';
}
echo '</SELECT></TD></TR>';
echo '</TABLE>';
echo '<BR><INPUT type="submit" value="Search" name="search">';
echo '</FORM>';
echo '</div>';

renderPageEnd(null);
?>
