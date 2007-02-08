<html>
<?php
$display = new jzDisplay();
$display->preheader(false,'100%','left',false);

$arr = array();
function mklink($l) {
  global $arr;
  $arr['jz_level'] = distanceTo("artist");
  $arr['jz_letter'] = $l;
  return urlize($arr);
}
?>

<style type="text/css">
a {
  text-decoration:none;
  font-size:large;
}
</style>
<?php
global $jzUSER;
if (checkPermission($jzUSER,'jukebox_queue')) {
  global $jbArr;
  if (isset($jbArr[0])) {
    ?>
    <script language="javascript">
       function myPlayback(player) {
	 if (player == 'stream') {
	   other = 'jukebox';
	   url = '<?php echo urlize(array('action' => 'setplayback', 'player' => 'stream')); ?>';
	 } else {
	   other = 'stream';
	   url = '<?php echo urlize(array('action' => 'setplayback', 'player' => $jbArr[0]['description'])); ?>';
	 }
	 ajax_direct_call(url, nothing);
	 document.getElementById(player).style.textDecoration = 'underline';
	 document.getElementById(other).style.textDecoration = '';
	 parent.frames['main'].location.reload(true);

	 parent.frames['embeddedPlayer'].location = '<?php echo urlize(array("frame" => "top")); ?>';
       }
    </script> 
    <table width="80%" align="center"><tr>
    <td align="center"><a href="javascript:;" onclick="myPlayback('stream')" id="stream" <?php if (checkPlayback() != "jukebox") echo 'style="text-decoration:underline;"'; ?>>Stream</a></td>
    <td align="center"><a href="javascript:;" onclick="myPlayback('jukebox')" id="jukebox" <?php if (checkPlayback() == "jukebox") echo 'style="text-decoration:underline;"'; ?>>Jukebox</a></td>
    </tr></table>
    <?php
  }
}
?>

<!--
<table width="100%" cellpadding="3" cellspacing="0" border="0">
  <tr>
    <td align="left">
      <a href="jb" target="embeddedPlayer">Player</a>
    </td>
<?php 
global $jzUSER;
if (checkPermission($jzUSER,'jukebox_queue')) {
    echo '<td align="center">';
      } else {
    echo '<td align="left">';
      }
?>
      <a href="jb" target="embeddedPlayer">List</a>
    </td>
      <?php if (checkPermission($jzUSER,'jukebox_queue')) {
?>
    <td align="right">
      <form id="jukeboxer" method="POST">
        <input type="hidden" name="">
      </form>
      <a href="#" onclick="" target="embeddedPlayer">Jukebox</a>
    </td>
<?php } ?>
  </tr>
</table>
-->


<br/>
<table width="100%" cellpadding="3" cellspacing="0" border="0">
  <tr>
    <td class="jz_block_body" align="center">
      
       <a target="main" href="<?php echo mklink('#');?>">#</a>
       <a target="main" href="<?php echo mklink('A');?>">A</a>
       <a target="main" href="<?php echo mklink('B');?>">B</a>
       <a target="main" href="<?php echo mklink('C');?>">C</a>
       <a target="main" href="<?php echo mklink('D');?>">D</a>
       <a target="main" href="<?php echo mklink('E');?>">E</a>
       <a target="main" href="<?php echo mklink('F');?>">F</a>
       <a target="main" href="<?php echo mklink('G');?>">G</a>
       <a target="main" href="<?php echo mklink('H');?>">H</a><br/>
       <a target="main" href="<?php echo mklink('I');?>">I</a>
       <a target="main" href="<?php echo mklink('J');?>">J</a>
       <a target="main" href="<?php echo mklink('K');?>">K</a>
       <a target="main" href="<?php echo mklink('L');?>">L</a>
       <a target="main" href="<?php echo mklink('M');?>">M</a>
       <a target="main" href="<?php echo mklink('N');?>">N</a>
       <a target="main" href="<?php echo mklink('O');?>">O</a>
       <a target="main" href="<?php echo mklink('P');?>">P</a>
       <a target="main" href="<?php echo mklink('Q');?>">Q</a>
       <a target="main" href="<?php echo mklink('R');?>">R</a><br/>
       <a target="main" href="<?php echo mklink('S');?>">S</a>
       <a target="main" href="<?php echo mklink('T');?>">T</a>
       <a target="main" href="<?php echo mklink('U');?>">U</a>
       <a target="main" href="<?php echo mklink('V');?>">V</a>
       <a target="main" href="<?php echo mklink('W');?>">W</a>
       <a target="main" href="<?php echo mklink('X');?>">X</a>
       <a target="main" href="<?php echo mklink('Y');?>">Y</a>
       <a target="main" href="<?php echo mklink('Z');?>">Z</a><br/>
       <a target="main" href="<?php echo mklink('*');?>">All Artists</a>&nbsp;&nbsp;&nbsp;&nbsp;
       <a target="main" href="<?php echo urlize(array('jz_level'=>distanceTo('artist'),'jz_charts'=>'true'));?>">Charts</a>
     </td>
   </tr>
 </table>
</body>
</html>