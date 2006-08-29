<div id="slickLeftBlock">
	<div id="slickLeftBlockPad">
		<strong>{$word_browsing}</strong>
		<br />
		{php}
			global $jz_path;
			$display = new jzDisplay();
			$node = new jzMediaNode($jz_path);
			$display->displayBrowseDropdown();
			echo "<br>";
			$display->displayPrevDropdown($node, "artist");
			echo "<br>";
			$display->displayPrevDropdown($node, "album");
		{/php}
	</div>
</div>
<div id="slickLeftBlockSpace"></div>