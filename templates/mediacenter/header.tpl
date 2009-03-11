<script src="{$root_dir}/templates/mediacenter/BasicFunctions.js" type="text/javascript"></script>
<script src="{$root_dir}/templates/mediacenter/Scrolling.js" type="text/javascript"></script>
<script src="{$root_dir}/templates/mediacenter/MoveFocus.js" type="text/javascript"></script>
<link rel="STYLESHEET" type="text/css" href="{$root_dir}/templates/mediacenter/mediacenter.css">

<!-- Start span used as stand-in for Shared Viewport -->
<span style="position: absolute; top: 0; left: 0; height: 100%;">
    <table style="position: absolute; top: 0; left: 0; height: 100%;" cellspacing="0" cellpadding="0">
    <tr><td valign="bottom" height="100%">
        <span id="SVP" style="width: 308; height: 216; vertical-align: bottom" MCFocusable="true"></span>

    </td></tr>
    </table>
</span>
<!-- End span used as stand-in for Shared Viewport -->


<!-- Item counter at lower right. Displays only if button menu is scrollable -->
<span id="itemCounterSpan" style="font: 20pt Arial; color: #f2f2f2; width: 610; Height: 42; text-align: right; display: block; position: absolute; top: 644; left: 119;">
    <span id="counterNum">1</span>&nbsp;of&nbsp;<span id="counterTotal"></span>
    <span id="arrowUp" class="arrowUp" onclick = "pageUpDown('up')"></span>
    <span id="arrowDown" class="arrowDown" onclick = "pageUpDown('down')"></span>

</span>
<!-- End item counter at lower right --><!-- Start of "scrolling" span -->
<span id="scrollspan" MCScrollable="true" style="position: absolute; top: 50; left: 20; width: 200; height: 485; overflow: hidden">
    <table id="listTable" border="0" cellpadding="0" cellspacing="3">
			<tr><td ID="btnGenres" class="button2" MCFocusable="true">Genres</td></tr>
			<tr><td ID="btnArtists" class="button2" MCFocusable="true">Artists</td></tr>
			<tr><td ID="btnAlbums" class="button2" MCFocusable="true">Albums</td></tr>
    </table>
</span>
{literal}
<script>
    function pageLoadFunctions()
    {
			setBGColor("#1a6fcc");
			checkSVP();
			setCounter();
			setArray();
			startFocus();
    }

    function doSelect()
    {
    /* This function determines what your buttons do when they are selected
    (navigate, call a function, etc.). This function will get called whenever user
    clicks a focusable item, or selects it with "OK" button on remote. Make sure to
    include a case below for each focusable item on the page */
        var url = ""
        switch(oCurFocus.id)
        {
					case "btnGenres": url = "default.php"; break;
					case "btnArtists": url = "artists.php"; break;
					case "btnAlbums": url = "albums.php"; break;
					
					case "btnGenreJazz": url = "default.php?genre=Jazz"; break;
					case "btnGenreRock": url = "default.php?genre=Rock"; break;
					case "btnGenreFunk": url = "default.php?genre=Funk"; break;
        }
        if (url != "") window.navigate(url);
    }
</script>
{/literal}
<!-- This is where the Genre's go -->
<span id="scrollGenre" MCScrollable="true" style="position: absolute; top: 50; left: 220; width: 75%; height: 485; overflow: hidden">
	<table id="genreTable" border="0" cellpadding="0" cellspacing="3" width="100%">
		<tr>
			<td ID="btnGenreJazz" width="33%" align="center" class="itemButton" MCFocusable="true">Jazz</td>
			<td ID="btnGenreRock" width="33%" align="center" class="itemButton" MCFocusable="true">Rock</td>
			<td ID="btnGenreFunk" width="33%" align="center" class="itemButton" MCFocusable="true">Funk</td>

		</tr>
	</table>
</span>
