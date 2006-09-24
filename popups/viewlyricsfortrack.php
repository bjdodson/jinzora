<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');
	/**
	* Pulls the lyrics from a track and displays just them
	*
	* @author Ross Carlson
	* @since 04/08/05
	* @version 04/08/05
	* @param $node object The node we are viewing
	*
	**/
	global $node;
		$track = new jzMediaTrack($node->getPath('String'));		
		$meta = $track->getMeta();
	
		$this->displayPageTop("",word("Lyrics for:"). " ". $meta['title']);
		$this->openBlock();
		
		echo nl2br($meta['lyrics']);
		
		echo '<br><br><center>';
		$this->closeButton();
		
		$this->closeBlock();
	
?>
