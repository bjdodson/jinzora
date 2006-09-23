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
  	
    switch ($type) {
    case "genre":
      $this->displayAllGenre();
      break;
    case "artist":
      $this->displayAllArtists();
      break;
    case "album":
      $this->displayAllAlbums();
      break;
    case "track":
      $this->displayAllTrack();
      break;
    case "mozilla":
      $this->displayinstMozPlug();
      break;
    case "scanformedia":
      $this->scanForMedia($node);
      break;
    case "readmore":
      $this->displayReadMore($node);
      break;
    case "addfeatured":
      $this->addToFeatured($node);
      break;	
    case "removefeatured":
      $this->removeFeatured($node);
      break;
    case "rateitem":
      $this->userRateItem($node);
      break;
    case "docs":
      $this->showDocs();
      break;
    case "topstuff":
      $this->displayTopStuff($node);
      break;
    case "preferences":
      $this->userPreferences();
      break;
    case "nodestats":
      $this->displayNodeStats($node);
      break;
    case "dupfinder":
      $this->displayDupFinder();
      break;
    case "sitenews":
      $this->displaySiteNews($node);
      break;
    case "jukezora":
      $this->displayJukezora();
      break;
    case "addlinktrack":
      $this->displayAddLinkTrack($node);
      break;
    case "setptype":
      $this->displaySetPType($node);
      break;
    case "uploadmedia":
      $this->displayUploadMedia($node);
      break;
    case "showuploadstatus":
      $this->displayUploadStatus();
      break;
    case "sitesettings":
      $this->displaySiteSettings();
      break;
	case "popplayer":
      $this->displayPopPlayer();
      break;
	case "autorenumber":
	  $this->displayRenumber($node);
      break;	
	case "getalbumart":
	  $this->displayGetAlbumArt($node);
      break;
	case "discussitem":
	  $this->displayDiscussion($node);
      break;
	case "requestmanager":
	  $this->displayRequestManager($node);
      break; 
	case "autopagetype":
	  $this->displayAutoPageType($node);
      break; 
	case "resizeart":
	  $this->displayArtResize($node);
      break; 
	case "viewlyricsfortrack":
	  $this->displayViewLyricsForTrack($node);
      break; 
    case "viewcurrentinfo":
      $this->viewCurrentlyPlaying($_GET['session']);
      break;
	case "artfromtags":
	  $this->displayArtFromTags($node);
	  break;
	case "importtagdata":
	  $this->displayTagDataImporter($node);
	  break;
	case "downloadtranscodedbundle":
	  $this->sendTranscodedBundle($node);
	  break;
	case "burncd":
	  $this->displayBurnCD($node);
	  break;
	case "admintools":
		$this->displayAdminTools($_GET['jz_path']);
		break;		
	case "addtofavorites":
		$this->displayAddToFavorites($_GET['jz_path']);
		break;
	case "cachemanager":
		$this->displayCacheManager($_GET['jz_path']);
		break;	
	case "wmptrack":
		$this->displayWMPTrack();
		break;
	case "purgeShoutbox":
		$this->displayPurgeShoutbox();
		break;
	
	default:
      echo word('error: invalid ptype for popup.');
      break;
    }
  }
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	//
	//             This section contains all the functions
	//
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	
	
	
	
	/**
	* Displays the tool to purge the shoutbox
	*
	* @author Ross Carlson
	* @since 8.9.06
	* @version 8.9.06
	**/
	function displayPurgeShoutbox(){
		global $root_dir, $web_root;
		
		// Let's start the page header
		$this->displayPageTop("",word("Purge Shoutbox"));
		$this->openBlock();
		
		// Let's kill the file
		@unlink($web_root. $root_dir. "/data/yshout/logs/main.txt");
		
		echo "<center>";
		echo "<br>". word("Shoutbox Data Purged"). "!<br><br><br>";
		$this->closeButton(true);
		echo "</center>";
		
		$this->closeBlock();
	}
	
	/**
	* Displays the track details for the currently playing track in WMP
	*
	* @author Ross Carlson
	* @since 8.3.06
	* @version 8.3.06
	**/
	function displayWMPTrack(){
		global $this_site, $root_dir, $jzSERVICES, $web_root;
		
		// Let's setup our display object
		$display = new jzDisplay();
	
		// Let's create the track object
		$track = new jzMediaTrack($_GET['jz_path']);
		$meta = $track->getMeta();
		
		// Now let's get the album and artist
		$album = $track->getNaturalParent("album");
		$artist = $album->getNaturalParent("artist");
		$desc = $album->getDescription();
		while (substr($desc,0,4) == "<br>" or substr($desc,0,6) == "<br />"){
			if (substr($desc,0,4) == "<br>"){
				$desc = substr($desc,5);
			}
			if (substr($desc,0,6) == "<br />"){
				$desc = substr($desc,7);
			}
		}
		
		// Now let's get the art
		$art = $album->getMainArt("200x200");
		if ($art <> ""){
			$albumArt = $display->returnImage($art,$album->getName(),150,150,"limit",false,false,"left","3","3");
		} else {
			$art = $jzSERVICES->createImage($web_root. $root_dir. '/style/images/default.jpg', "200x200", $track->getName(), "audio", "true");
			$albumArt = '<img src="'. $this_site. $root_dir. "/". $art. '" border="0" align="left" hspace="3" vspace="3">';
		}
		
		// Now let's setup Smarty
		$smarty = smartySetup();
		
		// Let's setup the Smarty variables
		$smarty->assign('trackName', $track->getName());
		$smarty->assign('albumName', $album->getName());
		$smarty->assign('artistName', $artist->getName());
		$smarty->assign('albumArt', $albumArt);
		$smarty->assign('lyrics', $meta['lyrics']);
		$smarty->assign('trackNum', $meta['number']);
		$smarty->assign('albumDesc', $desc);
		$smarty->assign('totalTracks', $_GET['totalTracks']);
		
		// Now let's display the template
		$smarty->display(SMARTY_ROOT. 'templates/general/asx-display.tpl');
	}
	
	/**
	* Displays the cache management tools
	*
	* @author Ross Carlson
	* @since 2.22.06
	* @version 2.22.06
	* @param $path The node that we are viewing
	**/
	function displayCacheManager($path){
		global $web_root, $root_dir;
		
		// Let's start the page header
		$this->displayPageTop("",word("Cache Manager"));
		$this->openBlock();
		
		// Let's create the node
		$node = new jzMediaNode($path);
		
		// Did they want to do something?
		if (isset($_GET['subpage'])){
			switch ($_GET['subpage']){
				case "deleteall":
					$i=0;
					$d = dir($web_root. $root_dir. "/temp/cache");
					while ($entry = $d->read()) {
						if ($entry == "." || $entry == "..") {
							continue;
						}
						if (@unlink($web_root. $root_dir. "/temp/cache/". $entry)){
							$i++;
						}
					}
					echo word('%s cache files deleted.', $i);
				break;
				case "thisnode":
					$display = new jzDisplay();
					$display->purgeCachedPage($node);
					$nodes = $node->getSubNodes("nodes", -1);
					$i=1;
					foreach ($nodes as $item){
						$display->purgeCachedPage($item);
						$i++;
					}
					echo word("%s nodes purged", $i);
				break;
				case "viewsize":
					$d = dir($web_root. $root_dir. "/temp/cache");
					$size=0;
					while ($entry = $d->read()) {
						$size = $size + filesize($web_root. $root_dir. "/temp/cache/". $entry);
					}
					echo word("Total cache size: %s MB", round((($size / 1024) / 1024),2));
				break;
			}
			echo "<br><br>";
		}
		
		
		$url_array = array();
		$url_array['jz_path'] = $node->getPath("String");
		$url_array['action'] = "popup";
		$url_array['ptype'] = "cachemanager";  
		
		$url_array['subpage'] = "deleteall";  
		echo '<a href="'. urlize($url_array). '">'. word("Purge ALL caches"). '</a><br>';
		
		$url_array['subpage'] = "thisnode";  
		echo '<a href="'. urlize($url_array). '">'. word("Purge Cache for"). ": ". $node->getName(). '</a><br>';
		
		$url_array['subpage'] = "viewsize";  
		echo '<a href="'. urlize($url_array). '">'. word("View Cache Size"). '</a><br><br>';		

		$this->closeButton();
		$this->closeBlock();	
	}
	
	
	/**
	* Displays the quick box to add an item to favorites
	*
	* @author Ross Carlson
	* @since 12.17.05
	* @version 12.17.05
	* @param $path The node that we are viewing
	**/
	function displayAddToFavorites($path){
		global $include_path, $jzUSER;
		
		$node = new jzMediaNode($path);
		$display = new jzDisplay();
		$be = new jzBackend();
		
		// Let's start the page header
		$this->displayPageTop("",word("Adding to Favorites"));
		$this->openBlock();
		echo word("Adding"). ": ". $node->getName();
		
		// Now let's add it
		
		
		$this->closeBlock();		
	}
	
	/**
	* Displays the Admin Tools Section
	*
	* @author Ross Carlson
	* @since 11/28/05
	* @version 11/28/05
	* @param $node The node that we are viewing
	**/
	function displayAdminTools($path){
		global $include_path, $jzUSER, $allow_filesystem_modify, $enable_podcast_subscribe;
		
		$node = new jzMediaNode($path);		
		$display = new jzDisplay();
		
		// Let's start the page header
		$this->displayPageTop("",word("Admin Tools"));
		$this->openBlock();
		
		if ($jzUSER->getSetting('admin') <> true){
			echo "<br><br><br><center>PERMISSION DENIED!!!";
			$this->closeBlock();		
		}
		
		// Let's start our tabs
		$display->displayTabs(array("Media Management","Meta Data","System Tools"));
		
		// Let's setup our links
		$url_array = array();
		$url_array['jz_path'] = $node->getPath("String");
		$url_array['action'] = "popup";
		
		// Now let's build an array of all the values for below
		if (checkPermission($jzUSER,"upload",$node->getPath("String")) and $allow_filesystem_modify == "true") {
			$url_array['ptype'] = "uploadmedia";  
			$valArr[] = '<a href="'. urlize($url_array). '">'. word("Add Media"). '</a>'; 
		}
		$url_array['ptype'] = "addlinktrack";  
		$valArr[] = '<a href="'. urlize($url_array). '">'. word("Add Link Track"). '</a>';
		
		$url_array['ptype'] = "setptype";  
		$valArr[] = '<a href="'. urlize($url_array). '">'. word("Set Page Type"). '</a>';
		
		$url_array['ptype'] = "scanformedia";  
		$valArr[] = '<a href="'. urlize($url_array). '">'. word("Rescan Media"). '</a>';
		
		$url_array['ptype'] = "artfromtags";  
		$valArr[] = '<a href="'. urlize($url_array). '">'. word("Pull art from Tag Data"). '</a>';
		
		if ($node->getPType() == "artist" or $node->getPType() == "album"){
			// Ok, is it already featured?
			if (!$node->isFeatured()){
				$url_array['ptype'] = "addfeatured"; 
				$valArr[] = '<a href="'. urlize($url_array). '">'. word("Add to Featured"). '</a>';
			} else {
				$url_array['ptype'] = "removefeatured"; 
				$valArr[] = '<a href="'. urlize($url_array). '">'. word("Remove from Featured"). '</a>';
			}
		}
		
		if ($node->getPType() == "album"){
			$url_array['ptype'] = "bulkedit"; 
			$valArr[] = '<a href="'. urlize($url_array). '">'. word("Bulk Edit"). '</a>';
			
			$url_array['ptype'] = "getalbumart"; 
			$valArr[] = '<a href="'. urlize($url_array). '">'. word("Search for Album Art"). '</a>';
			
			$url_array['ptype'] = "pdfcover"; 
			$valArr[] = '<a href="'. urlize($url_array). '">'. word("Create PDF Cover"). '</a>';
		}
		
		if ($enable_podcast_subscribe == "true"){
			$url_array['ptype'] = "addpodcast"; 
			$valArr[] = '<a href="'. urlize($url_array). '">'. word("Podcast Manager"). '</a>';
		}
		
		// Now let's put the content into the tabs
		$i=0;
		echo '<div id="panel1" class="panel"><table width="90%" cellpadding="8" cellspacing="0" border="0">';
		foreach ($valArr as $item){
			if ($i==0){
				echo "</tr><tr>";
			}
			echo "<td>";
			echo $item;
			echo "</td>";
			$i++;
			if ($i==3){$i=0;}
		}
		echo '</table></div>';
		?>
		
		<div id="panel2" class="panel">
			<table width="90%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td>
						<?php $url_array['ptype'] = "getmetadata";  echo '<a href="'. urlize($url_array). '">'. word("Retrieve Meta Data"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "searchlyrics";  echo '<a href="'. urlize($url_array). '">'. word("Retrieve Lyrics"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "resizeart";  echo '<a href="'. urlize($url_array). '">'. word("Resize All Art"). '</a>'; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php $url_array['ptype'] = "autorenumber";  echo '<a href="'. urlize($url_array). '">'. word("Auto Renumber"). '</a>'; ?>		
					</td>
					<td>
						<?php $url_array['ptype'] = "iteminfo";  echo '<a href="'. urlize($url_array). '">'. word("Item Information"). '</a>'; ?>		
					</td>
					<td>
						<?php $url_array['ptype'] = "retagger";  echo '<a href="'. urlize($url_array). '">'. word("Retag Tracks"). '</a>'; ?>		
					</td>
				</tr>
			</table>
		</div>   
		<div id="panel3" class="panel">
			<table width="90%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td>
						<?php $url_array['ptype'] = "mediamanager";  echo '<a href="'. urlize($url_array). '">'. word("Media Manager"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "usermanager";  echo '<a href="'. urlize($url_array). '">'. word("User Manager"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "sitesettings";  echo '<a href="'. urlize($url_array). '">'. word("Settings Manager"). '</a>'; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php $url_array['ptype'] = "sitenews";  echo '<a href="'. urlize($url_array). '">'. word("Manage Site News"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "nodestats"; unset($url_array['jz_path']); echo '<a href="'. urlize($url_array). '">'. word("Show Full Site Stats"). '</a>'; ?>
					</td>
					<td>
						<!--<?php $url_array['ptype'] = "dupfinder";  echo '<a href="'. urlize($url_array). '">'. word("Duplicate Finder"). '</a>'; ?>-->
					</td>
				</tr>
				<tr>
					<td>
						<?php $url_array['ptype'] = "cachemanager";  $url_array['jz_path'] = $node->getPath("String"); echo '<a href="'. urlize($url_array). '">'. word("Cache Manager"). '</a>'; ?>
					</td>
					<td>

					</td>
					<td>

					</td>
				</tr>
			</table>
		</div>   
		<?php
		$this->closeBlock();		
	}

	
	/**
	* Allows the user to burn a CD
	*
	* @author Ross Carlson
	* @since 6/20/05
	* @version 6/20/05
	* @param $node The node that we are viewing
	**/
	function displayBurnCD($node){
		global $include_path, $jzSERVICES;
		
		$this->displayPageTop("",word("Burn CD"));
		$this->openBlock();
		
		// Did they want to burn?
		if (isset($_GET['sub_action'])){
			if ($_GET['sub_action'] == "create"){
				// Ok, we need to get a list of all the tracks
				$tracks = $node->getSubNodes("tracks",-1);
				$fileArray = array();
				foreach ($tracks as $track){
					// Now we need to resample each one to a WAV file
					// First let's create the new file name - we'll make this random
					echo "Resampling: ". $track->getName(). "<br>";
					flushdisplay();
					$fileArray[] = $jzSERVICES->createResampledTrack($track->getDataPath(),"wav", "", "", getcwd(). "/data/burn/". $track->getName(). ".wav");
					flushdisplay();
				}
				
				// Now let's burn this list of files
				$album = $node->getName();
				$art = $node->getAncestor("artist");
				$artist = $art->getName();
				
				echo "<br><br>";
				$jzSERVICES->burnTracks($node, $artist, $album);
				
				exit();
			}
		}
		
		$dlarr = array();
		$dlarr['action'] = "popup";
		$dlarr['ptype'] = "burncd";
		$dlarr['sub_action'] = "create";
		$dlarr['jz_path'] = $node->getPath("string");
		
		echo '<a href="'. urlize($dlarr). '">Burn CD</a>';
		
		$this->closeBlock();
	}
	

	/**
	 * Jukezora popup.
	 *
	 * @author Ben Dodson
	 * @since 4/29/05
	 * @version 4/29/05
	 **/
     function displayJukezora() {
	   global $include_path;
	   include_once($include_path.'jukezora.php');
     }
	 
	 
	/**
	* Sends a transcoded file bundle
	*
	* @author Ross Carlson
	* @since 06/10/05
	* @version 06/10/05
	*
	**/
	function sendTranscodedBundle($node){
		global $include_path;
		
		// Now let's include the libraries
		include_once($include_path. 'lib/jzcomp.lib.php');
		
		// Now we have an array of files let's use them to create the download
		sendFileBundle(unserialize($_GET['jz_files']), $node->getName());
		
		exit();
	}
	 
	/**
	* Pulls the tag data from all tracks to import into the backend
	*
	* @author Ross Carlson
	* @since 04/12/05
	* @version 04/12/05
	* @param $node object The node we are viewing
	*
	**/
	function displayTagDataImporter($node){
	
		$this->displayPageTop("",word("Reading All Tag Data"));
		$this->openBlock();
		
		echo word('Searching, please wait...'). "<br><br>";
		echo '<div id="status"></div>';
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			s = document.getElementById("status");
			-->
		</SCRIPT>
		<?php
		flushdisplay();
		
		$ctr=0;
		$tracks = $node->getSubNodes("tracks",-1);
		foreach($tracks as $track){
			// let's pull the meta data so it gets updated
			$track->getMeta();
			$ctr++;
			if ($ctr % 10 == 0){ 
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
	}


  function viewCurrentlyPlaying($mysid) {
    global $status_blocks_refresh;
    $this->displayPageTop("",word("Current Information"));
    $this->openBlock();
    echo '<span id="currentInfo">&nbsp;</span>';
    ?>
		<script>function updateCurrentInfo(update) {
			currentInfo("<?php echo $mysid; ?>", update);
			setTimeout("updateCurrentInfo(true)",<?php echo ($status_blocks_refresh * 1000); ?>);
		}
		updateCurrentInfo(false);
    </script>
    <?php
    //echo '<br><br><center>';
    //$this->closeButton();
      
    $this->closeBlock();
    
  }

	/**
	* Pulls the lyrics from a track and displays just them
	*
	* @author Ross Carlson
	* @since 04/08/05
	* @version 04/08/05
	* @param $node object The node we are viewing
	*
	**/
	function displayViewLyricsForTrack($node){
		$track = new jzMediaTrack($node->getPath('String'));		
		$meta = $track->getMeta();
	
		$this->displayPageTop("",word("Lyrics for:"). " ". $meta['title']);
		$this->openBlock();
		
		echo nl2br($meta['lyrics']);
		
		echo '<br><br><center>';
		$this->closeButton();
		
		$this->closeBlock();
	}
	
	/**
	* Goes through each subnode, one by one, and resizes the art
	*
	* @author Ross Carlson
	* @since 04/05/05
	* @version 04/05/05
	* @param $node object The node we are viewing
	*
	**/
	function displayArtResize($node){
		
		$this->displayPageTop("",word("Resize all album art"));
		$this->openBlock();
		
		// Did they submit?
		if (isset($_POST['edit_resize_art'])){
			// Let's set the start time
			$start = time();
			
			echo word("Resizing, please wait...");
			echo "<br><br>";
			echo '<div id="artist"></div>';
			echo '<div id="album"></div>';
			echo '<div id="total"></div>';
			// Ok, now we need to recurisvely get ALL subnodes
			$i=0;
			$nodes = $node->getSubNodes("nodes", -1);
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				ar = document.getElementById("artist");
				a = document.getElementById("album");
				t = document.getElementById("total");
				-->
			</SCRIPT>
			<?php
			foreach($nodes as $node){
				if ($node->getName() <> "" and $node->getPtype() == "album"){
					$parent = $node->getParent();
					?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						ar.innerHTML = '<nobr><?php echo word("Artist"); ?>: <?php echo $parent->getName(); ?></nobr>';					
						a.innerHTML = '<nobr><?php echo word("Album"); ?>: <?php echo $node->getName(); ?></nobr>';					
						t.innerHTML = '<nobr><?php echo word("Analyzed"); ?>: <?php echo $i; ?></nobr>';					
						-->
					</SCRIPT>
					<?php
					flushdisplay();
					// Now let's look at the art for this item and resize it if needed
					// BUT we don't want to create blank ones with this tool...
					$node->getMainArt($_POST['edit_resize_dim'], false);
					$i++;
				}
			}
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				ar = document.getElementById("artist");
				a = document.getElementById("album");
				ar.innerHTML = '<nobr><?php echo word("Completed in"). " ". convertSecMins((time() - $start)). " ". word("seconds"); ?></nobr>';					
				a.innerHTML = '<nobr><?php echo word("Analyzed"); ?>: <?php echo $i; ?></nobr>';							
				t.innerHTML = '&nbsp;';							
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			echo "<br><br><center>";
			$this->closeButton();
			exit();
		}
		
		// Let's setup our form
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "resizeart";
		$arr['jz_path'] = $node->getPath('String');
		echo '<form action="'. urlize($arr). '" method="POST">';
		?>
		<?php echo word("This tool will resize all your art to the specified dimensions below.  This will not delete or remove your existing art.  This will precreate the art for tools like the random albums so that it will run faster."); ?>
		<br><br>
		<?php echo word("100x100 is used by the random album block<br>Other common values are 75x75 and 150x150"); ?>
		<br>
		<br>
		<?php echo word("Dimensions (WidthxHeight)"); ?><br><input type="text" class="jz_input" name="edit_resize_dim" value="100x100">
		<br><br>
		<input type="submit" class="jz_submit" value="<?php echo word("Resize All Art"); ?>" name="edit_resize_art">
		<?php
		$this->closeButton();
		echo '</form>';
		
		
		$this->closeBlock();
	}
	
	/**
	* Runs through all nodes and automatically sets the page type on them
	* This goes from the bottom and recursive up...
	*
	* @author Ross Carlson
	* @since 04/01/05
	* @version 04/01/05
	*
	**/
	function displayAutoPageType($node){
	  global $jzUSER;

	  if (!checkPermission($jzUSER,"admin",$node->getPath("String"))) {
	    echo word("Insufficient permissions.");
	    return;
	  }



		$this->displayPageTop("",word("Auto setting page types"));
		$this->openBlock();
		
		// Now let's setup our display elements
		echo word("Analysing..."). '<br><br>';		
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
		foreach($nodes as $node){
			// If there are NO subnodes let's assume that it's an album
			$snodes = $node->getSubNodes("nodes");
			if (count($snodes) == 0){
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
		
	}
	
	
	/**
	* Pulls art from the ID3 tags and adds it to the backend
	*
	* @author Ross Carlson
	* @since 04/01/05
	* @version 04/01/05
	*
	**/
	function displayArtFromTags($node){
	
		$this->displayPageTop("",word("Pull art from Tag Data"));
		$this->openBlock();
		
		// Now let's setup our display elements
		echo word("Searching..."). '<br><br>';		
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
		
		foreach($nodes as $node){
			// Ok, let's see if we can get art for this node
			if ($node->getMainArt() <> ""){
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
	}
	
	/**
	* Displays the request manager
	*
	* @author Ross Carlson
	* @since 03/19/05
	* @version 03/19/05
	*
	**/
	function displayRequestManager($node){
		global $jzUSER;
		
		$this->displayPageTop("",word("Request Manager"));
		$this->openBlock();
		
		// Now let's see if they wanted to add
		if (isset($_POST['edit_add'])){
			$node->addRequest($_POST['edit_request'], '', $jzUSER->getName());
		}
		// Did they want to delete
		if (isset($_POST['edit_delete'])){
			$node->removeRequest($_POST['edit_previous_requests']);
		}
		
		// Let's setup our form
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "requestmanager";
		$arr['jz_path'] = $node->getPath('String');
		echo '<form action="'. urlize($arr). '" method="POST">';
		
		?>
		<?php echo word('Enter your request below'); ?>:<br>
		<input type="text" name="edit_request" class="jz_input" size="30">
		<input type="submit" name="edit_add" class="jz_submit" value="<?php echo word('Go'); ?>">
		<br>
		<br>
		<br>
		<?php echo word('Current Requests'); ?>:<br>
		<select class="jz_select" name="edit_previous_requests" size="10" style="width:200px;">
			<?php
				$req = $node->getRequests(-1, "all");
		                rsort($req);
				for($i=0;$i<count($req);$i++){
					echo '<option value="'. $req[$i]['id']. '">'. $req[$i]['entry']. '</option>';
				}
			?>
		</select>
		<br><br>
		    <?php
			if ($jzUSER->getSetting('admin')){
			?>
			<input type="submit" name="edit_delete" class="jz_submit" value="<?php echo word('Delete'); ?>">
			<!--<input type="submit" name="edit_notify" class="jz_submit" value="<?php echo word("Notify requestor"); ?>">-->
			<?php
		}
		
		echo "</form>";
		
		$this->closeBlock();
	}
	
	/**
	* Displays the discussion page
	*
	* @author Ross Carlson
	* @since 03/07/05
	* @version 03/07/05
	* @param $node The node we are looking at
	*
	**/
	function displayDiscussion($node){
		global $jzUSER, $row_colors;
		
		// Let's setup the object		
		$item = new jzMediaElement($node->getPath('String'));		
		$track = new jzMediaTrack($node->getPath('String'));		
		
		// Let's grab the meta data from the file and display it's name
		$meta = $track->getMeta();
		
		$this->displayPageTop("","Discuss Item: ". $meta['title']);
		$this->openBlock();
		
		// Did they submit the form?
		if (isset($_POST['edit_addcomment'])){
			// Let's add it
			$item->addDiscussion($_POST['edit_newcomment'],$jzUSER->getName());
		}
		
		// Let's setup our form
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "discussitem";
		$arr['jz_path'] = $node->getPath('String');
		echo '<form action="'. urlize($arr). '" method="POST">';
		
		// Now let's setup the display
		$i=0;
		?>
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="20%" valign="top">
					<nobr>
						<?php echo word('New Comment'); ?>:
					</nobr>
				</td>
				<td width="80%" valign="top">
					<textarea name="edit_newcomment" rows="3" style="width:300px;" class="jz_input"></textarea>
					<br><br>
					<input type="submit" name="edit_addcomment" value="<?php echo word('Add Comment'); ?>" class="jz_submit">
					<br><br>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[0];?>">
				<td colspan="2" width="100%" align="center">
					<strong><?php echo word('Previous Comments'); ?></strong><br><br>
				</td>
			</tr>
			<?php
				// Now let's get the previous discussions
				$disc = $item->getDiscussion();
				if (count($disc) > 0){
					rsort($disc);
					foreach($disc as $comment){
						?>
						<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
							<td width="20%" valign="top">
								<nobr>
									<?php echo $comment['user']; ?>
								</nobr>
							</td>
							<td width="80%" valign="top">
								<?php echo $comment['comment']; ?>
							</td>
						</tr>
						<?php
					}
				}
			?>
		</table>
		</form>
		<?php
		
		
		$this->closeBlock();
	}
	
	/**
	* This tools displays a page of art so that the user can choose which one they want
	*
	* @author Ross Carlson
	* @since 03/07/05
	* @version 03/07/05
	* @param $node The node we are looking at
	*
	**/
	function displayGetAlbumArt($node){
		global $allow_filesystem_modify, $backend, $include_path;
		
		// Now let's see if they choose an image
		$i=0;
		while($i<5){
			if (isset($_POST['edit_download_'. $i])){
				// Ok, we got it, now we need to write this out
				$image = $_POST['edit_image_'. $i];
				$imageData = file_get_contents($image);
				
				// now let's set the path for the image
				if (stristr($backend,"id3") or $allow_filesystem_modify == "false"){
					$imgFile = $include_path. "data/images/". str_replace("/","--",$node->getPath("String")). "--". $node->getName(). ".jpg";
				} else {
					$imgFile = $node->getDataPath(). "/". $node->getName(). ".jpg";
				}
				
				// Now let's delete it if it already exists
				if (is_file($imgFile)){ unlink($imgFile); }
				// Now we need to see if any resized versions of it exist
				$retArray = readDirInfo($include_path. "data/images","file");
				foreach($retArray as $file){
					if (stristr($file,str_replace("/","--",$node->getPath("String")). "--". $node->getName())){	
						// Ok, let's wack it
						@unlink($include_path. "data/images/".$file);
					}
				}

				// Now let's get the data and add it to the node
				$handle = fopen($imgFile, "w");
				if (fwrite($handle,$imageData)){
					// Ok, let's write it to the backend
					$node->addMainArt($imgFile);
				}
				fclose ($handle);
				
				// now let's close out
				$this->closeWindow(true);
				exit();
			}
			$i++;
		}
	
		// Let's resize
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
			window.resizeTo(500,700)
		-->
		</SCRIPT>
		<?php	
		flushdisplay();
		
		$display = new jzDisplay();
	
		$this->displayPageTop("","Searching for art for: ". $node->getName());
		$this->openBlock();
		
		echo word('Searching, please wait...'). "<br><br>";
		flushdisplay();
		
		// Now let's display what we got
		$i=0;
		echo "<center>";
		// Let's setup our form
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "getalbumart";
		$arr['jz_path'] = $node->getPath('String');
		echo '<form action="'. urlize($arr). '" method="POST">';
		
		$i=0;
		// Ok, now let's setup a service to get the art for each of the providers
		// Now let's get a link from Amazon
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "amazon");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		
		// Now let's get a link from Rollingstone
		unset($jzService);unset($image);
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "google");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		
		// Now let's get a link from Rollingstone
		unset($jzService);unset($image);
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "rs");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		
		// Now let's get a link from Rollingstone
		unset($jzService);unset($image);
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "msnmusic");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		
		// Now let's get a link from Musicbrainz
		unset($jzService);unset($image);
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "musicbrainz");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		echo "<br>";
		$this->closeButton();
		echo "</form></center>";		
		
		$this->closeBlock();
	
	}
	
	/**
	* This tools lets us automatically renumber the tracks for an album
	*
	* @author Ross Carlson
	* @since 03/07/05
	* @version 03/07/05
	* @param $node The node we are looking at
	*
	**/
	function displayRenumber($node){
		global $allow_filesystem_modify,$jzSERVICES;

		$this->displayPageTop("",word("Renumbering tracks for"). ": ". $node->getName());
		$this->openBlock();

		if (!isset($_GET['renumber_type'])) {
		  $arr = array();
		  $arr['action'] = "popup";
		  $arr['ptype'] = "autorenumber";
		  $arr['jz_path'] = $_GET['jz_path'];

		  echo '<table><tr><td>';
		  $arr['renumber_type'] = "mb";
		  echo '<a href="'.urlize($arr).'">'.word("From Musicbrainz").'</a>';
		  echo '</td></tr><tr><td>';
		  $arr['renumber_type'] = "fn";
		  echo '<a href="'.urlize($arr).'">'.word("From filenames").'</a>';
		  echo '</td></tr></table>';
		}
		if ($_GET['renumber_type'] == "fn") {
		  $albums = array();
		  if (sizeof($albums = $node->getSubNodes("nodes")) == 0) {
		    $albums[] = $node;
		  }
		  foreach ($albums as $album) {
		    $tracks = $album->getSubNodes("tracks");
		    sortElements($tracks,"filename");
		    for ($i = 0; $i < sizeof($tracks); $i++) {
		      $meta = $tracks[$i]->getMeta();
		      $meta['number'] = $i+1;
		      $tracks[$i]->setMeta($meta);
		    }
		  }
		  echo 'Done!';
		  echo '<br><br><center>';
		  $this->closeButton(true);
		  exit();
		}
		else if ($_GET['renumber_type'] == "mb") {
		  $jzSERVICES->loadService("metadata", "musicbrainz");
		  // Did they submit the form?
		if (isset($_POST['edit_renumber'])){			
			echo word('Renumbing tracks, please stand by...'). "<br><br>";
			echo '<div id="status"></div>';
			echo '<div id="oldname"></div>';
			echo '<div id="newname"></div>';
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				o = document.getElementById("oldname");
				n = document.getElementById("newname");
				s = document.getElementById("status");
				s.innerHTML = '<nobr><?php echo word("Status: Getting track information"); ?>...</nobr>';
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			
			// Now let's get the tracks
			$tracks = $jzSERVICES->getAlbumMetadata($node, false, "tracks");
			$aTracks = $node->getSubNodes("tracks");
			$c=1;
			for($i=0;$i<count($tracks);$i++){
				if ($tracks[$i] <> ""){
					// Ok, let's see if we can match this to one of the tracks we have
					foreach($aTracks as $track){
						if (stristr($tracks[$i],$track->getName()) or stristr($track->getName(),$tracks[$i])){
							// Ok, let's make the number 2 chars
							if ($c < 10){ $num = "0". $c; } else { $num = $c; }
							// Now we need to get the meta on this track
							$meta = $track->getMeta();
							// Now let's set the track number
							$meta['number'] = $num;
							// Now let's write that to the meta on the file
							$track->setMeta($meta);
							?>
							<SCRIPT LANGUAGE=JAVASCRIPT><!--\
								o.innerHTML = '<nobr><?php echo word("Old Name"); ?>: <?php echo $track->getName(); ?></nobr>';
								n.innerHTML = '<nobr><?php echo word("New Name"); ?>: <?php echo $num. " - ". $tracks[$i]; ?></nobr>';
								s.innerHTML = '<nobr><?php echo word("Status: Renumbering"); ?></nobr>';
								-->
							</SCRIPT>
							<?php
							flushdisplay();
							sleep(1);
							// Now do they want to update the filename?
							if ($allow_filesystem_modify == "true"){
								$oldFile = $track->getDataPath();
								$tArr = explode("/",$track->getDataPath());
								$file = $tArr[count($tArr)-1];
								unset($tArr[count($tArr)-1]);
								$newPath = implode("/",$tArr);
								$newFile = $newPath. "/". $num. " - ". $file;
								$success="Failed";
								if (@rename($oldFile,$newFile)){
									$success = "Success";
								}
								?>
								<SCRIPT LANGUAGE=JAVASCRIPT><!--\
									o.innerHTML = '<nobr><?php echo word("Old Name"); ?>: <?php echo $oldFile; ?></nobr>';
									n.innerHTML = '<nobr><?php echo word("New Name"); ?>: <?php echo $newFile; ?></nobr>';
									s.innerHTML = '<nobr><?php echo word("Status: Renaming"); ?> - <?php echo $success; ?></nobr>';
									-->
								</SCRIPT>
								<?php
								flushdisplay();
							}
						}
					}
					$c++;
				}
			}
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				o.innerHTML = '&nbsp;';
				n.innerHTML = '&nbsp;';
				s.innerHTML = '<nobr><?php echo word("Complete!"); ?></nobr>';
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			echo '<br><br><center>';
			$this->closeButton(true);
			exit();
		}
		
		$this->displayPageTop("",word("Searching for data for: "). $node->getName());
   		$this->openBlock();
		flushdisplay();
		
		// Now let's get the tracks
		$tracks = $jzSERVICES->getAlbumMetadata($node, false, "tracks");
		if (count($tracks) > 1){
			// Ok, we got tracks, let's try to match them up...
			$c=1;
			$aTracks = $node->getSubNodes("tracks");
			for($i=0;$i<count($tracks);$i++){
				if ($tracks[$i] <> ""){
					// Ok, let's see if we can match this to one of the tracks we have
					$found=false;
					foreach($aTracks as $track){
						if (stristr($tracks[$i],$track->getName()) or stristr($track->getName(),$tracks[$i])){
							echo '<font color="green"><nobr>'. $track->getName(). " --- ". $c. " - ". $tracks[$i]. "</nobr></font><br>";
							$found=true;
						}
					}
					if (!$found){
						echo '<font color="red">'. $c. " - ". $tracks[$i]. " ". word('not matches'). "</font><br>";
					}
					$c++;
				}
			}
			$arr = array();
			$arr['action'] = "popup";
			$arr['ptype'] = "autorenumber";
			$arr['jz_path'] = $_GET['jz_path'];
			$arr['renumber_type'] = "mb";
			echo '<form action="'. urlize($arr). '" method="POST">';
			echo "<br><br>";			
			echo '<input type="submit" name="edit_renumber" value="'. word('Renumber Tracks'). '" class="jz_submit"> &nbsp; ';
			$this->closeButton();
			echo "</form><br><br>";
		} else {
			echo word("Sorry, we didn't get good data back for this album...");
			echo '<br><br><center>';
			$this->closeButton();
		}
		}
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
	* Sitewide settings editor
	*
	* @author Ben Dodson
	* @since 2/2/05
	* @version 2/2/05
	*
	**/
	function displayPopPlayer(){
		global $css, $jzSERVICES;
		
		// Let's setup the css for the page
		include_once($css);
		
		// Now let's open the service for this
		$jzSERVICES->loadService("players",$_GET['embed_player']);
		$jzSERVICES->displayPlayer();
	}



	/**
	 * Sitewide settings editor
	 *
	 * @author Ben Dodson
	 * @since 2/2/05
	 * @version 2/2/05
	 *
	 **/
	function displaySiteSettings() {
	  global $include_path,$jzUSER,$my_frontend;
	  
	  if ($jzUSER->getSetting('admin') !== true) {
	    exit();
	  }
	  
	  $display = new jzDisplay();
	  $page_array = array();
	  $page_array['action'] = 'popup';
	  $page_array['ptype'] = 'sitesettings';
	  if (isset($_GET['subpage'])) {
	    $page_array['subpage'] = $_GET['subpage'];
	  }
	  if (isset($_GET['subsubpage'])) {
	    $page_array['subsubpage'] = $_GET['subsubpage'];
	  }
	  if (isset($_GET['set_fe'])) {
	    $page_array['set_fe'] = $_GET['set_fe'];
	  }
	  if (isset($_POST['set_fe'])) {
	    $page_array['set_fe'] = $_POST['set_fe'];
	  }
	  
	  $this->displayPageTop("",word('Site Settings'));
	  $this->openBlock();
	  
	  // Index page:
	  if (!isset($_GET['subpage'])) {
	    echo "<table><tr><td>";
	    $page_array['subpage'] =  "main";
	    echo '<a href="'.urlize($page_array).'">'. word('Main Settings'). '</a>';
	    echo "</td></tr><tr><td>";
	    
	    $page_array['subpage'] =  "services";
	    echo '<tr><td><a href="'.urlize($page_array).'">'. word('Services'). '</a>';
	    echo "</td></tr><tr><td>";
	    
	    $page_array['subpage'] =  "frontend";
	    echo '<tr><td> <a href="'.urlize($page_array).'">'. word('Frontend Settings'). '</a>';
	    echo "</td></tr></table>";
	    
	    //unset($page_array['subpage']);
	    
	    $this->closeBlock();
	    return;
	  }
	  if ($_GET['subpage'] == "frontend" && !isset($page_array['set_fe'])) {
  ?>
 <form method="POST" action="<?php echo urlize($page_array); ?>">
    <select class="jz_select" name="<?php echo jz_encode('set_fe');?>">Frontend: 
   <?php
    $arr = readDirInfo($include_path.'frontend/frontends',"dir");
 foreach ($arr as $a) {
   if (file_exists($include_path."frontend/frontends/${a}/settings.php")) {
     echo "<option value=\"".jz_encode($a)."\"";
     if ($a == $my_frontend) {
       echo ' selected';
     }
     echo ">$a</option>";
   }
 }
   ?>
   </select>
       &nbsp;<input type="submit" class="jz_submit" value="<?php echo word('Go'); ?>">
		<?php 
		$this->closeBlock();
 return;
	  }
	  if (isset($_POST['update_postsettings']) && $_GET['subpage'] != "main") {
	    echo word('Settings Updated.'). "<br><br>";
	  }
	  
	  $display->openSettingsTable(urlize($page_array));
	  
	  if ($_GET['subpage'] == "main") {
	    $settings_file = $include_path.'settings.php';
	    $settings_array = settingsToArray($settings_file);

	    $urla = array();
	    $urla['subpage'] = "main";
	    $urla['action'] = "popup";
	    $urla['ptype'] = "sitesettings";



	      echo '<center>';
	      echo "| ";
	      $urla['subsubpage'] = "system";
	      echo '<a href="'.urlize($urla).'">'.word("System") . "</a> | ";
	      $urla['subsubpage'] = "playlist";
	      echo '<a href="'.urlize($urla).'">'.word("Playlist") . "</a> | ";
	      $urla['subsubpage'] = "display";
	      echo '<a href="'.urlize($urla).'">'.word("Display") . "</a> | ";
	      $urla['subsubpage'] = "image";
	      echo '<a href="'.urlize($urla).'">'.word("Image") . "</a> | ";
	      $urla['subsubpage'] = "groupware";
	      echo '<a href="'.urlize($urla).'">'.word("Groupware") . "</a> | ";
	      $urla['subsubpage'] = "jukebox";
	      echo '<a href="'.urlize($urla).'">'.word("Jukebox") . "</a> | ";
	      echo '<br>| ';
	      $urla['subsubpage'] = "resample";
	      echo '<a href="'.urlize($urla).'">'.word("Resampling") . "</a> | ";
	      $urla['subsubpage'] = "charts";
	      echo '<a href="'.urlize($urla).'">'.word("Charts/Random Albums") . "</a> | ";
	      $urla['subsubpage'] = "downloads";
	      echo '<a href="'.urlize($urla).'">'.word("Downloads") . "</a> | ";
	      $urla['subsubpage'] = "email";
	      echo '<a href="'.urlize($urla).'">'.word("Email") . "</a> | ";
	      $urla['subsubpage'] = "keywords";
	      echo '<a href="'.urlize($urla).'">'.word("Keywords") . "</a> | ";
	      echo "</center><br>";

	      if (isset($_POST['update_postsettings'])) {
		echo "<strong>".word("Settings Updated.")."</strong><br>";
	      }
	      echo "<br>";

	    switch ($_GET['subsubpage']) {
	    case "system":
	      $display->settingsTextbox("media_dirs","media_dirs",$settings_array);
	      $display->settingsTextbox("web_dirs","web_dirs",$settings_array);
	      $display->settingsDropdown("live_update","live_update",array("true","false"),$settings_array);
	      $display->settingsTextbox("audio_types","audio_types",$settings_array);
	      $display->settingsTextbox("video_types","video_types",$settings_array);
	      $display->settingsTextbox("ext_graphic","ext_graphic",$settings_array);
	      $display->settingsTextbox("track_num_seperator","track_num_seperator",$settings_array);
	      $display->settingsTextbox("date_format","date_format",$settings_array);
	      $display->settingsTextbox("short_date","short_date",$settings_array);
	      $display->settingsDropdown("allow_filesystem_modify","allow_filesystem_modify",array("true","false"),$settings_array);
	      $display->settingsDropdown("allow_id3_modify","allow_id3_modify",array("true","false"),$settings_array);
	      $display->settingsDropdown("gzip_handler","gzip_handler",array("true","false"),$settings_array);
	      $display->settingsDropdown("ssl_stream","ssl_stream",array("true","false"),$settings_array);
	      $display->settingsDropdown("media_lock_mode","media_lock_mode",array("off","track","album","artist","genre"),$settings_array);
	      break;
	    case "playlist":
	      $display->settingsDropdown("enable_playlist","enable_playlist",array("true","false"),$settings_array);
	      $display->settingsTextbox("playlist_ext","playlist_ext",$settings_array);
	      $display->settingsDropdown("use_ext_playlists","use_ext_playlists",array("true","false"),$settings_array);
	      $display->settingsTextbox("max_playlist_length","max_playlist_length",$settings_array);
	      $display->settingsTextbox("random_play_amounts","random_play_amounts",$settings_array);
	      $display->settingsTextbox("default_random_count","default_random_count",$settings_array);
	      $display->settingsTextbox("default_random_type","default_random_type",$settings_array);
	      $display->settingsTextbox("embedded_player","embedded_player",$settings_array);
	      break;
	    case "display":
	      $display->settingsTextbox("site_title","site_title",$settings_array);
	      $display->settingsDropdownDirectory("jinzora_skin","jinzora_skin",$include_path.'style',"dir",$settings_array);
	      $display->settingsDropdownDirectory("frontend","frontend",$include_path.'frontend/frontends',"dir",$settings_array);
	      $display->settingsDropdown("jz_lang_file","jz_lang_file",getLanguageList(),$settings_array);
	      $display->settingsDropdown("allow_lang_choice","allow_lang_choice",array("true","false"),$settings_array);
	      $display->settingsDropdown("allow_style_choice","allow_style_choice",array("true","false"),$settings_array);
	      $display->settingsDropdown("allow_interface_choice","allow_interface_choice",array("true","false"),$settings_array);
	      $display->settingsDropdown("use_ext_playlists","use_ext_playlists",array("true","false"),$settings_array);
	      $display->settingsDropdown("show_page_load_time","show_page_load_time",array("true","false"),$settings_array);
	      $display->settingsDropdown("show_sub_numbers","show_sub_numbers",array("true","false"),$settings_array);
	      $display->settingsTextbox("quick_list_truncate","quick_list_truncate",$settings_array);
	      $display->settingsTextbox("album_name_truncate","album_name_truncate",$settings_array);
	      $display->settingsDropdown("sort_by_year","sort_by_year",array("true","false"),$settings_array);
	      $display->settingsTextbox("num_other_albums","num_other_albums",$settings_array);	      
	      $display->settingsDropdown("header_drops","header_drops",array("true","false"),$settings_array);
	      $display->settingsDropdown("genre_drop","genre_drop",array("true","false","popup"),$settings_array);
	      $display->settingsDropdown("artist_drop","artist_drop",array("true","false","popup"),$settings_array);
	      $display->settingsDropdown("album_drop","album_drop",array("true","false","popup"),$settings_array);
	      $display->settingsDropdown("song_drop","song_drop",array("true","false","popup"),$settings_array);
	      $display->settingsDropdown("quick_drop","quick_drop",array("true","false"),$settings_array);
	      $display->settingsTextbox("days_for_new","days_for_new",$settings_array);	      
	      $display->settingsTextbox("hide_id3_comments","hide_id3_comments",$settings_array);	      
	      $display->settingsTextbox("show_all_checkboxes","show_all_checkboxes",$settings_array);	      
	      $display->settingsTextbox("status_blocks_refresh","status_blocks_refresh",$settings_array);	
	      $display->settingsDropdown("compare_ignores_the","compare_ignores_the",array("true","false"),$settings_array);      
	      $display->settingsDropdown("handle_compilations","handle_compilations",array("true","false"),$settings_array);      
	      $display->settingsTextbox("embedded_header","embedded_header",$settings_array);	      
	      $display->settingsTextbox("embedded_footer","embedded_footer",$settings_array);	      
	      break;
	    case "image":
	      $display->settingsDropdown("resize_images","resize_images",array("true","false"),$settings_array);
	      $display->settingsDropdown("keep_porportions","keep_porportions",array("true","false"),$settings_array);
	      $display->settingsDropdown("auto_search_art","auto_search_art",array("true","false"),$settings_array);
	      $display->settingsDropdown("create_blank_art","create_blank_art",array("true","false"),$settings_array);
	      //$display->settingsTextbox("default_art","default_art",$settings_array);	
	      break;
	    case "groupware":
	      $display->settingsDropdown("enable_discussions","enable_discussions",array("true","false"),$settings_array);
	      $display->settingsDropdown("enable_requests","enable_requests",array("true","false"),$settings_array);
	      $display->settingsDropdown("enable_ratings","enable_ratings",array("true","false"),$settings_array);
	      $display->settingsTextbox("rating_weight","rating_weight",$settings_array);
	      $display->settingsDropdown("track_plays","track_plays",array("true","false"),$settings_array);
	      $display->settingsDropdown("display_downloads","display_downloads",array("true","false"),$settings_array);
	      $display->settingsDropdown("secure_links","secure_links",array("true","false"),$settings_array);
	      $display->settingsDropdown("user_tracking_display","user_tracking_display",array("true","false"),$settings_array);
	      $display->settingsTextbox("user_tracking_age","user_tracking_age",$settings_array);
	      $display->settingsDropdown("disable_random","disable_random",array("true","false"),$settings_array);
	      $display->settingsTextbox("info_level","info_level",$settings_array);
	      $display->settingsDropdown("track_play_only","track_play_only",array("true","false"),$settings_array);
	      $display->settingsDropdown("allow_clips","allow_clips",array("true","false"),$settings_array);
	      $display->settingsTextbox("clip_length","clip_length",$settings_array);
	      $display->settingsTextbox("clip_start","clip_start",$settings_array);
	      break;
	    case "jukebox":
	      $display->settingsDropdown("jukebox","jukebox",array("true","false"),$settings_array);
	      $display->settingsDropdown("jukebox_display","jukebox_display",array("default","small","off"),$settings_array);
	      $display->settingsDropdown("jukebox_default_addtype","jukebox_default_addtype",array("current","begin","end","replace"),$settings_array);
	      $display->settingsTextbox("default_jukebox","default_jukebox",$settings_array);
	      $display->settingsTextbox("jb_volumes","jb_volumes",$settings_array);
	      break;
	    case "resample":
	      $display->settingsDropdown("allow_resample","allow_resample",array("true","false"),$settings_array);
	      $display->settingsDropdown("force_resample","force_resample",array("true","false"),$settings_array);
	      $display->settingsDropdown("allow_resample_downloads","allow_resample_downloads",array("true","false"),$settings_array);
	      $display->settingsTextbox("default_resample","default_resample",$settings_array);
	      $display->settingsTextbox("resampleRates","resampleRates",$settings_array);
	      $display->settingsTextbox("lame_cmd","lame_cmd",$settings_array);
	      $display->settingsTextbox("lame_opts","lame_opts",$settings_array);
	      $display->settingsTextbox("path_to_lame","path_to_lame",$settings_array);
	      $display->settingsTextbox("path_to_flac","path_to_flac",$settings_array);
	      $display->settingsTextbox("path_to_oggenc","path_to_oggenc",$settings_array);
	      $display->settingsTextbox("path_to_oggdec","path_to_oggdec",$settings_array);
	      $display->settingsTextbox("path_to_mpc","path_to_mpc",$settings_array);
	      $display->settingsTextbox("path_to_mpcenc","path_to_mpcenc",$settings_array);
	      $display->settingsTextbox("path_to_wavpack","path_to_wavpack",$settings_array);
	      $display->settingsTextbox("path_to_wavunpack","path_to_wavunpack",$settings_array);
	      $display->settingsTextbox("path_to_wmadec","path_to_wmadec",$settings_array);
	      $display->settingsTextbox("path_to_shn","path_to_shn",$settings_array);
	      $display->settingsTextbox("path_to_mplayer","path_to_mplayer",$settings_array);
	      $display->settingsTextbox("mplayer_opts","mplayer_opts",$settings_array);
	      $display->settingsTextbox("always_resample","always_resample",$settings_array);
	      $display->settingsTextbox("always_resample_rate","always_resample_rate",$settings_array);
	      $display->settingsTextbox("resample_cache_size","resample_cache_size",$settings_array);
	      break;
	    case "charts":
	      $display->settingsDropdown("display_charts","display_charts",array("true","false"),$settings_array);
	      $display->settingsTextbox("chart_types","chart_types",$settings_array);
	      $display->settingsTextbox("num_items_in_charts","num_items_in_charts",$settings_array);
	      $display->settingsTextbox("chart_timeout_days","chart_timeout_days",$settings_array);
	      $display->settingsTextbox("random_albums","random_albums",$settings_array);
	      $display->settingsTextbox("random_per_slot","random_per_slot",$settings_array);
	      $display->settingsTextbox("random_rate","random_rate",$settings_array);
	      $display->settingsTextbox("random_art_size","random_art_size",$settings_array);
	      $display->settingsDropdown("rss_in_charts","rss_in_charts",array("true","false"),$settings_array);
	      break;
	    case "downloads":
	      $display->settingsTextbox("multiple_download_mode","multiple_download_mode",$settings_array);
	      $display->settingsTextbox("single_download_mode","single_download_mode",$settings_array);
	      break;
	    case "email":
	      $display->settingsDropdown("allow_send_email","allow_send_email",array("true","false"),$settings_array);
	      //$display->settingsTextbox("email_from_address","email_from_address",$settings_array);
	      //$display->settingsTextbox("email_from_name","email_from_name",$settings_array);
	      //$display->settingsTextbox("email_server","email_server",$settings_array);
	      break;
	    case "keywords":
	      $display->settingsTextbox("keyword_radio","keyword_radio",$settings_array);
	      $display->settingsTextbox("keyword_random","keyword_random",$settings_array);
	      $display->settingsTextbox("keyword_play","keyword_play",$settings_array);
	      $display->settingsTextbox("keyword_track","keyword_track",$settings_array);
	      $display->settingsTextbox("keyword_album","keyword_album",$settings_array);
	      $display->settingsTextbox("keyword_artist","keyword_artist",$settings_array);
	      $display->settingsTextbox("keyword_genre","keyword_genre",$settings_array);
	      $display->settingsTextbox("keyword_lyrics","keyword_lyrics",$settings_array);
	      $display->settingsTextbox("keyword_limit","keyword_limit",$settings_array);
	      $display->settingsTextbox("keyword_id","keyword_id",$settings_array);
	      break;
	    default:
	      $this->closeBlock();
	      return;
	    }
	    /*
	    foreach ($settings_array as $key => $val) {
	      // The settingsTextbox (and other) functions update the array for us
	      // on a form submit. No other form handling is needed,
	      // other than to write the data back to the file!
	      // Plus, settings aren't modified if they aren't in the form.
	      if ($key == "jinzora_skin") {
		$display->settingsDropdownDirectory($key,$key,$include_dir."style","dir",$settings_array);
	      } else if ($key == "frontend") {
		$display->settingsDropdownDirectory($key,$key,$include_dir."frontend/frontends","dir",$settings_array);
	      } else {
		$display->settingsTextbox($key,$key,$settings_array);
	      }
	    }
	    */
	  } else if ($_GET['subpage'] == "services") {
	    $settings_file = $include_path.'services/settings.php';
	    $settings_array = settingsToArray($settings_file);
	    $display->settingsDropdownDirectory(word("Lyrics"), "service_lyrics", $include_path.'services/services/lyrics','file',$settings_array);
	    $display->settingsDropdownDirectory(word("Similar Artists"), "service_similar", $include_path.'services/services/similar','file',$settings_array);
	    $display->settingsDropdownDirectory(word("Links"), "service_link", $include_path.'services/services/link','file',$settings_array);
	    $display->settingsDropdownDirectory(word("Metadata Retrieval"), "service_metadata", $include_path.'services/services/metadata','file',$settings_array);
	    //$display->settingsDropdownDirectory(word("ID3 Tagging"), "service_tagdata", $include_path.'services/services/tagdata','file',$settings_array);
	  } else if ($_GET['subpage'] == "frontend") {
	    $settings_file = $include_path."frontend/frontends/".$page_array['set_fe']."/settings.php";
	    $settings_array = settingsToArray($settings_file);      
	    foreach ($settings_array as $key => $val) {
	      $display->settingsTextbox($key,$key,$settings_array);      
	    }
	  }
	  
	  $display->closeSettingsTable(is_writeable($settings_file));
	  //echo "&nbsp;";
	  //$this->closeButton();
	  if (isset($_POST['update_postsettings']) && is_writeable($settings_file)) {
	    arrayToSettings($settings_array,$settings_file);
	  }
	  $this->closeBlock();
	}

	/**
	* Displays the upload status box
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 03/01/05
	* @since 03/01/05
	*/
	function displayUploadStatus(){
		global $root_dir;
		
		$this->displayPageTop("",word("Uploading Media, Please wait..."));
		$this->openBlock();
		
		echo '<br><center>';
		echo word('<strong>File upload in progress!</strong><br><br>This page will go away automatically when the upload is complete. Please be patient!'). "<br><br>";
		echo '<img src="'. $root_dir. '/style/images/computer.gif" border="0">';
		echo '<img src="'. $root_dir. '/style/images/uploading.gif" border="0">';
		echo '<img src="'. $root_dir. '/style/images/computer.gif" border="0">';

		$this->closeBlock();
	}	
	
	/**
	* Allows the user to add media
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 03/01/05
	* @since 03/01/05
	*/
	function displayUploadMedia($node){
		global $audio_types, $video_types, $include_path, $root_dir, $jzUSER;
		

		if (checkPermission($jzUSER,"upload",$node->getPath("String")) === false) {
			echo word("Insufficient permissions.");
			exit();
		}
		// Did they want to actually create the link track
		if (isset($_POST['edit_add_link_track'])){
			// Ok, let's add the link
			$node->inject(array($_POST['edit_link_track_name']), $_POST['edit_link_track_url'],"track");

			exit();
			$this->closeWindow(true);
		}
		
		// Let's open the page
		$this->displayPageTop("",word("Add Media"). ": ". $node->getName());
		$this->openBlock();
		
		// Did they want to create a link track
		// This will show them the form
		if (isset($_POST['add_link_track'])){
			$arr = array();
			$arr['action'] = "popup";
			$arr['ptype'] = "uploadmedia";
			$arr['jz_path'] = $_GET['jz_path'];
			echo '<form action="'. urlize($arr). '" method="POST">';
			echo '<table class="jz_track_table" width="100%" cellpadding="3">';
			echo '<tr><td align="right">';
			echo word("Track Name"). ":";
			echo '</td><td>';
			echo '<input type="text" name="edit_link_track_name" class="jz_input" size="30">';
			echo '</td></tr>';
			echo '<tr><td align="right">';
			echo word("Track URL"). ":";
			echo '</td><td>';
			echo '<input type="text" name="edit_link_track_url" class="jz_input" size="30">';
			echo '</td></tr>';
			echo '</table>';
			echo '<br><center>';
			echo '<input type="submit" name="edit_add_link_track" value="'. word("Add Link Track"). '" class="jz_submit"></form> ';
			$this->closeButton(true);
			exit();
		}
		
		// Ok, did they want to uploade?
		if (isset($_POST['uploadfiles'])){
			// First let's flushout the display
			flushdisplay();
			
			echo word("Writing out files, please stand by..."). "<br><br>";
			echo '<div id="status"></div>';
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				s = document.getElementById("status");
				-->
			</SCRIPT>
			<?php
			// BEN PUT THIS IN:
			// I'm not sure what it's supposed to be set to.
			// fixing a PHP warning.
			$c=0;
			// Ok, did they want to add a new sub location
			if (isset($_POST['edit_new_sub'])){
				// Ok, we need to create that new dir
				$newDir = $node->getDataPath("String"). "/". $_POST['edit_new_sub'];
				// Now we need to make sure that exsists
				$dArr = explode("/",$newDir);
				$newDir = "";
				for ($i=0;$i<count($dArr)+$c;$i++){
					if ($dArr[$i] <> ""){
						// Now let's build the newdir
						$newDir .= "/". $dArr[$i];
						if (!is_dir($newDir)){
							mkdir($newDir);
							chmod($newDir,0666);
							?>
							<SCRIPT LANGUAGE=JAVASCRIPT><!--\
								s.innerHTML = '<nobr><?php echo word("Status: Creating Dir:"); ?> <?php echo $dArr[$i]; ?></nobr>';
								-->
							</SCRIPT>
							<?php
							flushdisplay();
							sleep(1);
						}
					}
				}
			} else {
				$newDir =  $node->getDataPath("String");
			}
			$c=0;
			for ($i=1;$i<6;$i++){
				// Now let's see what they uploaded
				if ($_FILES['edit_file'. $i]['name'] <> ""){
					// Ok, They wanted to upload file #1, let's do it
					$newLoc = $newDir. "/". $_FILES['edit_file'. $i]['name'];
					// Ok, now that we've got the new name let's put it there
					if (copy($_FILES['edit_file'. $i]['tmp_name'], $newLoc)){
						// Now let's set the permissions
						chmod($newLoc, 0666);
						?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							s.innerHTML = "<nobr><?php echo word('Status: Adding File:'); ?> <?php echo $_FILES['edit_file'. $i]['name']; ?></nobr>";
							-->
						</SCRIPT>
						<?php
						flushdisplay();
						sleep(1);
						$c++;
						// Ok, now was this a zip file?
						if (substr($_FILES['edit_file'. $i]['name'],-4) == ".zip"){
							?>
							<SCRIPT LANGUAGE=JAVASCRIPT><!--\
								s.innerHTML = "<nobr><?php echo word('Status: Extracting files in:'); ?> <?php echo $_FILES['edit_file'. $i]['name']; ?></nobr>";
								-->
							</SCRIPT>
							<?php
							flushdisplay();
							sleep(1);
							include_once($include_path. "lib/pclzip.lib.php");
							$zipfile = $newLoc;
							$archive = new PclZip($zipfile);
							if ($archive->extract(PCLZIP_OPT_PATH, $newDir) == 0) {
								?>
								<SCRIPT LANGUAGE=JAVASCRIPT><!--\
									s.innerHTML = "<nobr><?php echo word('Status: Extracting files in:'); ?> <?php echo $_FILES['edit_file'. $i]['name']; ?>!</nobr>";
									-->
								</SCRIPT>
								<?php
								flushdisplay();
							} else {
								$fileList = $archive->listContent();
								for ($i=0; $i < count($fileList); $i++){
									?>
									<SCRIPT LANGUAGE=JAVASCRIPT><!--\
										s.innerHTML = "<nobr><?php echo word('Status: Extracting file:'); ?> <?php echo $fileList[$i]['filename']; ?></nobr>";
										-->
									</SCRIPT>
									<?php
									flushdisplay();
									sleep(1);
									$c++;
								}
								$c=$c-1;
							}
							flushdisplay();
							// Now let's unlink that file
							unlink($zipfile);
						}
					}
				}
			}
			
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				s.innerHTML = "<nobr><?php echo word('Status: Upload Complete!'); ?><br><?php echo $c; ?> <?php echo word('files uploaded'); ?></nobr>";
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			sleep(1);
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
				thisWin = window.open('','StatusPop','');
				thisWin.close();
			-->
			</SCRIPT>
			<?php	
			echo '<br><br><center>';	
			$this->closeButton();	
			echo '</center>';
			exit();
		}
		// Did they just want to close?
		if (isset($_POST['justclose'])){
			$this->closeWindow(false);
		}
		
		echo word('When uploading you may upload single files or zip files containing all the files you wish to upload.  These will then be extracted once they have been uploaded.  You may also add your descritpion files and album art now and they will be displayed.  The following media types are supported by this system and may be uploaded:');
		echo "<br><br>". word('Audio'). ": ". $audio_types. "<br>". word('Video'). ": ". $video_types;
		echo "<br><br>";
		
		// Now let's start our form so they can upload
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "uploadmedia";
		$arr['jz_path'] = $_GET['jz_path'];
		echo '<form action="'. urlize($arr). '" method="POST" enctype="multipart/form-data">';
		?>		
		<center>
			<?php echo word("New Sub Path"); ?>: <br>
			<input type="text" name="edit_new_sub" class="jz_input" size="40"><br><br>
			<?php echo word('File'); ?> 1: <input type="file" name="edit_file1" class="jz_input" size="40"><br>
			<?php echo word('File'); ?> 2: <input type="file" name="edit_file2" class="jz_input" size="40"><br>
			<?php echo word('File'); ?> 3: <input type="file" name="edit_file3" class="jz_input" size="40"><br>
			<?php echo word('File'); ?> 4: <input type="file" name="edit_file4" class="jz_input" size="40"><br>
			<?php echo word('File'); ?> 5: <input type="file" name="edit_file5" class="jz_input" size="40"><br>
			<br><br>
			<input type=submit class="jz_submit" name="<?php echo jz_encode('justclose'); ?>" value="<?php echo word('Close'); ?>">
			<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
				function openStatusPop(obj, boxWidth, boxHeight){
					var sw = screen.width;
					var sh = screen.height;
					var winOpt = "width=" + boxWidth + ",height=" + boxHeight + ",left=" + ((sw - boxWidth) / 2) + ",top=" + ((sh - boxHeight) / 2) + ",menubar=no,toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=no";
					thisWin = window.open(obj,'StatusPop',winOpt);
				}	
			-->
			</SCRIPT>
			<?php
				$aRR = array();
				$aRR['action'] = "popup";
				$aRR['ptype'] = "showuploadstatus";
			?>
			<input onMouseDown="openStatusPop('<?php echo urlize($aRR); ?>',300,200)" type=submit class="jz_submit" name="<?php echo jz_encode('uploadfiles'); ?>" value="<?php echo word('Upload'); ?>">
			<!--<input type=submit class="jz_submit" name="<?php echo jz_encode('add_link_track'); ?>" value="<?php echo word('Add Link Track'); ?>">-->
		</center>
		<?php
		echo '</form>';
		
		$this->closeBlock();
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
	* Displays the tool to let the user add a link track.
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 9/22/05
	* @since 9/22/05
	* @param $node The node we are looking at
	*/
	function displayAddLinkTrack($node){

	  $this->displayPageTop("",word("Add Link Track in"). ": ". $node->getName());
	  $this->openBlock();

	  if (isset($_POST['edit_taddress'])) {
	    $path = array();
	    $path[] = $_POST['edit_tname'];
	    $tr = $node->inject($path,$_POST['edit_taddress']);
	    if ($tr !== false) {
	      $meta = $tr->getMeta();
	      $meta['title'] = $_POST['edit_tname'];
	      $tr->setMeta($meta);
	    }

	    echo word("Added") . ": " . $_POST['edit_tname'];
	    echo " (" . $_POST['edit_taddress'] . ")";

	    echo '<br><br>';
	    $this->closeButton();
	    $this->closeBlock();
	    return;
	  }


	  // Let's show the form to edit with
	  $arr = array();
	  $arr['action'] = "popup";
	  $arr['ptype'] = "addlinktrack";
	  $arr['jz_path'] = $node->getPath("String");
	  echo '<form action="'. urlize($arr). '" method="POST">';
	  echo '<table><tr><td width="30%">';
	  echo word("Name"). ": ";
	  echo '</td><td>';
	  echo '<input name="edit_tname" class="jz_input">';
	  echo '</td></tr>';
	  echo '<tr><td>';
	  echo word("Address"). ": ";
	  echo '</td><td>';
	  echo '<input name="edit_taddress" class="jz_input">';
	  echo '</td></tr></table>';
	  echo '<br><br>';
	  echo '<input type="submit" class="jz_submit" value="'.word('Add Link').'">';
	  $this->closeButton();
	  echo '</form>';
	  $this->closeBlock();
	}
	
	/**
	* Displays the tool to let the user set the page type
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 01/27/05
	* @since 01/27/05
	* @param $node The node we are looking at
	*/
	function displaySetPType($node){
	  global $jzUSER;
	  
	  if (!checkPermission($jzUSER,"admin",$node->getPath("String"))) {
	    echo word("Insufficient permissions.");
	    return;
	  }


		if (isset($_POST['edit_auto_set_ptype'])){
			$this->displayAutoPageType($node);
			exit();
		}
		
		// Let's see if they submitted the form
		if (isset($_POST['newPType'])){			
			// Now let's set the type
		  if ($_POST['newPType'] != "unchanged") {
		    $node->setPType($_POST['newPType']);
		  }

		  $i = 1;
		  while (isset($_POST["newPType-$i"])) {
		    if (($pt = $_POST["newPType-$i"]) != "unchanged") {
		      $nodes = $node->getSubNodes("nodes",$i);
		      foreach ($nodes as $n) {
				$n->setPType($pt);
		      }
		    }
		    $i++;
		  }
		  echo "<br><br><center>";
		  $this->closeButton(true);
		  exit();
		}
		$this->displayPageTop("",word("Set Page Type for"). ": ". $node->getName());
		$this->openBlock();
		
		// Let's show the form to edit with
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "setptype";
		$arr['jz_path'] = $node->getPath("String");
		echo '<form action="'. urlize($arr). '" method="POST">';
		echo word("Current Page Type"). ": ". $node->getPType(). "<br><br>";
		echo '<table><tr><td>';
		echo word("New Page Type"). ": ";
		echo '</td><td>';
		echo '<select name="'. jz_encode("newPType"). '" class="jz_select">';
		echo '<option value="'. jz_encode("unchanged"). '">'. word("Unchanged"). '</option>';
		echo '<option value="'. jz_encode("genre"). '">'. word("Genre"). '</option>';
		echo '<option value="'. jz_encode("artist"). '">'. word("Artist"). '</option>';
		echo '<option value="'. jz_encode("album"). '">'. word("Album"). '</option>';
		echo '<option value="'. jz_encode("disk"). '">'. word("Disk"). '</option>';
		echo '<option value="'. jz_encode("generic"). '">'. word("Generic"). '</option>';
		echo '</select>';
		echo '</td></tr>';
		$i = 1;
		while ($node->getSubNodeCount("nodes",$i) > 0) {
		  echo "<tr><td>Level $i:</td><td>";
		  echo '<select name="'. jz_encode("newPType-$i"). '" class="jz_select">';
		  echo '<option value="'. jz_encode("unchanged"). '">'. word("Unchanged"). '</option>';
		  echo '<option value="'. jz_encode("genre"). '">'. word("Genre"). '</option>';
		  echo '<option value="'. jz_encode("artist"). '">'. word("Artist"). '</option>';
		  echo '<option value="'. jz_encode("album"). '">'. word("Album"). '</option>';
		  echo '<option value="'. jz_encode("disk"). '">'. word("Disk"). '</option>';
		  echo '<option value="'. jz_encode("generic"). '">'. word("Generic"). '</option>';
		  echo '</select></td></tr>';
		  $i++;
		}
		echo "</table>";
		echo '<br><input type="submit" name="updatePType" value="'. word("Update Type"). '" class="jz_submit">';
		echo ' <input type="submit" name="edit_auto_set_ptype" value="'. word("Auto Set Page Type"). '" class="jz_submit">';
		echo " ";
		$this->closeButton();
		echo '</form>';
		
		$this->closeBlock();
		exit();
	}
	
	/**
	* Displays the site/location news block text to be edited
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 01/27/05
	* @since 01/27/05
	* @param $node The node we are looking at
	*/
	function displaySiteNews($node){
	  global $jzUSER;

	  if (!checkPermission($jzUSER,"admin",$node->getPath("String"))) {
	    echo word("Insufficient permissions.");
	    return;
	  }
	  

	  $be = new jzBackend();
		
		// Let's figure out the news location
		if ($node->getName() == ""){
			$news = "site-news";
			$title = word("Site News");
		} else {
			$news = $node->getName(). "-news";
			$title = word("Site News"). ": ". $node->getName();
		}
		
		$this->displayPageTop("",$title);
		$this->openBlock();
		
		// Did they submit the form to edit the news?
		if (isset($_POST['updateSiteNews'])){
			// Now let's store the data
			$be->storeData($news, nl2br(str_replace("<br />","",$_POST['siteNewsData'])));
		}
		
		// Let's show the form to edit with
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "sitenews";
		$arr['jz_path'] = $_GET['jz_path'];
		echo '<form action="'. urlize($arr). '" method="POST">';
		?>
		<br>
		<center>
			<textarea name="siteNewsData" cols="60" rows="20" class="jz_input"><?php echo $be->loadData($news); ?></textarea>
			<br><br>
			<input type="submit" value="<?php echo word("Update News"); ?>" name="<?php echo jz_encode("updateSiteNews"); ?>" class="jz_submit">
			&nbsp;
			<?php
				$this->closeButton(false);
			?>
		</center>
		<?php
		
		$this->closeBlock();
		exit();
	}
	
	/**
	* Displays the full top played list
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 01/27/05
	* @since 01/27/05
	*/
	function displayDupFinder(){
		
		$this->displayPageTop("",word("Duplicate Finder"));
		$this->openBlock();
		
		// Now let's see if they searched
		if (isset($_POST['searchDupArtists']) or isset($_POST['searchDupAlbums']) or isset($_POST['searchDupTracks'])){
			// Ok, let's search, but for what?
			if (isset($_POST['searchDupArtists'])){
				$distance = distanceTo("artist");
				$what = "nodes";
			}
			if (isset($_POST['searchDupAlbums'])){
				$distance = distanceTo("album");
				$what = "nodes";
			}
			
			// Ok, now we need to get a list of ALL artist so we can show possible dupes
			echo word("Retrieving full list..."). "<br><br>";
			flushdisplay();
			
			$root = new jzMediaNode();
			$artArray = $root->getSubNodes($what,$distance);
			for ($i=0;$i<count($artArray);$i++){
				$valArray[] = $artArray[$i]->getName();
			}
			echo word("Scanning full list..."). "<br><br>";
			flushdisplay();
			
			$found = $root->search($valArray,$what,$distance,sizeof($valArray),"exact");
			foreach ($found as $e) {
				$matches[] = $e->getName();
				echo $e->getName(). '<br>';
				flushdisplay();
			}
			
			

			
			$this->closeBlock();
			exit();
			
		}
		
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "dupfinder";
		echo '<form action="'. urlize($arr). '" method="POST">';
		echo "<br><br>";
		echo "<center>";
		echo word("Please select what you would like to search for"). "<br><br><br>";
		echo '<input type="submit" value="'. word("Search Artists"). '" name="'. jz_encode("searchDupArtists"). '" class="jz_submit">';
		echo ' &nbsp; ';
		echo '<input type="submit" value="'. word("Search Albums"). '" name="'. jz_encode("searchDupAlbums"). '" class="jz_submit">';
		echo ' &nbsp; ';
		echo '<input type="submit" value="'. word("Search Tracks"). '" name="'. jz_encode("searchDupTracks"). '" class="jz_submit">';
		
		echo "</center>";
		echo '</form>';
		
		$this->closeBlock();
	}
	
	
	/**
	* Displays the full top played list
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 01/27/05
	* @since 01/27/05
	* @param $node The node we are looking at
	*/
	function displayNodeStats($node){
		global $row_colors,$site_title,$jzUSER;
		

		if (!checkPermission($jzUSER,"admin",$node->getPath("String"))) {
		  echo word("Insufficient permissions.");
		  return;
		}

		$display = new jzDisplay();
		if ($node->getLevel() == 0) {
		  $this->displayPageTop("",word("Stats for"). ": " . $site_title);
		} else {
		  $this->displayPageTop("",word("Stats for"). ": ". $node->getName());
		}
		$this->openBlock();
		$stats = $node->getStats();
		$i=0;
		?>
		<table width="100%" cellpadding="5" cellspacing="0">
		   <?php if (distanceTo("artist",$node) !== false) { ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Artists"); ?>:
				</td>
				<td width="60%">
		   <?php echo $stats['total_artists']; ?>
				</td>
			</tr>
			<?php } ?>
		   <?php if (distanceTo("album",$node) !== false) { ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Albums"); ?>:
				</td>
				<td width="60%">
					<?php echo $stats['total_albums']; ?>
				</td>
			</tr>
				    <?php } ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Tracks"); ?>:
				</td>
				<td width="60%">
				<?php echo $stats['total_tracks']; ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Size"); ?>:
				</td>
				<td width="60%">
				    <?php echo $stats['total_size_str']; ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Length"); ?>:
				</td>
				<td width="60%">
				    <?php echo $stats['total_length_str']; ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Plays"); ?>:
				</td>
				<td width="60%">
				    <?php echo $node->getPlaycount(); ?>
				</td>
			</tr><?php /* ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Downloads"); ?>:
				</td>
				<td width="60%">
				    <?php echo $node->getDownloadCount(); ?>
				</td>
			</tr><?php */ ?>
				    <?php if (distanceTo("artist",$node) !== false) { ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Most Played Artist"); ?>:
				</td>
				<td width="60%">
				    <?php $a = $node->getMostPlayed("nodes",distanceTo("artist",$node),1);
		if (sizeof($a) > 0) { echo $a[0]->getName(); } ?>
				</td>
			</tr>
				   <?php } ?>
		<?php if (distanceTo("album",$node) !== false) { ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Most Played Album"); ?>:
				</td>
				<td width="60%">
				    <?php $a = $node->getMostPlayed("nodes",distanceTo("album",$node),1);
			if (sizeof($a) > 0) { 
			  if ($node->getPType() != "artist") {
			    echo getInformation($a[0],"artist") . " - " . $a[0]->getName(); 
			  }
			  else {
			    echo $a[0]->getName();
			  }
			} ?>
				</td>
			</tr>
				    <?php   } ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Most Played Track"); ?>:
				</td>
				<td width="60%">
				    <?php $a = $node->getMostPlayed("tracks",-1,1);
		if (sizeof($a) > 0) { 
		  if ($node->getPType() != "artist") {
		    echo getInformation($a[0],'artist') . " - " . $a[0]->getName(); 
		  } else {
		    echo $a[0]->getName();
		  }
		} ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Average Track Length"); ?>:
				</td>
				<td width="60%">
					<?php echo convertSecMins($stats['avg_length']); ?>
				</td></tr>
<tr class="<?php echo $row_colors[$i]; $i = 1 - $i; ?>">
<td width="40%">
				    <?php echo word("Average Bitrate"); ?>:
</td>
<td width="60%">
<?php
				    echo round($stats['avg_bitrate'],0); 
?>
</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Average Year"); ?>:
				</td>
				<td width="60%">
					<?php echo round($stats['avg_year'],0); ?>
				</td>
			</tr>
		</table>
		<br><center>
		<?php $this->closeButton(); ?>
		</center>
		<?php
		
		// Now let's get the stats
		
		
		$this->closeBlock();
	}
	
	/**
	* Displays the full top played list
	* 
	* @author Ross Carlson
	* @version 01/27/05
	* @since 01/27/05
	* @param $node The node we are looking at
	*/
	function displayTopStuff($node){
		global $img_tiny_play, $album_name_truncate, $root_dir; 
		
		$display = new jzDisplay();
		
		// First let's display the top of the page and open the main block
		$title = "Top ";
		switch ($_GET['tptype']){
			case "played-albums":
				$limit = 50;
				$title .= $limit. " ". word("Played Albums");
				$type = "album";
				$func = "getMostPlayed";
			break;
			case "played-artists":
				$limit = 50;
				$title .= $limit. " ". word("Played Artists");
				$type = "artist";
				$func = "getMostPlayed";
			break;
		        case "played-tracks":
				$limit = 50;
				$title .= $limit. " ". word("Played Tracks");
				$type = "track";
				$func = "getMostPlayed";
			break;
			case "downloaded-albums":
				$limit = 50;
				$title .= $limit. " ". word("Downloaded Albums");
				$type = "album";
				$func = "getMostDownloaded";
			break;
			case "new-albums":
				$limit = 100;
				$title .= $limit. " ". word("New Albums");
				$type = "album";
				$func = "getRecentlyAdded";
			break;
		        case "new-artists":
		                $limit = 100;
				$title .= $limit. " ". word("New Artists");
				$type = "artist";
				$func = "getRecentlyAdded";
			break;
		        case "new-tracks":
				$limit = 100;
				$title .= $limit. " ". word("New Tracks");
				$type = "track";
				$func = "getRecentlyAdded";
			break;
		case "recentplayed-albums":
		  $limit = 50;
		  $title .= $limit. " ". word("Played Albums");
		  $type = "album";
		  $func = "getRecentlyPlayed";
		  break;
		case "recentplayed-artists":
		  $limit = 50;
		  $title .= $limit. " ". word("Played Artists");
		  $type = "artist";
		  $func = "getRecentlyPlayed";
		  break;
		case "recentplayed-albums":
		  $limit = 50;
		  $title .= $limit. " ". word("Played Albums");
		  $type = "album";
		  $func = "getRecentlyPlayed";
		  break;
		case "recentplayed-tracks":
		  $limit = 50;
		  $title .= $limit. " ". word("Played Tracks");
		  $type = "track";
		  $func = "getRecentlyPlayed";
		  break;
		case "toprated-artists":
		  $limit = 50;
		  $title .= $limit. " ". word("Rated Artists");
		  $type = "artist";
		  $func = "getTopRated";
		  break;
		case "toprated-albums":
		  $limit = 50;
		  $title .= $limit. " ". word("Rated Albums");
		  $type = "album";
		  $func = "getTopRated";
		  break;
		case "topviewed-artists":
		  $limit = 50;
		  $title .= $limit. " ". word("Viewed Artists");
		  $type = "artist";
		  $func = "getMostViewed";
		  $showCount = "view";
		  break;



		}
		$this->displayPageTop("",$title);
		$this->openBlock();
		
		// Now let's get the recently added items
		if ($type == "track") {
		  $retType = "tracks";
		} else {
		  $retType = "nodes";
		}
		$recent = $node->$func($retType,distanceTo($type,$node),$limit);
		
		// Now let's loop through the results
		for ($i=0;$i<count($recent);$i++){
			// Now let's create our node and get the properties
			$item = $recent[$i];
			$album = $item->getName();
			$parent = $item->getParent();
			$artist = $parent->getName();
			
			// Now let's create our links
			$albumArr['jz_path'] = $item->getPath("String");
			$artistArr['jz_path'] = $parent->getPath("String");
			
			// Now let's create our short names
			$artistTitle = returnItemShortName($artist,$album_name_truncate);
			$albumTitle = returnItemShortName($album,$album_name_truncate);													
			
			// Now let's display it
			echo "<nobr>";
			$display->playLink($item, $img_tiny_play, $album);
			
			// Now let's set the hover code
			$innerOver = "";
			if (($art = $item->getMainArt()) <> false) {
				$innerOver .= $display->returnImage($art,$item->getName(),75,75,"limit",false,false,"left","3","3");
			}
			$desc_truncate = 200;
			$desc = $item->getDescription();
			$innerOver .= $display->returnShortName($desc,$desc_truncate);
			if (strlen($desc) > $desc_truncate){
				$innerOver .= "...";
			}
			$innerOver = str_replace('"',"",$innerOver);
			$innerOver = str_replace("'","",$innerOver);
			
			// Now let's return our tooltip													
			$capTitle = $artist. " - ". $album;
			$overCode = $display->returnToolTip($innerOver, $capTitle);
			echo ' <a onClick="opener.location.href=\''. urlize($albumArr) . '\';window.close();" '. $overCode. 'href="javascript:void()">'. $albumTitle;	
			$cval = false;
			// TODO: showCount values can be:
			// view,dowload,play
			if ($showCount == "view") {
			  $cval = $item->getViewCount();
			} else {
			  $cval = $item->getPlayCount();
			}
			if ($cval !== false && $cval <> 0){
				echo ' ('. $cval. ')';
			}
			echo "</a><br>";
			// Now let's set the hover code
			//echo ' <a title="'. $artist. ' - '. $album. '" href="'. urlize($albumArr). '">'. $albumTitle. '</a> ('. $albumPlayCount. ')';
			//echo "<br>";
			echo "</nobr>";
			flushdisplay();
		}
		
		$this->closeBlock();
	}


  /*
   * Pulls the user settings from POST to a settings array.
   * @author Ben Dodson
   * @since 12/7/05
   * @version 12/7/05
   **/
  function userPullSettings() {
    $settings = array();
    
    $settings['language'] = $_POST['usr_language'];		 
    $settings['theme'] = $_POST['usr_theme'];
    $settings['frontend'] = $_POST['usr_interface'];      
    $settings['home_dir'] = $_POST['home_dir'];
    if (isset($_POST['home_read'])) {
      $settings['home_read'] = true;
    } else {
      $settings['home_read'] = false;
    }
    if (isset($_POST['home_admin'])) {
      $settings['home_admin'] = true;
    } else {
      $settings['home_admin'] = false;
    }
    if (isset($_POST['home_upload'])) {
      $settings['home_upload'] = true;
    } else {
      $settings['home_upload'] = false;
    }
    
    $settings['cap_limit'] = $_POST['cap_limit'];
    $settings['cap_duration'] = $_POST['cap_duration'];
    $settings['cap_method'] = $_POST['cap_method'];
    
    $settings['player'] = $_POST['player'];
    
    $settings['resample_rate'] = $_POST['resample'];
    
    if (isset($_POST['lockresample'])) {
      $settings['resample_lock'] = true;
    } else {
      $settings['resample_lock'] = false;
    }

    if (isset($_POST['view'])) {
      $settings['view'] = true;
    } else {
      $settings['view'] = false;
    }
    
    if (isset($_POST['stream'])) {
      $settings['stream'] = true;
    } else {
      $settings['stream'] = false;
    }
    
    if (isset($_POST['download'])) {
      $settings['download'] = true;
    } else {
      $settings['download'] = false;
    }
    
    if (isset($_POST['lofi'])) {
      $settings['lofi'] = true;
    } else {
      $settings['lofi'] = false;
    }
    
    if (isset($_POST['jukebox_admin'])) {
      $settings['jukebox_admin'] = true;
      $settings['jukebox'] = true;
    } else {
      $settings['jukebox_admin'] = false;
    }
    
    if (isset($_POST['jukebox_queue'])) {
      $settings['jukebox_queue'] = true;
      $settings['jukebox'] = true;
    } else {
      $settings['jukebox_queue'] = false;
    }
    
    
    if (isset($_POST['powersearch'])) {
      $settings['powersearch'] = true;
    } else {
      $settings['powersearch'] = false;
    }
    
    if (isset($_POST['admin'])) {
      $settings['admin'] = true;
    } else {
      $settings['admin'] = false;
    }
    
    if (isset($_POST['edit_prefs'])) {
      $settings['edit_prefs'] = true;
    } else {
      $settings['edit_prefs'] = false;
    }
    $settings['playlist_type'] = $_POST['pltype'];

		if (isset($_POST['fullname'])) {
      $settings['fullname'] = $_POST['fullname'];
    }
    
    if (isset($_POST['email'])) {
      $settings['email'] = $_POST['email'];
    }

    return $settings;
  }



  /*
   * Displays the user/template settings page
   * @param purpose: Why the function is being called:
   * One of: new|update|custom
   * @param settings: the preloaded settings
   * @author Ben Dodson
   **/

  function userManSettings($purpose, $settings = false, $subaction = false, $post = false) {
    global $jzSERVICES,$resampleRates,$include_path;
    $be = new jzBackend();
    $display = new jzDisplay();
    $url_array = array();
    $url_array['action'] = "popup";
    $url_array['ptype'] = "usermanager";
    if ($subaction === false) {
      $url_array['subaction'] = "handleclass";
    } else {
      $url_array['subaction'] = $subaction;
    }

    // Why PHP pisses me off.
    foreach ($settings as $k=>$v) {
      if ($v == "true") {
	$settings[$k] = true;
      } else if ($v == "false") {
	$settings[$k] = false;
      } else {
	$settings[$k] = $v;
      }
    }
      ?>
      <form method="POST" action="<?php echo urlize($url_array); ?>">
	 <input type="hidden" name="update_settings" value="true">
	 <?php 
	 if (is_array($post)) {
	   foreach ($post as $p => $v) {
	     echo '<input type="hidden" name="'.$p.'" value="'.$v.'">';
	   }
	 }
	?>
	 <table>
	 <?php if ($purpose != "custom") { ?>
	 <tr><td width="30%" valign="top" align="right">
	 <?php echo word("Template:"); ?>
	 </td><td width="70%">
	     <?php
	     if ($purpose == "new") {
	       ?>
	       <input name="classname" class="jz_input">
	       <?php
	     } else if ($purpose == "update") {
	       echo '<input type="hidden" name="classname" class="jz_input" value="'.$_POST['classname'].'">';
	       echo $_POST['classname'];
	     }
	   ?>
	     </td></tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr>
					   <?php } ?>
							<tr>
							<td width="30%" valign="top" align="right">
							<?php echo word("Interface"); ?>:
	       </td>
		   <td width="70%">
		   <?php
		   $overCode = $display->returnToolTip(word("INTERFACE_NOTE"), word("Default Interface"));
		 ?>
		   <select <?php echo $overCode; ?> name="usr_interface" class="jz_select" style="width:135px;">
			 <?php
			 // Let's get all the interfaces
			 $retArray = readDirInfo($include_path. "frontend/frontends","dir");
		    sort($retArray);
		    for($i=0;$i<count($retArray);$i++){
		      echo '<option ';
		      if ($settings['frontend'] == $retArray[$i]) { echo 'selected '; }
		      echo 'value="'. $retArray[$i]. '">'. $retArray[$i]. '</option>'. "\n";
		    }
		      ?>
			</select>
			</td>
			</tr>
			<tr>
			<td width="30%" valign="top" align="right">
			<?php echo word("Theme"); ?>:
			</td>
			<td width="70%">
			<?php
			$overCode = $display->returnToolTip(word("THEME_NOTE"), word("Default Theme"));
			 ?>
			<select <?php echo $overCode; ?> name="usr_theme" class="jz_select" style="width:135px;">
			<?php
			// Let's get all the interfaces
			$retArray = readDirInfo($include_path. "style","dir");
		    sort($retArray);
		    for($i=0;$i<count($retArray);$i++){
		      if ($retArray[$i] == "images"){continue;}
		      echo '<option ';
		      if ($settings['theme'] == $retArray[$i]) { echo 'selected '; }
		      echo 'value="'. $retArray[$i]. '">'. $retArray[$i]. '</option>'. "\n";
		    }
		      ?>
			</select>
			</td>
			</tr>
			<tr>
			<td width="30%" valign="top" align="right">
			<?php echo word("Language"); ?>:
			</td>
			<td width="70%">
			<?php
				$overCode = $display->returnToolTip(word("LANGUAGE_NOTE"), word("Default Language"));
			 ?>
			<select <?php echo $overCode; ?> name="usr_language" class="jz_select" style="width:135px;">
			<?php
			// Let's get all the interfaces
			$languages = getLanguageList();
		    for($i=0;$i<count($languages);$i++){
		      echo '<option ';
		      if ($languages[$i] == $settings['language']){echo ' selected '; }
		      echo 'value="'.$languages[$i]. '">'.$languages[$i]. '</option>'. "\n";
		    }
		      ?>
							</select>
							    </td>
							    </tr>
							    <tr>
							    <td width="30%" valign="top" align="right">
							    <?php echo word("Home Directory"); ?>:
							  </td>
							    <td width="70%">
								<?php
								$overCode = $display->returnToolTip(word("HOMEDIR_NOTE"), word("User Home Directory"));
								 ?>
							    <input <?php echo $overCode; ?> type="input" name="home_dir" class="jz_input" value="<?php echo $settings['home_dir']; ?>">
							    </td>
							    </tr>
							    <tr>
							    <td width="30%" valign="middle" align="right">
							    <?php echo word("Home Permissions"); ?>:
							  </td>
							    <td width="70%">
							    <br>
								<?php
									$overCode = $display->returnToolTip(word("HOMEREAD_NOTE"), word("Read Home Directory"));
									$overCode2 = $display->returnToolTip(word("HOMEADMIN_NOTE"), word("Admin Home Directory"));
									$overCode3 = $display->returnToolTip(word("HOMEUPLOAD_NOTE"), word("Home Directory Upload"));
								 ?>
							    <input <?php echo $overCode; ?> type="checkbox" name="home_read" class="jz_input" <?php if ($settings['home_read'] == true) { echo 'CHECKED'; } ?>> Read only from home directory<br>
							    <input <?php echo $overCode2; ?> type="checkbox" name="home_admin" class="jz_input" <?php if ($settings['home_admin'] == true) { echo 'CHECKED'; } ?>> Home directory admin<br>
							    <input <?php echo $overCode3; ?> type="checkbox" name="home_upload" class="jz_input" <?php if ($settings['home_upload'] == true) { echo 'CHECKED'; } ?>> Upload to home directory
							    <br><br>
							    </td>
							    </tr>
							    
							    <tr>
							    <td width="30%" valign="middle" align="right">
							    <?php echo word("User Rights"); ?>:
							  </td>
							    <td width="70%">
								<?php
									$overCode = $display->returnToolTip(word("VIEW_NOTE"), word("User can view media"));
									$overCode2 = $display->returnToolTip(word("STREAM_NOTE"), word("User can stream media"));
									$overCode3 = $display->returnToolTip(word("LOFI_NOTE"), word("User can access lo-fi tracks"));
									$overCode4 = $display->returnToolTip(word("DOWNLOAD_NOTE"), word("User can download"));
									$overCode5 = $display->returnToolTip(word("POWERSEARCH_NOTE"), word("User can power search"));
									$overCode6 = $display->returnToolTip(word("JUKEBOXQ_NOTE"), word("User can queue jukebox"));
									$overCode7 = $display->returnToolTip(word("JUKEBOXADMIN_NOTE"), word("User can admin jukebox"));
									$overCode8 = $display->returnToolTip(word("SITE_NOTE"), word("Site Admin"));
									$overCode9 = $display->returnToolTip(word("EDIT_NOTE"), word("Edit Preferences"));
								 ?>
							    <input <?php echo $overCode; ?> type="checkbox" name="view" class="jz_input" <?php if ($settings['view'] == true) { echo 'CHECKED'; } ?>> View
							    <input <?php echo $overCode2; ?> type="checkbox" name="stream" class="jz_input" <?php if ($settings['stream'] == true) { echo 'CHECKED'; } ?>> Stream
							    <input <?php echo $overCode3; ?> type="checkbox" name="lofi" class="jz_input" <?php if ($settings['lofi'] == true) { echo 'CHECKED'; } ?>> Lo-Fi<br>
							    <input <?php echo $overCode4; ?> type="checkbox" name="download" class="jz_input" <?php if ($settings['download'] == true) { echo 'CHECKED'; } ?>> Download
							    <input <?php echo $overCode5; ?> type="checkbox" name="powersearch" class="jz_input" <?php if ($settings['powersearch'] == true) { echo 'CHECKED'; } ?>> Power Search<br>
							    <input <?php echo $overCode6; ?> type="checkbox" name="jukebox_queue" class="jz_input" <?php if ($settings['jukebox_queue'] == true) { echo 'CHECKED'; } ?>> Jukebox Queue
							    <input <?php echo $overCode7; ?> type="checkbox" name="jukebox_admin" class="jz_input" <?php if ($settings['jukebox_admin'] == true) { echo 'CHECKED'; } ?>> Jukebox Admin<br>
							    <input <?php echo $overCode8; ?> type="checkbox" name="admin" class="jz_input" <?php if ($settings['admin'] == true) { echo 'CHECKED'; } ?>> Site Admin
						        <input <?php echo $overCode9; ?> type="checkbox" name="edit_prefs" class="jz_input" <?php if ($settings['edit_prefs'] == true) { echo 'CHECKED'; } ?>> Edit Prefs
							    <br><br>
							    </td>
							    </tr>
							    <tr>
								<td width="30%" valign="top" align="right">
							    <?php echo word("Playlist Type"); ?>:
								</td><td width="70%">
								<?php
								$overCode = $display->returnToolTip(word("PLAYLIST_NOTE"), word("Playlist Type"));
								 ?>
								<select <?php echo $overCode; ?> name="pltype" class="jz_select" style="width:135px;">
							 <?php
						 $list = $jzSERVICES->getPLTypes();
						foreach ($list as $p=>$desc) {
						  echo '<option value="' . $p . '"';
						  if ($p == $settings['playlist_type']) {
						    echo ' selected';
						  }
						  echo '>' . $desc . '</option>';
						} ?>
				    </select></td></tr>

							    <tr>
							    <td width="30%" valign="top" align="right">
							    <?php echo word("Resample Rate"); ?>:
							  </td>
					<td width="70%">
					<?php
						$overCode = $display->returnToolTip(word("RESAMPLE_NOTE"), word("Resample Rate"));
						$overCode2 = $display->returnToolTip(word("LOCK_NOTE"), word("Resample Rate Lock"));
					 ?>
						<select <?php echo $overCode; ?> name="resample" class="jz_select" style="width:50px;">
							<option value="">-</option>
							<?php
								// Now let's create all the items based on their settings
								$reArr = explode("|",$resampleRates);
								for ($i=0; $i < count($reArr); $i++){
									echo '<option value="'. $reArr[$i]. '"';
									if ($settings['resample_rate'] == $reArr[$i]) {
									  echo ' selected';
									}
									echo '>'. $reArr[$i]. '</option>'. "\n";
								}
							?>
						</select> 
						    <input <?php echo $overCode2; ?> type="checkbox" name="lockresample" class="jz_input" <?php if ($settings['resample_lock'] == true) { echo 'CHECKED'; } ?>> <?php echo word('Locked'); ?>
					</td>
				</tr>
				<tr>
					<td width="30%" valign="top" align="right">
						<?php echo word("External Player"); ?>:
					</td>
					<td width="70%">
						<?php
						 $overCode = $display->returnToolTip(word("PLAYER_NOTE"), word("External Player"));
						?>
						<select <?php echo $overCode; ?> name="player" class="jz_select" style="width:135px;">
							<option value=""> - </option>
							<?php
								// Let's get all the interfaces
								$retArray = readDirInfo($include_path. "services/services/players","file");
								sort($retArray);
								for($i=0;$i<count($retArray);$i++){
									if (!stristr($retArray[$i],".php") and !stristr($retArray[$i],"qt.")){continue;}
									$val = substr($retArray[$i],0,-4);
									echo '<option value="'. $val. '"';
									if ($settings['player'] == $val) {
									  echo ' selected';
									}
									echo '>'. $val. '</option>'. "\n";
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td width="30%" valign="top" align="right">
						<?php echo word("Playback Limit"); ?>:
					</td>
					<td width="70%"><td></tr><tr><td></td><td>
					    <table><tr><td>
					    
						<?php
					    echo word("Limit:"); 
								echo '</td><td>';
					                        $overCode = $display->returnToolTip(word("Sets a streaming limit for users based on the size or number of songs played."), word("Playback Limit"));
								$cap_limit = $settings['cap_limit'];
								if (isNothing($cap_limit)) { $cap_limit = 0; }
						?>
					        <input <?php echo $overCode; ?> name="cap_limit" class="jz_select" style="width:35px;" value="<?php echo $cap_limit; ?>">
					</td></tr>
                                        <tr><td>					    
						<?php
					    echo word("Method:"); 
								echo '</td><td>';
					                        $overCode = $display->returnToolTip(word("Sets the method for limiting playback"), word("Limiting method"));
								$cap_method = $settings['cap_method'];
						?>
					        <select name="cap_method" class="jz_select" <?php echo $overCode; ?>>
					       <option value="size"<?php if ($cap_method == "size") { echo ' selected'; } ?>><?php echo word('Size (MB)');?></option>
					       <option value="number"<?php if ($cap_method == "number") { echo ' selected'; } ?>><?php echo word('Number');?></option>
					</td></tr>
                                        <tr><td>
					    
						<?php
					    echo word("Duration:"); 
								echo '</td><td>';
					                        $overCode = $display->returnToolTip(word("How long the limit lasts, in days."), word("Limit duration"));
								$cap_duration = $settings['cap_duration'];
								if (isNothing($cap_duration)) { $cap_duration = 30; }
						?>
					        <input <?php echo $overCode; ?> name="cap_duration" class="jz_select" style="width:35px;" value="<?php echo $cap_duration; ?>">
					</td></tr>
										  </table>
				</tr>
								
				
				<tr>
					<td width="30%" valign="top">
					</td>
					<td width="70%">
					<input type="submit" name="handlUpdate" value="<?php echo word("Save"); ?>" class="jz_submit">
					</td>
				</tr>
						    </table>
<?php
  }



function userPreferences() {
  global $include_path, $jzUSER, $jzSERVICES, $cms_mode, $enable_audioscrobbler, $as_override_user, $as_override_all;
	
	$this->displayPageTop("",word("User Preferences"));
	$this->openBlock();
	// Now let's show the form for it
	if (isset($_POST['update_settings'])) {
	  if (strlen($_POST['field1']) > 0 && $_POST['field1'] != "jznoupd") {
	    if ($_POST['field1'] == $_POST['field2']) {
	      // update the password:
	      $jzUSER->changePassword($_POST['field1']);
	    }
	  }

	  $arr = array();
	  $arr['email'] = $_POST['email'];
	  $arr['fullname'] = $_POST['fullname'];
	  $arr['frontend'] = $_POST['def_interface'];
	  $arr['theme'] = $_POST['def_theme'];
	  $arr['language'] = $_POST['def_language'];
	  $arr['playlist_type'] = $_POST['pltype'];
		$arr['asuser'] = $_POST['asuser'];
		$arr['aspass'] = $_POST['aspass'];
	  $jzUSER->setSettings($arr);

	  if (isset($_SESSION['theme'])) {
	    unset($_SESSION['theme']);
	  }
	  if (isset($_SESSION['frontend'])) {
	    unset($_SESSION['frontend']);
	  }
	  if (isset($_SESSION['language'])) {
	    unset($_SESSION['language']);
	  }
		
		?>
			<script language="javascript">
			opener.location.reload(true);
			-->
			</SCRIPT>
		<?php

    //$this->closeWindow(true);
	  //return;
	}

	$url_array = array();
	$url_array['action'] = "popup";
	$url_array['ptype'] = "preferences";	
	echo '<form action="'. urlize($url_array). '" method="POST">';
	?>
	<table width="100%" cellpadding="3">
<?php	if ($cms_mode == "false") { ?>
		<tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Password"); ?>:
			</td>
			<td width="70%">
				<input type="password" name="field1" class="jz_input" value="jznoupd"><br>
				<input type="password" name="field2" class="jz_input" value="jznoupd">
			</td>
		</tr><?php } else { ?> <input type="hidden" name="field1" value="jznoupd"> <?php } ?>
		<tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Full Name"); ?>:
			</td>
			<td width="70%">
				<input name="fullname" class="jz_input" value="<?php echo $jzUSER->getSetting('fullname'); ?>">
			</td>
		</tr>
		<tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Email"); ?>:
			</td>
			<td width="70%">
				<input name="email" class="jz_input" value="<?php echo $jzUSER->getSetting('email'); ?>">
			</td>
		</tr>
		
		
		<?php
			// Did they enable audioscrobbler?
			if ($enable_audioscrobbler == "true" and ($as_override_user == "" or $as_override_all == "false")){
				?>
				<tr>
					<td width="30%" valign="top" align="right">
						<?php echo word("AS User"); ?>:
					</td>
					<td width="70%">
						<input name="asuser" class="jz_input" value="<?php echo $jzUSER->getSetting('asuser'); ?>">
					</td>
				</tr>
				<tr>
					<td width="30%" valign="top" align="right">
						<?php echo word("AS pass"); ?>:
					</td>
					<td width="70%">
						<input type="password" name="aspass" class="jz_input" value="<?php echo $jzUSER->getSetting('aspass'); ?>">
					</td>
				</tr>
				<?php
			}
		?>
		
		
		<tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Interface"); ?>:
			</td>
			<td width="70%">
				<select name="def_interface" class="jz_select" style="width:135px;">
					<?php
						// Let's get all the interfaces
						$retArray = readDirInfo($include_path. "frontend/frontends","dir");
						sort($retArray);
						for($i=0;$i<count($retArray);$i++){
							echo '<option ';
							if ($retArray[$i] == $jzUSER->getSetting("frontend")){echo ' selected '; }
							echo 'value="'. $retArray[$i]. '">'. $retArray[$i]. '</option>'. "\n";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Theme"); ?>:
			</td>
			<td width="70%">
				<select name="def_theme" class="jz_select" style="width:135px;">
					<?php
						// Let's get all the interfaces
						$retArray = readDirInfo($include_path. "style","dir");
						sort($retArray);
						for($i=0;$i<count($retArray);$i++){
							if ($retArray[$i] == "images"){continue;}
							echo '<option ';
							if ($retArray[$i] == $jzUSER->getSetting('theme')) {echo ' selected '; }
							echo 'value="'. $retArray[$i]. '">'. $retArray[$i]. '</option>'. "\n";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Language"); ?>:
			</td>
			<td width="70%">
				<select name="def_language" class="jz_select" style="width:135px;">
					<?php
						// Let's get all the interfaces
			                        $languages = getLanguageList();
						for($i=0;$i<count($languages);$i++){
						  echo '<option ';
							if ($languages[$i] == $jzUSER->getSetting('language')){echo ' selected '; }
							echo 'value="'.$languages[$i].'">'.$languages[$i]. '</option>'. "\n";
						}
					?>
				</select>
			</td>
		</tr>
				    <tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Playlist Type"); ?>:
			</td>
			<td width="70%">
				<select name="pltype" class="jz_select" style="width:135px;">
				    <?php
				    $list = $jzSERVICES->getPLTypes();
						foreach ($list as $p=>$desc) {
						  echo '<option value="' . $p . '"';
						  if ($jzUSER->getSetting('playlist_type') == $p) {
						    echo " selected";
						  }
						  echo '>' . $desc . '</option>';
						} ?>
				    </select>
			</td>
		</tr>
	</table>
	<br><center>
		<input type="submit" name="update_settings" value="<?php echo word("Update Settings"); ?>" class="jz_submit">
		<?php $this->closeButton(); ?> 
	</center>
	<br>
	</form>
	<?php

  $this->closeBlock();
}
	
	/**
	* Shows the documentation system
	* 
	* @author Ross Carlson
	* @version 01/19/05
	* @since 01/19/05
	*/
	function showDocs(){
		global $root_dir, $jz_lang_file;
		
		// Let's refresh
		echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL='. $root_dir. "/docs/". $jz_lang_file. "/index.html". '">';
	}
	

	/**
	* Rates the currently viewed item
	* 
	* @author Ross Carlson
	* @version 01/19/05
	* @since 01/19/05
	* @param $node The node that we are viewing
	*/
	function userRateItem($node){

		// Let's see if they rated it?
		if (isset($_POST['itemRating'])){
			// Ok, let's rate and close
			$node->addRating($_POST['itemRating']);
			$this->closeWindow(true);
		}
		
		// First let's display the top of the page and open the main block
		$this->displayPageTop("",word("Rate Item"). "<br>". $node->getName());
		$this->openBlock();
		
		// Now let's setup the values
		$url_array = array();
		$url_array['jz_path'] = $node->getPath("String");
		$url_array['action'] = "popup";
		$url_array['ptype'] = "rateitem";
		
		echo '<form action="'. urlize($url_array). '" method="POST">';
		echo '<center><br>'. word("Rating"). ': ';
		echo '<select name="' . jz_encode('itemRating') . '" class="jz_select">';
		for ($i=5; $i > 0;){
			echo '<option value="'. jz_encode($i). '">'. $i. '</option>';
			$i=$i-.5;
		}
		echo '</select>';
		echo '<br><br><input type="submit" name="' . jz_encode('submitRating') . '" value="'. word("Rate Item"). '" class="jz_submit">';
		echo " ";
		$this->closeButton();
		echo '</center>';
		echo '</form>';
		
		// Now let's close out
		$this->closeBlock();				
	}

	/**
	* Removes the selected node to the featured list
	* 
	* @author Ross Carlson
	* @version 01/19/05
	* @since 01/19/05
	* @param $node The node that we are viewing
	*/
	function removeFeatured($node){
		// First let's display the top of the page and open the main block
		$this->displayPageTop("",word("Removing from featured"). "<br>". $node->getName());
		$this->openBlock();
		
		// Now let's add this puppy
		$node->removeFeatured();
		
		// Let's display status
		echo "<br>". word("Remove complete!");
		
		// Now let's close out
		$this->closeBlock();		
		flushDisplay();
		
		sleep(3);
		$this->closeWindow(true);
	}
	
	/**
	* Adds the selected node to the featured list
	* 
	* @author Ross Carlson
	* @version 01/19/05
	* @since 01/19/05
	* @param $node The node that we are viewing
	*/
	function addToFeatured($node){
		
		// First let's display the top of the page and open the main block
		$this->displayPageTop("",word("Adding to featured"). "<br>". $node->getName());
		$this->openBlock();
		
		// Now let's add this puppy
		$node->addFeatured();
		
		// Let's display status
		echo "<br>". word("Add complete!");
		
		// Now let's close out
		$this->closeBlock();		
		flushDisplay();
		
		sleep(3);
		$this->closeWindow(true);
	}
	
	/**
	* Displays the read more information on an artist from a popup
	* 
	* @author Ross Carlson
	* @version 01/19/05
	* @since 01/19/05
	* @param $node The node that we are viewing
	*/
	function displayReadMore($node){
		global $cms_mode;
	
		// Let's setup our objects
		$display = new jzDisplay();
		
		// First let's display the top of the page and open the main block
		$this->displayPageTop("",word("Profile"). ": ". $node->getName());
		$this->openBlock();
		
		// Now let's display the artist image and short description
		if (($art = $node->getMainArt("200x200")) <> false) {
			$display->image($art,$node->getName(),200,200,"limit",false,false,"left","5","5");
		}
		if ($cms_mode == "false"){
			echo '<span class="jz_artistDesc">';
		}
		echo fixAMGUrls($node->getDescription());
		if ($cms_mode == "false"){
			echo '</span>';		
		}

		$this->closeBlock();
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

	/**
	* Scans the users system for newly added media
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	* @param $node The node we are looking at
	*/
	function scanForMedia($node){
	  global $backend;

		if ($backend == "id3-cache" || $backend == "id3-database") {
		  $node = new jzMediaNode(); // Root only, just to be sure...
		}

		// First let's display the top of the page and open the main block
		$title = $node->getName();
		if ($title == ""){$title = word("Root Level"); }
		$this->displayPageTop("","Scanning for new media in: ". $title);
		$this->openBlock();
		
		// Let's show them the form
		if (!isset($_POST['edit_scan_now'])){
			$url_array = array();
			$url_array['action'] = "popup";
			$url_array['ptype'] = "scanformedia";
			$url_array['jz_path'] = $_GET['jz_path'];
			$i=0;
			?>
			<form action="<?php echo urlize($url_array); ?>" method="post">
			   <?php
			   if (!($backend == "id3-cache" || $backend == "id3-database")) {
			     ?>
				<input name="edit_scan_where" value="only" checked type="radio"> <?php echo word("This level only"); ?><br>
				<input name="edit_scan_where" value="all" type="radio"> <?php echo word("All sub items (can be very slow)"); ?><br><br>
			     <?php
			   } else {
			     ?>
 				<input name="edit_scan_where" value="all" type="hidden">
                             <?php
			   }
			  ?>
                                <input name="edit_force_scan" value="true" type="checkbox"> <?php echo word("Ignore file modification times (slow)"); ?><br>
				<br>
				&nbsp; &nbsp; &nbsp; <input type="submit" name="edit_scan_now" value="<?php echo word("Scan Now"); ?>" class="jz_submit">
			</form>		
			<?php
			exit();
		}
		
		// Ok, let's do it...		
		echo "<b>". word("Scanning"). ":</b>";
		echo '<div id="importStatus"></div>';
		?>
		<script language="javascript">
		d = document.getElementById("importStatus");
		-->
		</SCRIPT>
		<?php
		set_time_limit(0);
		flushdisplay();
		
		// Now how to scan?
		if ($_POST['edit_scan_where'] == "only"){
			$recursive = false;
		} else {
			$recursive = true;
		}

		// Let's scan...
		if (isset($_POST['edit_force_scan'])) {
		  $force_scan = true;
		} else {
		  $force_scan = false;
		}

		updateNodeCache($node,$recursive,true,$force_scan);
		
		echo "<br><br><b>". word("Complete!"). "</b>";
		$this->closeBlock();
		flushdisplay();
		
		// Now let's close out
		echo "<br><br><center>";
		$this->closeButton();
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
	
	// This function will display the complete list of Genres
	// Added 4.6.04 by Ross Carlson
	function displayAllGenre(){
	  global $this_page;
		global $row_colors, $web_root, $root_dir;
		
		// Let's display the top of our page	
		$this->displayPageTop("",word("All Genres"));
		$this->openBlock();
		
		echo "<center>";

		// Now let's give them a list of choices
				
		// Let's give them a search bar.
		
		$url_array = array();
		$url_array['action'] = "popup";
		$url_array['ptype'] = "genre";

		$search = isset($_POST['query']) ? $_POST['query'] : "";
		echo "<form action=\"".urlize($url_array)."\" method=\"post\" name=\"selectGenre\">";
		echo "<input type=\"text\" class=\"jz_input\" size=\"18\" value=\"$search\" name=\"query\">";
		echo '<input class="jz_submit" type="submit" name="'.jz_encode('lookup').'" value="'. word("Go"). '">';
		echo "</form><br>";
		// That's all for the search bar.
		
		$i=97; $c=2;
		

		$url_array['g'] = "#";
		echo '<a href="'. urlize($url_array).'">1-10</a> | ';
		while ($i < 123){
		  $url_array['g'] = chr($i);
			echo '<a href="'. urlize($url_array). '">'. strtoupper(chr($i)). '</a>';
			if ($c % 9 == 0){ echo "<br>"; } else { echo " | "; }
			$i++;
			$c++;
		}
		echo "<br>";
		
		// Now let's setup our form

		echo '<form action="'. urlize($url_array). '" method="post" name="selectGenre">';
		// Now let's set so we'll know where to go back to
		echo '<input type="hidden" name="return" value="'. $_GET['return']. '">';

		// See if they ran a search.
		if ($search != "") {
			// Now let's get all the genres from our cache file
			$root = &new jzMediaNode();
			$matches = $root->search($search, "nodes", distanceTo("genre"));
			// arrayify search.
			echo '<select name="' . jz_encode("chosenPath") . '"size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				echo '<option value="'. htmlentities(jz_encode($matches[$i]->getPath("String"))).'">'. $matches[$i]->getName();
			}
			echo "</select>";
		}
		// End search stuff.
		
		// Now let's see if they wanted a letter or not
		else if (isset($_GET['g'])){
			// Now let's get all the artists from our cache file
			$root = &new jzMediaNode();
			$matches = $root->getAlphabetical($_GET['g'],"nodes",distanceTo("genre"));
			echo '<select name="' . jz_encode("chosenPath") . '" size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				echo '<option value="'. htmlentities(jz_encode($matches[$i]->getPath("String"))).'">'. $matches[$i]->getName();
			}
			echo '</select>';
		}
		echo "</form>";
		echo "<br><br>";
		$this->closeButton();
		echo "</center>";
		
		$this->closeBlock();
		exit();
	}
		
	function displayAllTrack(){
	  global $this_page;
		global $row_colors, $web_root, $root_dir,$embedded_player,$jzUSER;
		
		// Let's display the top of our page	
		$this->displayPageTop("",word("All Tracks"));
		$this->openBlock();
		
		echo "<center>";

		// Now let's give them a list of choices
				
		// Let's give them a search bar.
		
		$url_array = array();
		$url_array['action'] = "popup";
		$url_array['ptype'] = "track";

		$search = isset($_POST['query']) ? $_POST['query'] : "";
		echo "<form action=\"".urlize($url_array)."\" method=\"post\" name=\"selectTrack\">";
		echo "<input type=\"text\" class=\"jz_input\" size=\"18\" value=\"$search\" name=\"query\">";
		echo '<input class="jz_submit" type="submit" name="'.jz_encode('lookup').'" value="'. word("Go"). '">';
		echo "</form><br>";
		// That's all for the search bar.
		
		$i=97; $c=2;
		

		$url_array['g'] = "#";
		echo '<a href="'. urlize($url_array).'">1-10</a> | ';
		while ($i < 123){
		  $url_array['g'] = chr($i);
			echo '<a href="'. urlize($url_array). '">'. strtoupper(chr($i)). '</a>';
			if ($c % 9 == 0){ echo "<br>"; } else { echo " | "; }
			$i++;
			$c++;
		}
		echo "<br>";
		
		// Now let's setup our form

		echo '<form action="'. urlize($url_array). '" method="post" name="selectTrack"';
		if (checkPermission($jzUSER,'embedded_player') === true) {
		  echo ' target="embeddedPlayer"';
		}
		echo '>';
		// Now let's set so we'll know where to go back to
		echo '<input type="hidden" name="return" value="'. $_GET['return']. '">';

		// See if they ran a search.
		if ($search != "") {
			// Now let's get all the genres from our cache file
			$root = &new jzMediaNode();
			$matches = $root->search($search, "tracks", -1);
			// arrayify search.
			echo '<input type="hidden" name="' . jz_encode('jz_type') . '" value="' . jz_encode('track') . '"';
			if (checkPermission($jzUSER,'embedded_player') === true) {
			  echo '<select name="' . jz_encode("chosenPath") . '"size="18" class="jz_select" style="width: 200px" onChange="openMediaPlayer('."''".',300,150); submit()">';
			} else {
			echo '<select name="' . jz_encode("chosenPath") . '"size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			}
			for ($i=0; $i < count($matches); $i++){
				echo '<option value="'. htmlentities(jz_encode($matches[$i]->getPath("String"))).'">'. $matches[$i]->getName();
			}
			echo "</select>";
		}
		// End search stuff.
		
		// Now let's see if they wanted a letter or not
		else if (isset($_GET['g'])){
			// Now let's get all the artists from our cache file
			$root = &new jzMediaNode();
			$matches = $root->getAlphabetical($_GET['g'],"tracks",-1);
			echo '<input type="hidden" name="' . jz_encode('jz_type') . '" value="' . jz_encode('track') . '"';
			echo '<select name="' . jz_encode("chosenPath") . '" size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				echo '<option value="'. htmlentities(jz_encode($matches[$i]->getPath("String"))).'">'. $matches[$i]->getName();
			}
			echo '</select>';
		}
		echo "</form>";
		echo "<br><br>";
		$this->closeButton();
		echo "</center>";
		
		$this->closeBlock();
		exit();
	}

	// This function will display the complete list of artists
	function displayAllArtists(){
		global $row_colors, $web_root, $root_dir, $this_page;
		
		// Let's display the top of our page	
		$this->displayPageTop("",word("All Artists"));
		$this->openBlock();
		
		echo "<center>";
		
		// Now let's give them a list of choices

		// Let's give them a search bar.
		$ua = array();
		$ua['action'] = "popup";
		$ua['ptype'] = "artist";

		$search = isset($_POST['query']) ? $_POST['query'] : "";
		echo "<form action=\"" . urlize($ua) . "\" method=\"post\" name=\"selectArtist\">";
		echo "<input type=\"text\" class=\"jz_input\" size=\"18\" value=\"$search\" name=\"query\">";
		echo '<input class="jz_submit" type="submit" name="'.jz_encode('lookup').'" value="'. word("Go"). '">';
		echo "</form><br>";
		// That's all for the search bar.

		$i=97; $c=2;
		$ua['i'] = "#";
		echo '<a href="'. urlize($ua).'">1-10</a> | ';
		while ($i < 123){
		  $ua['i'] = chr($i);
			echo '<a href="'. urlize($ua). '">'. strtoupper(chr($i)). '</a>';
			if ($c % 9 == 0){ echo "<br>"; } else { echo " | "; }
			$i++;
			$c++;
		}
		echo "<br>";
		
		// Now let's setup our form
		echo '<form action="'. urlize($ua). '" method="post" name="selectArtist">';
		// Now let's set so we'll know where to go back to
		if (isset($_GET['return'])) {
		  echo '<input type="hidden" name="return" value="'. $_GET['return']. '">';
		}
		
		// See if they ran a search.
		if ($search != "") {
			// Now let's get all the genres from our backend
			$root = &new jzMediaNode();
			$matches = $root->search($search, "nodes", distanceTo("artist"));
			// arrayify search.
			echo '<select name="' . jz_encode("chosenPath") . '"size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				echo '<option value="'. jz_encode($matches[$i]->getPath("String")).'">'. $matches[$i]->getName();
			}
			echo "</select>";
		}
		// End search stuff.
		
		// Now let's see if they wanted a letter or not
		else if (isset($_GET['i'])){
			// Now let's get all the artists from our cache file
			$root = &new jzMediaNode();
			$matches = $root->getAlphabetical($_GET['i'],"nodes",distanceTo("artist"));
			echo '<select name="' . jz_encode("chosenPath") . '"size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				echo '<option value="'. jz_encode($matches[$i]->getPath("String")).'">'. $matches[$i]->getName();
			}
			echo '</select>';
		}
		echo "</form>";
		echo "<br><br>";
		$this->closeButton();
		echo "</center>";
		
		$this->closeBlock();
		exit();
	}
	
	// This function will display the complete list of artists
	function displayAllAlbums(){
		global $row_colors, $web_root, $root_dir, $directory_level;
		
		// Let's display the top of our page	
		$this->displayPageTop("",word("All Albums"));
		$this->openBlock();
		
		echo "<center>";
		
		// Now let's give them a list of choices
		$ua = array();
		$ua['action'] = "popup";
		$ua['ptype'] = "album";

		
		// Let's give them a search bar.
		$search = isset($_POST['query']) ? $_POST['query'] : "";
		echo "<form action=\"".urlize($ua)."\" method=\"post\" name=\"selectAlbum\">";
		echo "<input type=\"text\" class=\"jz_input\" size=\"18\" value=\"$search\" name=\"query\">";
		echo '<input class="jz_submit" type="submit" name="'.jz_encode('lookup').'" value="'. word("Go"). '">';
		echo "</form><br>";
		// That's all for the search bar.
		
		$i=97; $c=2;
		$ua['a'] = "#";
		echo '<a href="'. urlize($ua).'">1-10</a> | ';
		while ($i < 123){
		  $ua['a'] = chr($i);
			echo '<a href="'. urlize($ua). '">'. strtoupper(chr($i)). '</a>';
			if ($c % 9 == 0){ echo "<br>"; } else { echo " | "; }
			$i++;
			$c++;
		}
		echo "<br>";
		
		// Now let's setup our form
		echo '<form action="'. urlize($ua) . '" method="post" name="selectAlbum">';
		// Now let's set so we'll know where to go back to
		echo '<input type="hidden" name="return" value="'. $_GET['return']. '">';
		
		// See if they ran a search.
		if ($search != "") {
			// Now let's get all the genres from our cache file
			$root = &new jzMediaNode();
			$matches = $root->search($search, "nodes", distanceTo("album"));

			// arrayify search.
			echo '<select name="' . jz_encode("chosenPath") . '"size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				$parent = $matches[$i]->getNaturalParent();
				echo '<option value="'. jz_encode(htmlentities($matches[$i]->getPath("String"))).'">'. $matches[$i]->getName() . " (" . $parent->getName() . ")";
			}
			echo "</select>";
		}
		// End search stuff.
		
		// Now let's see if they wanted a letter or not
		else if (isset($_GET['a'])){
			// Now let's get all the artists from our cache file
			$root = &new jzMediaNode();
			$matches = $root->getAlphabetical($_GET['a'],"nodes",distanceTo("album"));
			echo '<select name="' . jz_encode("chosenPath") . '"size="15" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				$parent = $matches[$i]->getNaturalParent();
				echo '<option value="'. jz_encode(htmlentities($matches[$i]->getPath("String"))).'">'. $matches[$i]->getName() . " (" . $parent->getName() . ")";
			}
			echo '</select>';
		}
		echo "</form>";
		echo "<br><br>";
		$this->closeButton();
		echo "</center>";

		$this->closeBlock();
		exit();
	}
		
	/**
	* Displays the configuration system
	* 
	* @author Ben Dodson
	* @version 09/15/04
	* @since 09/15/04
	*/
	function displayinstMozPlug(){
		global $web_root, $root_dir, $site_title;
		
		// Let's display the top of our page	
		$this->displayPageTop();
		
		// Now let's execute the plugin creation
		include('extras/mozilla.php');
		makePlugin();
		
		// Now let's set the JavaScript that will actually install the plugin
		$weblink = "http://".$_SERVER['HTTP_HOST']."${root_dir}";
		
		?>
			<script>
				function addEngine()
				{
					if ((typeof window.sidebar == "object") &&
					  (typeof window.sidebar.addSearchEngine == "function"))
					{
						window.sidebar.addSearchEngine(
							"<?php echo $weblink; ?>/data/jinzora.src",
							"<?php echo $weblink; ?>/data/jinzora.gif",
							"<?php echo $site_title; ?>",
							"Multimedia" );  }
					else
					{
						alert('<?php echo word("Mozilla M15 or later is required to add a search engine"); ?>');
					}
				}
			</script>
		<?php
		
		echo '<br><center>';
		echo word("Click below to install the Mozilla Search Plugin<br>(You will be prompted by Mozilla<br>to complete the install, please click 'Ok')");
		echo '<br><br><br><input type="button" onClick="addEngine();window.close();" value="'. word("Install Now"). '" class="jz_submit"><center>';
	}
}
?>
