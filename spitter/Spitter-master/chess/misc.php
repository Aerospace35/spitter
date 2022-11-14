<?php /* A bunch of helper functions needed in various scripts. */

/* Convert board coordinate [a-h][1-8] to 1dim index [0..63] */
function bc2i($coord)
{
	switch ($coord[0]) {
		case 'a': $x=0; break;
		case 'b': $x=1; break;
		case 'c': $x=2; break;
		case 'd': $x=3; break;
		case 'e': $x=4; break;
		case 'f': $x=5; break;
		case 'g': $x=6; break;
		case 'h': $x=7; break;
		default: return 64; /* error code */
	}
	$y=$coord[1]-1; 
	if ($y<0 || $y>7 )
		return 64; /* error code */
	$index=$y*8+$x;
	return $index;
}

/* Convert index [0..63] to board coordinate [a-h][1-8] */
function i2bc($index)
{
	if ($index<0 || $index>63)
		return '';
	$y=floor($index/8)+1;
	$x=chr(($index%8)+97);
	$coord=$x.$y;
	return $coord;
}

/* Get full name of chessman from chessman identifier. */
function getCMName($short)
{
	$name='empty';
	switch ($short) {
		case 'P': $name='Pawn'; break;
		case 'R': $name='Rook'; break;
		case 'N': $name='Knight'; break;
		case 'B': $name='Bishop'; break;
		case 'K': $name='King'; break;
		case 'Q': $name='Queen'; break;
	}
	return $name;
}

/* Get difference in chessmen for each class (pawn=0,queen=5).
 * Positive value = White has more, Negative value = Black has more. */
function getCMDiff($board)
{
	$diff = array(0,0,0,0,0);
	for ($i=0;$i<64;$i++) {
		switch ($board[$i]) {
			case 'wP': $diff[0]++; break;
			case 'wN': $diff[1]++; break;
			case 'wB': $diff[2]++; break;
			case 'wR': $diff[3]++; break;
			case 'wQ': $diff[4]++; break;
			case 'bP': $diff[0]--; break;
			case 'bN': $diff[1]--; break;
			case 'bB': $diff[2]--; break;
			case 'bR': $diff[3]--; break;
			case 'bQ': $diff[4]--; break;
		}
	}
	for ($i=0;$i<5;$i++)
		if ($diff[$i] != 0)
			return $diff;
	return null;
}

/** Return URL to game with id $gid. */
function getGameURL( $gid )
{
	/* XXX access $_SERVER; otherwise $GLOBALS seems to be empty; 
	 * maybe a bug in PHP? */
	$temp = $_SERVER['SERVER_NAME']; // dummy access
	$prot=($GLOBALS['_SERVER']['HTTPS']=='on')?'https':'http';
	$script=$GLOBALS['_SERVER']['SCRIPT_NAME'];
	/* $script is the script calling this function but we want to redirect
	 * to board.php so we have to replace the name (can't use board.php 
	 * directly since we don't know path to script on server). */
	$script = dirname($script)."/board.php";
	return $prot.'://'.$GLOBALS['_SERVER']['HTTP_HOST'].$script.'?gid='.$gid;
}

?>
