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

$art_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR;
global $jzSERVICES;

// Check all albums.
foreach ($nodes as $node) {
	$tracks = $node->getSubNodes("tracks");
	// For each track in album.
	foreach ($tracks as $track) {
		$meta = $jzSERVICES->getTagData($track->getFilePath());
		// If we have pic data
		if ($meta['pic_data'] <> ""){
			$art = realpath( $art_dir ) . DIRECTORY_SEPARATOR ."art_" . $node->getID() . ".jpg" ;

			if($art !== false) {
				$handle = fopen($art, "wb");
				fwrite($handle,$meta['pic_data']);				
				fclose($handle);
				$node->addMainArt("data" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "art_" . $node->getID() .  ".jpg");
			}
		} 
	}
?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				c.innerHTML = '<?php echo word("Art found for"); ?>: <?php echo $node->getName();?>';					
				-->
			</SCRIPT>
			<?php

	flushdisplay();
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
