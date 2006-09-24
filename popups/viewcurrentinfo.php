<?php if (!defined(JZ_SECURE_ACCESS)) die('Security breach detected.');
global $status_blocks_refresh, $mysid;
$this->displayPageTop("", word("Current Information"));
$this->openBlock();
echo '<span id="currentInfo">&nbsp;</span>';
?>
		<script>function updateCurrentInfo(update) {
			currentInfo("<?php echo $mysid; ?>", update);
			setTimeout("updateCurrentInfo(true)",<?php echo ($status_blocks_refresh * 1000); ?>);
		}
		updateCurrentInfo(false);
    </script>
    <?php

//echo '<br><br><center>';
//$this->closeButton();

$this->closeBlock();
?>
