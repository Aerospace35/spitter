<?php /* Functions to apply game results to rating according to Elo's formular */

/* Compute initial rating based on average opponent strength */
function getInitRating($wins,$draws,$losses,$avg_opp_rating)
{
	$n=$wins+$draws+$losses;
	if ($n==0)
		return 0;
	$rating=$avg_opp_rating+700*(($wins+0.5*$draws)/$n-0.5)*$n/($n+2);
	return round($rating);
}

/* Get propability for player to win from difference in rating */
function getWinProp($diff)
{
	$absdiff=abs($diff);
	if ($diff>735)
		return 1;
	else if ($diff<-735) 
		return 0;
	return 0.5 
		+ 1.4217*0.001*$diff
		- 2.4336*0.0000001*$diff*$absdiff
		- 2.5140*0.000000001*$diff*$absdiff*$absdiff
		+ 1.9910*0.000000000001*$diff*$absdiff*$absdiff*$absdiff;
}

/* Get coefficient K which is used to scale expected propability before
 * updating the rating. */
function getK($rating)
{
	if ($rating<2000)
		$K=30;
	else if ($rating>2400)
		$K=10;
	else
		$K=130-$rating/20;
	return $K;
}

/* Update user stats and return modified stats. Score is 2 (user won),
 * 1 (user drawed) or 0 (user lost). The rating is updated every five games.
 * Modification in between is stored in rating change. If less than five games
 * are finished, sum up opponent strength in rating change. If five games are
 * finished compute initial rating based on wdl and average opponent strength.
 * Otherwise store base change (result-expected) in rating change. 
 * Computation is done by Elo's formula. Thus to get real change of rating
 * rating change is multiplied by coefficient K which depends on the current
 * rating (<2000?30:>2400?10:130-R/20). Initial rating and expected result based
 * on player strength is computed by Elo, too.
 * The final rating change is truncated to four digits after the comma. */
function getUpdatedStats($ustats,$ostats,$score)
{
	$n=$ustats['wins']+$ustats['draws']+$ustats['losses'];

	/* To update user rating of opponent is required. If none is given yet
	 * (<5 games) use initial elo formula to get a temporary value or assume
	 * 1200 if none finished yet. */
	$or=$ostats['rating'];
	if ($or==0) {
		if ($ostats['rgames']>0)
			$or=getInitRating($ostats['wins'],$ostats['draws'],
						$ostats['losses'],
						$ostats['rchange']/
							$ostats['rgames']);
		else
			$or=1200;
	}

	/* Update wins/draws/losses */
	if ($score==2)
		$ustats['wins']++;
	else if ($score==1)
		$ustats['draws']++;
	else
		$ustats['losses']++;

	/* Update rating change */
	if ($n < 5) {
		/* No rating yet so store strength of opponent */
		$ustats['rgames']++;
		$ustats['rchange']+=$or;
	} else {
		/* Update rating change according to the winning probability. */ 
		$WP=getWinProp($ustats['rating']-$or);
		$dW=0.5*$score- $WP;
		$ustats['rgames']++;
		$ustats['rchange']+=$dW;
	}

	/* Update rating/get initial rating if five games are finished. 
	 * Use actual number of w+d+l for this. */
	if (($n+1)%5==0) {
		if ($n+1==5) {
			/* Get initial rating from the first five games */
			$ustats['rating']=getInitRating($ustats['wins'],
						$ustats['draws'],
						$ustats['losses'],
						$ustats['rchange']/
							$ustats['rgames']);
		} else {
			/* Update rating */
			$ustats['rating']+=round(getK($ustats['rating'])*
							$ustats['rchange']);
		}
		$ustats['rgames']=0;
		$ustats['rchange']=0;
	}

	/* Truncate rating change to four digits after comma. */
	$ustats['rchange']=sprintf('%.4f',$ustats['rchange']);

	return $ustats;
}

/* Update stats of both users according to result (w,b,-). See updateUser above
 * for details. */
function updateStats($white,$black,$result)
{
	/* Load stats. Is always successful (returns zero array if not found).
	 * Old format is also updated,see compatiblity info in io.php. */
	$wstats=ioLoadUserStats($white);
	$bstats=ioLoadUserStats($black);
  
	/* Translate result to score of White and update both players. */
	if ($result=='-') 
		$score=1;
	else if ($result=='w') 
		$score=2;
	else
		$score=0;
	$wstats_new=getUpdatedStats($wstats,$bstats,$score);
	$bstats_new=getUpdatedStats($bstats,$wstats,2-$score);
	$wstats=$wstats_new;
	$bstats=$bstats_new;
  
	/* Save changes */
	ioSaveUserStats($white,$wstats);
	ioSaveUserStats($black,$bstats);
}
?>

