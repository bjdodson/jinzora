<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *        
	* JINZORA | Web-based Media Streamer   
	*
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL.
	* 
	* Jinzora Author:
	* Ross Carlson: ross@jasbone.com 
	* http://www.jinzora.org
	* Documentation: http://www.jinzora.org/docs	
	* Support: http://www.jinzora.org/forum
	* Downloads: http://www.jinzora.org/downloads
	* License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* Contributors:
	* Please see http://www.jinzora.org/modules.php?op=modload&name=jz_whois&file=index
	*
	* Code Purpose: Header for the default frontend.
	* Created: 10/3/04 by Ben Dodson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	require_once($include_path. 'frontend/class.php');
	require_once($include_path. 'frontend/blocks.php');
    
	// use all the default blocks.
	class jzBlocks extends jzBlockClass {
	  function jzBlocks() {
		
	  } 
	}	

	class jzFrontend extends jzFrontendClass {
		function pageTop($title = false, $endBreak = "true", $ratingItem = ""){
			global $this_page, $img_home, $quick_list_truncate, $img_random_play, $cms_mode, 
			$random_play_amounts, $directory_level, $img_up_arrow, $header_drops, $genre_drop, $artist_drop, 
			$album_drop, $quick_drop, $root_dir, $web_root, $song_drop, $audio_types, $video_types, $media_dir, 
			$img_more,$img_random_play_dis, $url_seperator, $help_access, $jukebox, $jukebox_num,
			$disable_random, $jz_lang_file, $show_slimzora, $img_slim_pop, $allow_resample, $resampleRates, $default_random_type, 
			$default_random_count, $display_previous, $echocloud, $display_recommended, $enable_requests, $enable_ratings, 
			$enable_search, $enable_meta_search, $user_tracking_display, $user_tracking_admin_only, $site_title, $node, $jzUSER, $img_play, 
			$img_playlist, $jinzora_skin, $include_path,
			$img_play_dis, $img_random_play_dis, $img_download_dis, $img_add_dis, $img_playlist_dis, $allow_filesystem_modify, $disable_leftbar,
			$allow_interface_choice, $allow_style_choice, $allow_language_choice, $show_now_streaming, $show_who_is_where, $show_user_browsing, 
			$jukebox_height, $backend, $config_version, $allow_resample,$jukebox_display;
			
			// First let's include the settings for Netjuke
			include_once($include_path. "frontend/frontends/netjuke/settings.php");
			
			// Let's see if they wanted to pass a title
			if (!$title) { $title = $site_title; }			
			if (!isset($_GET['jz_path'])){$_GET['jz_path']="";}
										
			// Let's setup our objects
			$root = &new jzMediaNode();
			$display = &new jzDisplay();
			$blocks = new jzBlocks();
			
			// First let's see if our session vars are set for the number of items
			if (!isset($_SESSION['jz_num_genres'])){
				$_SESSION['jz_num_genres'] = $root->getSubNodeCount("nodes",distanceTo("genre"));
			}
			if (!isset($_SESSION['jz_num_artists'])){
				$_SESSION['jz_num_artists'] = $root->getSubNodeCount("nodes",distanceTo("artist"));
			}
			if (!isset($_SESSION['jz_num_albums'])){
				$_SESSION['jz_num_albums'] = $root->getSubNodeCount("nodes",distanceTo("album"));
			}
			if (!isset($_SESSION['jz_num_tracks'])){
				$_SESSION['jz_num_tracks'] = $root->getSubNodeCount("tracks",-1);
			}

			?>
			<a name="pageTop"></a>
			<table width="100%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td>
						<table width="100%" cellpadding="3" cellspacing="0" border="0">
							<tr>
								<td align="center" class="jz_block_td">
									<?php
										echo '<a href="' . urlize(array()) . '">';
										echo '<strong>BROWSE</strong>';
										echo '</a>';
									?>
								</td>
								<td align="center" class="jz_block_td">
									<strong>
								    <?php $urla = array();
										$urla['action'] = "powersearch";
										echo "<a href=\"".urlize($urla)."\">SEARCH</a>"; ?>
										</strong>
								</td>
								<td align="center" class="jz_block_td">
									<strong>
									<?php
										$display->randomPlayButton($node, false, word("RANDOM"));
									?>
									</strong>
								</td>
								<td align="center" class="jz_block_td">
									<strong><?php
									$urla['action'] = "popup";
									$urla['ptype'] = "playlistedit";
									echo "<a href=\"".urlize($urla)."\" onclick=\"openPopup(this, 550, 600); return false;\">PLAYLISTS</a>"; ?>
									</strong>
								</td>
								<td align="center" class="jz_block_td">
									<strong><?php
									$display->popupLink("preferences","PREFERENCES"); ?>
									</strong>
								</td>
								<td align="center" class="jz_block_td">
									<strong><?php $display->loginLink("LOGIN","LOGOUT"); ?></strong>
								</td>
							</tr>
						</table>
						<br>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>

								<?php
									// Can this user powersearch?
									if ($jzUSER->getSetting('powersearch')){
								?>
								<td align="center" valign="top">
									<table width="100%" cellpadding="3" cellspacing="0" border="0">
										<tr>
											<td class="jz_block_td">
												<?php 
													$url_search = array();
													$url_search['action'] = "powersearch";
													echo '<a href="' . urlize($url_search) . '">'; 
												?>
												<strong>QUICK SEARCH</strong></a>
											</td>
										</tr>
										<tr>
								<?php
								$onSubmit = "";
								if ($jukebox == "true" && !defined('NO_AJAX_JUKEBOX')) {
								  $onSubmit = 'onSubmit="return searchKeywords(this,\'' . htmlentities($this_page) . '\');"';
								}
								if ($cms_mode == "true") {
									$method = "GET";
								} else {
									$method = "POST";
								}
								?>
											<td class="jz_nj_block_body" align="center">
												<form action="<?php echo $this_page  ?>" name="searchForm" method="<?php echo $method; ?>" <?php echo $onSubmit; ?>>
												<?php foreach (getURLVars($this_page) as $key => $val) { echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) . '">'; } ?>
													<input type="text" name="search_query" class="jz_input" style="width:150px; font-size:10px; margin-bottom:3px;">
													<br>
													<select class="jz_select" name="search_type" style="width:85px">
														<option value="ALL"><?php echo word("All Media"); ?></option>
														<?php
														if (distanceTo("artist") !== false){
															echo '<option value="artists">'. word("Artists"). '</option>'. "\n";
														}
														if (distanceTo("album") !== false) {
															echo '<option value="albums">' . word("Albums"). '</option>'. "\n";
														}
														?>
														<option value="tracks"><?php echo word("Tracks"); ?></option>
														<option value="lyrics"><?php echo word("Lyrics"); ?></option>
													</select>
								                                        <input type="hidden" name="doSearch" value="true">
													<input type="submit" class="jz_submit" name="doSearch" value="Go">
												</form>
											</td>
										</tr>
									</table>
								</td>
								<?php
									// This ends the if they can powersearch statement
									}
								?>
								
								<?php
									// Are they resampling?
									if ($display->wantResampleDropdown($node)){
										$display->displayResampleDropdown($node);
										// PROBLEM: Currently can't use small jukebox and resampling. 
									} 
									if (checkPermission($jzUSER,"jukebox_queue") && ($jukebox_display == "small" or $jukebox_display == "minimal")) {
									?>
									<td align="center">&nbsp; &nbsp;</td>
									<td align="center" valign="top">
										<table width="100%" cellpadding="3" cellspacing="0" border="0">
											<tr>
												<td class="jz_block_td" width="100%" nowrap>
													<strong>PLAYBACK</strong>
												</td>
											</tr>
											<tr>
												<td class="jz_nj_block_body" align="left" width="1%" nowrap><div id="smallJukebox">
													<?php 
                                                    	$blocks->smallJukebox(false,"top");
													?>
												</div></td>
											</tr>
										</table>
									</td>
									<?php
                                                                        }
								?>
								
								
								
								<td align="center">&nbsp; &nbsp;</td>
								<td align="center" valign="top">
									<table width="100%" cellpadding="3" cellspacing="0" border="0">
										<tr>
											<td class="jz_block_td" colspan="3" width="100%" nowrap>
												<strong>CONTENT SUMMARY</strong>
											</td>
										</tr>
										<?php
											// Let's get the stats
											if (!isset($_SESSION['jz_total_tracks'])){
												$root = new jzMediaNode();
												$stats = $root->getStats();
												$_SESSION['jz_total_tracks'] = $stats['total_tracks'];
												$_SESSION['jz_total_genres'] = $stats['total_genres'];
												$_SESSION['jz_total_artists'] = $stats['total_artists'];
												$_SESSION['jz_total_albums'] = $stats['total_albums'];
												$_SESSION['jz_total_size'] = $stats['total_size_str'];
												$_SESSION['jz_total_length'] = $stats['total_length'];
											}
										?>
										<tr>
											<td class="jz_nj_block_body" align="center" width="1%" nowrap>
												<?php echo number_format($_SESSION['jz_total_tracks']); ?> Tracks
											</td>
											<td class="jz_nj_block_body" align="center" width="1%" nowrap>
												<?php echo number_format($_SESSION['jz_total_artists']); ?> Artists
											</td>
											<td class="jz_nj_block_body" align="center" width="1%" nowrap>
												<?php echo formatTime($_SESSION['jz_total_length']); ?>
											</td>
										</tr>
										<tr>
											<td class="jz_nj_block_body" align="center" width="1%" nowrap>
												<?php echo number_format($_SESSION['jz_total_albums']); ?> Albums
											</td>
											<td class="jz_nj_block_body" align="center" width="1%" nowrap>
												<?php echo number_format($_SESSION['jz_total_genres']); ?> Genres
											</td>
											<td class="jz_nj_block_body" align="center" width="1%" nowrap>
												<?php echo $_SESSION['jz_total_size']; ?>
											</td>
										</tr>
									</table>
								</td>
								<td align="center">&nbsp; &nbsp;</td>
								<td align="center" valign="top">
									<table width="100%" cellpadding="3" cellspacing="0" border="0">
										<tr>
											<td class="jz_block_td">
												<strong>ARTISTS A-Z <?php echo "(". $_SESSION['jz_num_artists']. ")"; ?></strong> - 
												<?php
													$urlar = array();
													//$urlar['jz_path'] = $node->getPath("String");
													$urlar['jz_level'] = distanceTo("artist");
													$urlar['jz_letter'] = "*";
													echo "<a href=\"".urlize($urlar) . "\">". word("All"). "</a>"; 
												?>
											</td>
										</tr>
										<tr>
											<td class="jz_nj_block_body" align="center">
												<?php 
													for ($let = 'A'; $let != 'Z'; $let++) {
														$urlar['jz_letter'] = $let;
														echo "<a href=\"" . urlize($urlar) . "\">".$let."</a> ";
														if ($let == 'L' or $let == 'X'){echo "<br>";}
													}
													$urlar['jz_letter'] = "Z";
													echo "<a href=\"".urlize($urlar) . "\">Z</a> "; 
													for ($let = '1'; $let != '10'; $let++) {
														$urlar['jz_letter'] = $let;
														echo "<a href=\"" . urlize($urlar) . "\">".$let."</a> ";
													}
													$urlar['jz_letter'] = "*";
													echo "<a href=\"".urlize($urlar) . "\">0</a>&nbsp;"; 
												?>
											</td>
										</tr>
									</table>
								</td>
								<td align="center">&nbsp; &nbsp;</td>
								<td align="center" valign="top">
									<table width="100%" cellpadding="3" cellspacing="0" border="0">
										<tr>
											<td class="jz_block_td">
												<strong>ALBUMS A-Z <?php echo "(". $_SESSION['jz_num_albums']. ")"; ?></strong> - 
												<?php
													$urlar['jz_level'] = distanceTo("album");
													$urla['jz_letter'] = "*";
													echo "<a href=\"".urlize($urlar) . "\">". word("All"). "</a>"; 
												?>
											</td>
										</tr>
										<tr>
											<td class="jz_nj_block_body" align="center">
												<?php 
													for ($let = 'A'; $let != 'Z'; $let++) {
														$urlar['jz_letter'] = $let;
														echo "<a href=\"" . urlize($urlar) . "\">".$let."</a> ";
														if ($let == 'L' or $let == 'X'){echo "<br>";}
													}
													$urlar['jz_letter'] = "Z";
													echo "<a href=\"".urlize($urlar) . "\">Z</a> "; 
													for ($let = '1'; $let != '10'; $let++) {
														$urlar['jz_letter'] = $let;
														echo "<a href=\"" . urlize($urlar) . "\">".$let."</a> ";
													}
													$urlar['jz_letter'] = "0";
													echo "<a href=\"".urlize($urlar) . "\">0</a>&nbsp;"; 
												?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						
						<?php
							// Are they in Jukebox mode?
							if (checkPermission($jzUSER,"jukebox_queue") && $jukebox_display != "small" && $jukebox_display != "off"){
							?>
							<br>
							<table width="100%" cellpadding="3" cellspacing="0" border="0">
								<tr>
									<td align="center" class="jz_block_td">
										<div id="jukebox">
							    <?php include(jzBlock('jukebox')); ?>
							                        </div>
									</td>
								</tr>
							</table>
							<?php
							}
						?>
					</td>
				</tr>
			</table>
			
			<?php
		}
		
		function footer($node = false){
			global $jinzora_url, $this_pgm, $version, $allow_lang_choice,
			  $this_page, $web_root, $root_dir, $allow_theme_change, $cms_mode, $jinzora_skin, $show_loggedin_level, $allow_interface_choice,
			$jz_lang_file, $shoutcast, $sc_refresh, $sc_host, $sc_port, $sc_password, $url_seperator, $jukebox, $show_jinzora_footer, 
			$hide_pgm_name, $media_dir, $img_sm_logo, $show_page_load_time, $allow_speed_choice, $img_play, $img_random_play, $img_playlist,
			$config_version, $jzUSER,$allow_style_choice, $jzSERVICES; 
			
			$display = new jzDisplay();

			// First let's make sure they didn't turn the footer off
			if ($show_jinzora_footer){
				?>
				<table width="100%" cellpadding="5" cellspacing="0" border="0">
					<tr>
						<td align="center" valign="top">
							<table width="100%" cellpadding="5" cellspacing="0" border="0">
								<tr>
				   					<td class="jz_block_td" align="center" width="25%">&nbsp;
										<?php 
										if ($allow_interface_choice == "true") {
											$display->interfaceDropdown();
										}
										if ($allow_interface_choice == "true" && $allow_style_choice == "true") {
											echo '&nbsp;';
										}
										if ($allow_style_choice == "true") {
											$display->styleDropdown(); 
										}
										
										?>
									</td>
									<td class="jz_block_td" align="center" width="50%">
										&raquo; Powered by Jinzora <?php echo $config_version; ?> &laquo;
									</td>
									<td class="jz_block_td" align="center" width="25%">&nbsp;
										<?php
											if ($jzUSER->getSetting("admin") == true && $node !== false) {
												$display->mediaManagementDropdown($node);
												echo "&nbsp;";
												$display->systemToolsDropdown($node);
											}
										?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table></td></tr></table>
				<a name="pageBottom"></a>
				<?php
			}
			$jzSERVICES->cmsClose();
		}
		
		function jzFrontend() {
			parent::_constructor();
		}
		// use all of the default functions.
	}
?>