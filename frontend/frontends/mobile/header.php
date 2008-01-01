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
	* - Contains the Slimzora display functions
	*
	* @since 02.17.04 
	* @author Ross Carlson <ross@jinzora.org>
	* @author Ben Dodson <ben@jinzora.org>
	*/
require_once(dirname(__FILE__).'/../../class.php');
require_once(dirname(__FILE__).'/../../blocks.php');		
	
        class jzBlocks extends jzBlockClass {}

	class jzFrontend extends jzFrontendClass {
		function jzFrontend() {
			parent::_constructor();
		}
		
		/**
		* Draws the login page.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 11/3/04
		* @since 5/13/04
		*/
		function loginPage($failed = false) {
		
			$display = &new jzDisplay();
			
			echo '<body onLoad="document.getElementById(\'loginform\').field1.focus();"></body>';
			
			$urla = array();			
			$urla['jz_path'] = isset($_GET['jz_path']) ? $_GET['jz_path'] : '';
			?>
				<style>
					body {
						background-color: #F5F5D0;
						background: #F5F5D0;
						font-family: Verdana, Sans;
						font-size: 10px;
						color: #9c9b9b;
						margin: 0 0 0 0;
					}
					td {
						font-family: Verdana, Sans;
						font-size: 10px;
					}
					submit {
						border: 1px solid black;
						background: #EFEFCC;
						color: #000000;
						font-size: 11px;
						border-width: 1px;
					}
					input {
						font-family: Verdana, Sans;
						color: #000000;
						background-color: #EFEFCC;
						font-size: 11px;
						border-width: 1px;
					}
				</style>
				    <script language="javascript" src="lib/md5.js"></script>
				    <script language="javascript">
				    function submitLogin() {
				      if (document.getElementById("loginform").doregister.value == 'true') {
					return true;
				      } else {
					// submit the other form
					// so we can submit a non-cleartext PW without changing browser's stored PW.
					document.getElementById("loginSecureForm").field1.value = 
					         document.getElementById("loginform").field1.value;

					document.getElementById("loginSecureForm").field2.value = 
					hex_md5(document.getElementById("loginform").field2.value);

					document.getElementById("loginSecureForm").remember.value =
					document.getElementById("loginform").remember.value;

					document.getElementById("loginSecureForm").submit();
					return false;
				      }
				    }
				    </script>
				<body style="background-color: #F5F5D0;">
				<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #F5F5D0;">
					<tr>
						<td align="center" height="100%" width="100%" style="background-color: #F5F5D0;">
							<?php
								if ($failed) {
									echo "<center><strong><font color=white>Incorrect password</font></strong></center>";
								}
							?>
							<form name="loginSecureForm" id="loginSecureForm" method="POST" action="<?php echo urlize($urla); ?>">
								<input type="hidden" name="field1" value="">
								<input type="hidden" name="field2" value="">
	                            <input type="hidden" name="remember" value="">
								<input type="hidden" name="<?php echo jz_encode('action'); ?>" value="<?php echo jz_encode('login'); ?>">
                            </form>
							<form name="loginform" id="loginform" method="POST" action="<?php echo urlize($urla); ?>" onsubmit="return submitLogin()">
								<input type="hidden" name="<?php echo jz_encode('action'); ?>" value="<?php echo jz_encode('login'); ?>">
								<?php
									if (!$failed) {
										echo "<br><br><br>";
									}
								?>
								<?php echo word("Username"); ?><br>
								<input size="18" type="text" class="jz_input" name="field1" style="width:146px;">
								<br>
								<?php echo word("Password"); ?><br>
								<input size="18" type="password" class="jz_input" name="field2" style="width:146px;">
								<br>
								<input type="checkbox" class="jz_checkbox" name="remember"> <?php echo word("Remember me"); ?>
								<br><br>
								<input class="jz_submit" type="submit" name="<?php echo jz_encode('submit_login'); ?>" value="<?php echo word("Login"); ?>">
								   <input type="hidden" name="doregister" value="false" />
								<?php $be = new jzBackend();
									$data = $be->loadData('registration');
									if ($data['allow_registration'] == "true") {
									?>
										&nbsp;<input class="jz_submit" type="submit" name="<?php echo jz_encode('self_register'); ?>" value="<?php echo word("Register"); ?>" onclick="document.getElementById('loginform').doregister.value='true'">
									<?php 
									} 
									?>

							</form>
							<br /><br />
							<img src="style/images/login-footer-logo.gif" border="0">
							<br /><br />
						</td>
					</tr>
				</table>
				</body>
			<?php
			//this->footer();
		}
			
		function standardPage(&$node) {
		  global $jinzora_url,$root_dir,$cms_mode;
		  
		  /* header */
		  /* use one smarty object so we can use variables in
		     both header and footer
		  */
		  $display = new jzDisplay();
		  $smarty = smartySetup();
		  
		  $path = $node->getPath("String");
		  $tabs = array();
		  $tabs[] = array('name'=>word('Browse'), 'link' => urlize(array('page'=>'browse', 'jz_path'=>$path)));
		  $tabs[] = array('name'=>word('Playback'), 'link' => urlize(array('page'=>'playback', 'jz_path'=>$path)));
		  $tabs[] = array('name'=>word('Lists'), 'link' => urlize(array('page'=>'lists', 'jz_path'=>$path)));
		  $smarty->assign('tabs',$tabs);

		  $smarty->assign('cms', $cms_mode == "false" ? false : true);
		  $smarty->assign('login_link',$display->loginLink(false,false,true,false,true));
		  $smarty->assign('jinzora_url',$jinzora_url);
		  $smarty->assign('jinzora_img',$root_dir.'/style/images/slimzora.gif');

		  $display->preheader($node->getName(),$this->width,$this->align,true,true,true,true);
		  include_once(dirname(__FILE__). "/css.php");

		  jzTemplate($smarty,'header');
		  if (isset($_REQUEST['page'])) {
		    $page = $_REQUEST['page'];
		  } else {
		    $page = "browse";
		  }

		  switch ($page) {
		  case "browse":
		    showPageBrowse($node);
		    break;
		  case "playback":
		    showPagePlayback();
		    break;
		  case "lists";
		  showPageLists();
		  }

		  jzTemplate($smarty,'footer');
		}
	}

function showPagePlayback() {
  $display = &new jzDisplay();
  $smarty = smartySetup();
  $smarty->assign('templates',dirname(__FILE__).'/templates');

    jzTemplate($smarty,'playback');
}

function showPageLists() {
  $display = &new jzDisplay();
  $smarty = smartySetup();
  $smarty->assign('templates',dirname(__FILE__).'/templates');
 
  jzTemplate($smarty,'lists');
}


function showPageBrowse($node) {
  $display = &new jzDisplay();
  $smarty = smartySetup();
  $smarty->assign('templates',dirname(__FILE__).'/templates');

  $breadcrumbs = array();
  if (isset($_REQUEST['jz_path'])) {
    $me = $node;
    while ($me->getLevel() > 0) {
      $breadcrumbs[] = array("name" => $me->getName(),"link" => urlize(array('jz_path'=>$me->getPath("String"))));
      $me = $me->getParent();
    }
  }
  
  $breadcrumbs[] = array("name"=>word("Home"),"link"=>urlize(array()));
  $smarty->assign('breadcrumbs',$breadcrumbs);
  $smarty->assign('templates',dirname(__FILE__).'/templates');
  
  
  $myNodes = $node->getSubNodes('nodes');
  $myTracks = $node->getSubNodes('tracks');
  
  $nodes = array();
  for ($i = 0; $i < sizeof($myNodes); $i++) {
    $e = $myNodes[$i];
    $nodes[] = smartyNode($myNodes[$i]);
  }
  $smarty->assign('nodes',$nodes);

  $tracks = array();
  for ($i = 0; $i < sizeof($myTracks); $i++) {
    $e = $myNodes[$i];
    $tracks[] = smartyTrack($myTracks[$i]);
  }
  $smarty->assign('tracks',$tracks);
  
  jzTemplate($smarty,'browse');
}

function smartyNode($e) {
  static  $anchor = 'A';

  $display = new jzDisplay();
  $arr = array();
  $arr['name'] = $e->getName();
  $arr['link'] = urlize(array('jz_path'=>$e->getPath("String")));
    
  if ($e->getPType() == "album" || $e->getPType == "disk") {
    $arr['openPlayTag'] = $display->getOpenPlayTag($e);
  } else {
    $arr['openPlayTag'] = $display->getOpenPlayTag($e,true,50);
  }
  
  $compName = $arr['name'];
  if (substr($compName,0,4) == 'The ') {
    $compName = substr($compName,4);
  }
  
  $anchors = array();
  if ($i == 0) {
    $anchors[]='anchor_NUM';
    $first = false;
  }
  while (strlen($anchor) == 1 && ($anchor < $compName || $i == sizeof($items)-1)) {
    $anchors[] = 'anchor_'.$anchor++;
  }
  $arr['anchors'] = $anchors;

  return $arr;
}

function smartyTrack($e) {
  $display = new jzDisplay();

  // meta  
  $arr = $e->getMeta();
  if (!is_array($arr)) $arr = array();

  $arr['length'] = convertSecMins($arr['length']);
  $arr['name'] = $e->getName();
  $arr['openPlayTag'] = $display->getOpenPlayTag($e);
  
  return $arr;
}

?>