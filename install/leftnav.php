<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	// Let's figure out the step
	if (isset($_GET['install'])){
		$step = substr($_GET['install'],4,1);
	} else {
		$step = 0;
	}
	
	// Now let's include the language file
	include_once($include_path. 'install/lang/english/lang.php');
	$lang = "english";
	if (isset($_POST['jz_lang_file'])){
		include_once($include_path. 'install/lang/'. $_POST['jz_lang_file']. '/lang.php');
		$lang = $_POST['jz_lang_file'];
	}
?>
<link rel="stylesheet" type="text/css" href="<?php echo $include_path; ?>install/style.css">
<script type="text/javascript" src="<?php echo $include_path; ?>lib/overlib.js"></script>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<?php 
			$titleStep = $step; 
			if ($titleStep == 0){
				$titleStep = 1;
			}
		?>
		<title>Jinzora <?php echo $version; ?> Installer - Step <?php echo $titleStep; ?></title>
	</head>
	<body>
		<div id="box">
			<div id="navbar">
				<h2><?php echo $word_install_steps; ?></h2>
				<ol>
					<?php
						if ($step == 1 or $step == 0){
							echo '<li class="navitem_sel">';
						} else {
							echo '<li class="navitem">';
						}
					?>					
						<?php
							if ($step > 1){
								$step_num = 1;
								echo '<form action="index.php?install=step'. $step_num. '" name="setup'. $step_num. '" method="post">';
								$PostArray = $_POST;
								foreach ($PostArray as $key => $val) {
									if (!stristr($key,"submit")){
										echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
									}
								}
								echo '<span class="success">&radic;</span> &nbsp;<span class="nav_text"><strong>';
								echo '<a href="javascript:void(0);" onClick="setup'. $step_num. '.submit();"><nobr>'. $word_language. "</nobr></a>";
								echo '</strong></span>';
								echo '</form>';
							} else {
								if ($step == 1 or $step == 0){
									echo '<span class="nav_num">1</span> &nbsp;<span class="nav_text"><em><strong><nobr>- '. $word_language. ' -</nobr></strong></em></span>';
								} else {
									echo '<span class="nav_num">1</span> &nbsp;<span class="nav_text"><nobr>'. $word_language. '</nobr></span>';
								}
							}
						?>
					</li>
					<?php
						if ($step == 2){
							echo '<li class="navitem_sel">';
						} else {
							echo '<li class="navitem">';
						}
					?>
					<?php
						if ($step > 2){
							$step_num = 2;
							echo '<form action="index.php?install=step'. $step_num. '" name="setup'. $step_num. '" method="post">';
							$PostArray = $_POST;
							foreach ($PostArray as $key => $val) {
								if (!stristr($key,"submit")){
									echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
								}
							}
							echo '<span class="success">&radic;</span> &nbsp;<span class="nav_text"><strong>';
							echo '<a href="javascript:void(0);" onClick="setup'. $step_num. '.submit();"><nobr>'. $word_package_verify. "</nobr></a>";
							echo '</strong></span>';
							echo '</form>';							
						} else {
							if ($step == 2){
								echo '<span class="nav_num">2</span> &nbsp;<span class="nav_text"><em><strong><nobr>- '. $word_package_verify. ' -</nobr></strong></em></span>';
							} else {
								echo '<span class="nav_num">2</span> &nbsp;<span class="nav_text"><nobr>'. $word_package_verify. '</nobr></span>';
							}
						}
					?>
				</li>
				<?php
					if ($step == 3){
						echo '<li class="navitem_sel">';
					} else {
						echo '<li class="navitem">';
					}
				?>
					<?php
						if ($step > 3){
							$step_num = 3;
							echo '<form action="index.php?install=step'. $step_num. '" name="setup'. $step_num. '" method="post">';
							$PostArray = $_POST;
							foreach ($PostArray as $key => $val) {
								if (!stristr($key,"submit")){
									echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
								}
							}
							echo '<span class="success">&radic;</span> &nbsp;<span class="nav_text"><strong>';
							echo '<a href="javascript:void(0);" onClick="setup'. $step_num. '.submit();"><nobr>'. $word_license. "</nobr></a>";
							echo '</strong></span>';
							echo '</form>';
						} else {
							if ($step == 3){
								echo '<span class="nav_num">3</span> &nbsp;<span class="nav_text"><em><strong><nobr>- '. $word_license. ' -</nobr></strong></em></span>';
							} else {
								echo '<span class="nav_num">3</span> &nbsp;<span class="nav_text"><nobr>'. $word_license. '</nobr></span>';
							}
						}
					?>
				</li>
				<?php
					if ($step == 4){
						echo '<li class="navitem_sel">';
					} else {
						echo '<li class="navitem">';
					}
				?>
					<?php
						if ($step > 4){
							$step_num = 4;
							echo '<form action="index.php?install=step'. $step_num. '" name="setup'. $step_num. '" method="post">';
							$PostArray = $_POST;
							foreach ($PostArray as $key => $val) {
								if (!stristr($key,"submit")){
									echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
								}
							}
							echo '<span class="success">&radic;</span> &nbsp;<span class="nav_text"><strong>';
							echo '<a href="javascript:void(0);" onClick="setup'. $step_num. '.submit();"><nobr>'. $word_install_type. "</nobr></a>";
							echo '</strong></span>';
							echo '</form>';
						} else {
							if ($step == 4){
								echo '<span class="nav_num">4</span> &nbsp;<span class="nav_text"><em><strong><nobr>- '. $word_install_type. ' -</nobr></strong></em></span>';
							} else {
								echo '<span class="nav_num">4</span> &nbsp;<span class="nav_text"><nobr>'. $word_install_type. '</nobr></span>';
							}
						}
					?>
				</li>
				<?php
					if ($step == 5){
						echo '<li class="navitem_sel">';
					} else {
						echo '<li class="navitem">';
					}
				?>
					<?php
						if ($step > 5){
							$step_num = 5;
							echo '<form action="index.php?install=step'. $step_num. '" name="setup'. $step_num. '" method="post">';
							$PostArray = $_POST;
							foreach ($PostArray as $key => $val) {
								if (!stristr($key,"submit")){
									echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
								}
							}
							echo '<span class="success">&radic;</span> &nbsp;<span class="nav_text"><strong>';
							echo '<a href="javascript:void(0);" onClick="setup'. $step_num. '.submit();"><nobr>'. $word_main_settings. "</nobr></a>";
							echo '</strong></span>';
							echo '</form>';
						} else {
							if ($step == 5){
								echo '<span class="nav_num">5</span> &nbsp;<span class="nav_text"><em><strong><nobr>- '. $word_main_settings. ' -</nobr></strong></em></span>';
							} else {
								echo '<span class="nav_num">5</span> &nbsp;<span class="nav_text"><nobr>'. $word_main_settings. '</nobr></span>';
							}
						}
					?>
				</li>
				<?php
					if ($step == 6){
						echo '<li class="navitem_sel">';
					} else {
						echo '<li class="navitem">';
					}
				?>
					<?php
						if ($step > 6){
							$step_num = 6;
							echo '<form action="index.php?install=step'. $step_num. '" name="setup'. $step_num. '" method="post">';
							$PostArray = $_POST;
							foreach ($PostArray as $key => $val) {
								if (!stristr($key,"submit")){
									echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
								}
							}
							echo '<span class="success">&radic;</span> &nbsp;<span class="nav_text"><strong>';
							echo '<a href="javascript:void(0);" onClick="setup'. $step_num. '.submit();">'. $word_backend_setup. "</nobr></a>";
							echo '</strong></span>';
							echo '</form>';
						} else {
							if ($step == 6){
								echo '<span class="nav_num">6</span> &nbsp;<span class="nav_text"><em><strong><nobr>- '. $word_backend_setup. ' -</nobr></strong></em></span>';
							} else {
								echo '<span class="nav_num">6</span> &nbsp;<span class="nav_text"><nobr>'. $word_backend_setup. '</nobr></span>';
							}
						}
					?>
				</li>
				<?php
					if ($step == 7){
						echo '<li class="navitem_sel">';
					} else {
						echo '<li class="navitem">';
					}
				?>
					<?php
						if ($step > 7){
							$step_num = 7;
							echo '<form action="index.php?install=step'. $step_num. '" name="setup'. $step_num. '" method="post">';
							$PostArray = $_POST;
							foreach ($PostArray as $key => $val) {
								if (!stristr($key,"submit")){
									echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
								}
							}
							echo '<span class="success">&radic;</span> &nbsp;<span class="nav_text"><strong>';
							echo '<a href="javascript:void(0);" onClick="setup'. $step_num. '.submit();"><nobr>'. $word_import_media. "</nobr></a>";
							echo '</strong></span>';
							echo '</form>';
						} else {
							if ($step == 7){
								echo '<span class="nav_num">7</span> &nbsp;<span class="nav_text"><em><strong><nobr>- '. $word_import_media. ' -</nobr></strong></em></span>';
							} else {
								echo '<span class="nav_num">7</span> &nbsp;<span class="nav_text"><nobr>'. $word_import_media. '</nobr></span>';
							}
						}
					?>
				</li>
				<?php
					if ($step == 8){
						echo '<li class="navitem_sel">';
					} else {
						echo '<li class="navitem">';
					}
				?>
					<?php
						if ($step > 8){
							$step_num = 8;
							echo '<form action="index.php?install=step'. $step_num. '" name="setup'. $step_num. '" method="post">';
							$PostArray = $_POST;
							foreach ($PostArray as $key => $val) {
								if (!stristr($key,"submit")){
									echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
								}
							}
							echo '<span class="success">&radic;</span> &nbsp;<span class="nav_text"><strong>';
							echo '<a href="javascript:void(0);" onClick="setup'. $step_num. '.submit();"><nobr>'. $word_save_config. "</nobr></a>";
							echo '</strong></span>';
							echo '</form>';
						} else {
							if ($step == 8){
								echo '<span class="nav_num">8</span> &nbsp;<span class="nav_text"><em><strong><nobr>- '. $word_save_config. ' -</nobr></strong></em></span>';
							} else {
								echo '<span class="nav_num">8</span> &nbsp;<span class="nav_text"><nobr>'. $word_save_config. '</nobr></span>';
							}
						}
					?>
				</li>
				<?php
					if ($step == 9){
						echo '<li class="navitem_sel">';
					} else {
						echo '<li class="navitem">';
					}
				?>
					<?php
						if ($step > 9){
							$step_num = 9;
							echo '<form action="index.php?install=step'. $step_num. '" name="setup'. $step_num. '" method="post">';
							$PostArray = $_POST;
							foreach ($PostArray as $key => $val) {
								if (!stristr($key,"submit")){
									echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
								}
							}
							echo '<span class="success">&radic;</span> &nbsp;<span class="nav_text"><strong>';
							echo '<a href="javascript:void(0);" onClick="setup'. $step_num. '.submit();"><nobr>'. $word_launch_jinzora. "</nobr></a>";
							echo '</strong></span>';
							echo '</form>';
						} else {
							if ($step == 9){
								echo '<span class="nav_num">9</span> &nbsp;<span class="nav_text"><em><strong><nobr>- '. $word_launch_jinzora. ' -</nobr></strong></em></span>';
							} else {
								echo '<span class="nav_num">9</span> &nbsp;<span class="nav_text"><nobr>'. $word_launch_jinzora. '</nobr></span>';
							}
						}
					?>
				</li>
				</ol>
				<ul>
					<li><span class="helpBox">?</span><a href="http://www.jinzorahelp.com/wiki/Web_Based_Installer" target="_blank"><?php echo $word_installer_help; ?></a></li>
				</ul>
			</div>