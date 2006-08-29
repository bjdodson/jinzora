<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
include_once('settings.php');
include_once('system.php'); 

function makePlugin() {
	global $root_dir, $site_title, $_SERVER;
	$file = "data/jinzora.src";
	$weblink = "http://".$_SERVER['HTTP_HOST']."${root_dir}";
	$code = "
# Mozilla/Jinzora plugin by Ben Dodson, bdodson@seas.upenn.edu
# www.jinzora.org

<search
   name=\"" . $site_title . "\"
   description=\"Media search for " . $site_title . "\"
   method=\"GET\"
   action=\"${weblink}/index.php\"
   queryEncoding=\"utf-8\"
   queryCharset=\"utf-8\"
   update=\"$weblink/data/jinzora.src\"
   updateIcon=\"$weblink/data/jinzora.gif\"
   updateCheckDays=\"99999\"
>
 
<input name=\"song_title\" user>
<input name=\"sourceid\" value=\"mozilla-search\">
<input name=\"doSearch\" value=\"true\">
<input name=\"search_type\" value=\"ALL\">
";
$code .= "
<interpret
    browserResultType=\"result\"
    charset = \"UTF-8\"
    resultListStart=\"<!--a-->\"
    resultListEnd=\"<!--z-->\"
    resultItemStart=\"<!--m-->\"
    resultItemEnd=\"<!--n-->\"
>

<browser>
   update=\"$weblink/data/jinzora.src\"
   updateIcon=\"$weblink/data/jinzora.gif\"
   updateCheckDays=\"99999\"
</browser>

";


	if (is_writable($file)) {
		$handle = fopen($file,"w");	
	}
	else {
		if (!@touch($file)) {
			die ("Could not open $file for writing.");
		}
		else {
			$handle = fopen($file,"w");
		}
	}

	fwrite($handle,$code);
	fclose($handle);
}
?>
