<?php
/* Chess game logic to handle moves. */

/* Return the array of adjacent tiles (<=8). */
function getAdjTiles($fig_pos)
{
	$adj_tiles=array(); 
	$i=0;
	$x=$fig_pos%8; 
	$y=floor($fig_pos/8);

	if ($x>0 && $y>0) 
		$adj_tiles[$i++]=$fig_pos-9;
	if ($y>0) 
		$adj_tiles[$i++]=$fig_pos-8;
	if ($x<7 && $y>0) 
		$adj_tiles[$i++]=$fig_pos-7;
	if ($x<7) 
		$adj_tiles[$i++]=$fig_pos+1;
	if ($x<7 && $y<7) 
		$adj_tiles[$i++]=$fig_pos+9;
	if ($y<7) 
		$adj_tiles[$i++]=$fig_pos+8;
	if ($x>0 && $y<7) 
		$adj_tiles[$i++]=$fig_pos+7;
	if ($x>0) 
		$adj_tiles[$i++]=$fig_pos-1;

	/* DEBUG: foreach($adj_tiles as $tile)
	   echo 'adj: '.$tile.' '; */ 

	return $adj_tiles;
}

/* Check a number of tiles given a start, an end tile
 * (which is not included to the check) and a position
 * change for each iteration. Return true if not blocked. 
 * All values are given for 1dim board. */
function pathIsNotBlocked($start,$end,$change)
{
	global $board;

	for ($pos=$start; $pos!=$end; $pos+=$change) {
		/* DEBUG: echo 'path: $pos: '$board[$pos]' '; */
		if ($board[$pos]!='')
			return 0;
	}
	return 1;
}

/* Get empty tiles between start and end as 1dim array.
 * Whether the path is clear is not checked. */
function getPath($start,$end,$change)
{
	$path=array(); 
	$i=0;
	for ($pos=$start; $pos!=$end; $pos+=$change)
		$path[$i++]=$pos;
	return $path;
}

/* Get the change value that must be added to create
 * the 1dim path for figure moving from fig_pos to
 * dest_pos. It is assumed that the move is valid!
 * No additional checks as in tileIsReachable are
 * performed. Rook,queen and bishop are the only
 * units that can have empty tiles in between. */
function getPathChange($fig,$fig_pos,$dest_pos)
{
	$change=0;
	$fy=floor($fig_pos/8); 
	$fx=$fig_pos%8;
	$dy=floor($dest_pos/8); 
	$dx=$dest_pos%8;
	switch ($fig) {
		/* bishop */
		case 'B':
			if ($dy<$fy) 
				$change=-8; 
			else 
				$change=8;
			if ($dx<$fx) 
				$change-=1; 
			else 
				$change+=1;
			break;
		/* rook */
		case 'R':
			if ($fx==$dx) {
				if ($dy<$fy) 
					$change=-8; 
				else 
					$change=8;
			} else {
				if ($dx<$fx) 
					$change=-1; 
				else 
					$change=1;
			}
			break;
		/* queen */
		case 'Q':
			if (abs($fx-$dx)==abs($fy-$dy)) {
				if ($dy<$fy) 
					$change=-8; 
				else 
					$change=8;
				if ($dx<$fx) 
					$change-=1; 
				else 
					$change+=1;
			} else if ($fx==$dx) {
				if ($dy<$fy) 
					$change=-8; 
				else 
					$change=8;
			} else {
				if ($dx<$fx) 
					$change=-1; 
				else 
					$change=1;
			}
			break;
	}
	return $change;
}

/* Check whether dest_pos is in reach for unit of fig_type
 * at tile fig_pos. It is not checked whether the tile
 * itself is occupied but only the tiles in between. 
 * This function does not check pawns. */
function tileIsReachable($fig,$fig_pos,$dest_pos)
{
	global $board;

	if ($fig_pos==$dest_pos) 
		return;
	$result=0;
	$fy=floor($fig_pos/8); 
	$fx=$fig_pos%8;
	$dy=floor($dest_pos/8); 
	$dx=$dest_pos%8;
	/* DEBUG: echo "$fx,$fy--> $dx,$dy: "; */
	switch ($fig) {
		/* knight */
		case 'N':
			if (abs($fx-$dx)==1 && abs($fy-$dy)==2)
				$result=1;
			if (abs($fy-$dy)==1 && abs($fx-$dx)==2)
				$result=1;
			break;
		/* bishop */
		case 'B':
			if (abs($fx-$dx)!=abs($fy-$dy))
				break;
			if ($dy<$fy) 
				$change=-8; 
			else 
				$change=8;
			if ($dx<$fx) 
				$change-=1; 
			else 
				$change+=1;
			if (pathIsNotBlocked($fig_pos+$change,$dest_pos,
								$change))
				$result=1;
			break;
		/* rook */
		case 'R':
			if ($fx!=$dx && $fy!=$dy)
				break;
			if ($fx==$dx) {
				if ($dy<$fy) 
					$change=-8; 
				else 
					$change=8;
			} else {
				if ($dx<$fx) 
					$change=-1; 
				else 
					$change=1;
			}
			if (pathIsNotBlocked($fig_pos+$change,$dest_pos,
								$change))
				$result=1;
			break;
		/* queen */
		case 'Q':
			if (abs($fx-$dx)!=abs($fy-$dy) && $fx!=$dx && $fy!=$dy)
				break;
			if (abs($fx-$dx)==abs($fy-$dy))	{
				if ($dy<$fy) 
					$change=-8; 
				else 
					$change=8;
				if ($dx<$fx) 
					$change-=1; 
				else 
					$change+=1;
			} else if ($fx==$dx) {
				if ($dy<$fy) 
					$change=-8; 
				else 
					$change=8;
			} else {
				if ($dx<$fx) 
					$change=-1; 
				else 
					$change=1;
			}
			if (pathIsNotBlocked($fig_pos+$change,$dest_pos,
								$change))
				$result=1;
			break;
		/* king */
		case 'K':
			if (abs($fx-$dx)>1 || abs($fy-$dy)>1) 
				break;
			$kings=0;
			$adj_tiles=getAdjTiles($dest_pos);
			foreach($adj_tiles as $tile)
				if ($board[$tile][1]=='K') 
					$kings++;
			if ($kings==2) 
				break;
			$result=1;
			break;
	}

	/* DEBUG: echo " $result<BR>"; */
	return $result;
}

/* Check whether pawn at figpos may attack destpos thus if positioning
 * is diagonal. */
function checkPawnAttack($fig_pos,$dest_pos)
{
	global $board;

	if ($board[$fig_pos][0]=='w') {
		if (($fig_pos % 8)>0 && $dest_pos==$fig_pos+7)
			return 1;
		if (($fig_pos % 8)<7 && $dest_pos==$fig_pos+9)
			return 1;
	} else if ($board[$fig_pos][0]=='b') {
		if (($fig_pos % 8)<7 && $dest_pos==$fig_pos-7)
			return 1;
		if (($fig_pos % 8)>0 && $dest_pos==$fig_pos-9)
			return 1;
	}
	return 0;
}

/* Check whether pawn at figpos may move to destpos.
 * First move may be two tiles instead of just one. 
 * Again the last tile is not checked but just the path
 * in between. */
function checkPawnMove($fig_pos,$dest_pos)
{
	global $board;
	$first_move=0;

	if ($board[$fig_pos][0]=='w') {
		if ($fig_pos>=8 && $fig_pos<=15)
			$first_move=1;
		if ($dest_pos==$fig_pos+8)
			return 1;
		if ($first_move && ($dest_pos==$fig_pos+16))
			if ($board[$fig_pos+8]=='')
				return 1;
	} else if ($board[$fig_pos][0]=='b') {
		if ($fig_pos>=48 && $fig_pos<=55)
			$first_move=1;
		if ($dest_pos==$fig_pos-8)
			return 1;
		if ($first_move && ($dest_pos==$fig_pos-16))
			if ($board[$fig_pos-8]=='')
				return 1;
	}
	return 0;
}

/* Check all figures of player whether they attack the given position. */
function tileIsUnderAttack($pcolor,$dest_pos)
{
	global $board;

	for ($i=0; $i<64; $i++)
		if ($board[$i][0]==$pcolor) {
			if (($board[$i][1]=='P' && 
					checkPawnAttack($i,$dest_pos)) ||
					($board[$i][1]!='P' && 
					 tileIsReachable($board[$i][1],$i,
					 			$dest_pos))) {
				/*DEBUG: echo 'attack: $i: ',$pcolor,'P<BR>';*/
				return 1;
			}
		}
	return 0;
}

/* Check whether player's king is in check. */
function kingIsUnderAttack($pcolor)
{
	global $board;

	for ($i=0; $i<64; $i++)
		if ($board[$i]==$pcolor.'K') {
			$king_pos=$i;
			break;
		}
	/*DEBUG echo "$pcolor king is at $king_pos<BR>";*/
	if ($pcolor=='w')
		return tileIsUnderAttack('b',$king_pos);
	else
		return tileIsUnderAttack('w',$king_pos);
}

/* Check whether player's king is check mate */
function isCheckMate($pcolor)
{
	global $board;

	if ($pcolor=='w')
		$opp='b';
	else
		$opp='w';

	for ($i=0; $i<64; $i++)
		if ($board[$i]==$pcolor.'K') {
			$king_pos=$i;
			$king_x=$i % 8;
			$king_y=floor($i/8);
			break;
		}

	/* Test adjacent tiles while king is temporarily removed */
	$adj_tiles=getAdjTiles($king_pos);
	$contents=$board[$king_pos]; 
	$board[$king_pos]='';
	foreach ($adj_tiles as $dest_pos) {
		if ($board[$dest_pos][0]==$pcolor) 
			continue;
		if (tileIsUnderAttack($opp,$dest_pos)) 
			continue;
		$board[$king_pos]=$contents;
		return 0;
	}
	$board[$king_pos]=$contents;

	/* DEBUG: echo 'King cannot escape by itself! '; */

	/* Get all figures that attack the king */
	$attackers=array(); 
	$count=0;
	for ($i=0; $i<64; $i++)
		if ($board[$i][0]==$opp) {
			if (($board[$i][1]=='P' && 
					checkPawnAttack($i,$king_pos)) ||
					($board[$i][1]!='P' && 
					 tileIsReachable($board[$i][1],$i,
					 			$king_pos))) {
				$attackers[$count++]=$i;
			}
		}
	/* DEBUG: 
	   for($i=0; $i<$count; $i++)
	   echo 'Attacker: $attackers[$i] ';
	   echo 'Attackercount: ',count($attackers),' '; */

	/* If more than one there is no chance to escape */
	if ($count>1) 
		return 1;

	/* Check whether attacker can be killed by own figure */
	$dest_pos=$attackers[0];
	for ($i=0; $i<64; $i++)
		if ($board[$i][0]==$pcolor) {
			if (($board[$i][1]=='P' && 
					checkPawnAttack($i,$dest_pos)) ||
					($board[$i][1]!='P' && 
					$board[$i][1]!='K' &&
					 tileIsReachable($board[$i][1],$i,
					 			$dest_pos)) ||
					($board[$i][1]=='K' && 
					 tileIsReachable($board[$i][1],$i,
					 			$dest_pos) &&
					 !tileIsUnderAttack($opp,$dest_pos))) {
				/* DEBUG: echo 'candidate: $i '; */
				$can_kill_atk=0;
				$contents_def=$board[$i];
				$contents_atk=$board[$dest_pos];
				$board[$dest_pos]=$board[$i];
				$board[$i]='';
				if (!tileIsUnderAttack($opp,$king_pos))
					$can_kill_atk=1;
				$board[$i]=$contents_def;
				$board[$dest_pos]=$contents_atk;
				if ($can_kill_atk) {
					/*DEBUG: echo '$i can kill attacker';*/
					return 0;
				}
			}
		}

	/* Check whether own unit can block the way */

	/* If attacking unit is a knight there
	 * is no way to block the path. */
	if ($board[$dest_pos][1]=='N') 
		return 1;

	/* If enemy is adjacent to king there is no
	 * way either */
	$dest_x=$dest_pos % 8;
	$dest_y=floor($dest_pos/8);
	if (abs($dest_x-$king_x)<=1 && abs($dest_y-$king_y)<=1)
		return 1;

	/* Get the list of tiles between king and attacking
	 * unit that can be blocked to stop the attack */
	$change=getPathChange($board[$dest_pos][1],$dest_pos,$king_pos);
	/* DEBUG:  echo 'path change: $change '; */
	$path=getPath($dest_pos+$change,$king_pos,$change);
	/* DEBUG: foreach($path as $tile) echo 'tile: $tile '; */
	foreach($path as $pos) {
		for ($i=0; $i<64; $i++)
			if ($board[$i][0]==$pcolor) {
				if (($board[$i][1]=='P' && 
						checkPawnMove($i,$pos)) ||
						($board[$i][1]!='P' && 
						$board[$i][1]!='K' &&
						tileIsReachable($board[$i][1],
								$i,$pos))) {
					$board[$pos]=$board[$i];
					$old=$board[$i]; 
					$board[$i]='';
					$is_bound=kingIsUnderAttack($pcolor);
					$board[$i]=$old;
					$board[$pos]='';
					if (!$is_bound) {
						/*DEBUG: echo '$i can block ';*/
						return 0;
					}
				}
			}
	}
	return 1;
}

/* HACK: this function checks whether en-passant is possible */
function enPassantOkay($pcolor,$pos,$dest,$opp_ep_flag)
{
	if ($opp_ep_flag!='x')
		if ($dest%8==$opp_ep_flag)
			/* if (checkPawnAttack($pos,$dest)) right now 
			 * this is not required as we only use this
			 * function in isStaleMate which uses correct dests */
			if (($pcolor=='w' && floor($dest/8)==5) ||
					($pcolor=='b' && floor($dest/8)==2))
				return 1;
	return 0;
}

/* Move chessman from pos to dest, check whether king is under attack and 
 * restore the old board settings. whether pos-> dest is a valid move is 
 * NOT checked! */
function moveIsOkay($pcolor,$pos,$dest)
{
	global $board;

	/* DEBUG: echo '$pcolor-$opp: $pos-> $dest: '; */
	$old_pos=$board[$pos];
	$old_dest=$board[$dest];
	$board[$dest]=$board[$pos];
	$board[$pos]='';
	if (kingIsUnderAttack($pcolor)) 
		$ret=0; 
	else 
		$ret=1;
	$board[$pos]=$old_pos;
	$board[$dest]=$old_dest;
	/* DEBUG: echo '$ret<BR>'; */
	return $ret;
}
  
/* Check whether there is no further move possible */
function isStaleMate($pcolor,$w_ep,$b_ep /*line of en-passant*/)
{
	global $board;

	if ($pcolor=='w')
		$opp='b';
	else
		$opp='w';

	for ($i=0; $i<64; $i++) {
		if ($board[$i][0]!=$pcolor)
			continue;
		/* Can the figure move theoretically thus is there 
		 * at least one tile free for one figure? */
		switch ($board[$i][1]) {
			case 'K':
				$adj_tiles=getAdjTiles($i);
				foreach ($adj_tiles as $pos) {
					if ($board[$pos][0]==$pcolor) 
						continue;
					if (tileIsUnderAttack($opp,$pos)) 
						continue;
					/* Special case: if tile is not actively under
					 * attack it may still be blocked due to close
					 * opponent king */
					$kingtooclose=0;
					$adj_tiles2=getAdjTiles($pos); 
					foreach ($adj_tiles2 as $pos2) 
						if ($board[$pos2]==$opp.'K') {
							$kingtooclose=1;
							break;
						}
					if ($kingtooclose)
						continue;
					return 0;
				}
				/* DEBUG: echo 'King cannot escape alone!'; */
				break;
			case 'P':
				if ($pcolor=='w') {
					if ($board[$i+8]=='' && moveIsOkay($pcolor,$i,$i+8)) 
						return 0;
					if (($i%8)>0 && ($board[$i+7][0]==$opp || enPassantOkay('w',$i,$i+7,$b_ep))) 
						if (moveIsOkay($pcolor,$i,$i+7))
							return 0;
					if (($i%8)<7 && ($board[$i+9][0]==$opp || enPassantOkay('w',$i,$i+9,$b_ep))) 
						if (moveIsOkay($pcolor,$i,$i+9))
							return 0;
				} else {
					if ($board[$i-8]=='' && moveIsOkay($pcolor,$i,$i-8)) 
						return 0;
					if (($i%8)>0 && ($board[$i-9][0]==$opp || enPassantOkay('b',$i,$i-9,$w_ep))) 
						if (moveIsOkay($pcolor,$i,$i-9))
							return 0;
					if (($i%8)<7 && ($board[$i-7][0]==$opp || enPassantOkay('b',$i,$i-7,$w_ep))) 
						if (moveIsOkay($pcolor,$i,$i-7)) 
							return 0;
				}
				break;
			case 'B':
				if ($i-9>=0  && $board[$i-9][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i-9)) 
					return 0;
				if ($i-7>=0  && $board[$i-7][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i-7)) 
					return 0;
				if ($i+9<=63 && $board[$i+9][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i+9)) 
					return 0;
				if ($i+7<=63 && $board[$i+7][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i+7)) 
					return 0;
				break;
			case 'R':
				if ($i-8>=0 && $board[$i-8][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i-8)) 
					return 0;
				if ($i-1>=0 && $board[$i-1][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i-1)) 
					return 0;
				if ($i+8<=63 && $board[$i+8][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i+8)) 
					return 0;
				if ($i+1<=63 && $board[$i+1][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i+1)) 
					return 0;
				break;
			case 'Q':
				$adj_tiles=getAdjTiles($i);
				foreach ($adj_tiles as $pos)
					if ($board[$pos][0]!=$pcolor) 
						if (moveIsOkay($pcolor,$i,$pos))
							return 0;
				break;
			case 'N':
				if ($i-17>=0  && $board[$i-17][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i-17)) 
					return 0; 
				if ($i-15>=0  && $board[$i-15][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i-15)) 
					return 0;
				if ($i-6>=0  && $board[$i-6][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i-6)) 
					return 0;
				if ($i+10<=63 && $board[$i+10][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i+10)) 
					return 0;
				if ($i+17<=63 && $board[$i+17][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i+17)) 
					return 0;
				if ($i+15<=63 && $board[$i+15][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i+15)) 
					return 0;
				if ($i+6<=63 && $board[$i+6][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i+6)) 
					return 0;
				if ($i-10>=0  && $board[$i-10][0]!=$pcolor && 
						moveIsOkay($pcolor,$i,$i-10))
					return 0;
				break;
		}
	}
	return 1;
}

/* Convert short to full chess notation (e.g. Pe4 -> Pe2-e4).
 *	$move: short notation of move
 *	$pcolor: player color (w or b)
 * Return null on error or new move (or same if already full notation).
 * If an error occured set global acerror to reason. Return values:
 * [a-h][1-8|a-h][RNBQK]              pawn move/attack
 * [PRNBQK][a-h][1-8]                 figure move 
 * [PRNBQK][:x][a-h][1-8]             figure attack
 * [PRNBQK][1-8|a-h][a-h][1-8]        ambigous figure move
 * [a-h][:x][a-h][1-8][[RNBQK]        ambigous pawn attack 
 * [PRNBQK][1-8|a-h][:x][a-h][1-8]    ambigous figure attack
 */
function autocomplete($pcolor,$move)
{
	global $acerror,$board;

	/* Strip away # from possible PGN move */
	if ($move[strlen($move)-1]=='#')
		$move=substr($move,0,strlen($move)-1);

	if (strlen($move)>=6) {
		/* Full move: a pawn requires a ? in the end
		 * to automatically choose a queen on last line. */
		if ($move[0]=='P')
			if ($move[strlen($move)-1]<'A' || $move[strlen($move)-1]>'Z')
				$move=$move.'?';
		return $move;
	}

	/* For a pawn the last character may be A-Z to indicate promotion 
	 * chessman. We split this character to keep the autocompletion 
	 * process the same. */
	$pawn_upg='?';
	if ($move[strlen($move)-1]>='A' && $move[strlen($move)-1]<='Z') {
		$pawn_upg=$move[strlen($move)-1];
		$move=substr($move,0,strlen($move)-1);
	}
	if ($pawn_upg!='N' && $pawn_upg!='B' && $pawn_upg!='R' && 
					$pawn_upg!='Q' && $pawn_upg!='?') {
		$acerror='pawn may only become Knight, Bishop, Rook or Queen';
		return null;
	}

	if ($move[0]>='a' && $move[0]<='h') {
		/* Pawn move */
		if (strlen($move)==4) {
			/* [a-h]x[a-h][1-8] */
			if ($move[1]!='x') {
				$acerror='use x to indicate attack';
				return null;
			}
			$dest_x=$move[2];
			$dest_y=$move[3];
			$src_x=$move[0];
			if ($pcolor=='w')
				$src_y=$dest_y-1;
			else
				$src_y=$dest_y+1;
			return sprintf('P%s%dx%s%d%s',
					$src_x,$src_y,$dest_x,$dest_y,
					$pawn_upg);
		} else if (strlen($move)==2) {
			$fig=sprintf('%sP',$pcolor);
			if ($move[1] >='1' && $move[1] <='8') {
				/* [a-h][1-8] */
				$pos=bc2i($move);
				if ($pos==64) {
					$acerror='coordinate '.$move.' is invalid';
					return null;
				}
				if ($pcolor=='w') {
					while($pos>=0 && $board[$pos]!=$fig)
						$pos-=8;
					if ($pos<0) 
						$not_found=1;
				} else {
					while($pos<=63 && $board[$pos]!=$fig)
						$pos +=8;
					if ($pos>63) 
						$not_found=1;
				}
				$pos=i2bc($pos);
				if ($not_found || $pos=='') {
					$acerror='could not find '.$pcolor.' pawn in '.$move[0];
					return null;
				} 
				return sprintf('P%s-%s%s',$pos,$move,$pawn_upg);
			} else {
				/* [a-h][a-h] old attack notation: only 
				 * possible if single pawn in column */
				$pawns=0;
				$start=bc2i(sprintf('%s1',$move[0]));
				if ($start==64) {
					$acerror='coordinate '.$move[0].' is invalid';
					return null;
				}
				for ($i=1; $i<=8; $i++,$start+=8)
					if ($board[$start]==$fig) {
						$pawns++;
						$pawn_line=$i;
					}
				if ($pawns==0) {
					$acerror='no pawns in '.$move[0];
					return null;
				} 
				if ($pawns > 1) {
					$acerror='multiple pawns in '.$move[0];
					return null;
				} 
				if ($pcolor=='w')
					$dest_line=$pawn_line+1;
				else
					$dest_line=$pawn_line-1;
				return sprintf('P%s%dx%s%d',$move[0],$pawn_line,
							$move[1],$dest_line);
			}
		}
		/* If we got here pawn move could not be parsed */
		$acerror='could not parse pawn move '.$move;
		return null;
	}

	/* Other chessman move */
	$dest_coord=substr($move,strlen($move)-2,2);
	$action=$move[strlen($move)-3];
	if ($action!='x')
		$action='-';
	$fig_count=0;
	for ($i=0;$i<64;$i++)
		if ($board[$i]==$pcolor.$move[0]) {
			$fig_count++;
			if ($fig_count==1)
				$pos1=i2bc($i);
			else
				$pos2=i2bc($i);
		}
	if ($fig_count==0) {
		$acerror=sprintf('%s=%s not found',$move[0],getCMName($move[0]));
		return null;
	} 
	if ($fig_count==1)
		return sprintf('%s%s%s%s',$move[0],$pos1,$action,$dest_coord);
	/* Two chessmen - may cause ambiguity */
	$dest_pos=bc2i($dest_coord);
	if ($dest_pos==64) {
		$acerror='coordinate '.$dest_coord.' is invalid';
		return null;
	}
	if (tileIsReachable($move[0],bc2i($pos1),$dest_pos))
		$fig1_can_reach=1;
	if (tileIsReachable($move[0],bc2i($pos2),$dest_pos))
		$fig2_can_reach=1;
	if (!$fig1_can_reach && !$fig2_can_reach) {
		$acerror=sprintf('no %s can reach %s',getCMName($move[0]),
								$dest_coord);
		return null;
	} 
	if ($fig1_can_reach && $fig2_can_reach) {
		/* Ambiguity - check whether a hint is given */
		if (($action=='-' && strlen($move)==4) ||
				($action=='x' && 
				 strlen($move)==5))
			$hint=$move[1];
		if (empty($hint)) {
			$acerror=sprintf('both %s can reach %s',getCMName($move[0]),
								$dest_coord);
			return null;
		} 
		if ($hint>='1' && $hint<='8') {
			if ($pos1[1]==$hint && $pos2[1]!=$hint)
				$move_fig1=1;
			if ($pos2[1]==$hint && $pos1[1]!=$hint)
				$move_fig2=1;
		} else {
			if ($pos1[0]==$hint && $pos2[0]!=$hint)
				$move_fig1=1;
			if ($pos2[0]==$hint && $pos1[0]!=$hint)
				$move_fig2=1;
		}
		if (!$move_fig1 && !$move_fig2) {
			$acerror='could not resolve ambiguity';
			return null;
		}
		if ($move_fig1)
			return sprintf('%s%s%s%s',$move[0],$pos1,$action,
								$dest_coord);
		else
			return sprintf('%s%s%s%s',$move[0],$pos2,$action,
								$dest_coord);
	} else {
		if ($fig1_can_reach)
			return sprintf('%s%s%s%s',$move[0],$pos1,$action,
								$dest_coord); 
		else
			return sprintf('%s%s%s%s',$move[0],$pos2,$action,
								$dest_coord); 
	}

	/* If we got here chessman move could not be parsed */
	$acerror='could not parse chessman move '.$move;
	return null;
}

/* Get short notation of move for move history.
 * XXX Use autocomplete for this in a pretty messy way.
 * If an error occurs the move is kept unchanged (no error is generated). */
function getHistoryNotation($pcolor,$move)
{
	global $acerror;
	
	/* Valid pawn moves are always non-ambigious */
	if ($move[0]=='P') {
		/* Always skip P. For attacks skip source digit
		   and for moves skip source pos and - */
		if ($move[3]=='-')
			return substr($move,4);
		else if ($move[3]=='x')
			return sprintf('%s%s',$move[1],substr($move,3));
		else
			return $move; /* should not happen */
	}

	/* Try to remove the source position and check whether it
	 * is a non-ambigious move. If it is not add one of the components
	 * and check again. */
	if ($move[3]=='-')
		$dest=substr($move,4);
	else if ($move[3]=='x')
		$dest=substr($move,3);
	$new_move=sprintf('%s%s',$move[0],$dest);
	if (autocomplete($pcolor,$new_move)==null) {
		/* Add source column [a-h] */
		$new_move=sprintf('%s%s%s',$move[0],$move[1],$dest);
		if (autocomplete($pcolor,$new_move)==null) {
			/* Add source row [1-8] */
			$new_move=sprintf('%s%s%s',$move[0],$move[2],$dest);
			if (autocomplete($pcolor,$new_move)==null)
				$new_move=$move; /* give it up */
		}
	}
	return $new_move;
}

/* Verify move (full notation), execute it and modify game. */
function handleMove($gid,$uid,$move,$comment)
{
	global $acerror,$mail_header;

	$game=ioLoadGame($gid,$uid);
	if ($game==null)
		return 'ERROR: Game "'.$gid.'" not found!';

	/* Almost all helper functions require access to the chess board
	 * and passing it all the time is to complicated. Therefore, export
	 * it as global and operate on this global var even in this function.
	 * Before saving it is stored to the game context again. */
	global $board;
	$board=$game['board'];

	$move=trim($move);
	$result='undefined';
	$move_handled=0;

	/* Check whether moving is okay and get some vars. */
	$player_w=$game['white'];
	$player_b=$game['black'];
	$cur_move=$game['curmove'];
	$cur_player=$game['curplyr']; /* b or w */
	if (($cur_player=='w' && $uid!=$player_w) ||
			($cur_player=='b' && $uid!=$player_b))
		return 'It is not your turn!';
	if ($cur_player=='w')
		$cur_opp='b';
	else 
		$cur_opp='w';
	if ($game['curstate']!='?' && $game['curstate']!='D') 
		return 'Game is over.';
	/* Castling meaning: 
	 * 0 - rook or king moved
	 * 1 - possible
	 * 9 - performed */
	if ($cur_player=='w') {
		$may_castle_short=$game['wcs'];
		$may_castle_long=$game['wcl'];
	} else {
		$may_castle_short=$game['bcs'];
		$may_castle_long=$game['bcl'];
	}

	/* DEBUG echo 'HANDLE: w=$player_w,b=$player_b,c=$cur_player,';
	   echo 'm=$cur_move,may_castle=$may_castle_short,';
	   echo '$may_castle_long  <BR>';*/

	/* Allow two-step of king to indicate castling. */
	if ($cur_player=='w' && $move=='Ke1-g1')
		$move='0-0';
	else if ($cur_player=='w' && $move=='Ke1-c1')
		$move='0-0-0';
	else if ($cur_player=='b' && $move=='Ke8-g8')
		$move='0-0';
	else if ($cur_player=='b' && $move=='Ke8-c8')
		$move='0-0-0';

	/* Accept --- although it is now called resign. */
	if ($move=='---' || $move=='resign')
		$move='resigned';

	/* Remember move for history */
	$history_move=$move;

	/* Clear last move */
	$game['lastmove']='x';
	$game['lastkill']='x';
	$game['oscf']='x';
	$game['olcf']='x';

	/* HANDLE MOVES:
	 * resign                            resign
	 * 0-0                               short castling
	 * 0-0-0                             long castling
	 * draw?                             offer a draw
	 * accept_draw                       accept the draw
	 * refuse_draw                       refuse the draw
	 * [PRNBQK][a-h][1-8][-:x][a-h][1-8] unshortened move
	 */
	if ($move=='draw?' && $game['curstate']=='?') {
		/* Offer draw */
		$game['curstate']='D';
		$result='You have offered a draw.';
		$draw_handled=1;
		$game['lastmove']='DrawOffered';
	} else if ($move=='refuse_draw' && $game['curstate']=='D') {
		/* Refuse draw */
		$game['curstate']='?';
		$draw_handled=1;
		$result='You refused the draw.';
		$game['lastmove']='DrawRefused';
	} else if ($move=='accept_draw' && $game['curstate']=='D') {
		/* Accept draw */
		$game['curstate']='-';
		$draw_handled=1;
		$result='You accepted the draw.';
		$game['lastmove']='DrawAccepted';
		if ($game['curplyr']=='b')
			$game['curmove']++; // new move as white offered
		$game['mhistory'][count($game['mhistory'])]='draw';
	} else if ($move=='resigned') {
		/* Resignation */
		$game['curstate']=$cur_opp;
		$result='You have resigned.';
		$move_handled=1;
		$game['lastmove']='Resignation';
	} else if ($move=='0-0') {
		/* Short castling */
		if ($may_castle_short!=1 || $may_castle_long==9)
			return 'ERROR: You cannot castle short anymore!';
		if ($cur_player=='b' && $board[61]=='' && $board[62]=='') {
			if (kingIsUnderAttack('b'))
				return 'ERROR: You cannot escape check by castling!';
			if (tileIsUnderAttack('w',62) || 
						tileIsUnderAttack('w',61))
				return 'ERROR: Either king or rook would be under attack after short castling!';
			$may_castle_short=9;
			$board[60]='';
			$board[62]='bK';
			$board[61]='bR';
			$board[63]='';
		}
		if ($cur_player=='w' && $board[5]=='' && $board[6]=='') {
			if (kingIsUnderAttack('w'))
				return 'ERROR: You cannot escape check by castling!';
			if (tileIsUnderAttack('b',5) || 
						tileIsUnderAttack('b',6))
				return 'ERROR: Either king or rook would be under attack after short castling!';
			$may_castle_short=9;
			$board[4]='';
			$board[6]='wK';
			$board[5]='wR';
			$board[7]='';
		}
		if ($may_castle_short!=9)
			return 'ERROR: Cannot castle short because the way is blocked!';
		$result='You castled short.';
		$move_handled=1;
		$game['lastmove']='0-0';
	} else if ($move=='0-0-0') {
		/* Long castling */
		if ($may_castle_long!=1 || $may_castle_short==9)
			return 'ERROR: You cannot castle long anymore!';
		if ($cur_player=='b' && $board[57]=='' && $board[58]=='' && 
							$board[59]=='') {
			if (kingIsUnderAttack('b'))
				return 'ERROR: You cannot escape check by castling!';
			if (tileIsUnderAttack('w',58) || 
						tileIsUnderAttack('w',59))
				return 'ERROR: Either king or rook would be under attack after short castling!';
			$may_castle_long=9;
			$board[56]='';
			$board[58]='bK';
			$board[59]='bR';
			$board[60]='';
		}
		if ($cur_player=='w' && $board[1]=='' && $board[2]=='' && 
							$board[3]=='') {
			if (kingIsUnderAttack('w'))
				return 'ERROR: You cannot escape check by castling!';
			if (tileIsUnderAttack('b',2) || 
						tileIsUnderAttack('b',3))
				return 'ERROR: Either king or rook would be under attack after short castling!';
			$may_castle_long=9;
			$board[0]='';
			$board[2]='wK';
			$board[3]='wR';
			$board[4]='';
		}
		if ($may_castle_long!=9)
			return 'ERROR: Cannot castle long because the way is blocked!';
		$result='You castled long.';
		$move_handled=1;
		$game['lastmove']='0-0-0';
	} else {
		/* Normal move: [PRNBQK][a-h][1-8][-:x][a-h][1-8][RNBQK] */

		/* Autocomplete short notation (or keep full) */
		$acmove=autocomplete($cur_player,$move);
		if ($acmove==null)
			return 'ERROR: autocomplete: '.$acerror;
		else 
			$move=$acmove;

		/* A final capital letter may only be N,B,R,Q for the
		 * appropiate chessman. 
		 * FIXME it is not checked whether multiple promotion identifiers are
		 * there. In that case last one is used and move executed properly 
		 * but the history browsing will be broken. */
		$c=$move[strlen($move)-1];
		if ($c >='A' && $c <='Z' && 
				$c!='N' && $c!='B' && $c!='R' && $c!='Q')
			return 'ERROR: only N (knight),B (bishop),R (rook) and Q (queen) are valid chessman';

		/* Validate figure and position. */
		$fig_type=$move[0];
		$fig_name=getCMName($fig_type);
		if ($fig_name=='empty')
			return 'ERROR: Figure '.$fig_type.' is unknown!';
		$fig_coord=$move[1].$move[2];
		$fig_pos=bc2i($fig_coord);
		if ($fig_pos==64) 
			return 'ERROR: '.$fig_coord.' is invalid!';
		/* DEBUG  echo 'fig_type: $fig_type,fig_pos: $fig_pos<BR>'; */
		if ($board[$fig_pos]=='')
			return 'ERROR: '.$fig_coord.' is empty.';
		if ($board[$fig_pos][0]!=$cur_player)
			return 'ERROR: Figure does not belong to you!';
		if ($board[$fig_pos][1]!=$fig_type)
			return 'ERROR: Figure does not exist!';

		/* Get target index */
		$dest_coord=$move[4].$move[5];
		$dest_pos=bc2i($dest_coord);
		if ($dest_pos==64)
			return 'ERROR: '.$dest_coord.' is invalid!';
		if ($dest_pos==$fig_pos)
			return 'ERROR: Current position and destination are equal!';
		/* DEBUG  echo 'dest_pos: $dest_pos<BR>'; */

		/* Get action */
		$action=$move[3];
		if ($move[3]=='-') 
			$action='M'; /* move */
		else if ($move[3]=='x')
			$action='A'; /* attack */
		else
			return 'ERROR: '.$action.' is unknown! Please use "-" for move and "x" for attack.';
		/* Replace - with x if this is meant to be en-passant. */
		if ($fig_type=='P' && (abs($fig_pos-$dest_pos)==7 || abs($fig_pos-$dest_pos)==9)) {
			$action='A';
			$history_move=str_replace('-','x',$history_move);
			$move=str_replace('-','x',$move);
		}

		/* Save for undo */
		$game['lastmove']=str_replace('?','',$move);

		/* If attacking an enemy unit must be present on tile
		 * and if move then tile must be empty. In both cases
		 * the king must not be in check after moving. */

		/* Check whether the move is along a valid path and
		 * whether all tiles in between are empty thus the path
		 * is not blocked. The final destination tile is not 
		 * checked here. */
		if ($fig_type!='P') {
			if (!tileIsReachable($fig_type,$fig_pos,$dest_pos))
				return 'ERROR: '.$dest_coord.' is not in moving range of '.$fig_name.' at '.$fig_coord.'!';
		} else {
			if ($action=='M' && !checkPawnMove($fig_pos,$dest_pos))
				return 'ERROR: '.$dest_coord.' is not in moving range of '.$fig_name.' at '.$fig_coord.'!';
			else if ($action=='A' && !checkPawnAttack($fig_pos,$dest_pos))
				return 'ERROR: '.$dest_coord.' is not in attacking range of '.$fig_name.' at '.$fig_coord.'!';
		}

		$en_passant_okay=0;
		/* Check action */
		if ($action=='M' && $board[$dest_pos]!='')
			return 'ERROR: '.$dest_coord.' is occupied!';
		if ($action=='A' && $board[$dest_pos]=='') {
			/* En passant of pawn? */
			if ($fig_type=='P') {
				if ($cur_player=='w') {
					if ($game['b2spm']!='x' && 
							$dest_pos%8==$game['b2spm'] && 
							floor($dest_pos/8)==5)
						$en_passant_okay=1;
				} else {
					if ($game['w2spm']!='x' &&
							$dest_pos%8==$game['w2spm'] &&
							floor($dest_pos/8)==2)
						$en_passant_okay=1;
				}
				if ($en_passant_okay==0)
					return 'ERROR: En-passant is not possible!';
			} else
				return 'ERROR: '.$dest_coord.' is empty!';
		}
		if ($action=='A' && $board[$dest_pos][0]==$cur_player)
			return 'ERROR: You cannot attack own chessman at '.$dest_coord.'!';

		/* Backup affected tiles */
		$old_fig_tile=$board[$fig_pos];
		$old_dest_tile=$board[$dest_pos];

		/* Get short notation of move for history before 
		 * actually moving since this function relies on 
		 * autocomplete which in turn requires the initial board
		 * to work properly (after move, first chessman is on
		 * destination tile already and second can't reach it
		 * which would result in 'no one can reach' error) */
		if (strlen($history_move)>=6)
			$history_move=getHistoryNotation($cur_player,
							$history_move);
		/* DEBUG: echo 'Move: $move ($history_move)<BR>'; */

		/* Perform move */
		$board[$fig_pos]='';
		if ($board[$dest_pos]!='')
			$game['lastkill']=sprintf('%s%s',$board[$dest_pos],
								$dest_pos);
		$board[$dest_pos]=$cur_player.$fig_type;
		if ($en_passant_okay) {
			/* Kill pawn */
			if ($cur_player=='w') {
				$board[$dest_pos-8]='';
				$game['lastkill']=sprintf('bP%s',$dest_pos-8);
			} else {
				$board[$dest_pos+8]='';
				$game['lastkill']=sprintf('wP%s',$dest_pos+8);
			}
		}

		/* If king is in check undo */
		if (kingIsUnderAttack($cur_player)) {
			$board[$fig_pos]=$old_fig_tile;
			$board[$dest_pos]=$old_dest_tile;
			if ($en_passant_okay) {
				/* Respawn en-passant pawn */
				if ($cur_player=='w') 
					$board[$dest_pos-8]='bP';
				else
					$board[$dest_pos+8]='wP';
			}
			return 'ERROR: Move is invalid because king would be under attack then.';
		}

		/* Check whether this forbids any castling */
		if ($fig_type=='K') {
			if ($may_castle_short==1)
				$may_castle_short=0;
			if ($may_castle_long==1)
				$may_castle_long=0;
		}
		if ($fig_type=='R') {
			if ($may_castle_long==1 && ($fig_pos%8)==0)
				$may_castle_long=0;
			if ($may_castle_short==1 && ($fig_pos%8)==7)
				$may_castle_short=0;
		}

		/* If a pawn moved two tiles this will allow 'en passant'
		 * for next turn. */
		if ($fig_type=='P' && abs($fig_pos-$dest_pos)==16) {
			if ($cur_player=='w')
				$game['w2spm']=$fig_pos%8;
			else
				$game['b2spm']=$fig_pos%8;
		} else {
			/* Clear 'en passant' of OUR last move */
			if ($cur_player=='w')
				$game['w2spm']='x';
			else
				$game['b2spm']='x';
		}

		$result='Your last move: '.str_replace('?','',$move);

		/* If pawn reached last line promote it */
		if ($fig_type=='P') {
			if (($cur_player=='w' && $dest_pos>=56) || 
					($cur_player=='b' && $dest_pos<=7)) {
				$pawn_upg=$move[strlen($move)-1];
				if ($pawn_upg=='?') {
					$pawn_upg='Q';
					$history_move=sprintf('%sQ',
								$history_move);
				}
				$board[$dest_pos]=$cur_player.$pawn_upg;
				$result=sprintf('%s... promotion to %s!',
						$result,getCMName($pawn_upg));
			}
		}

		$move_handled=1;
	}

	/* If move was executed update game state. */
	if ($move_handled) {
		/* Check checkmate/stalemate */
		if (kingIsUnderAttack($cur_opp)) {
			/* If this is check mate finish the game otherwise
			 * add '+' to the move. */
			if (isCheckMate($cur_opp)) {
				$game['curstate']=$cur_player;
				$mate_type=1;
			} else
				$result=$result.'... CHECK!';
			$history_move=sprintf('%s+',$history_move);
		} else if (isStaleMate($cur_opp,$game['w2spm'],$game['b2spm'])){
			$game['curstate']='-';
			$mate_type=2;
		}

		/* Backup castling info if rook or king has moved */
		if ($fig_type=='R' || $fig_type=='K') {
			if ($cur_player=='w') {
				$game['oscf']=$game['wcs'];
				$game['olcf']=$game['wcl'];
			} else {
				$game['oscf']=$game['bcs'];
				$game['olcf']=$game['bcl'];
			}
		}

		/* Update castling flags */
		if ($cur_player=='w') {
			$game['wcs']=$may_castle_short;
			$game['wcl']=$may_castle_long;
		} else {
			$game['bcs']=$may_castle_short;
			$game['bcl']=$may_castle_long;
		}

		/* Update move number and current player */
		if ($game['curplyr']=='w')
			$game['curmove']++;
		$game['mhistory'][count($game['mhistory'])]=$history_move;

		/* If other player can't move anymore end the game. */
		if ($mate_type > 0) {
			if ($mate_type==1) {
				$mate_name='mate';
				$result=$result.'... CHECKMATE!';
			} else {
				$mate_name='stalemate';
				$result=$result.'... STALEMATE!';
			}
			if ($game['curplyr']=='b')
				$game['curmove']++;
			$game['mhistory'][count($game['mhistory'])]=$mate_name;
		}
	}
	if ($move_handled || $draw_handled) {
		/* If game is over update user stats (includes resignation). */
		if ($game['curstate']!='?' && $game['curstate']!='D') {
			{include 'rating.php';}
			updateStats($game['white'],$game['black'],$game['curstate']);
		}

		/* Set next player */
		if ($game['curplyr']=='b')
			$game['curplyr']='w';
		else
			$game['curplyr']='b';

		/* Add comment to head of chatter. Right now we have only two
		 * chatter items. Strip backslashes and replace newlines to get
		 * a single line. */
		if (empty($comment))
			$comment='(silence)';
		else {
			$comment=str_replace("\\",'',strip_tags($comment));
			$comment=str_replace("\n",'<br>',$comment);
		}
		$comment='<u>'.$uid.'</u>: '.$comment;
		$game['chatter'][1]=$game['chatter'][0];
		$game['chatter'][0]=$comment;

		/* Store changed board */
		$game['board']=$board;

		/* Save game */
		ioSaveGame($game,$gid);

		/* Send a notification if email address was supplied. */
		if ($cur_opp=='b')
			$oid=$player_b;
		else
			$oid=$player_w;
		$email=ioLoadUserEmailAddress($oid);
		if ($email) {
			$message="Dear $oid,\n\n$uid has just moved.\n\n".
					"Move:\n$move\n\n".
					"Comment:\n$comment\n\n".
					"It is your turn now!\n\n".
					"Enter the game:\n".getGameURL($gid);
			mail($email,"[OCC] $player_w vs $player_b: $move",
						$message, $mail_header);
		}

	}
	return $result;
}

/* Undo last move (only possible if game is not over yet). */
function handleUndo($gid,$uid)
{
	$game=ioLoadGame($gid,$uid);
	if ($game==null)
		return 'ERROR: Game "'.$gid.'" does not exist!';
	if (!$game['p_mayundo'])
		return 'ERROR: Undo is not possible!';

	$move=$game['lastmove'];
	$fixhist=1;
	if ($move=='DrawOffered' || $move=='DrawRefused')
		$fixhist=0;
	else if ($move=='0-0') {
		if ($game['curplyr']=='b') {
			/* white castled short */
			$game['board'][5]='';
			$game['board'][6]='';
			$game['board'][4]='wK';
			$game['board'][7]='wR';
			$game['wcs']=1;
		} else {
			/* black castled short */
			$game['board'][61]='';
			$game['board'][62]='';
			$game['board'][60]='bK';
			$game['board'][63]='bR';
			$game['bcs']=1;
		}
	} else if ($move=='0-0-0') {
		if ($game['curplyr']=='b') {
			/* white castled long */
			$game['board'][2]='';
			$game['board'][3]='';
			$game['board'][4]='wK';
			$game['board'][0]='wR';
			$game['wcl']=1;
		} else {
			/* black castled long */
			$game['board'][58]='';
			$game['board'][59]='';
			$game['board'][60]='bK';
			$game['board'][56]='bR';
			$game['bcl']=1;
		}
	} else {
		/* Undo normal move (includes en passant) */
		$src=bc2i($move[1].$move[2]);
		$dest=bc2i($move[4].$move[5]);
		if ($src>=0 && $src<64 && $dest>=0 && $dest<64) {
			if ($game['curplyr']=='w')
				$oldclr='b';
			else
				$oldclr='w';
			$game['board'][$src]=$oldclr.$move[0];
			$game['board'][$dest]='';
			if ($game['lastkill']!='x') {
				$kill=$game['lastkill'];
				$dest=$kill[2].$kill[3];
				$game['board'][$dest]=$kill[0].$kill[1];
			}
			/* Adjust castling flags */
			if ($move[0]=='R'||$move[0]=='K') {
				if ($game['curplyr']=='b') {
					/* white castling info */
					$game['wcs']=$game['oscf'];
					$game['wcl']=$game['olcf'];
				} else {
					/* black castling info */
					$game['bcs']=$game['oscf'];
					$game['bcl']=$game['olcf'];
				}
			}
		}
	}

	/* Fix chatter. Fill in ... for lost chatter. */
	$game['chatter'][0]=$game['chatter'][1];
	$game['chatter'][1]='<u>'.$uid.'</u>: ...';
	/* Correct game state */
	if ($move=='DrawRefused')
		$game['curstate']='D';
	else
		$game['curstate']='?';
	/* Clear own en passant flag */
	if ($game['curplyr']=='b')
		$game['w2spm']='x';
	else
		$game['b2spm']='x';
	/* Modify history */
	if ($fixhist) {
		/* Adjust turn counter if nescessary */
		if ($game['curplyr']=='b')
			$game['curmove']=$game['curmove']-1;
		/* Delete last move */
		$i=count($game['mhistory'])-1;
		unset($game['mhistory'][$i]);
	}
	/* Switch players */
	if ($game['curplyr']=='b')
		$game['curplyr']='w';
	else
		$game['curplyr']='b';
	/* Clear last move */
	$game['lastmove']='x';
	$game['lastkill']='x';
	$game['oscf']='x';
	$game['olcf']='x';
	/* Save game */
	ioSaveGame($game,$gid);

	return 'Move "'.$move.'" undone!';
}

?>
