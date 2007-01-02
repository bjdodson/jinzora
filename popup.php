<?php define('JZ_SECURE_ACCESS','true');
	/** 
	* - JINZORA | Web-based Media Streamer -  
	* 
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL.
	* 
	* - Resources -
	* - Jinzora Author: Ross Carlson <ross@jasbone.com>
	* - Web: http://www.jinzora.org
	* - Documentation: http://www.jinzora.org/docs	
	* - Support: http://www.jinzora.org/forum
	* - Downloads: http://www.jinzora.org/downloads
	* - License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* - Contributors -
	* Please see http://www.jinzora.org/team.html
	* 
	* - Code Purpose -
	* - This page contains a number of tools that are used and displayed in popup boxes
	*
	* @since 02.17.04 
	* @author Ross Carlson <ross@jinzora.org>
	*/
	$include_path = '';
	include_once('jzBackend.php');
	$_GET = unurlize($_GET);
	$_POST = unpostize($_POST);
	$node = new jzMediaNode($_GET['jz_path']);
	$popup = new jzPopup();
	$popup->popupSwitch($_GET['ptype'],$node);
	exit();

	
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	//
	//             This section processes any form posts that may hit this page
	//
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	class jzPopup {	
	
  /* Constructor
   *
   **/
  function jzPopup() {
    global $jzUSER;

    // Now let's se if they selected a Genre, Artist, or Album:
    if (isset($_POST['chosenPath'])) {
      if (isset($_POST['jz_type']) && $_POST['jz_type'] == "track") {
				if (checkPermission($jzUSER,'play',$_POST['chosenPath']) === false) {
					$this->closeWindow(false);
				}
				$e = new jzMediaTrack($_POST['chosenPath']);
				$pl = new jzPlaylist();
				$pl->add($e);
				$pl->play();
				exit();
      } else {
				$return = $this->returnGoBackPage($_POST['return']);
      }
      
      //$url = $return. "&" . jz_encode("path") . "=". jz_encode(urlencode($_POST['chosenPath']));
      $link = array();
      $link['jz_path'] = $_POST['chosenPath'];
      
      // Now let's fix that if we need to
      
      // Ok, now that we've got the URL let's refresh the parent and close this window
      echo '<body onload="opener.location.href=\''. urlize($link) . '\';window.close();">';
      exit();
    }
  }

  /* The switch that controls the popup type.
   *
   * @author Ben Dodson
   * @version 1/28/05
   * @since 1/28/05
   *
   **/
  function popupSwitch($type,$node) {
  	if (false !== stristr($type,'..') || false !== stristr($type,'/') || false !== stristr($type,'\\')) {
  		die('security breach detected in popupswitch');
  	}
  	if (file_exists(($file = dirname(__FILE__).'/popups/'.$type.'.php'))) {
  		include($file);
  		return;	
  	}
  	die('invalid popup: ' . $type);
  }
  
  
	/**
	* Displays a close window input button
	*
	* @author Ross Carlson
	* @since 03/07/05
	* @version 03/07/05
	* @param $reload Should we reload the parent on click (default to false)
	*
	**/
	function closeButton($reload = false){
		echo '<input type="submit" value="'. word('Close'). '" name="close" onClick="window.close();';
		if ($reload){
			echo 'opener.location.reload(true);';
		}
		echo '" class="jz_submit">';
	}
	
	
  /**
   * This is a 'smart' function that displays the user 
   * information about a piece of media.
   *
   * @author Ben Dodson
   * @since 7/6/05
   * @version 7/6/05
   **/
  function itemInformation($item) {
    if ($item->getType() == "jzMediaNode" || $item->getType() == "jzMediaTrack") {
      if ($item->isLeaf()) {
		$this->displayTrackInfo($item);
      } else { // node
		if (isNothing($item->getDescription())) {
		  $this->displayNodeStats($item);
		} else {
		  $this->displayReadMore($item);
		}
      }
    }
  }
	
	
 	/**
	* Displays the top of the page for the popup window
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	* @param $bg_color a hex value for the background color, IF we want one
	* @param $headerTitle The title for the page
	*/
	function displayPageTop($bg_color = "", $headerTitle = "", $js = true){
		global $row_colors, $web_root, $root_dir, $skin, $cms_mode, $cms_type, $cur_theme, $css;
		$display = new jzDisplay();
		//handleSetTheme();
		// AJAX:
		$display->handleAJAX();

		// Let's include the javascript
		if ($js){
			echo '<script type="text/javascript" src="'. $root_dir. '/lib/jinzora.js"></script>'. "\n";
			echo '<script type="text/javascript" src="'. $root_dir. '/lib/overlib.js"></script>';
			echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>';
		}
		
		// Let's start our page
		echo '<title>Jinzora</title>'. "\n";
		
		// Let's output the Jinzora style sheet
		//include_once($css);
		echo '<link rel="stylesheet" title="'. $skin. '" type="text/css" media="screen" href="'. $css. '">'. "\n";

		// Now let's see if they wanted a different background color
		if ($bg_color <> ""){
			echo '<span style="font-size:0px">.</span><body marginwidth=0 marginheight=0 style="margin: 0px" style="background-color:'. $bg_color. '">'. "\n";
		}
			 
		// Now let's output the CMS style sheet, if necessary
		if ($cms_mode <> "false"){
			switch ($cms_type){
				case "postnuke" :
				case "phpnuke" :
				case "cpgnuke" :
				case "mdpro" :
					echo '<LINK REL="StyleSheet" HREF="'. $_SESSION['cms-style']. '" TYPE="text/css">';
					
					// Now let's get the data we need from the session var
					$cArr = explode("|",urldecode($_SESSION['cms-theme-data']));
					echo "<style type=\"text/css\">" .
						 ".jz_row1 { background-color:". $cArr[0]. "; }".
						 ".jz_row2 { background-color:". $cArr[1]. "; }".
						 ".and_head1 { background-color:". $cArr[0]. "; }".
						 ".and_head2 { background-color:". $cArr[1]. "; }".
						 "</style>";
				break;
				case "mambo" :
					echo '<LINK REL="StyleSheet" HREF="'. $_SESSION['cms-style']. '" TYPE="text/css">'. "\n";
					$row_colors = array('sectiontableentry2','tabheading');
				break;
			}
		}
		if (stristr($skin,"/")){
			$img_path = $root_dir. "/". $skin;
		} else {
			$img_path = $root_dir. "/style/". $skin;
		}

		// Now let's show the page title
		if ($headerTitle <> ""){
			?>
			<table width="100%" cellpadding="3" cellspacing="0" border="0"><tr><td>
			<table width="100%" cellpadding="3" cellspacing="0" border="0">
				<tr>
					<td width="6" height="6" style="background: url(<?php echo $img_path; ?>/inner-block-top-left.gif); background-repeat:no-repeat"></td>
					<td width="99%" height="6" style="background: url(<?php echo $img_path; ?>/inner-block-top-middle.gif);"></td>
					<td width="6" height="6" style="background: url(<?php echo $img_path; ?>/inner-block-top-right.gif); background-repeat:no-repeat"></td>
				</tr>
				<tr>
					<td width="6" style="background: url(<?php echo $img_path; ?>/inner-block-left.gif); background-repeat:repeat"></td>
					<td width="99%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="100%">
									<font size="1" color="<?php echo jz_font_color; ?>">
										<strong><?php echo $headerTitle; ?></strong>
									</font>
								</td>
								<td align="right"><a href="javascript:window.close();"><?php echo word('Close'); ?></a></td>
							</tr>
						</table>
					</td>
					<td width="6" style="background: url(<?php echo $img_path; ?>/inner-block-right.gif); background-repeat:repeat"></td>
				</tr>
				<tr>
					<td width="6" height="6" style="background: url(<?php echo $img_path; ?>/inner-block-bottom-left.gif); background-repeat:no-repeat"></td>
					<td width="99%" height="6" style="background: url(<?php echo $img_path; ?>/inner-block-bottom-middle.gif);"></td>
					<td width="6" height="6" style="background: url(<?php echo $img_path; ?>/inner-block-bottom-right.gif); background-repeat:no-repeat"></td>
				</tr>
			</table>
			</td></tr></table>
			<?php
		}
		flushDisplay();
	}
	
	/**
	* Opens a block to have rounded corners
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	*/
	function openBlock(){
		global $root_dir;
		?>
		<table width="100%" cellpadding="5" cellspacing="0" border="0"><tr><td>
		<?php
	}
	
	/**
	* Closes a block to have rounded corners
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	*/
	function closeBlock(){
		echo "</td></tr></table>". "\n";
		flushdisplay();
	}
	
	
	/**
	* Closes the popup window for us
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	* @param $parent_reload Should we refresh the calling page (defaults to true)?
	*/
	function closeWindow($parent_reload = true){	
		if ($parent_reload){
			?>
			<script language="javascript">
			opener.location.reload(true);
			window.close();
			-->
			</SCRIPT>
			<?php
		} else {	
			?>
			<script language="javascript">
			window.close();
			-->
			</SCRIPT>
			<?php
		}
	}	

	function readAllDirs2($dirName, &$readCtr){
		global $audio_types, $video_types;

		// Let's up the max_execution_time
		ini_set('max_execution_time','6000');
		// Let's look at the directory we are in		
		if (is_dir($dirName)){
			$d = dir($dirName);
			if (is_object($d)){
				while($entry = $d->read()) {
					// Let's make sure we are seeing real directories
					if ($entry == "." || $entry == "..") { continue;}
					if ($readCtr % 100 == 0){ 
						?>
						<script language="javascript">
							p.innerHTML = '<b><?php echo $readCtr. " ". word("files analyzed"); ?></b>';									
							-->
						</SCRIPT>
						<?php 
						@flush(); @ob_flush();
					}
					// Now let's see if we are looking at a directory or not
					if (filetype($dirName. "/". $entry) <> "file"){
						// Ok, that was a dir, so let's move to the next directory down
						readAllDirs2($dirName. "/". $entry, $readCtr);
					} else {
						if (preg_match("/\.($audio_types|$video_types)$/i", $entry)){
							$readCtr++;
							$_SESSION['jz_full_counter']++;
						}							
					}			
				}
				// Now let's close the directory
				$d->close();
			}
		}		
	}

	/**
	* Searches for meta data of the given node but shows the results step by step
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	* @param $node The node we are looking at
	*/
	function stepMetaSearch($node){
		global $jzSERVICES, $row_colors, $allow_id3_modify, $include_path, $allow_filesystem_modify; 
		
		echo '<div id="artist"></div>';
		echo '<div id="arStatus"></div>';
		echo '<div id="count"></div>';
		echo '<div id="art"></div>';
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ar = document.getElementById("artist");
			ars = document.getElementById("arStatus");
			c = document.getElementById("count");
			i = document.getElementById("art");
			-->
		</SCRIPT>
		<?php
		
		flushdisplay();
		// Now let's search, first we need to get all the nodes from here down
		$nodes = $node->getSubNodes("nodes",-1);
		
		// Now let's add the node for what we are viewing
		$nodes = array_merge(array($node),$nodes);
		$total = count($nodes);$c=0;$start=time();
		
		foreach($nodes as $item){
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				ar.innerHTML = '<nobr><?php echo word("Item"); ?>: <?php echo $item->getName(); ?></nobr>';					
				ars.innerHTML = '<?php echo word("Status: Searching..."); ?>';
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			
			// Is this an artist?
			if ($item->getPType() == 'artist'){
				
			}
			// Is this an album?
			if ($item->getPType() == 'album'){
				// Now let's loop all the services
				$sArr = array("jinzora", "yahoo","rs","musicbrainz","google");
				foreach ($sArr as $service){
					?>
					<SCRIPT><!--\
						ars.innerHTML = '<?php echo word("Searching"). ": ". $service; ?>'					
						-->
					</SCRIPT>
					<?php
					flushdisplay();
					include_once($include_path. "services/services/metadata/". $service. ".php");
					$func = "SERVICE_GETALBUMMETADATA_". $service;
					$itemData = $func($item, false, "array");
					
					if ($itemData['image'] <> ""){
						echo '<table width="100%" cellpadding="3" cellspacing="0" border="0"><tr><td>';
								
						echo '<img width="75" align="left" src="'. $itemData['image']. '" border="0">';
						if (!isNothing($itemData['year'])){
							echo $itemData['year']. "<br>";
						}
						if (!isNothing($itemData['rating'])){
							echo $itemData['rating'];
						}
						echo $itemData['review'];
						
						echo '</td></tr><tr><td><input class="jz_submit" type="button" name="edit_download_image" value="'. word("Download"). " - ". $service. '">';
						echo "<br><br></td></tr></table>";
					}
					unset($itemData);
					flushdisplay();
				}
			}
		}
		?>
		<br>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ars.innerHTML = '&nbsp;';					
			c.innerHTML = '&nbsp;';								
			-->
		</SCRIPT>
		<?php
		echo "<br><center>";
		$this->closeButton(true);
		exit();
	}
	
	
	// This function figures out the page to return too
	// Added 4.6.04 by Ross Carlson
	// Returns the page to go back to
	function returnGoBackPage($page){
		global $row_colors, $cms_mode;
		
		// Now let's split this into an array so we can get all the paramaters
		$pageArray = explode("&",$page);
		
		// Let's split the page name from the paramaters
		$splitArray = explode("?",$pageArray[0]);		
		$pageName = $splitArray[0];

		// Now let's fix up the first one, so we'll have just the URL
		$pageArray[0] = $splitArray[1];
		for ($i=0; $i < count($pageArray); $i++){
			// now let's fix it up
			if (stristr($pageArray[$i],"path")){
				$pageArray[$i] = "";
			}
		}
		// Now let's put it back together
		$page = implode("&",$pageArray);
		
		// Now let's remove any &&
		while (stristr($page,"&&")){
			$page = str_replace("&&","",$page);
		}
		$page = $pageName . "?". $page;
		
		return $page;
	}
	
}
?>