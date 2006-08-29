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
	 * - This is the Jinzora Metadata service - it retrieves data from www.jinzora.com
	 *
	 * @since 01.14.05
	 * @author Ross Carlson <ross@jinzora.org>
	 * @author Ben Dodson <ben@jinzora.org>
	 */
	
	$jzSERVICE_INFO = array();
	$jzSERVICE_INFO['name'] = "Amazon Affiliate Shopping Service";
	$jzSERVICE_INFO['url'] = "http://www.amazon.com";

	define('SERVICE_SHOPPING_amazon','true');

	

	function SERVICE_CREATE_SHOPPING_LINK_amazon($node) {
		global $img_dollar, $include_path;
		
		// Let's include the service settings
		include($include_path. "services/settings.php");
		
		// Now let's get the ID from the node
		$tracks = $node->getSubNodes("tracks");
		if (!isset($tracks[0])){
			return false;
		}
		$meta = $tracks[0]->getMeta();
		
		if ($meta['id'] == ""){
			return false;
		}				
		return '<a href="http://www.amazon.com/exec/obidos/tg/detail/-/'. $meta['id']. '/'. $service_shopping_amazon_id. '/" target="_blank">'. $img_dollar. "</a>";
	}
?>
