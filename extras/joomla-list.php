<?php
	function readAllDirs($dirName, &$readCtr, &$mainArray, $searchExt = "false", $displayProgress = "false"){
		
		// Let's up the max_execution_time
		ini_set('max_execution_time','600');
		
		// Let's look at the directory we are in		
		if (is_dir($dirName)){
			$d = dir($dirName);
			while($entry = $d->read()) {
				// Let's make sure we are seeing real directories
				if ($entry == "." || $entry == "..") { continue;}
				
				// Now let's see if we are looking at a directory or not
				if (filetype($dirName. "/". $entry) <> "file"){
					// Ok, that was a dir, so let's move to the next directory down
					readAllDirs($dirName. "/". $entry, $readCtr, $mainArray, $searchExt, $displayProgress);
				} else {			
					$mainArray[$readCtr] = $dirName. "/". $entry;
					$readCtr++;
				}			
			}
			// Now let's close the directory
			$d->close();
			
			// Now let's sort that array
			@sort($mainArray);
		}		
		// Ok, let's return the data
		return $mainArray;
	}
	
	$ctr = 0;
	$dir = "C:/Reactor/Core/htdocs/filelist/";
	$fArr = readAllDirs($dir, $ctr, $fArr);

	foreach($fArr as $file){
		echo str_replace($dir. "/","",$file). "<br>";
		flush();
		ob_flush();
	}
?>