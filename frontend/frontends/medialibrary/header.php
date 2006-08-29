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
 * Code Purpose: This page contains all the album display related functions
 * Created: 9.24.03 by Ross Carlson
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once($include_path. 'frontend/class.php');
require_once($include_path. 'frontend/blocks.php');

class jzBlocks extends jzBlockClass {

}

class jzFrontend extends jzFrontendClass {		
  function jzFrontend() {
    global $cms_mode;
    parent::_constructor();
    if ($cms_mode === false || $cms_mode == "false") {
      $this->width = "800px";
    } else {
      $this->width = "100%";
    }
  }
  
  function standardPage($node, $maindiv=false) {
    global $hierarchy, $include_path,$live_update;
    
    $display = &new jzDisplay();
	$blocks = new jzBlocks();
    
    include($include_path. 'frontend/frontends/medialibrary/medialibrary.php');
    
    if ($node->getLevel() > 0) {
      while ($node->getLevel() > 0 && $hierarchy[$node->getLevel()-1] == "hidden") {
	$d = $node->getNaturalDepth();
	$node = $node->getParent();
	$node->setNaturalDepth($d+1);
	
      }
    }
    
    if (!$maindiv) {
      if ($node->getName() <> ""){
	$display->preheader($node->getName(),$this->width,$this->align);
	$this->pageTop($node->getName());
      } else {
	$display->preheader(false, $this->width,$this->align);
	$this->pageTop();
      }
    }

    if (!$maindiv) {
      echo '<div id="mainDiv">';
    }
    drawPage($node);
    if (!$maindiv) {
      echo '</div>';
      $this->footer($node);
    }
  }
}
?>
