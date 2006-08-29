{php}
	$display = new jzDisplay();
{/php}
<div id="slickMainBlockBody">		
	<table width="95%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td width="50%" valign="middle">
				<nobr>
				<a style="cursor:pointer" onClick="CheckBoxes('albumForm',true); return false;" href="javascript:;">{$img_check}</a>
				<a style="cursor:pointer" onClick="CheckBoxes('albumForm',false); return false;" href="javascript:;">{$img_check_none}</a>
				{php}
					echo $display->sendListButton();
					echo "&nbsp;";
					echo $display->sendListButton(true);
				{/php}
				</nobr>
			</td>
			<td width="50%" valign="middle" align="right">
				<nobr>
				&nbsp; &nbsp;
				{php}
					$display->addListButton(); 
					echo "&nbsp;";
					$display->playlistSelect(115,false,"all");
				{/php}
				</nobr>
			</td>
		</tr>
	</table>
</div>