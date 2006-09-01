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
	* Code Purpose: Header for the default frontend.
	* Created: 10/3/04 by Ben Dodson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	require_once($include_path. 'frontend/class.php');
	require_once($include_path. 'frontend/blocks.php');
	require_once($include_path. 'frontend/frontends/slick/blocks.php');		

	class jzFrontend extends jzFrontendClass {
		function pageTop($title = false, $endBreak = "true", $ratingItem = ""){
			global $site_title, $node, $jzUSER, $include_path, $disable_leftbar;
			
			$smarty = smartySetup();
			$blocks = new jzBlocks();
			// First let's include the settings for Slick
			include_once($include_path. "frontend/frontends/slick/settings.php");
			
			$smarty->display(SMARTY_ROOT. 'templates/slick/header.tpl');	
			
			$blocks->slickHeaderBlock($node);				
			$blocks->slickJukeboxBlock();
		}
		
		function footer(){
		  global $jinzora_url, $this_pgm, $version, $root_dir, $show_page_load_time, $skin, $show_jinzora_footer, $jzSERVICES, $cms_mode;

			// First let's make sure they didn't turn the footer off
			if ($show_jinzora_footer){
				$display = new jzDisplay();			
				$smarty = smartySetup();
				
				$smarty->assign('jinzora_url', $jinzora_url);
				$smarty->assign('link_title', $this_pgm. " ". $version);
				$smarty->assign('logo', $root_dir. '/style/'. $skin. '/powered-by-small.gif');
				$smarty->assign('page_load_time', "");
				if ($show_page_load_time == "true" and $_SESSION['jz_load_time'] <> ""){
					// Ok, let's get the difference
					$diff = round(microtime_diff($_SESSION['jz_load_time'],microtime()),3);
					if ($cms_mode == "false"){
						$page_load = '<span class="jz_artistDesc">';
					}
					$page_load .= word("Page generated in"). ": ". $diff. " ". word("seconds");
					if ($cms_mode == "false"){
						$page_load .= "</span>";
					}
					$smarty->assign('page_load_time', $page_load);
				}
				
				// Now let's display
				$smarty->display(SMARTY_ROOT. 'templates/slick/block-footer.tpl');
			}
			
			$jzSERVICES->cmsClose();
		}
		
		function jzFrontend() {
			parent::_constructor();
			$this->standardFooter = false;
		}
	}
?>