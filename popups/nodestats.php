<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');
/**
* Displays the full top played list
* 
* @author Ross Carlson, Ben Dodson
* @version 01/27/05
* @since 01/27/05
* @param $node The node we are looking at
*/
global $row_colors, $site_title, $jzUSER, $node;

if (!checkPermission($jzUSER, "admin", $node->getPath("String"))) {
	echo word("Insufficient permissions.");
	return;
}

$display = new jzDisplay();
if ($node->getLevel() == 0) {
	$this->displayPageTop("", word("Stats for") . ": " . $site_title);
} else {
	$this->displayPageTop("", word("Stats for") . ": " . $node->getName());
}
$this->openBlock();
$stats = $node->getStats();
$i = 0;
?>
		<table width="100%" cellpadding="5" cellspacing="0">
		   <?php if (distanceTo("artist",$node) !== false) { ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Artists"); ?>:
				</td>
				<td width="60%">
		   <?php echo $stats['total_artists']; ?>
				</td>
			</tr>
			<?php } ?>
		   <?php if (distanceTo("album",$node) !== false) { ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Albums"); ?>:
				</td>
				<td width="60%">
					<?php echo $stats['total_albums']; ?>
				</td>
			</tr>
				    <?php } ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Tracks"); ?>:
				</td>
				<td width="60%">
				<?php echo $stats['total_tracks']; ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Size"); ?>:
				</td>
				<td width="60%">
				    <?php echo $stats['total_size_str']; ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Length"); ?>:
				</td>
				<td width="60%">
				    <?php echo $stats['total_length_str']; ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Plays"); ?>:
				</td>
				<td width="60%">
				    <?php echo $node->getPlaycount(); ?>
				</td>
			</tr><?php
 /* ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Downloads"); ?>:
				</td>
				<td width="60%">
				    <?php echo $node->getDownloadCount(); ?>
				</td>
			</tr><?php */
?>
				    <?php if (distanceTo("artist",$node) !== false) { ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Most Played Artist"); ?>:
				</td>
				<td width="60%">
				    <?php

$a = $node->getMostPlayed("nodes", distanceTo("artist", $node), 1);
if (sizeof($a) > 0) {
	echo $a[0]->getName();
}
?>
				</td>
			</tr>
				   <?php } ?>
		<?php if (distanceTo("album",$node) !== false) { ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Most Played Album"); ?>:
				</td>
				<td width="60%">
				    <?php

$a = $node->getMostPlayed("nodes", distanceTo("album", $node), 1);
if (sizeof($a) > 0) {
	if ($node->getPType() != "artist") {
		echo getInformation($a[0], "artist") . " - " . $a[0]->getName();
	} else {
		echo $a[0]->getName();
	}
}
?>
				</td>
			</tr>
				    <?php   } ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Most Played Track"); ?>:
				</td>
				<td width="60%">
				    <?php

$a = $node->getMostPlayed("tracks", -1, 1);
if (sizeof($a) > 0) {
	if ($node->getPType() != "artist") {
		echo getInformation($a[0], 'artist') . " - " . $a[0]->getName();
	} else {
		echo $a[0]->getName();
	}
}
?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Average Track Length"); ?>:
				</td>
				<td width="60%">
					<?php echo convertSecMins($stats['avg_length']); ?>
				</td></tr>
<tr class="<?php echo $row_colors[$i]; $i = 1 - $i; ?>">
<td width="40%">
				    <?php echo word("Average Bitrate"); ?>:
</td>
<td width="60%">
<?php

echo round($stats['avg_bitrate'], 0);
?>
</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Average Year"); ?>:
				</td>
				<td width="60%">
					<?php echo round($stats['avg_year'],0); ?>
				</td>
			</tr>
		</table>
		<br><center>
		<?php $this->closeButton(); ?>
		</center>
		<?php


// Now let's get the stats

$this->closeBlock();
?>
