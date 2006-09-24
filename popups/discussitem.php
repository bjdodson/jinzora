<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');

/**
* Displays the discussion page
*
* @author Ross Carlson
* @since 03/07/05
* @version 03/07/05
* @param $node The node we are looking at
*
**/
global $jzUSER, $row_colors, $node;

// Let's setup the object		
$item = new jzMediaElement($node->getPath('String'));
$track = new jzMediaTrack($node->getPath('String'));

// Let's grab the meta data from the file and display it's name
$meta = $track->getMeta();

$this->displayPageTop("", "Discuss Item: " . $meta['title']);
$this->openBlock();

// Did they submit the form?
if (isset ($_POST['edit_addcomment'])) {
	// Let's add it
	$item->addDiscussion($_POST['edit_newcomment'], $jzUSER->getName());
}

// Let's setup our form
$arr = array ();
$arr['action'] = "popup";
$arr['ptype'] = "discussitem";
$arr['jz_path'] = $node->getPath('String');
echo '<form action="' . urlize($arr) . '" method="POST">';

// Now let's setup the display
$i = 0;
?>
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="20%" valign="top">
					<nobr>
						<?php echo word('New Comment'); ?>:
					</nobr>
				</td>
				<td width="80%" valign="top">
					<textarea name="edit_newcomment" rows="3" style="width:300px;" class="jz_input"></textarea>
					<br><br>
					<input type="submit" name="edit_addcomment" value="<?php echo word('Add Comment'); ?>" class="jz_submit">
					<br><br>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[0];?>">
				<td colspan="2" width="100%" align="center">
					<strong><?php echo word('Previous Comments'); ?></strong><br><br>
				</td>
			</tr>
			<?php

// Now let's get the previous discussions
$disc = $item->getDiscussion();
if (count($disc) > 0) {
	rsort($disc);
	foreach ($disc as $comment) {
?>
						<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
							<td width="20%" valign="top">
								<nobr>
									<?php echo $comment['user']; ?>
								</nobr>
							</td>
							<td width="80%" valign="top">
								<?php echo $comment['comment']; ?>
							</td>
						</tr>
						<?php

	}
}
?>
		</table>
		</form>
		<?php


$this->closeBlock();
?>
