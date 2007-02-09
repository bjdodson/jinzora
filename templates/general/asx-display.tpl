{literal}
<style>
	td{ 
		background-color: #000000;
		font-family: Verdana;
		font-size: 10px;
		color: #FFFFFF;
	}
	body, textarea{
		margin: 5px;
		background-color: #000000;
		color: #FFFFFF;
		font-family: Verdana;
		font-size: 10px;
	}
	
</style>
{/literal}
<body>
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
				<strong>
					{if $trackNum <> ""}
						{$trackNum}/{$totalTracks} - 
					{/if}
					{$trackName}<br />
					{$artistName}
					{if $artistName <> ""}
					- 
					{/if}
					{$albumName}
				</strong>
				<br>
				{$albumArt}{$albumDesc}
			</td>
			{if $lyrics and $lyrics <> "false"}
				<td valign="top">
					<br>
					<strong>Lyrics</strong><br>
					<textarea rows="20" cols="30" style="border:0px;">{$lyrics}</textarea>
				</td>
			{/if}
		</tr>
	</table>
</body>