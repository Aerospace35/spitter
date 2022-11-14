<?php 
/* Javascript functions for input mode (command assembly and highlighting). */

/* Import theme variable to Javascript. */
echo '<script language="Javascript">theme="'.$theme.'";</script>';
?>

<script language="Javascript">

/* Highlight source/destination tile of move. If cmd is null or empty just
 * clear highlighting. 
 * XXX this by-passes style sheet classes and sets background images 
 * directly. So this will not work if a theme uses image names other than
 * the defaults. */
var moveIdx=new Array(-1,-1); // [0] is Src, [1] is Dst
var lastMoveIsHighlighted = 0;
function highlightMove(cmd)
{
	/* Clear old highlighting */
	for (i=0;i<2;i++)
		if (moveIdx[i]!=-1) {
			x=moveIdx[i]%8;
			y=parseInt(moveIdx[i]/8);
			if ((y+1+x)%2==0)
				img="wsquare.jpg";
			else
				img="bsquare.jpg";
			obj=window.document.getElementById("btd"+moveIdx[i]);
			if (obj)
				obj.style.backgroundImage="url(images/"+theme+"/"+img+")";
			moveIdx[i]=-1;
		}
	
	/* If command is empty don't highlight again */
	if (cmd==null || cmd=="")
		return;
		
	/* see render.php: on entering chessboard last move of opponent is 
	 * highlighted for some time; a new selection will 'overwrite' this
	 * highlighting so clear flag to prevent timer from clearing a new
	 * selection. */
	lastMoveIsHighlighted = 0;

	/* Parse command for source/destination and highlight it */
	moveIdx[0]=(cmd.charCodeAt(2)-49)*8+(cmd.charCodeAt(1)-97);
	if (cmd.length>=6)
		moveIdx[1]=(cmd.charCodeAt(5)-49)*8+(cmd.charCodeAt(4)-97);
	else
		moveIdx[1]=-1;

	/* Set new highlighting */
	for (i=0;i<2;i++)
		if (moveIdx[i]!=-1) {
			x=moveIdx[i]%8;
			y=parseInt(moveIdx[i]/8);
			if ((y+1+x)%2==0)
				img="whsquare.jpg";
			else
				img="bhsquare.jpg";
			obj=window.document.getElementById("btd"+moveIdx[i]);
			if (obj)
				obj.style.backgroundImage="url(images/"+theme+"/"+img+")";
		}
}

/** When board is rendered last move of opponent is highlighted, this function
 * is called to clear highlight after some time; if player started to enter a
 * move before timer is called, flag will be cleared in highlightMove(). */
function clearLastMoveHighlight()
{
	if (lastMoveIsHighlighted) {
		highlightMove(null);
		lastMoveIsHighlighted = 0;
	}
}

function checkMoveButton()
{
	if (window.document.commandForm && 
					window.document.commandForm.moveButton) {
		if (window.document.commandForm.move.value.length >= 6)
			window.document.commandForm.moveButton.disabled=false;
		else
			window.document.commandForm.moveButton.disabled=true;
	}
}

/* Assemble command into commandForm.move and submit move if destination is
 * clicked twice. */
function assembleCmd(part)
{
	var cmd = window.document.commandForm.move.value;
	if (cmd == part)
		window.document.commandForm.move.value = "";
	else if (cmd.length == 0 || cmd.length >= 6) {
		if (part.charAt(0) != '-' && part.charAt(0) != 'x')
			window.document.commandForm.move.value = part;
		else if (cmd.length >= 6 && cmd.substring(3,6)==part) {
			if (confirm("Execute move "+cmd+"?"))
				onClickMove();
		}
	} else if (part.charAt(0) == '-' || part.charAt(0) == 'x')
		window.document.commandForm.move.value = cmd + part;
	else
		window.document.commandForm.move.value = part;
	highlightMove(window.document.commandForm.move.value);
	checkMoveButton();
	return false;
}

function confirmUndo()
{
	if (confirm("Are you sure you want to undo your last move?"))
		return true; 
	else
		return false;
}

function confirmAbort()
{
	if (confirm("Are you sure you want to abort this game?"))
		return true; 
	else
		return false;
}

function gatherCommandFormData() 
{
	fm=document.commandForm;
	if (document.commentForm && document.commentForm.comment)
		fm.comment.value=document.commentForm.comment.value;
	if (document.pnotesForm && document.pnotesForm.privnotes)
		fm.privnotes.value=document.pnotesForm.privnotes.value;
	else
		fm.privnotes.disabled=true;
}


function onClickMove()
{
	if (document.commandForm.move.value!="") {
		var move=document.commandForm.move.value;
		/* If pawn enters last line ask for promotion */
		if (move[0]=='P' && (move[5]=='8' || move[5]=='1')) {
			if (confirm('Promote to Queen? (Press Cancel for other options)'))
				move=move+'Q';
			else if (confirm('Promote to Rook? (Press Cancel for other options)'))
				move=move+'R';
			else if (confirm('Promote to Bishop? (Press Cancel for other options)'))
				move=move+'B';
			else if (confirm('Promote to Knight? (Press Cancel to abort move)'))
				move=move+'N';
			else
				return;
		}
		document.commandForm.cmd.value=move;
		gatherCommandFormData();
		document.commandForm.submit();
	}
}

function onClickOfferDraw()
{
	if (confirm("Are you sure you want to offer a draw?")) {
		document.commandForm.move.value="draw?";
		onClickMove();
	} 
}

function onClickAbortGame()
{
	if (confirm("Are you sure you want to abort this game?")) {
		document.commandForm.move.value="abort";
		onClickMove();
	}
}

function onClickResign()
{
	if (confirm("Are you sure you want to resign?")) {
		document.commandForm.move.value="resign";
		onClickMove();
	}
}


function onClickAcceptDraw()
{
	if (confirm("Are you sure you want to accept the draw?")) {
		document.commandForm.cmd.value="acceptdraw";
		gatherCommandFormData();
		document.commandForm.submit();
	}
}

function onClickRefuseDraw()
{
	if (confirm("Are you sure you want to refuse the draw?")) {
		document.commandForm.cmd.value="refusedraw";
		gatherCommandFormData();
		document.commandForm.submit();
	}
}

</script>
