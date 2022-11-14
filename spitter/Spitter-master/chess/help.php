<?php /* Help page */
include 'verifysession.php';
include 'render.php';

function anchor($name,$title)
{
  echo '<h3 id="'.$name.'">'.$title.'</h3>';
}

$links=array();
$links['Overview']='index.php';
if ($_GET['gid'])
	$links['Chessboard']='board.php?gid='.$_GET['gid'];
$links['Search']='search.php';
$links['Rankings']='rankings.php';
$links['Logout']='logout.php';
renderPageBegin('OCC - Help',helpPageTable,$links,'Help Page');
?>

<div class=helpwrapper>

<P>
<h3 id="toc">Topics:</h3>
<UL>
  <LI><A href="#overview">Overview</A></LI>
  <LI><A href="#mygames">My Games</A></LI>
  <LI><A href="#search">Search</A></LI>
  <LI><A href="#rankings">Rankings</A></LI>
  <LI><A href="#logout">Logout</A></LI>
  <LI><A href="#chessboard">Chessboard</A>
    <UL>
      <LI><A href="#move">How to Move</A></LI>
      <LI><A href="#special">Special Moves and Commands</A></LI>
      <LI><A href="#undo">How to Undo a Move</A></LI>
      <LI><A href="#notes">Private Notes</A></LI>
      <LI><A href="#browser">Browsing Mode</A></LI>
      <LI><A href="#pgn">PGN Format</A></LI>
    </UL>
  </LI>
</UL>
</P>

<?php anchor("overview","Overview");?>
<P>After successful login your are at the overview. Here you can find
a list of either your open games or search 
results. Each game has a red or green mark at the left-hand side that 
indicates whether it is your move. The games are generally sorted by date of 
last move (most recent games come first) and if your open games are displayed 
all games with a green mark are put to the beginning. To view a 
game use the <I>View</I> link on the right-hand side.</P>

<?php anchor("mygames","My Games");?>
<P>Display your open games at the overview.</P>

<?php anchor("search","Search");?>
<P>Here you can either search in open or archived games. Possible criteria are
player, player color and opponent. Search results are displayed at the 
overview. To see your open games again you must click on <I>My Games</I>.</P>

<?php anchor("rankings","Rankings");?>
<P>This page shows the user rankings. It is possible to sort either by number
of games, wins/draws/losses (wins give one, draws half a point), 
rating, provisional rating or activity. The
currently selected criteria is underlined. A criteria may be selected
by clicking on its name. The default criteria is rating. The rating is updated
every five games to stabilize rankings. However, you can find the 
modification since the last update in brackets right beside your
rating. Thus, the current rating plus this modification gives you the 
provisional rating (=rating updated after each game). You may sort by
provisional rating by clicking on <I>(Prov.)</I>. At least
five games are required to show up in the rankings.</P>
<P>The rating formula for OCC is basically the one created by Arpad
Elo with a different development coefficient <I>K</I> which is
<I>30</I> for <I>Rating<2000</I>, <I>10</I> for <I>Rating>2400</I> 
and <I>130-20*Rating</I> between <I>2000-2400</I>. The initial rating
(after five games are finished) is also computed with Arpad Elo's formula
which takes into account opponent strength.</P>
<P class="warning">The coefficient K is simplier than Elo's, which
is for 1800+ players only, anway. Otherwise the formulas are the
same. Nonetheless, compared to official stuff (e.g. from FIDE) it is 
somewhat simple and inaccurate but should suffice for nice
rankings. As the games are not played in tournament mode and thus
cannot give a truly good estimation of the player's strength there
is no need to improve the rating system to reflect any offical rules
better. So please don't ask for it.
</P>

<?php anchor("logout","Logout");?>
<P>This option is always present at the right-hand side of the navigation bar.
It logs you out from OCC and displays the login screen.</P>

<?php anchor("chessboard","Chessboard");?>
<P>The chessboard itself is displayed on the left-hand side. On the right-hand
side you can find who is playing, the move input field, the move history,
the imbalance of chessmen and the private notes dialogue. Below the chessboard
you can find the chat messages. Details about various components are explained 
below.</P>

<?php anchor("move","How to Move");?>
<P>
Click on the chessman you want to move. It will be highlighted then. Next, click 
on the destination which is highlighted, too. Now either press the move button on
the right-hand side of the board (which will be only active if chessman and destination
have been selected) or click again on the destination. For the latter, a confirm 
dialogue will pop up before the move is submitted. You can enter a comment into 
the textbox below the chessboard and private notes into the textbox at the lower 
right-hand side of the chessboard before you submit your move.
</p><P>If one of your pawns reaches the last line you will be asked to what 
chessman it should be promoted (Queen, Rook, Bishop, 
Knight).
</p><P>Whether your move is correct or not will be checked after submission.
In case of any errors an error message will be displayed and the
move command will not be executed.
</P>

<?php anchor("special","Special Moves and Commands");?>
<P>Castling is done by selecting the king first and then as destination the 
second-next tile to its left or right <B>NOT</B> by selecting the rook.
If you try this you will <U>only</U> move the rook.</P>
<P>For en-passant you will have to click the tile behind the other pawn (the 
one it just skipped).</P>
<P><B>Offer Draw</B> allows you to offer a draw which your opponent may either reject 
or accept. If he/she rejects it is again your move. Must be confirmed before 
execution.</P>
<P><B>Abort Game</B> allows you to abort a game if you did not move yet. This will
have no impact on the game statistics. Must be confirmed before 
execution.</P>
<P><B>Resign</B> allows you to resign and your opponent wins the game. Must be
confirmed before execution.</P>
<P class="warning">If four weeks have passed since the last move 
of your opponent you may abort the game. It is <B>not</B> counted as win or
finished but is simply deleted.</P>

<?php anchor("undo","How to Undo a Move");?>
<P>You may undo a successfully submitted and applied move for 20 minutes. The 
button is displayed behind the move result. A confirmation is nescessary before
undoing a move. If you opponent did move already in the meantime the undo will
have no effect. After 20 minutes it is no longer possible to undo your move.</P>
<P class="warning">If a move finishes a game it may not be undone to 
prevent anyone from tampering with the statistics by winning over and over 
again.</P>

<?php anchor("notes","Private Notes");?>
<P>The memo box on the lower right-hand side of the chessboard allows you to 
keep some private notes about the game (to remember possible dangers and such 
when logging in again later). These notes are encrypted and only you can read 
them. Use the disc icon to save private notes disregarding of whether you can
or want to submit a move. If you submit a move private notes will be updated,
too.</P>
<P class="warning">Private notes are separated user-wise. So in case you 
play multiple games against the same user the same notes will be displayed
in all games.</P>

<?php anchor("browser","Browsing Mode");?>
<P>In browsing mode you may browse through the move history. In this case
not only the last few moves but all are displayed and this time as links to
directly go to any move in the game. Private notes and chatter is not 
displayed at all. You may also rotate the board to get the other player's
view.</P>

<?php anchor("pgn","PGN Format");?>
<P>This displays the current game in the wide-spread PGN
format. You can copy and paste the text into a file with
the extension .pgn and view it with any chess software, e.g.
Fritz. This way you can analyze games, play variants or comment
the quality of certain moves.</P>

</DIV>

<P>[ <A href="#toc">Top</A> ]</P>

<?php
renderPageEnd(null);
?>
