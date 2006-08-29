<div id="slickLeftBlock">
	<div id="slickLeftBlockPad">
		<a href="{$search_url}"><strong>{$word_search}</strong></a>
		<br />
		<form action="{$this_page}" method="{$method}" name="searchForm" {$searchOnSubmit}>
		{php}
		global $this_page;
		foreach (getURLVars($this_page) as $key => $val) { echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) . '">'; }
		{/php}
			<input type="text" name="search_query" class="jz_input" style="width:125px; font-size:10px; margin-bottom:3px;">
			<br />
			<select class="jz_select" name="search_type" style="width:85px">
				<option value="ALL">{$word_all_media}</option>
				{$artistSearch}
				{$albumSearch}
				<option value="tracks">{$word_tracks}</option>
				<option value="lyrics">{$word_lyrics}</option>
			</select>
			<input type="hidden" name="doSearch" value="true">
			<input type="submit" class="jz_submit" value="{$word_go}">			
		</form>
	</div>
</div>
<div id="slickLeftBlockSpace"></div>