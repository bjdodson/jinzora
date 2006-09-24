<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Pulls the tag data from all tracks to import into the backend
*
* @author Ross Carlson
* @since 04/12/05
* @version 04/12/05
* @param $node object The node we are viewing
*
**/
global $node;

$this->displayPageTop("", word("Reading All Tag Data"));
$this->openBlock();

echo word('Searching, please wait...') . "<br><br>";
echo '<div id="status"></div>';
?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			s = document.getElementById("status");
			-->
		</SCRIPT>
		<?php

flushdisplay();

$ctr = 0;
$tracks = $node->getSubNodes("tracks", -1);
foreach ($tracks as $track) {
	// let's pull the meta data so it gets updated
	$track->getMeta();
	$ctr++;
	if ($ctr % 10 == 0) {
?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					s.innerHTML = '<nobr><?php echo word("Analyzed"); ?>: <?php echo $ctr; ?></nobr>';					
					-->
				</SCRIPT>
				<?php

		flushdisplay();
	}
}
echo "<br><br><center>";
$this->closeButton();
$this->closeBlock();
?>
