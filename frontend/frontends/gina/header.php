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
  // use all of the default functions.
}

// use all the default blocks.
class jzBlocks extends jzBlockClass {
  function jzBlocks() {
    
  } 
}

?>
