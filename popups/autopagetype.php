<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Runs through all nodes and automatically sets the page type on them
* This goes from the bottom and recursive up...
*
* @author Ross Carlson
* @since 04/01/05
* @version 04/01/05
*
**/

global $jzUSER, $node;

if (!checkPermission($jzUSER, "admin", $node->getPath("String"))) {
	echo word("Insufficient permissions.");
	return;
}

$this->displayPageTop("", word("Auto setting page types"));
$this->openBlock();

// Now let's setup our display elements
echo word("Analysing...") . '<br><br>';
echo '<div id="artist"></div>';
echo '<div id="album"></div>';
?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ar = document.getElementById("artist");
			a = document.getElementById("album");
			-->
		</SCRIPT>
		<?php

flushdisplay();

$nodes = $node->getSubNodes("nodes", -1);
foreach ($nodes as $node) {
	// If there are NO subnodes let's assume that it's an album
	$snodes = $node->getSubNodes("nodes");
	if (count($snodes) == 0) {
		// Now let's get it's parent, it must be an artist
		$parent = $node->getParent();
		$parent->setPType('artist');
?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ar.innerHTML = '<?php echo word("Artist"); ?>: <?php echo $parent->getName(); ?>';					
					-->
				</SCRIPT>
				<?php

		flushdisplay();
?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					a.innerHTML = '<?php echo word("Album"); ?>: <?php echo $node->getName(); ?>';					
					-->
				</SCRIPT>
				<?php

		flushdisplay();
		$node->setPType('album');
	}
}
?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ar.innerHTML = '<?php echo word("Complete!"); ?>';					
			a.innerHTML = '&nbsp;';					
			-->
		</SCRIPT>
		<?php

echo "<br><br><center>";
$this->closeButton(false);
$this->closeBlock();
?>
