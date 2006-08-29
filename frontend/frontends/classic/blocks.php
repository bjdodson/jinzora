<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
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
	* Creates many of the different blocks that are used by the Slick interface
	*
	* @since 01.11.05
	* @author Ross Carlson <ross@jinzora.org>
	*/

	class jzBlocks extends jzBlockClass {
	  
		/**
		* Constructor for the class.
		* 
		* @author Ben Dodson
		* @version 12/22/04
		* @since 12/22/04
		*/
		function jzBlocks() {
		
		}

		/**
		* Displays the block header
		* @author Ben Dodson
		* @version 8/20/05
		* @since 8/20/05
		*/
		function blockHeader($title = "", $align = "center"){
		?>
		<table class="jz_track_table" width="100%" cellpadding="10" cellspacing="0" border="0">
			<tr class="jz_row2">
				<td width="1%" align="<?php echo $align; ?>" valign="top" class="jz_track_table_songs_td" >
					<strong><?php echo $title; ?></strong>
				</td>
			</tr>
		</table>
		<?php
		}
	
		/**
		* Displays the random albums block
		* @author Ross Carlson
		* @version 12/22/04
		* @since 12/22/04
		* @param object $node the node that we are looking at
		* @param string $level The level we are looking at, like a subartist
		*/
		function classicRandomAlbums(&$node, $level = ""){
			global $hierarchy;
			
			$title = word("Random Albums");
			if ($node->getName() <> ""){
			  $title = word("Random Albums"). " :: ". $node->getName();
			}
			$this->blockHeader($title);
			$this->blockBodyOpen();
			$this->randomAlbums($node, $level);
			$this->blockBodyClose();
		}
}
?>