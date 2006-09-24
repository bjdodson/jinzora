<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Displays the configuration system
* 
* @author Ben Dodson
* @version 09/15/04
* @since 09/15/04
*/
global $web_root, $root_dir, $site_title;

// Let's display the top of our page	
$this->displayPageTop();

// Now let's execute the plugin creation
include ('extras/mozilla.php');
makePlugin();

// Now let's set the JavaScript that will actually install the plugin
$weblink = "http://" . $_SERVER['HTTP_HOST'] . "${root_dir}";
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
echo '<br><br><br><input type="button" onClick="addEngine();window.close();" value="' . word("Install Now") . '" class="jz_submit"><center>';
?>
