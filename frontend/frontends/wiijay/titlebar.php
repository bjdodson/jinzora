<?php 
global $fe; 
$display = new jzDisplay();
$display->preheader(false,'100%','left',false);
?>
<style>
a img {
  border:none;
}
</style>
<center><a href="<?php echo htmlentities(urlize()); ?>" target="main"><img src="frontend/frontends/<?php echo $fe->name; ?>/img/header.gif"></a></center>
</body>
</html>