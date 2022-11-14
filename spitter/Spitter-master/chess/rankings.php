<?php /* Ranking table page */
include 'verifysession.php';
include 'rating.php';
include 'render.php';
include 'io.php';

/* Create rankings - set ranking score according to selected criteria and 
 * ignore users with less than five games. */
$sortby='rating';
if (isset($_GET['sortby']))
	$sortby=$_GET['sortby'];
$users=ioLoadUserList();
foreach ($users as $usr) {
	$stats=ioLoadUserStats($usr);
	$numgames=$stats['wins']+$stats['draws']+$stats['losses'];
	if ($numgames<5)
		continue;
	if ($stats['rgames']>0)
		$ratingmod=round(getK($stats['rating'])*$stats['rchange']);
	else
		$ratingmod=0;
	$activity=ioGetUserActivity($usr);
	switch ($sortby) {
		case 'provrating': 
			$score=$stats['rating']+$ratingmod;
			break;
		case 'wdl':
			$score=$stats['wins']+$stats['draws']/2;
			break;
		case 'games':
			$score=$numgames;
			break;
		case 'activity':
			$score=$activity; 
			break;
		default: 
			$sortby='rating'; /* fix the name to catch garbage */
			$score=$stats['rating']; 
			break;
	}
	$rankings[$usr]=array('uid'=>$usr,
				'score'=>$score,
				'activity'=>$activity,
				'numgames'=>$numgames,
				'wins'=>$stats['wins'],
				'draws'=>$stats['draws'],
				'losses'=>$stats['losses'],
				'rating'=>$stats['rating'],
				'ratingmod'=>$ratingmod);
}
function compareScore($a,$b)
{
	global $sortby;

	if ($a['score']==$b['score']) {
		/* If scoring is the same decide by rating. If sorting by
		 * rating use provisional rating. */
		$a2=$a['rating'];
		$b2=$b['rating'];
		if ($sortby=='rating') {
			$a2+=$a['ratingmod'];
			$b2+=$b['ratingmod'];
		}
		if ($a2==$b2)
			return 0;
		else if ($a2<$b2)
			return 1;
		else
			return -1;
	} else if ($a['score']<$b['score'])
		return 1;
	else
		return -1;
}
if ($rankings)
	uasort($rankings,'compareScore');

/* Activity colors XXX not in style sheet of theme */
$actcolors=array('#6d0f0f','#b08500','#ece40a','#84b814','#01f60c','#01f60c');

/* Render page */
renderPageBegin('OCC - Rankings',null,array(
	'Overview'=>'index.php',
	'Search'=>'search.php',
	'Help'=>'help.php',
	'Logout'=>'logout.php'),
	'User Rankings');

/* Render head of table. Allow selection of sort criteria via link and 
 * underline current criteria. */
echo '<TABLE border=0 cellspacing=0 cellpadding=5 class="textBox">';
echo '<TR>';
echo '<TD><B>Rank</B></TD>';
echo '<TD><B>Name</B></TD>';
$criteria=array('games'=>'Games',
		'wdl'=>'W,D,L',
		'rating'=>'Rating',
		'provrating'=>'(Prov.)',
		'activity'=>'Activity');
foreach ($criteria as $key=>$entry) {
	echo '<TD align=right><B>';
	if ($sortby==$key)
		echo '<U>';
	else
		echo '<A class="head" href=rankings.php?sortby='.$key.'>';
	echo $entry;
	if ($sortby==$key)
		echo '</U>';
	else
		echo '</A>';
	echo '</B></TD>';
}
echo '</TR>';

/* Render ranking entries. */
$i=1;
if ($rankings==null) 
	echo '<TR><TD align=center colspan=7>No entries.</TD></TR>';	
else foreach ($rankings as $entry) {
	if ($uid==$entry['uid'])
		$class='textBoxHighlight';
	else
		$class='textBox';
	echo '<TR class="'.$class.'">';
	echo '<TD>'.$i.'.</TD>';
	echo '<TD>'.$entry['uid'].'</TD>';
	echo '<TD align=right>'.$entry['numgames'].'</TD>';
	$wdl=$entry['wins'].','.$entry['draws'].','.$entry['losses'];
	echo '<TD align=right>'.$wdl.'</TD>';
	echo '<TD align=right>'.sprintf('%d',$entry['rating']).'</TD>';
	if ($entry['ratingmod']>0)
		$sign='+';
	else
		$sign='';
  	$perf='('.$sign.$entry['ratingmod'].')';
	echo '<TD align=right>'.$perf.'</TD>';
	if ($entry['activity']==0)
		$color='#000000';
	else
		$color=$actcolors[floor($entry['activity']/20)];
	echo '<TD align=right><FONT color="'.$color.'">';
	if ($entry['activity']==100)
		echo '<B>'.$entry['activity'].'</B>';
	else
		echo $entry['activity'];
	echo '</FONT></TD>';
	echo '</TR>';
	$i++;
}
echo '</TABLE>';
echo '<P class="warning">';
echo '(You must have finished at least 5 games to show up.)';
echo '</P>';

renderPageEnd(null);
?>
