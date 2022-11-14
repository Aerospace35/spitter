<?php /* Chess board page */

/* Verify session */
include 'verifysession.php';

/* Check game id */
if (isset($_POST['gid']))
	$gid=$_POST['gid'];
else if (isset($_GET['gid']))
	$gid=$_GET['gid'];
if (preg_match('/[^\w\-\.]/',$gid)) 
	$gid=null;

/* Browser variables */
if (isset($_GET['browse']))
	$browse=$_GET['browse'];
if (isset($_GET['rotate']) && $_GET['rotate']==0)
	$rotate=1;
else
	$rotate=0;

/* Includes */
include 'misc.php';
include 'io.php';
include 'render.php';
include 'chess.php';

/* Check posted command. The POST name is always 'cmd' in any formular.
 * The comment is always named 'comment' (if any).
 * Valid commands are:
 * [PKBRQK][a-h][1-8][-x][a-h][1-8][?PKBRQ]: chess move
 * acceptdraw: accept draw offer
 * refusedraw: refuse draw offer
 * undo: undo last move
 * ---/resign: give up game
 * delete: delete game
 * archive: archive game
 */
if (!$browse && isset($_POST['cmd']) && !empty($_POST['cmd'])) {

	$cmd=$_POST['cmd'];
	$comment=$_POST['comment'];
	$cmdres='';

	/* Lock access to games/user stats. Any other access (like login
	 * history or private notes) is not locked since it is only 
	 * accessible by one user which won't do it twice the same time. */
	ioLock();

	if ($cmd=='abort')
		$cmdres=ioAbortGame($gid,$uid);
	else if ($cmd=='undo')
		$cmdres=handleUndo($gid,$uid);
	else if ($cmd=='acceptdraw')
		$cmdres=handleMove($gid,$uid,'accept_draw',$comment);
	else if ($cmd=='refusedraw')
		$cmdres=handleMove($gid,$uid,'refuse_draw',$comment);
	else if ($cmd=='archive') 
		$cmdres=ioArchiveGame($gid,$uid);
	else /* try as chess move */
		$cmdres=handleMove($gid,$uid,$cmd,$comment);

	ioUnlock();
}

/* Check whether any backup was posted. This is, e.g., used to restore
 * move/comment when saving priv notes. */
if ($_POST['movebackup'])
	$move=$_POST['movebackup'];
else
	$move=null;
if ($_POST['commentbackup'])
	$comment=$_POST['commentbackup'];
else
	$comment=null;

/* Load game */
$game=ioLoadGame($gid,$uid);

/* If privnotes is posted save notes (requires opponent from game) */
if (isset($_POST['privnotes']) && $game['p_opponent'])
	ioSavePrivateNotes($uid,$game['p_opponent'],$_POST['privnotes']);

/* Force browsing mode for archived games. */
if ($game['archived'] && !$browse) {
	$browse=1;
	$rotate=0;
}

/* Get mode depending javascript */
if ($browse) {
	/* $pcolor tells which user is at bottom for generating javascript
	 * (and rendering board) */
	if ($uid==$game['white'])
		$pcolor='w';
	else
		$pcolor='b';
	if ($rotate) {
		if ($pcolor=='w')
			$pcolor='b';
		else
			$pcolor='w';
	}
	include 'browser.js';
} else
	include 'board.js';

/* Build page */
if ($browse)
	$pagetitle='OCC - Browsing Mode ('.$game['white'].' - '.$game['black'].')';
else
	$pagetitle='OCC - Input Mode ('.$game['white'].' - '.$game['black'].')';
$links=array();
$links['Overview']='index.php';
if ($browse) {
	if (!$game['archived'])
		$links['Play']='board.php?gid='.$gid;
	$links['Rotate']='board.php?gid='.$gid.'&browse=1&rotate='.$rotate;
} else if ($game['curmove']>0)
	$links['Browse']='board.php?gid='.$gid.'&browse=1&rotate=1';
$links['PGN']='pgnformat.php?gid='.$gid;
$links['Help']='help.php?gid='.$gid;
$links['Logout']='logout.php';
renderPageBegin($pagetitle,null,$links,null);
if ($game==null)
	echo '<p class="warning">Game "'.$gid.'" not found!</p>';
else {
	echo '<div class=row>';
	
	echo '<div class=col50><section class=sctn_board>';
	if ($browse)
		renderBoard(null,$pcolor,null);
	else
		renderBoard($game['board'],$game['p_color'],
						$game['p_maymove'],0);
	echo '</section></div>';
	
	echo '<div class=col50><section class=sctn_command>';
	if ($browse) {
		renderBrowserForm($game);
		renderHistory($game['mhistory'],null,1);
	} else {
		renderCommandForm($game,$cmdres,$move);
		renderHistory($game['mhistory'],getCMDiff($game['board']),0);
	}
	echo '</section>';
	
	if (!$browse && $game['p_color']) {
		echo '<section class=sctn_notes>';
		renderPrivateNotes($uid,$game['p_opponent']);
		echo '</section>';
	}
	echo '</div>';
	
	echo '</div>';
	
	if (!$browse) {
		echo '<section class=sctn_chat>';
		renderChatter($game,$comment);
		echo '</section>';
	}
}
include "images/$theme/credits.php";
renderPageEnd($credits);
if ($browse)
	echo '<script language="Javascript">gotoMove(0);gotoMove(move_count-1);renderBoard();</script>;';
?>
