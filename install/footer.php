<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
?>
	<div id="bottom">
		<table class="statusTable">
			<tr>
				<td colspan="2">
					<h4>Installation Status</h4>
				</td>
			</tr>
			<tr>
				<?php 
					if ($step  == 1){ $step = 0; }
					$complete = round(($step / 9)*100,0);
					$left = 100 - $complete;
					if ($complete == "100"){
						echo '<td width="<?php echo $complete; ?>%" align="center" bgcolor="#ff6611" style="border: 1px solid #ddd">&nbsp;</td>';
					} elseif ($complete == "0"){
						echo '<td width="100%" bgcolor="#eeeeee" style="border: 1px solid #ddd; border-left: 0px;">&nbsp;</td>';
					} else {
						echo '<td width="'. $complete. '%" align="center" bgcolor="#ff6611" style="border: 1px solid #ddd">&nbsp;</td>'.
							 '<td width="'. $left. '%" bgcolor="#eeeeee" style="border: 1px solid #ddd; border-left: 0px;">&nbsp;</td>';
					}
				?>
			</tr>
		</table>
		<table class="statusTable">
			<tr>
				<td>
					<span class="statusLine">
						<?php 
							echo 'Install '. $complete. '% complete';
						?>
					</span>
				</td>
			</tr>
		</table>
	</div>  
	</div>
	<div id="footer">
		Jinzora :: Free Your Media
		&raquo; <a target="_blank" href="http://www.jinzora.com/">www.jinzora.com</a> 
	</div>
  </body>
</html>