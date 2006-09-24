<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Pulls art from the ID3 tags and adds it to the backend
*
* @author Ross Carlson
* @since 04/01/05
* @version 04/01/05
*
**/
global $node;

$this->displayPageTop("", word("Pull art from Tag Data"));
$this->openBlock();

// Now let's setup our display elements
echo word("Searching...") . '<br><br>';
echo '<div id="current"></div>';
?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			c = document.getElementById("current");
			-->
		</SCRIPT>
		<?php


// Ok, let's get ALL the tracks and look at each node and see if we can get art for it
// and see if we can get art for them
$nodes = $node->getSubNodes("nodes", -1);

foreach ($nodes as $node) {
	// Ok, let's see if we can get art for this node
	if ($node->getMainArt() <> "") {
		// Now let's add art for this node
		$node->addMainArt($node->getMainArt());
?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					c.innerHTML = '<?php echo word("Art found for"); ?>: <?php echo $node->getName(); ?>';					
					-->
				</SCRIPT>
				<?php

		flushdisplay();
	}
}
?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			c.innerHTML = '<?php echo word("Complete!"); ?>';					
			-->
		</SCRIPT>
		<?php

echo "<br><br><center>";
$this->closeButton(true);
$this->closeBlock();
?>
