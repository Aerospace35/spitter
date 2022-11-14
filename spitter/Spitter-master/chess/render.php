<?php
/* General render functions */
$version = '1.4';
$pagetitle='SpitterChess';
/* Render beginning of page (HTML+logo+link bar+title). $class is CSS 
 * class of main table (empty= no class). $title may be null. $pagetitle
 * (window title) may be null (is just 'OCC' then). */
function renderPageBegin($pagetitle,$class,$links,$title)
{
	global $theme;

	if ($pagetitle==null)
		$pagetitle='SpitterChess';

	echo '<title>'.$pagetitle.'</title>'.
		'<meta name="viewport" content="width=device-width, initial-scale=1.0">'.
		'<link rel=stylesheet type="text/css" '.
		'href="images/'.$theme.'/style.css">'.
		'<meta charset=utf-8">';
		
	echo '<div class=mainwrapper>';
	if (strstr($pagetitle,"Browsing Mode") !== false || 
			strstr($pagetitle,"Input Mode") !== false)
		echo '<header class=pconly>SpitterChess</header>';
	else
		echo '<header>SpitterChess</header>';
	if ($links) {
		echo '<nav>[ ';
		$i=0;
		foreach ($links as $name=>$url) {
			echo "<a href=$url>$name</a>";
			$i++;
			if ($i < count($links))
				echo ' | ';
		}
		echo ' ]</nav>';
	}
	if ($title)
		echo '<p class=subtitle>'.$title.'</p>';

	echo '<main>';
}

/* Render end of page (footer+HTML). If $credit is not empty display it. 
 * Show build-time if $btstart is set (in session.php). This is a good
 * place here, since it is most likely the last function to be called on a page. */
function renderPageEnd($credits)
{
	global $btstart, $version;

	echo '</main>';
	echo '<footer class=clearall>';

	echo '<hr>';
	
	echo '<div class=row>';
	echo '<div class=leftcredits>';
	echo 'SpitterChess v'.$version.'<br>Published under GNU GPL';
	if (!empty($credits))
		echo '<br><br>'.$credits;
	echo '</div><div class=rightcredits>';
	echo '<br> <A class="tiny" href="</A>';
	if (!empty($btstart))
		echo '<br><br>Build-time: '.sprintf("%.3f",1000*(microtime(true)-$btstart)).' msecs';
	echo '</div></div>';
	
	echo '</footer>';

	echo '</div>';
	
}

/* Render functions for various components of chess page */

/* Render command form which contains information about players and last 
 * moves and all main buttons (shown when nescessary). The final command 
 * is mapped to hidden field 'cmd' on submission. Show previous command 
 * result $cmdres if set or last move if any. Fill move edit with $move
 * if set (to restore move when notes were saved). */
function renderCommandForm($game,$cmdres,$move)
{
	$i_move=$game['curmove'];
	$i_plyr='Black';
	if ($game['curplyr']=='w') {
		$i_plyr='White';
		$i_move++;
	} else if ($game['curstate']=='D')
		$i_move++;
	echo '<p class=boardtitle>';
	echo '<b>'.$game['white'].'</b> - <b>'.$game['black'].'</b> <br class=pconly>';
	echo '('.$i_plyr.'\'s turn #'.$i_move.')</P>';
	echo '<FORM name="commandForm" method="post">';
	echo '<INPUT type="hidden" name="cmd" value="">';
	echo '<INPUT type="hidden" name="comment" value="">';
	echo '<INPUT type="hidden" name="privnotes" value="">';
	if ($game['p_mayabort'] && !$game['p_maymove']) {
		/* Info that game is very old + abort form */
		echo '<P class="warning">You may abort this game since your opponent did not move for more than four weeks (it is not counted then).<BR>';
		echo '<DIV align="center"><INPUT type="submit" value="Abort Game" onClick="if (confirmAbort()) {document.commandForm.cmd.value=\'abort\'; gatherCommandFormData(); return true;} else return false;"></DIV>';
		echo '</P>';
	}
	if ($cmdres || ($game['archived']==0 && $game['lastmove']!='x' &&
			($game['curstate']=='D' || $game['curstate']=='?'))) {
		/* Info about last move + possible undo */
		if (!empty($cmdres)) 
			$info=$cmdres;
		else {
			if ($game['curplyr']=='b')
				$info='White\'s';
			else
				$info='Black\'s';
			$info=$info.' last move: '.$game['lastmove'];
		}
		echo '<P class=info><B>'.$info.'</B>';
		if ($game['p_mayundo'])
			echo '&nbsp;&nbsp;<INPUT type="submit" value="Undo" onClick="if (confirmUndo()) {document.commandForm.cmd.value=\'undo\'; gatherCommandFormData(); return true;} else return false;">';
		echo '</P>';
	}
	if ($game['p_maymove'] && $game['curstate']=='D') {
		/* Info if draw is offered + draw form */
		if (empty($info)) {
			$info=$game['p_opponent'].' has offered a draw.';
			echo '<P class=info><B>'.$info.'</B></P>';
		}
		echo '<div>';
		echo '<INPUT class=warning type="submit" value="Accept Draw" onClick="onClickAcceptDraw()">';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<INPUT type="submit" value="Refuse Draw" onClick="onClickRefuseDraw()">';
		echo '</div>';
	} else if ($game['p_maymove'] && $game['curstate']=='?') {
		/* Normal move form */
		echo '<div>';
		echo '<INPUT id=moveButton type="button" value="Move" onClick="onClickMove()">';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<INPUT type="button" value="Offer Draw" onClick="onClickOfferDraw()">';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;';
		if ($game['p_mayabort'])
			echo '<INPUT class=warning type="button" value="Abort Game" onClick="onClickAbortGame()">';
		else
			echo '<INPUT class=warning type="button" value="Resign" onClick="onClickResign()">';
		echo '</div>';
		echo '<INPUT type="hidden" size=10 name="move" value="'.$move.'">';
		/* clear move highlight and move button */
		echo '<script language="Javascript">checkMoveButton(); highlightMove(null)</script>';
		/* if last move was a normal one, highlight it so one quickly
		 * sees what the opponent did */
		if (preg_match("/[NnBbQqPpRrKk][a-h][1-8][x-][a-h][1-8]/", 
					$game['lastmove']) == "1")
			echo '<script language="Javascript">'.
				'highlightMove(\''.$game['lastmove'].'\');'.
				'lastMoveIsHighlighted = 1;'.
				'window.setTimeout(clearLastMoveHighlight,2000);'.
				'</script>';
	} else {
		/* User may not move. Show proper info and possibly archive/refresh form */
		if ($game['curstate']=='D' || $game['curstate']=='?') {
			echo '<P align="center">';
			echo '<INPUT type="submit" value="Refresh Board" onClick="document.commandForm.cmd.value=\'\'; gatherCommandFormData(); return true;"></P>';
		} else if (empty($info)) {
			if ($game['curstate']=='-')
				$info='draw';
			else if ($game['curstate']=='w')
				$info=$game['white'].' won';
			else if ($game['curstate']=='b')
				$info=$game['black'].' won';
			$info='Game result: '.$info;
			echo '<P><B style="color: 8888ff">'.$info.'</B></P>';
			if ($game['p_mayarchive'])
				echo '<P align="center"><INPUT type="submit" value="Archive Game" onClick="document.commandForm.cmd.value=\'archive\'; gatherCommandFormData(); return true;"></P>';
		}
	}
	echo '</FORM>';
}

/* Render browser form which contains title about who is playing and browsing
 * buttons to move forward/backward in history. */
function renderBrowserForm($game)
{
	global $theme;

	echo '<P class=boardtitle>';
	echo '<B>'.$game['white'].'</B> - <B>'.$game['black'].'</B><BR class=pconly> ';
	if ($game['curstate']!='?' && $game['curstate']!='D') {
		if ($game['curstate']=='-')
			$res='draw';
		else if ($game['curstate']=='w')
			$res='White won';
		else 
			$res='Black won';
		echo '<span class=info><b>Game result: '.$res.'</b></span>';
	}
	echo '</P><div class=inlineblock>';
	echo '<A class=playbutton href="first" onClick="return gotoMove(0);">&nbsp;&nbsp;&nbsp;|&#9664;&nbsp;&nbsp;&nbsp;</A>';
	echo '<A class=playbutton href="prev" onClick="return gotoMove(cur_move-1);">&nbsp;&nbsp;&nbsp;&#9664;&nbsp;&nbsp;&nbsp;</A>';
	echo '<a id=turncounter class=playbutton>0</a>';
	echo '<A class=playbutton href="next" onClick="return gotoMove(cur_move+1);">&nbsp;&nbsp;&nbsp;&#9654;&nbsp;&nbsp;&nbsp;</A>';
	echo '<A class=playbutton href="last" onClick="return gotoMove(move_count-1);">&nbsp;&nbsp;&nbsp;&#9654;|&nbsp;&nbsp;&nbsp;</A>';
	echo '</div>';
}

/* Render move history and chessmen difference. 
 * $list: w1,b1,w2,b2,... 
 * If $browsing is set ignore $diff (create empty slots instead) and show full 
 * history with javascript links. Otherwise show only few last moves. */
function renderHistory($list,$diff,$browsing)
{
	global $theme;

	if (count($list)==0)
		return;

	echo '<div class=textFrame>';
	$num=floor((count($list)+1)/2);
	/* Show only few last moves if not browsing */
	if (!$browsing && $num>12) {
		$start=floor($num-12);
		echo '... ';
	} else
		$start=0;
	for ($i=1+$start,$j=$start*2;$i<=$num;$i++,$j+=2) {
		echo '<b>'.$i.'.</b>&nbsp;';
		if ($browsing) {
			$jspos=$j;
			echo '<a href="'.$jspos.'" onClick="return gotoMove('.$jspos.');">'.$list[$j].'</a> ';
			$jspos++;
			echo '<a href="'.$jspos.'" onClick="return gotoMove('.$jspos.');">'.$list[$j+1].'</a> ';
		} else
			echo $list[$j].' '.$list[1+$j].' ';
	}
	echo '</div>';
	
	if ($browsing) {
		echo '<div class=textFrame>';
		for ($i=0; $i<15; $i++)
			echo '<img class=lostfigure name="tslot'.$i.'" src="images/'.$theme.'/empty.png">';
		echo '</div>';
	} else if (!empty($diff)) {
		$names=array('pawn','knight','bishop','rook','queen');
		echo '<div class=textFrame>';
		/* White first */
		$src=null;
		for ($i=0;$i<5;$i++)
			if ($diff[$i]>0)
				for ($j=0;$j<$diff[$i];$j++) {
					$src='images/'.$theme.'/w'.$names[$i].'.svg';
					echo '<img class=lostfigure src="'.$src.'">';
				}
		if ($src != null)
			echo '<img class=lostfigure src="images/'.$theme.'/empty.png">';
		/* Black second */
		for ($i=0;$i<5;$i++)
			if ($diff[$i]<0)
				for ($j=0;$j>$diff[$i];$j--) {
					$src='images/'.$theme.'/b'.$names[$i].'.svg';
					echo '<img class=lostfigure src="'.$src.'">';
				}
		echo '</div>';
	}
}

/* Render chess board. 
 * $board: 1dim chess board (a1=0,...,h8=63) with color/chessmen ('bQ','wP',...)
 * $pc: playercolor ('w' or 'b' or empty)
 * $active: may move (add javascript calls for command assembly)
 * If $board is null create empty board for history browser.
 */
function renderBoard($board,$pc,$active)
{
	global $theme;
	
	/* show white at bottom if not playing */
	if (empty($pc))
		$pc='w';

	/* build chessboard */
	echo '<div class=boardFrame>';
	echo '<table class="board">';
	if ($pc=='w') {
		$index=56;
		$pos_change = 1;
		$line_change = -16;
	} else {
		$index=7;
		$pos_change = -1;
		$line_change = 16;
	}
	for ($y=0;$y<9;$y++) {
		echo '<tr>';
		for ($x=0;$x<9;$x++) {
    			if ($y==8) {
				/* number at bottom */
				if ($x>0) {
					if ( $pc == 'w' )
						$c = chr(96+$x);
					else
						$c = chr(96+9-$x);
					echo '<td class="boardCoord"><span class=pconly>'.$c.'</span></td>';
				} else
					echo '<td class="boardCoord"></td>';
			} else if ($x==0) {
				/* number on the left */
				if ( $pc == 'w' )
					$i = 8-$y;
				else
					$i = $y+1;
				echo '<td class="boardCoord"><span class=pconly>'.$i.'</span></td>';
			} else {
				/* normal tile */
				if ($board) {
					$entry=$board[$index];
					$color=substr($entry,0,1);
					$name=strtolower(getCMName($entry[1]));
				}
				if ((($y+1)+($x))%2==0)
					$class='boardTileWhite';
				else
					$class='boardTileBlack';
				if ($board==null) {
					echo '<td class="'.$class.'"><img class=figure name="b'.$index.'" src="images/'.$theme.'/empty.png"></td>';
				} else if ($name!='empty') {
					if ($active) {
						if ($pc!=$color)
							$cmdpart=sprintf('x%s',i2bc($index));
						else
							$cmdpart=sprintf('%s%s',$board[$index][1],i2bc($index));
						echo '<td id="btd'.$index.'" class="'.$class.'"><a href="" onClick="return assembleCmd(\''.$cmdpart.'\');"><img class=figure border=0 src="images/'.$theme.'/'.$color.$name.'.svg"></a></td>';
        			} else
						echo '<td class="'.$class.'"><img class=figure src="images/'.$theme.'/'.$color.$name.'.svg"></td>';
				} else {
					if ($active) {
						$cmdpart=sprintf('-%s',i2bc($index));
						echo '<td id="btd'.$index.'" class="'.$class.'"><a href="" onClick="return assembleCmd(\''.$cmdpart.'\');"><img class=figure border=0 src="images/'.$theme.'/empty.png"></a></td>';
					} else
						echo '<td class="'.$class.'"><img class=figure src="images/'.$theme.'/empty.png"></td>';
				}
				$index += $pos_change;
			}
		}
		$index += $line_change;
		echo "</tr>";
	}
	echo "</table></div>";
}

/* Render private notes formular
 * $notes: current unencrypted contents
 */
function renderPrivateNotes($uid,$oid)
{
	global $theme;

	$notes=ioLoadPrivateNotes($uid,$oid);

	echo '<script language="Javascript">';
	echo 'function gatherPNotesFormData() {';
	echo 'fm=document.pnotesForm;';
	echo 'if (document.commentForm && document.commentForm.comment)';
	echo '  fm.commentbackup.value=document.commentForm.comment.value;';
	echo 'if (document.commandForm && document.commandForm.move)';
	echo '  fm.movebackup.value=document.commandForm.move.value;';
	echo '} </script>';
	echo '<FORM method="post" name="pnotesForm">';
	echo '<INPUT type="hidden" name="commentbackup" value="">';
	echo '<INPUT type="hidden" name="movebackup" value="">';
	echo 'Private Notes:<span class="warning"> (encrypted)</span>&nbsp;';
	echo '<INPUT type="button" value=Save onClick="gatherPNotesFormData(); return true;"><BR>';
	echo '<TEXTAREA class=textinput rows=3 name="privnotes">'.$notes.'</TEXTAREA>';
	echo '</FORM>';
}

/* Render chatter and add comment formular if wanted
 * $game: game context 
 * $comment: current comment */
function renderChatter($game, $comment)
{
	for ($i=count($game['chatter'])-1;$i>=0;$i--)
		echo '<div class=chatmessage>'.$game['chatter'][$i].'</div>';
	if ($game['p_maymove']) {
		echo '<FORM name="commentForm" method="post">';
		echo '<TEXTAREA class=textinput rows=3 name="comment">'.$comment.'</TEXTAREA>';
		echo '</FORM>';
	}
}

?>
