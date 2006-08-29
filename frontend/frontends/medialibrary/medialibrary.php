<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
function drawPage(&$node) {
  global $this_page, $jzUSER;	

	$parent = $node->getNaturalParent();
	doNaturalDepth($parent);
	$grandparent = $parent->getNaturalParent();
	doNaturalDepth($grandparent);
	$greatgrandparent = $grandparent->getNaturalParent();
	doNaturalDepth($greatgrandparent);
	
	$display = &new jzDisplay();
	$blocks = new jzBlocks();
	$root = &new jzMediaNode();
	doNaturalDepth($root);

	// let's display the site description
	$news = $blocks->siteNews($node);
	if ($news <> ""){
		echo "<br><center>". $news. "<center><br>";
	}
	
	$MAX_TRACKS = 100;
	// if we have POST variable songs[], we need to perform some action on the songs.
	// probably handle this in the header.php file (or even index.php?)
	
	// TODO:
	// 1) if no art found, display some stats or something.
	// 2) add counts in parenethesis next to nodes.
	// 3) lock page width
	
	if (isset($_POST['clear'])) {
		unset($_POST['query']);
	}
	
	if ($node->getLevel() == 0) {
		$left = $node->getSubNodes();
		$right = array();
		$backstring = false;
	}
	else if ($node->getSubNodes() == array()) {
	
		// weird but possible case: $node == $parent. Happens in a 2-tier layout.
		if ($parent->getLevel() == $grandparent->getLevel()) {
			$leftpath = $node->getPath();
			$rightpath = array();
			
			$left = $parent->getSubNodes();
			$right = array();
			$backstring = false;
		}
		else {
			$leftpath = $parent->getPath(); // preselect parent on the left
			$rightpath = $node->getPath(); // preselect me on the right
			if ($grandparent->getLevel() == $greatgrandparent->getLevel()) {
				$backstring = false;
			}
			else {
				$backstring = $grandparent->getPath("String");
			}
			
			$left = $grandparent->getSubNodes();
			$right = $parent->getSubNodes();
		}
	} else {
		$leftpath = $node->getPath(); // preselect me on the left
		$rightpath = array(); // don't select on the right
		
		if ($grandparent->getLevel() == $parent->getLevel()) {
			$backstring = false;
		} 
		else {
			$backstring = $parent->getPath("String");
		}
		
		if (isset($_POST['doquery']) && $_POST['query'] != "") {
			$right = $node->search($_POST['query'],"nodes",false,$MAX_TRACKS);
		} else {
			$right = $node->getSubNodes();
		}
		$left = $parent->getSubNodes();
	}
	
	
	if (isset($_POST['doquery']) && $_POST['query'] != "") {		
		if ($_POST['how'] == "filter") {
			$songs = $node->search($_POST['query'],"tracks",-1,$MAX_TRACKS);
		} else {
			$songs = $root->search($_POST['query'],"tracks",-1,$MAX_TRACKS);
		}
	}
	else if ($node->getSubNodeCount("leaves",-1) > $MAX_TRACKS) {
		if ($node->getLevel() == 0) {
			$songs = array();
		}
		else {
			$songs = $node->getSubNodes("leaves",-1,true,$MAX_TRACKS);
		}
	} else {
		$songs = $node->getSubNodes("leaves",-1,false);
	}
	echo '<br>';
	$blocks->blockBodyOpen();

?>

<table width="100%" cellspacing="0" cellpadding="0">
<tr><td width="32%">
<form id="leftForm" method="GET" action="<?php echo $this_page ?>">
<?php
	keepVars($_POST);
	$display->hiddenPageVars();
	if (!defined('NO_AJAX_LINKS')) {
	  $display->hiddenVariableField('maindiv','true');
	}
?>
<div id="left_box">
<select name="<?php echo jz_encode("jz_path"); ?>" size="14" class="full jz_select" style="height:210px;" onChange="submitForm(this.form,'<?php echo htmlentities($this_page); ?>')">
<?php
	if ($backstring !== false) {
		echo "<option value=\"".htmlentities(jz_encode($backstring))."\">".word('[ Back ]')."</option";
	}
	foreach ($left as $el) {
		echo "<option value=\"".htmlentities(jz_encode($el->getPath("String")))."\"";
		if ($el->getPath() == $leftpath) {
			echo " selected";
		}
		echo ">".htmlentities($display->returnShortName($el->getName(),35))."</option>";
	}
?>
</select>
</div>
</form>
</td><td width="32%">
<form method="GET" action="<?php echo $this_page ?>">
<?php 
    keepVars($_POST);
    $display->hiddenPageVars();
    if (!defined('NO_AJAX_LINKS')) {
      $display->hiddenVariableField('maindiv','true');
    }
?>
<div id="right_box">
<select name="<?php echo jz_encode("jz_path"); ?>" size="14" class="full jz_select" style="height:210px;" onChange="submitForm(this.form,'<?php echo htmlentities($this_page); ?>')">
<?php
	foreach ($right as $el) {
		echo "<option value=\"".htmlentities(jz_encode($el->getPath("String")))."\"";
		if ($el->getPath() == $rightpath) {
			echo " selected";
		}
		echo ">".htmlentities($display->returnShortName($el->getName(),35))."</option>";
	}
?>
</select>
</div>
</form>
</td>
<td width="20%" valign="top">
<?php
if (($art = $node->getMainArt("200x200")) !== false) {
	$display->image($art,$node->getName(),200,200,"limit");
	// todo: if the description exists, make the image a link to the description.
} else {
  //$blocks->showCharts($node,'newalbums');
}
?>
</td>
</tr>
<tr><td nowrap colspan="2">
<form method="POST" action="<?php echo $this_page ?>">
<input type="hidden" name="<?php echo jz_encode("jz_path"); ?>" value="<?php echo jz_encode($node->getPath("String")); ?>">
    <?php if (isset($_POST['frontend']) || isset($_GET['frontend'])) {
      echo "<input type=\"hidden\" name=\"" . jz_encode("frontend") . "\" value=\"" . jz_encode("medialibrary") . "\">";
    }
  ?>
<select name="<?php echo jz_encode("how"); ?>" class="jz_select">
<option value="<?php echo jz_encode("filter"); ?>"
<?php
	if ($_POST['how'] == "filter")
		echo "selected";
?>
>Filter: </option>
<option value="<?php echo jz_encode("search"); ?>"
<?php
	if ($_POST['how'] == "search")
		echo "selected";
?>
>Search: </option>
</select>
<input name="query" style="width:65%" class="jz_input" 
<?php
	if (isset($_POST['query']))
		echo "value=\"".htmlentities($_POST['query'])."\"";
?>
>
<input type="submit" name="<?php echo jz_encode("doquery"); ?>" value="Go" class="jz_submit">&nbsp;<input type="submit" name="<?php echo jz_encode("clear"); ?>" value="Clear" class="jz_submit">
</form>
</td>
<td>
<p>
<?php
$pl = $jzUSER->loadPlaylist();
 if (checkPermission($jzUSER,'play',$node->getPath('string')) === true) {
   $display->playButton($pl);
   $display->randomPlayButton($pl);
 }
 if ($jzUSER->getSetting('download') === true) {
   $display->downloadButton($pl);
 }
echo "&nbsp;";
echo $pl->getName() . ":";
?>
</p>
</td>
</tr>
<tr><td colspan="2" valign="top">

<table width="100%">
<form name="tracklist" method="POST" action="<?php echo $this_page ?>">
<tr><td colspan="2">
<?
	if (isset($_POST['query']) && $_POST['query'] != "") {
		echo "<input type=\"hidden\" name=\"query\" value=\"" . htmlentities($_POST['query']) . "\">";
		if ($_POST['how'] == "filter") {
			echo "<input type=\"hidden\" name=\"" . jz_encode("how") . "\" value=\"" . jz_encode("filter") . "\">";
		}
		else {
			echo "<input type=\"hidden\" name=\"" . jz_encode("how") . "\" value=\"" . jz_encode("search") . "\">";
		}
	}
?>
<input type="hidden" name="<?php echo jz_encode("action"); ?>" value="<?php echo jz_encode("mediaAction");?>">
<input type="hidden" name="<?php echo jz_encode("type"); ?>" value="<?php echo jz_encode("tracks")?>">
<div id="track_box">
<input type="hidden" name="<?php echo jz_encode("jz_path"); ?>" value="<?php echo htmlentities(jz_encode($node->getPath("String"))); ?>">
<select name="jz_list[]" size="18" class="full jz_select" ondblclick="if (submitPlaybackForm(this,'<?php echo htmlentities($this_page); ?>')) submit()" multiple>
<?php
	// this should be fixed to however playlists get loaded.
	foreach ($songs as $el) {
		echo "<option value=\"".htmlentities(jz_encode($el->getPath("String")))."\">".htmlentities($display->returnShortName($el->getName(),75))."</option>";
	}
?>
</select>
</div>
</td></tr>
<tr><td nowrap><div align="left">
    <?php if (checkPermission($jzUSER,'play',$node->getPath("String")) === true) { ?>
<input type="submit" name="<?php echo jz_encode("sendList"); ?>" value="<?php echo word('Play'); ?>" onClick="return submitPlaybackForm(this,'<?php echo htmlentities($this_page); ?>')" class="jz_submit">
<input type="submit" name="<?php echo jz_encode("sendPath"); ?>" value="<?php echo word('Play All'); ?>" onClick="return submitPlaybackForm(this,'<?php echo htmlentities($this_page); ?>')" class="jz_submit">
<input type="submit" name="<?php echo jz_encode("sendPathRandom"); ?>" value="<?php echo word('Random Play All'); ?>" onClick="return submitPlaybackForm(this,'<?php echo htmlentities($this_page); ?>')" class="jz_submit">
<!--<input type="submit" name="<?php echo jz_encode("info"); ?>" value="Get Info" class="jz_submit">--!>
<?php } ?>
</div></td>
<td nowrap><div align="right">
<input type="submit" name="<?php echo jz_encode("addList"); ?>" value="<?php echo word('Add'); ?>" class="jz_submit" onClick="return submitPlaybackForm(this,'<?php echo htmlentities($this_page); ?>')">
<input type="submit" name="<?php echo jz_encode("addPath"); ?>" value="<?php echo word('Add All'); ?>" class="jz_submit" onClick="return submitPlaybackForm(this,'<?php echo htmlentities($this_page); ?>')">
</div></td></tr>
</form>
</table>
<td>
<form action="<?php echo $this_page; ?>" method="POST" name="playlistForm">
<div id="playlistDisplay">
    <?php $blocks->playlistDisplay(); ?>
</div>
    <?php $display->playlistSelect('125px', true); ?>
</select>
<input type="hidden" name="playlistname" value="">
    <?php $display->hiddenVariableField("action","playlistAction");
	$display->hiddenVariableField("noaction","true"); 
	keepVars($_POST);
	$display->hiddenVariableField("jz_path",$_POST['jz_path']);
	  ?>
<input type="submit" name="<?php echo jz_encode("createlist"); ?>" value="<?php echo word('New'); ?>" class="jz_submit" onClick="variablePrompt('playlistForm','playlistname','<?php echo word('Please enter a name for your playlist.'); ?>')">
</form>

</td>
</tr>
<tr>
<td align="left" nowrap>&nbsp;

</td>
<td align="right">&nbsp;

</td>
<td aligh="right">
</td>
</tr>
</table>
<?php
    $blocks->blockBodyClose();
	echo '<br>';
}

function keepVars($GET) {
  if (isset($GET['query']))
    echo "<input type=\"hidden\" name=\"query\" value=\"".htmlentities($GET['query'])."\">";
  if ($GET['how'] == "filter")
    echo "<input type=\"hidden\" name=\"" . jz_encode("how") . "\" value=\"" . jz_encode("filter") . "\">";
  if ($GET['how'] == "search")
    echo "<input type=\"hidden\" name=\"" . jz_encode("how") . "\" value=\"" . jz_encode("search") . "\">";
}

?>
