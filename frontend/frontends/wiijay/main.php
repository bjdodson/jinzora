<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">
<?php
$fename = $this->name;
?>
<HTML>
	<FRAMESET rows="30,*" frameborder=no framespacing=0 border=0 marginwidth="0" marginheight="0">
		<FRAME name="header" src="<?php echo urlize(array("view"=>$fename,"frame"=>'titlebar'));?>" marginwidth="0" marginheight="0" noresize scrolling="no">
		<FRAMESET cols="260,*" frameborder=no framespacing=0 border=0 marginwidth="0" marginheight="0">
			<FRAMESET rows="280,*">
				<FRAME name="embeddedPlayer" src="<?php echo urlize(array("view"=>$fename,"frame"=>'top'));?>" marginwidth="0" marginheight="0" noresize scrolling="no">
				<FRAME name="bottomframe" src="<?php echo urlize(array("view"=>$fename,"frame"=>'bottom'));?>" marginwidth="0" marginheight="0" noresize scrolling="no">
			</FRAMESET>
			<FRAME name="main" src="<?php echo urlize(array("view"=>$fename));?>" frameborder="0">
		</FRAMESET>
	</FRAMESET>
</HTML>