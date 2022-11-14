<?php
/* Javascript functions for browsing history. Board and history is converted
 * from PHP to JS for this (thus JS function to build history/board is created
 * via PHP. */

/* Import theme variable to Javascript. */
echo '<script language="Javascript">theme="'.$theme.'";</script>';
?>

<script language="Javascript">
/* Preload images */
var preload=new Image(); 

/* Variables */
var parse_error="";
var cur_move=-1,move_count=0,orig_move_count=0;
var bottom="w";
var name="",draw_gap=0,slot_id=0;
var diff=Array(0,0,0,0,0);
var diff_names=Array("pawn","knight","bishop","rook","queen");
var d1,d2;
var board=new Array(
	0,0,0,0,0,0,0,0,
	0,0,0,0,0,0,0,0,
	0,0,0,0,0,0,0,0,
	0,0,0,0,0,0,0,0,
	0,0,0,0,0,0,0,0,
	0,0,0,0,0,0,0,0,
	0,0,0,0,0,0,0,0,
	0,0,0,0,0,0,0,0
);
var moves=new Array(); /* moves*3=src,dest,kill pairs */

/* Go forward or backward in history to given move (from current position
 * cur_move). Update board too. Move 0 is handled special to allow 
 * initialization: board is cleared and first move is performed. */
function gotoMove(move_id)
{
	if (move_id<0)
		move_id=0;
	if (move_id >=move_count) {
		move_id=move_count-1;
		if (move_count<orig_move_count)
			alert("Parse error after "+move_count+" moves: "+parse_error);
	}
	if (move_id==cur_move) 
		return false;
	if (move_id==0) {
		cur_move=0;
		board=new Array(
			/* 0=a1 - 63=h8 */
			/* chessmen codes: 
			 * 0 empty
			 * 1-6 white PNBRQK
			 * 7-12 black PNBRQK */
			4,2,3,5,6,3,2,4,
			1,1,1,1,1,1,1,1,
			0,0,0,0,0,0,0,0,
			0,0,0,0,0,0,0,0,
			0,0,0,0,0,0,0,0,
			0,0,0,0,0,0,0,0,
			7,7,7,7,7,7,7,7,
			10,8,9,11,12,9,8,10);
		board[moves[1]]=board[moves[0]]; 
		board[moves[0]]=0;
	} else {
		//alert(moves[move_id*3]+"-"+moves[move_id*3+1]+" ("+moves[move_id*3+2]+")");
		/* Go forward or backward */
		if (cur_move > move_id)
			while(cur_move > move_id)
				moveBackward();
		else while(cur_move<move_id)
			moveForward();
	}
	var str = "B";
	if (move_id%2==0)
		str = "W";
	str = str + (Math.floor(move_id/2)+1);
	document.getElementById('turncounter').innerHTML = str;
	renderBoard();
	return false;
}

/* Go one move forward in history without rendering the board. */
function moveForward()
{
	if (cur_move==move_count-1)
		return;
	cur_move++; 
	pos=cur_move*3;
	if (moves[pos]==0 && moves[pos+1]==0) 
		return;
	/* castling is special */
	if (moves[pos] > 63) {
		rook_start=Math.floor(moves[pos]%100);
		rook_end=Math.floor(moves[pos+1]%100);
		king_start=Math.floor(moves[pos]/100);
		king_end=Math.floor(moves[pos+1]/100);

		//alert(rook_start+"-"+rook_end+"  "+king_start+"-"+king_end);

		board[rook_end]=board[rook_start]; 
		board[rook_start]=0;
		board[king_end]=board[king_start]; 
		board[king_start]=0;
	} else {
		if (moves[pos+1] > 63) {
			/*promotion*/
			dest=Math.floor(moves[pos+1]%100);
			upg=Math.floor(moves[pos+1]/100);
			board[dest]=board[moves[pos]]+upg;
		} else
			board[moves[pos+1]]=board[moves[pos]]; 
		board[moves[pos]]=0;
		if (moves[pos+2] > 63) {
			pawn_pos=Math.floor(moves[pos+2]/100);
			board[pawn_pos]=0;
		}
	}
}

/* Go one move backward without rendering board. */
function moveBackward()
{
	if (cur_move==0) 
		return;
	pos=cur_move*3;
	cur_move--;
	if (moves[pos]==0 && moves[pos+1]==0) 
		return;
	/* castling is special */
	if (moves[pos] > 63) {
		rook_start=Math.floor(moves[pos]%100);
		rook_end=Math.floor(moves[pos+1]%100);
		king_start=Math.floor(moves[pos]/100);
		king_end=Math.floor(moves[pos+1]/100);

		//alert(rook_start+"-"+rook_end+"  "+king_start+"-"+king_end);

		board[rook_start]=board[rook_end]; 
		board[rook_end]=0;
		board[king_start]=board[king_end]; 
		board[king_end]=0;
	} else {
		if (moves[pos+1] > 63) {
			dest=Math.floor(moves[pos+1]%100);
			upg=Math.floor(moves[pos+1]/100);
			board[moves[pos]]=board[dest]-upg; 
		} else {
			dest=moves[pos+1];
			board[moves[pos]]=board[dest];
		}
		if (moves[pos+2] > 0) {
			if (moves[pos+2] > 12) {
				/* en passant move */
				pawn_pos=Math.floor(moves[pos+2]/100);
				chessman=Math.floor(moves[pos+2]%100);
				board[pawn_pos]=chessman;
				board[dest]=0;
			} else
				board[dest]=moves[pos+2];
		}
		else
			board[dest]=0;
	}
}

/* Show difference in chessmen using image slots 'tslot0-15' (below move
 * history). */
function showDiff()
{
	for (i=0; i<15; i++)
		document.images["tslot"+i].src="images/"+theme+"/empty.png";

	/* Compute chessmen difference */
	for (i=0; i<5; i++) 
		diff[i]=0;
	for (i=0; i<64; i++)
		if (board[i] > 0) {
			if (board[i] >=7 && board[i]<12)
				diff[board[i]-7]--;
			else
				if (board[i] >=1 && board[i]<6)
					diff[board[i]-1]++;
		}

	/* Bottom player first */
	slot_id=0;
	for(i=0; i<5; i++) {
		name=diff_names[4-i];
		if (bottom=="b" && diff[4-i]<0) {
			for (j=0; j<-diff[4-i]; j++) {
				document.images["tslot"+slot_id].src=
					"images/"+theme+"/b"+name+".svg";
				slot_id++;
			}
		} else if (bottom=="w" && diff[4-i]>0) {
			for (j=0; j<diff[4-i]; j++) {
				document.images["tslot"+slot_id].src=
					"images/"+theme+"/w"+name+".svg";
				slot_id++;
			}
		}
	}
	/* Have one slot empty for separation */
	if (slot_id > 0) {
		document.images["tslot"+slot_id].src="images/"+theme+"/empty.png";
		slot_id++;
	}
	/* Top player next */
	for(i=0; i<5; i++) {
		name=diff_names[4-i];
		if (bottom=="b" && diff[4-i]>0) {
			for (j=0; j<diff[4-i]; j++) {
				document.images["tslot"+slot_id].src=
					"images/"+theme+"/w"+name+".svg";
				slot_id++; 
			}
		}
		else if (bottom=="w" && diff[4-i]<0) {
			for (j=0; j<-diff[4-i]; j++) {
				document.images["tslot"+slot_id].src=
					"images/"+theme+"/b"+name+".svg";
				slot_id++;
			}
		}
	}
}

/* Render chessboard by filling b0-b63 slots with the proper image (these
 * slot ids are set in renderBoard). */
function renderBoard()
{
	for (i=0; i<64; i++) {
		if (board[i]==0) {
			document.images["b"+i].src="images/"+theme+"/empty.png";
			continue;
		}
		value=board[i];
		if (value >=7) {
			pref="b"; 
			value-=6;
		} else 
			pref="w";
		switch (value) {
			case 1: chessman="pawn.svg"; break;
			case 2: chessman="knight.svg"; break;
			case 3: chessman="bishop.svg"; break;
			case 4: chessman="rook.svg"; break;
			case 5: chessman="queen.svg"; break;
			case 6: chessman="king.svg"; break;
		}
		document.images["b"+i].src="images/"+theme+"/"+pref+chessman;
	}
	showDiff();   
}
</script>

<?php
/* Perform a full notation move without any checks. The provided move history 
 * must be completely valid. Return encoded integer of killed chessman or 0
 * if none killed. $src,$dest could be parsed from move but they are required
 * in generating JS move history so we re-use them. */
function quickMove($pcolor,$move,$src,$dest)
{
	global $board;
	$kill=0;

	if ($move=='0-0') {
		if ($pcolor=='w') {
			$board[4]='';
			$board[6]='wK';
			$board[5]='wR';
			$board[7]='';
		} else {
			$board[60]='';
			$board[62]='bK';
			$board[61]='bR';
			$board[63]='';
		}
	} else if ($move=='0-0-0') {
		if ($pcolor=='w') {
			$board[0]='';
			$board[2]='wK';
			$board[3]='wR';
			$board[4]='';
		} else {
			$board[56]='';
			$board[58]='bK';
			$board[59]='bR';
			$board[60]='';
		}
	} else {
		/* Pawn promotion? */
		$c=$move[strlen($move)-1];
		if ($c!='Q' && $c!='R' && $c!='B' && $c!='N')
			$c=$move[0];
		else
			$board[$src]=$pcolor.$c;
		/* If this was attack kill chessman */
		if ($move[3]=='x') {
			if ($board[$dest]=='') {
				/* En passant kill */
				if ($pcolor=='w') {
					$kill=100*($dest-8)+7;
					$board[$dest-8]='';
				} else {
					$kill=100*($dest+8)+1;
					$board[$dest+8]='';
				}
			} else {
				if ($board[$dest][0]=='w')
					$kill=1;
				else
					$kill=7;
				switch ($board[$dest][1]) {
					case 'P': $kill+=0; break;
					case 'N': $kill+=1; break;
					case 'B': $kill+=2; break;
					case 'R': $kill+=3; break;
					case 'Q': $kill+=4; break;
					case 'K': $kill+=5; break;
				}
			}
		}
		$board[$dest]=$board[$src];
		$board[$src]='';
	}
	return $kill;
}

/* Init chess board */
$board=array(
	'wR','wN','wB','wQ','wK','wB','wN','wR',
	'wP','wP','wP','wP','wP','wP','wP','wP',
	'','','','','','','','',
	'','','','','','','','',
	'','','','','','','','',
	'','','','','','','','',
	'bP','bP','bP','bP','bP','bP','bP','bP',
	'bR','bN','bB','bQ','bK','bB','bN','bR'
);

/* Build javascript moves (each PHP move is a triple in JS: src,dest,kill */
echo '<script language="Javascript">';
$js_index=0; 
$clr='w';
for ($i=0; $i<count($game['mhistory']); $i++) {
	$move=$game['mhistory'][$i];
	if ($move=='draw' || $move=='mate' || $move=='stalemate' || 
					$move=='---' ||	$move=='resigned') {
		echo 'moves['.$js_index.']=0;';
		$js_index++;
		echo 'moves['.$js_index.']=0;';
		$js_index++;
		echo 'moves['.$js_index.']=0;'; 
		$js_index++;
		break;
	}
	$src=0;
	$dest=0;
	$kill=0;
	if ($move[strlen($move)-1]=='+')
		$move=substr($move,0,strlen($move)-1);
	if ($move=='0-0') {
		if ($clr=='w') {
			$src=407; 
			$dest=605;
		} else {
			$src=6063; 
			$dest=6261;
		}
		$fmove='0-0';
	} else if ($move=='0-0-0') {
		if ($clr=='w') {
			$src=400;
			$dest=203;
		} else {
			$src=6056;
			$dest=5859;
		}
		$fmove='0-0-0';
	} else
		$fmove=autocomplete($clr,$move);
	if ($fmove!=null) {
		if ($src==0 && $dest==0) {
			$src=bc2i($fmove[1].$fmove[2]);
			$dest=bc2i($fmove[4].$fmove[5]);
		}
		/* $src,$dest will not be changed when castling */
		$kill=quickMove($clr,$fmove,$src,$dest);
		/* modify $dest to reflect chessman promotion if any */
		$c=$move[strlen($move)-1];
		if ($c=='Q' || $c=='R' || $c=='B' || $c=='N') {
			switch ($c) {
				case 'N': $dest+=100; break;
				case 'B': $dest+=200; break;
				case 'R': $dest+=300; break;
				case 'Q': $dest+=400; break;
			}
		}
	} else {
		echo 'move_count='.$js_index.'/2;';
		echo 'parse_error="'.$js_index.': '.$clr.': '.$fmove.': '.$acerror.'";';
		break;
	}
	echo 'moves['.$js_index.']='.$src.';';
	$js_index++;
	echo 'moves['.$js_index.']='.$dest.';';
	$js_index++;
	echo 'moves['.$js_index.']='.$kill.';';
	$js_index++;
	if ($clr=='w')
		$clr='b';
	else
		$clr='w';
}
echo 'move_count='.count($game['mhistory']).';';
echo 'orig_move_count=move_count;';
echo 'bottom="'.$pcolor.'";';
echo '</script>';
?>
