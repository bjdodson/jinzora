<td align="center">&nbsp; &nbsp;</td>
<td align="center" valign="top">
	<table width="100%" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td class="jz_block_td" width="100%" nowrap>
				<strong>RESAMPLING</strong>
			</td>
		</tr>
		<tr>
			<td class="jz_nj_block_body" align="center" width="1%" nowrap>
				{$title}
				<form action="{$action}" method="post">
					<select class="jz_select" id="resamplerate" name="'. jz_encode("jz_resample"). '" style="width:50" onChange="{$onchange}">
						<option value="">-</option>
						{html_options values=$resample_rates output=$resample_rates selected=$cur_rate}
					</select> Kbps
				</form>
			</td>
		</tr>
	</table>
</td>