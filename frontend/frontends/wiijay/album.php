<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *    
	* JINZORA | Web-based Media Streamer  
	*
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL. 
	* 
	* Jinzora Author:
	* Ross Carlson: ross@jasbone.com 
	* http://www.jinzora.org
	* Documentation: http://www.jinzora.org/docs	
	* Support: http://www.jinzora.org/forum
	* Downloads: http://www.jinzora.org/downloads
	* License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* Contributors:
	* Please see http://www.jinzora.org/modules.php?op=modload&name=jz_whois&file=index
	*
	* Code Purpose: This page contains all the album related related functions
	* Created: 9.24.03 by Ross Carlson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

 	// This function displays all the Genres or Artists 
	function drawPage(&$node){
		global $media_dir, $jinzora_skin, $hierarchy, $album_name_truncate, $row_colors, 
		       $img_download, $img_more, $img_email, $img_play, $img_random_play, $img_rate, $img_discuss, 
			   $num_other_albums, $root_dir, $enable_ratings, $short_date, $jzUSER, $img_play_dis, $img_random_play_dis,
			   $img_download_dis, $show_similar, $show_radio, $jzSERVICES, $show_album_art, $this_page, $num_track_cols;
		// Let's setup the new display object
		$display = &new jzDisplay();
		$blocks = &new jzBlocks();
		$fe = &new jzFrontend();
		$parent = $node->getAncestor("artist");
		handleFrontendOverrides();
		?>	

		  <table width="100%" cellpadding="5" cellspacing="0" border="0">
		     <tr>
		     <td>
		     <table width="100%" cellpadding="3" cellspacing="0" border="0">
		     <?php
		     $colwidth = floor(100/$num_track_cols);
		$tracks = $node->getSubNodes('tracks',-1);
		$c = 1;
		$c2 = 0;
		
		echo '<tr><td class="jz_nj_block_body" colspan="2">';
		$artist = $tracks[0]->getAncestor("artist");
		if ($artist){
		  $display->playButton($artist);
		  echo " ";
		  $display->link($artist,$artist->getName());
		} else {
		  echo $meta['artist'];
		}
		
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		
		$display->playButton($node);
		echo " ";
		$display->link($node,$node->getName());
		  ?>
		  </td></tr>
		  <tr>
		     <td class="jz_block_td" colspan="<?php echo $num_track_cols; ?>" nowrap>
		     <strong>Track Names</strong>
		     </td>
		     </tr>
		     
		     <form name="tracklist" method="POST" action="<?php echo urlize(array()); ?>">
		     <input type="hidden" name="<?php echo jz_encode("action"); ?>" value="<?php echo jz_encode("mediaAction"); ?>">
		     <input type="hidden" name="<?php echo jz_encode("jz_path"); ?>" value="<?php echo htmlentities(jz_encode($node->getPath("String"))); ?>">
		     <input type="hidden" name="<?php echo jz_encode("jz_list_type"); ?>" value="<?php echo jz_encode("tracks"); ?>">
		     <input type="hidden" name="<?php echo jz_encode("sendList"); ?>" value="<?php echo jz_encode("true"); ?>">
		     <input type="hidden" name="randomize" value="false">
		     <?php
		     
		     
		     foreach($tracks as $track){
		       $meta = $track->getMeta();
		       
		       $c = 2;
		       
		       if ($c2 % $num_track_cols == 0){
			 if ($c2 > 0) {
			   echo '</tr>';
			 }
			 echo '<tr>';
		       }
		       echo '<td class="jz_nj_block_body" width="'.$colwidth.'%">';
		       $display->playButton($track);
		       echo " ";
		       
		       $dispname = '';
		       $meta = $track->getMeta();
		       if (!isNothing($meta['number'])) {
			 $dispname .= $meta['number'] . ' ';
		       }
		       $dispname .= $track->getName();
		       $display->playLink($track, $dispname, "Play ". $track->getName());
		       
		       echo '</td>';
		       $c2++;
		       if ($c2 % $num_track_cols == 0) {
			 echo '</tr>';
		       }
		       
		     }
		while ($c2 % $num_track_cols != 0) {
		  $c2++;
		  echo '<td class="jz_nj_block_body">&nbsp;</td>';
		}
		    ?>
		     </tr>
		     </table>
			 </form>
			 </td>
			 </tr>
			 </table>
			 
			 <?php
			 }
?>
