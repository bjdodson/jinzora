<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	/**
	* - JINZORA | Web-based Media Streamer -  
	* 
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL.
	* 
	* - Resources -
	* - Jinzora Author: Ross Carlson <ross@jasbone.com>
	* - Web: http://www.jinzora.org
	* - Documentation: http://www.jinzora.org/docs	
	* - Support: http://www.jinzora.org/forum
	* - Downloads: http://www.jinzora.org/downloads
	* - License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* - Contributors -
	* Please see http://www.jinzora.org/team.html
	* 
	* - Code Purpose -
	* - Resample or transcode files on the fly to MP3 so they can more easily be streamed
	*
	* @since 05.25.05
	* @author Ross Carlson <ross@jinzora.org>
	* @author Ben Dodson <ben@jinzora.org>
	*/
	 
	define('SERVICE_RESAMPLE_resample','true');
	

	/**
	* Creates a resampled track
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 06.10.05
	* @since 06.10.05
	* @param $file The file that we are resampling/transcoding
	*/
	function SERVICE_CREATE_RESAMPLED_TRACK($file, $format, $bitrate, $meta, $destination = false){
		global $path_to_lame, $path_to_flac, $path_to_mpc, $path_to_wavpack, 
			   $path_to_oggdec, $path_to_oggenc, $lame_cmd, $include_path, 
			   $jzSERVICES, $path_to_mpcenc, $path_to_wavunpack, $path_to_wmadec,
				 $path_to_mplayer, $mplayer_opts, $path_to_faad;
			   
		// Ok, now based on the input file let's create the beginning of the command
		$extArr = explode(".",$file);
		$ext = $extArr[count($extArr)-1];

		// Now let's create the output filename
		// Did they specify one?
		if ($destination){
			$outFile = $destination;
		} else {
			$outArry = explode("/",$file);
			$outFile = $outArry[count($outArry)-1];
			$outFile = getcwd(). "/data/resampled/". str_replace($ext,$format,$outFile);
			$outFile = str_replace(".". $format,"",$outFile). "-". $bitrate. ".". $format;
		}

		// Now, does the output file already exist?  If so let's NOT do this again, ok?
		if (is_file($outFile)){
			return $outFile;
		}

		switch ($ext){
			case "flac":
				$command = $path_to_flac. ' -d -c --totally-silent "'. $file. '"';
			break;
			case "mpc":
				$command = $path_to_mpc. ' --wav "'. $file. '"';
			break;
			case "mp3":
				$command = $path_to_lame. ' --decode -S --silent --quiet "'.  $file. '" - '; 
			break;
			case "wv":
				$command = $path_to_wavunpack. ' -q "'.  $file. '" - '; 
			break;
			case "ogg":
				if (stristr($path_to_oggdec,"oggdec")){
				  //$command = $path_to_oggdec. ' --stdout "'. $file. '"';
				  $command = $path_to_oggdec . ' -Q "' . $file . '" -o -';
				} else {
					$command = $path_to_oggdec. ' --skip 1 -q -d wav -f - "'. $file. '"';
				}
			break;
			case "wma":
				$command .= ' | '. $path_to_wmadec. ' -w "'. $file. '"';
			break;
			case "wav":
			case "ra":
			case "ram":
			case "rm":
			case "m4a":
					$command = $path_to_mplayer. ' ' . $mplayer_opts . ' "' . $file . '"';
			break;
			default:
			  return false;
			break;
		}

		// Ok, now that we have the input command let's create the output command
		switch ($format){
			case "mp3":
				// Now let's add the proper options to the lame command
				$command .= ' | '. $lame_cmd. $bitrate . ' -f -  > "'. $outFile. '"';
			break;
			case "wav":
				$command .= ' > "'. $outFile. '"';
			break;
			case "mpc":
				// First let's figure out the quality setting
				switch ($bitrate){
					case "128":
						$quality = " --standard";
					break;
					case "192":
						$quality = " --xtreme";
					break;
					case "320":
					case "original":
						$quality = " --insane";
					break;
				}
				$command .= ' | '. $path_to_mpcenc. $quality. ' --silent --overwrite --standard - "'. $outFile. '"';
			break;
			case "wv":
				$command .= ' | '. $path_to_wavpack. ' -y -i -q - "'. $outFile. '"';
			break;
			case "flac":
				$command .= ' | '. $path_to_flac. ' --totally-silent - > "'.  $outFile. '" ';
			break;
			case "ogg":
			  return false;
			break;
			default:
			  return false;
			break;
		}
		
		// Now let's fix up the paths for Windows
		if (stristr($_ENV['OS'],"win")){
			$command = str_replace("/","\\",$command);
		}

		// Let's log the command we just passed
		writeLogData("resample-command",$command);

		// Now let's execute the command
		exec($command);

		// Ok, now let's write the meta data to our new track
		$jzSERVICES->setTagData($outFile, $meta);
		
		// Now let's return the newly created filename
		return $outFile;
	}
	

	/**
	* Returns if the file is resampleable
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 06.10.05
	* @since 06.10.05
	* @param $file The file that we are resampling/transcoding
	*/
	function SERVICE_IS_RESAMPLABLE($file){
		
		// Ok, let's check the file extension and see if we can resample it
		//Now let's figure out the file type
		$extArr = explode(".",$file);
		$ext = $extArr[count($extArr)-1];
		
		switch ($ext){
			case "mp3":
			case "flac":
			case "mpc":
			case "wv":
			case "ogg":
			case "wav":
			case "wma":
			case "ra":
			case "ram":
			case "rm":
			case "m4a":
			  return true;
			break;
			default:
			  return false;
			break;
		}
	}
	 
	/**
	* The general resample/transcode function
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 05.25.05
	* @since 05.25.05
	* @param $file The file that we are resampling/transcoding
	* @param $name The name of the stream
	* @param $resample The rate to resample/transcode to
	*/
	function SERVICE_RESAMPLE($file, $name, $resample){
		global $path_to_lame, $path_to_flac, $path_to_mpc, $path_to_wavunpack, 
		 	   $path_to_oggdec, $lame_cmd, $path_to_wmadec, $path_to_shn,
				 $path_to_mplayer, $mplayer_opts, $path_to_faad, $path_to_macpipe, $path_to_ofr;
		

		$jzSERVICES = new jzServices();
		$jzSERVICES->loadStandardServices();
		// Now let's add the proper options to the lame command
		$lame_cmd .= $resample . ' -f -';
		
		//Now let's figure out the file type
		$extArr = explode(".",$file);
		$ext = $extArr[count($extArr)-1];

		// Now if we're on Windows we need to change the slashes for the command line
		if (stristr($_ENV['OS'],"win")){
			$file = str_replace("/","\\",$file);
		}
		
		switch ($ext){
			case "mp3":
			  $meta = $jzSERVICES->getTagData($file);
			  if ($meta['bitrate'] <= $resample) {
			    header("Content-Type: audio/x-mp3");
			    streamFile($file,$meta['artist'] . $meta['title'], $resample);
			    exit();
			  } else {
			    $command = $path_to_lame. " --mp3input -S --silent --quiet --lowpass 12.0 --resample 22.05 -m j -b ". $resample. ' - < "'.  $file. '" -';
			  }
			break;
			case "flac":
			  $command = $path_to_flac. ' -d -c --totally-silent "'. $file. '" | '. $lame_cmd;
			break;
			case "mpc":
			  $command = $path_to_mpc. ' --wav "'. $file. '" | '. $lame_cmd;
			break;
			case "wv":
			  $command = $path_to_wavunpack. ' -q "'. $file. '" - | '. $lame_cmd;
			break;
			case "ogg":
			  // Ok, are they using oggdec or ogg123?
			  if (stristr($path_to_oggdec,"oggdec")){
			    //$command = $path_to_oggdec. ' --stdout "'. $file. '" | '. $lame_cmd;
			    $command = $path_to_oggdec. ' -Q "'. $file. '" -o - | '. $lame_cmd;
			  } else {
			  	$command = $path_to_oggdec. ' --skip 1 -q -d wav -f - "'. $file. '" | '. $lame_cmd;
			  }
			break;
			case "wav":
			 $command = $path_to_lame. " -S --silent --quiet --lowpass 12.0 --resample 22.05 -m j -b ". $resample. ' - < "'.  $file. '" -';
			break;
			case "shn":
				if (stristr($_ENV['OS'],"win")){
					$command = $path_to_shn. ' -x "' . $file. '" - | '. str_replace(" -S --silent"," -x -S --silent",$lame_cmd);
				} else {
					$command = $path_to_shn. ' -x "' . $file. '" - | '. $lame_cmd;
				}
			break;
			case "wma":
				$command = $path_to_wmadec. ' -w "' . $file. '" | '. $lame_cmd;
			break;
			case "ape":
				$command = $path_to_macpipe. ' "' . $file. '" - -d | '. $lame_cmd;
			break;
			case "ofr":
				$command = $path_to_ofr. ' --decode --silent "' . $file. '" --output - | '. str_replace(" -S --silent"," -x -S --silent",$lame_cmd);
			break;
			case "ra":
			case "ram":
			case "rm":
			case "m4a":
				if (stristr($_ENV['OS'],"win")){
					$command = $path_to_faad. ' -w "' . $file. '" | '. str_replace(" -S --silent"," -x -S --silent",$lame_cmd);
				} else {
					$command = $path_to_mplayer. ' ' . $mplayer_opts . ' "' . $file . '" | '. $lame_cmd;
				}
			break; 
			default:
			  exit();
			break;
		}

		// Let's log the command we just passed
		writeLogData("resample-command",$command);
		
		// Now let's send the resampled data
		sendResampledFile($command,$name);
		exit();
	}
	
	/**
	* Sends the resampled/transcoded file
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 05.25.05
	* @since 05.25.05
	* @param $command The command to execute to resample/transcode
	* @param $name The name of the stream
	*/
	function sendResampledFile($command, $name){
		// Now let's send the header
		// CRUCIAL: //
		ignore_user_abort(false);
		header("ICY 200 OK");
		header("icy-name: $name");
		header("Content-type: audio/mpeg");
		// header("Content-length: ".(string)(filesize($file))); // TODO: get the real filesize.
		header("Content-Disposition: inline; filename=\"".$name."\"");
		header("Connection: close");
		passthru($command);	
	}
?>
