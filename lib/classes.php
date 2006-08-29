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
	* Code Purpose: This page contains all the classes used by Jinzora
	* Created: 9.24.03 by Ross Carlson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	
	// This class let's us access our flat file database
	class jz_db{
		// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		//
		// Example usage
		//
		// SELECT
		// $query = new jz_db;
		// $result = $query->select("select where GENRE = Jazz");
		//
		// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		
		// This function will query our database for us
		function select($query){
			global $root_dir, $web_root;
			
			// Now we need to figure out which colum they wanted to search on
			$queryCol = trim(substr(str_replace("select where ","",$query),0,strpos(str_replace("select where ","",$query)," =")));
			$queryVal = trim(substr($query,strpos($query," = ")+3,strlen($query)));
						
			// Let's make sure our database is really there
			$database = $web_root. $root_dir. "/data/jz_database.csv";
			if (!is_file($database)){
				die ("Database not found at: ". $web_root. $root_dir. "/data/jz_database.csv");
			}
			
			// Ok, now let's open the database and read the data in it
			$handle = fopen ($database, "rb");
			$contents = fread ($handle, filesize ($database));
			fclose ($handle);
			
			// Now let's query that file by breaking it out into rows
			$e=0;
			$dataArray = explode("\n",$contents);
			for ($c=0; $c < count($dataArray); $c++){
				// Let's ignore the first row, since it has the column names in it
				if ($c <> 0){
					// Ok, now we know what row to match on so let's see if we have a match
					$colArray = explode(",",$dataArray[$c]);
					if ($colArray[$qCol] == $queryVal){
						$finalArray[$e] = $dataArray[$c];
						$e++;
					}
				} else {
					// Ok, this is the first row so let's get all the colums
					$colArray = explode(",",$dataArray[$c]);
					// Now let's find the colum they were querying on
					for ($i=0; $i < count($colArray); $i++){
						if ($queryCol == $colArray[$i]){
							$qCol = $i;
						}
					}
				}
			}
			return $finalArray;
		}
	}
	
	
	/*
	 * genres - reuturns an array of the ID3v1 genres
	 */
	function genres() {
	return array(
		0   => 'Blues',
		1   => 'Classic Rock',
		2   => 'Country',
		3   => 'Dance',
		4   => 'Disco',
		5   => 'Funk',
		6   => 'Grunge',
		7   => 'Hip-Hop',
		8   => 'Jazz',
		9   => 'Metal',
		10  => 'New Age',
		11  => 'Oldies',
		12  => 'Other',
		13  => 'Pop',
		14  => 'R&B',
		15  => 'Rap',
		16  => 'Reggae',
		17  => 'Rock',
		18  => 'Techno',
		19  => 'Industrial',
		20  => 'Alternative',
		21  => 'Ska',
		22  => 'Death Metal',
		23  => 'Pranks',
		24  => 'Soundtrack',
		25  => 'Euro-Techno',
		26  => 'Ambient',
		27  => 'Trip-Hop',
		28  => 'Vocal',
		29  => 'Jazz+Funk',
		30  => 'Fusion',
		31  => 'Trance',
		32  => 'Classical',
		33  => 'Instrumental',
		34  => 'Acid',
		35  => 'House',
		36  => 'Game',
		37  => 'Sound Clip',
		38  => 'Gospel',
		39  => 'Noise',
		40  => 'Alternative Rock',
		41  => 'Bass',
		42  => 'Soul',
		43  => 'Punk',
		44  => 'Space',
		45  => 'Meditative',
		46  => 'Instrumental Pop',
		47  => 'Instrumental Rock',
		48  => 'Ethnic',
		49  => 'Gothic',
		50  => 'Darkwave',
		51  => 'Techno-Industrial',
		52  => 'Electronic',
		53  => 'Pop-Folk',
		54  => 'Eurodance',
		55  => 'Dream',
		56  => 'Southern Rock',
		57  => 'Comedy',
		58  => 'Cult',
		59  => 'Gangsta',
		60  => 'Top 40',
		61  => 'Christian Rap',
		62  => 'Pop/Funk',
		63  => 'Jungle',
		64  => 'Native US',
		65  => 'Cabaret',
		66  => 'New Wave',
		67  => 'Psychadelic',
		68  => 'Rave',
		69  => 'Showtunes',
		70  => 'Trailer',
		71  => 'Lo-Fi',
		72  => 'Tribal',
		73  => 'Acid Punk',
		74  => 'Acid Jazz',
		75  => 'Polka',
		76  => 'Retro',
		77  => 'Musical',
		78  => 'Rock & Roll',
		79  => 'Hard Rock',
		80  => 'Folk',
		81  => 'Folk-Rock',
		82  => 'National Folk',
		83  => 'Swing',
		84  => 'Fast Fusion',
		85  => 'Bebob',
		86  => 'Latin',
		87  => 'Revival',
		88  => 'Celtic',
		89  => 'Bluegrass',
		90  => 'Avantgarde',
		91  => 'Gothic Rock',
		92  => 'Progressive Rock',
		93  => 'Psychedelic Rock',
		94  => 'Symphonic Rock',
		95  => 'Slow Rock',
		96  => 'Big Band',
		97  => 'Chorus',
		98  => 'Easy Listening',
		99  => 'Acoustic',
		100 => 'Humour',
		101 => 'Speech',
		102 => 'Chanson',
		103 => 'Opera',
		104 => 'Chamber Music',
		105 => 'Sonata',
		106 => 'Symphony',
		107 => 'Booty Bass',
		108 => 'Primus',
		109 => 'Porn Groove',
		110 => 'Satire',
		111 => 'Slow Jam',
		112 => 'Club',
		113 => 'Tango',
		114 => 'Samba',
		115 => 'Folklore',
		116 => 'Ballad',
		117 => 'Power Ballad',
		118 => 'Rhytmic Soul',
		119 => 'Freestyle',
		120 => 'Duet',
		121 => 'Punk Rock',
		122 => 'Drum Solo',
		123 => 'Acapella',
		124 => 'Euro-House',
		125 => 'Dance Hall',
		126 => 'Goa',
		127 => 'Drum & Bass',
		128 => 'Club-House',
		129 => 'Hardcore',
		130 => 'Terror',
		131 => 'Indie',
		132 => 'BritPop',
		133 => 'Negerpunk',
		134 => 'Polsk Punk',
		135 => 'Beat',
		136 => 'Christian Gangsta Rap',
		137 => 'Heavy Metal',
		138 => 'Black Metal',
		139 => 'Crossover',
		140 => 'Contemporary Christian',
		141 => 'Christian Rock',
		142 => 'Merengue',
		143 => 'Salsa',
		144 => 'Trash Metal',
		145 => 'Anime',
		146 => 'Jpop',
		147 => 'Synthpop'
			);
	} // genres
	
	/*
	
	Zip file creation class
	makes zip files on the fly...
	
	use the functions add_dir() and add_file() to build the zip file;
	see example code below
	
	by Eric Mueller
	http://www.themepark.com
	
	v1.1 9-20-01
	  - added comments to example
	
	v1.0 2-5-01
	
	initial version with:
	  - class appearance
	  - add_file() and file() methods
	  - gzcompress() output hacking
	by Denis O.Philippov, webmaster@atlant.ru, http://www.atlant.ru
	
	*/  
	
	// official ZIP file format: http://www.pkware.com/appnote.txt
	
	class zipfile   
	{   
	
		var $datasec = array(); // array to store compressed data
		var $ctrl_dir = array(); // central directory    
		var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00"; //end of Central directory record
		var $old_offset = 0;  
	
		function add_dir($name)    
	
		// adds "directory" to archive - do this before putting any files in directory!
		// $name - name of directory... like this: "path/"
		// ...then you can add files using add_file with names like "path/file.txt"
		{   
			$name = str_replace("\\", "/", $name);   
	
			$fr = "\x50\x4b\x03\x04";  
			$fr .= "\x0a\x00";    // ver needed to extract
			$fr .= "\x00\x00";    // gen purpose bit flag
			$fr .= "\x00\x00";    // compression method
			$fr .= "\x00\x00\x00\x00"; // last mod time and date
	
			$fr .= pack("V",0); // crc32
			$fr .= pack("V",0); //compressed filesize
			$fr .= pack("V",0); //uncompressed filesize
			$fr .= pack("v", strlen($name) ); //length of pathname
			$fr .= pack("v", 0 ); //extra field length
			$fr .= $name;   
			// end of "local file header" segment
	
			// no "file data" segment for path
	
			// "data descriptor" segment (optional but necessary if archive is not served as file)
			$fr .= pack("V",$crc); //crc32
			$fr .= pack("V",$c_len); //compressed filesize
			$fr .= pack("V",$unc_len); //uncompressed filesize
	
			// add this entry to array
			$this -> datasec[] = $fr;  
	
			$new_offset = strlen(implode("", $this->datasec));  
	
			// ext. file attributes mirrors MS-DOS directory attr byte, detailed
			// at http://support.microsoft.com/support/kb/articles/Q125/0/19.asp
	
			// now add to central record
			$cdrec = "\x50\x4b\x01\x02";  
			$cdrec .="\x00\x00";    // version made by
			$cdrec .="\x0a\x00";    // version needed to extract
			$cdrec .="\x00\x00";    // gen purpose bit flag
			$cdrec .="\x00\x00";    // compression method
			$cdrec .="\x00\x00\x00\x00"; // last mod time & date
			$cdrec .= pack("V",0); // crc32
			$cdrec .= pack("V",0); //compressed filesize
			$cdrec .= pack("V",0); //uncompressed filesize
			$cdrec .= pack("v", strlen($name) ); //length of filename
			$cdrec .= pack("v", 0 ); //extra field length    
			$cdrec .= pack("v", 0 ); //file comment length
			$cdrec .= pack("v", 0 ); //disk number start
			$cdrec .= pack("v", 0 ); //internal file attributes
			$ext = "\x00\x00\x10\x00";  
			$ext = "\xff\xff\xff\xff";   
			$cdrec .= pack("V", 16 ); //external file attributes  - 'directory' bit set
	
			$cdrec .= pack("V", $this -> old_offset ); //relative offset of local header
			$this -> old_offset = $new_offset;  
	
			$cdrec .= $name;   
			// optional extra field, file comment goes here
			// save to array
			$this -> ctrl_dir[] = $cdrec;   
	
			  
		}  
	
	
		function add_file($data, $name)    
	
		// adds "file" to archive    
		// $data - file contents
		// $name - name of file in archive. Add path if your want
	
		{   
			$name = str_replace("\\", "/", $name);   
			//$name = str_replace("\\", "\\\\", $name);
	
			$fr = "\x50\x4b\x03\x04";  
			$fr .= "\x14\x00";    // ver needed to extract
			$fr .= "\x00\x00";    // gen purpose bit flag
			$fr .= "\x08\x00";    // compression method
			$fr .= "\x00\x00\x00\x00"; // last mod time and date
	
			$unc_len = strlen($data);   
			$crc = crc32($data);   
			$zdata = gzcompress($data);   
			$zdata = substr( substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
			$c_len = strlen($zdata);   
			$fr .= pack("V",$crc); // crc32
			$fr .= pack("V",$c_len); //compressed filesize
			$fr .= pack("V",$unc_len); //uncompressed filesize
			$fr .= pack("v", strlen($name) ); //length of filename
			$fr .= pack("v", 0 ); //extra field length
			$fr .= $name;   
			// end of "local file header" segment
			  
			// "file data" segment
			$fr .= $zdata;   
	
			// "data descriptor" segment (optional but necessary if archive is not served as file)
			$fr .= pack("V",$crc); //crc32
			$fr .= pack("V",$c_len); //compressed filesize
			$fr .= pack("V",$unc_len); //uncompressed filesize
	
			// add this entry to array
			$this -> datasec[] = $fr;  
	
			$new_offset = strlen(implode("", $this->datasec));  
	
			// now add to central directory record
			$cdrec = "\x50\x4b\x01\x02";  
			$cdrec .="\x00\x00";    // version made by
			$cdrec .="\x14\x00";    // version needed to extract
			$cdrec .="\x00\x00";    // gen purpose bit flag
			$cdrec .="\x08\x00";    // compression method
			$cdrec .="\x00\x00\x00\x00"; // last mod time & date
			$cdrec .= pack("V",$crc); // crc32
			$cdrec .= pack("V",$c_len); //compressed filesize
			$cdrec .= pack("V",$unc_len); //uncompressed filesize
			$cdrec .= pack("v", strlen($name) ); //length of filename
			$cdrec .= pack("v", 0 ); //extra field length    
			$cdrec .= pack("v", 0 ); //file comment length
			$cdrec .= pack("v", 0 ); //disk number start
			$cdrec .= pack("v", 0 ); //internal file attributes
			$cdrec .= pack("V", 32 ); //external file attributes - 'archive' bit set
	
			$cdrec .= pack("V", $this -> old_offset ); //relative offset of local header
	//        echo "old offset is ".$this->old_offset.", new offset is $new_offset<br>";
			$this -> old_offset = $new_offset;  
	
			$cdrec .= $name;   
			// optional extra field, file comment goes here
			// save to central directory
			$this -> ctrl_dir[] = $cdrec;   
		}  
	
		function file() { // dump out file    
			
			
			$data = implode("", $this -> datasec);   
			$ctrldir = implode("", $this -> ctrl_dir);   
	
			return    
				$data.   
				$ctrldir.   
				$this -> eof_ctrl_dir.   
				pack("v", sizeof($this -> ctrl_dir)).     // total # of entries "on this disk"
				pack("v", sizeof($this -> ctrl_dir)).     // total # of entries overall
				pack("V", strlen($ctrldir)).             // size of central dir
				pack("V", strlen($data)).                 // offset to start of central dir
				"\x00\x00";                             // .zip file comment length
		}  
	}  
	
	
	class SCXML {

/* DO NOT CHANGE ANYTHING FROM THIS POINT ON - THIS MEANS YOU !!! */

  var $depth = 0;
  var $lastelem= array();
  var $xmlelem = array();
  var $xmldata = array();
  var $stackloc = 0;

  var $parser;

  function set_host($sc_host) {
    $this->host=$sc_host;
  }

  function set_port($sc_port) {
    $this->port=$sc_port;
  }

  function set_password($sc_password) {
    $this->password=$sc_password;
  }

  function startElement($parser, $name, $attrs) {
    $this->stackloc++;
    $this->lastelem[$this->stackloc]=$name;
    $this->depth++;
  }

  function endElement($parser, $name) {
    unset($this->lastelem[$this->stackloc]);
    $this->stackloc--;
  }

  function characterData($parser, $data) {
    $data=trim($data);
    if ($data) {
      $this->xmlelem[$this->depth]=$this->lastelem[$this->stackloc];
      $this->xmldata[$this->depth].=$data;
    }
  }

  function retrieveXML() {
    $rval=1;

    $sp=@fsockopen($this->host,$this->port,$errno,$errstr,10);
    if (!$sp) $rval=0;
    else {

      set_socket_blocking($sp,false);

      // request xml data from sc server

      fputs($sp,"GET /admin.cgi?pass=$this->password&mode=viewxml HTTP/1.1\nUser-Agent:Mozilla\n\n");

      // if request takes > 15s then exit

      for($i=0; $i<30; $i++) {
    if(feof($sp)) break; // exit if connection broken
    $sp_data.=fread($sp,31337);
    usleep(500000);
      }

      // strip useless data so all we have is raw sc server XML data

      $sp_data=ereg_replace("^.*<!DOCTYPE","<!DOCTYPE",$sp_data);

      // plain xml parser

      $this->parser = xml_parser_create();
      xml_set_object($this->parser,$this);
      xml_set_element_handler($this->parser, "startElement", "endElement");
      xml_set_character_data_handler($this->parser, "characterData");

      if (!xml_parse($this->parser, $sp_data, 1)) {
    $rval=-1;
      }

      xml_parser_free($this->parser);

    }
    return $rval;
  }

  function debugDump(){
    reset($this->xmlelem);
    while (list($key,$val) = each($this->xmlelem)) {
      echo "$key. $val -> ".$this->xmldata[$key]."\n";
    }

  }

  function fetchMatchingArray($tag){
    reset($this->xmlelem);
    $rval = array();
    while (list($key,$val) = each($this->xmlelem)) {
      if ($val==$tag) $rval[]=$this->xmldata[$key];
    }
    return $rval;
  }

  function fetchMatchingTag($tag){
    reset($this->xmlelem);
    $rval = "";
    while (list($key,$val) = each($this->xmlelem)) {
      if ($val==$tag) $rval=$this->xmldata[$key];
    }
    return $rval;
  }

}

?>