<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
/**
* - JINZORA | Web-based Media Streamer -  
* 
* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
* (but can be used for any media file that can stream from HTTP). 
* Jinzora can be integrated into a CMS site, run as a standalone application, 
* or integrated into any PHP website.  It is released under the GNU GPL.
* 
* - Ressources -
* - Jinzora Author: Ross Carlson <ross@jasbone.com>
* - Web: http://www.jinzora.org
* - Documentation: http://www.jinzora.org/docs	
* - Support: http://www.jinzora.org/forum
* - Downloads: http://www.jinzora.org/downloads
* - License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
* 
* - Contributors -
* Please see http://www.jinzora.org/modules.php?op=modload&name=jz_whois&file=index
* 
* - Code Purpose -
* Handles zip, tar, and raw streaming for downloads in Jinzora
*
* @since 02/09/04 
* @author Laurent Perrin <laurent@la-base.org>
*/


/*
* This private class handles low level file access.
* 
* @access private
* @author Laurent Perrin 
* @version 02/09/04
* @since 02/09/04
*/
class _jzRessource
{
	function _jzRessource($files) {
		if (is_array($files))
		    $this->files = $files;
		else
			$this->files = array($files);
		$this->cur_file_handle = false;
	}

	function getFilesSize() {
		foreach ($this->files as $file) {
			$sizes[] = filesize($file);
		}
		return $sizes;
	}
		
	function isOpen() {
		return $this->cur_file_handle !== false;
	}

	function Close() {
		if ($this->isOpen()) fclose($this->cur_file_handle);
		$this->cur_file_handle = false;
	}

	function OpenNext() {
		if ($this->isOpen()) {
		    $this->Close();
			array_shift($this->files);
		}
		if ( $this->eofs() ) return false;
		if ( ($this->cur_file_handle = @fopen($this->files[0],'rb')) !== false ) return $this->files[0];
		return false;
	}
	
	function eof() {
		$now = ftell($this->cur_file_handle);
		fseek ($this->cur_file_handle, 0,  SEEK_END);
		$end = ftell($this->cur_file_handle);
		fseek ($this->cur_file_handle, $now);
		return $now==$end;
	}
	
	function eofs() {
		return count($this->files) == 0;
	}
	
	function getData($size=0) {
		if ($size == 0) {
		    return fread($this->cur_file_handle, filesize($this->files[0]));
		}
		return fread($this->cur_file_handle, $size);
	}

	var $files = array();
	var $cur_file_handle = false;
}


/* 
* jzStreamRaw.
* Streams a simple file. This might look stupid, but it is a simple implementation of a common interface.
* 
* @author Laurent Perrin 
* @version 02/09/04
* @since 02/09/04
*/
class jzStreamRaw
{
	/**
	 * Constructor.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 * @param string $files filename.
	 **/
	function jzStreamRaw($file) {
		$this->_ressource = & new _jzRessource($file);
	}

	/**
	 * Gets the total size that this stream will send out.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 * @return integer size.
	 **/
	function FinalSize() {
		$sizes = $this->_ressource->getFilesSize();
		return $sizes[0];
	}
	
	/**
	 * Opens the stream.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 * @return boolean false on error.
	 **/
	function Open() {
		return $this->_ressource->OpenNext();
	}
	
	/**
	 * Reads some data from the stream.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 * @param integer $size size of data to read, rounded to 512 bytes.
	 * @return string data red, or '' on end of file.
	 **/
	function Read($size) {
		return  $this->_ressource->getData($size);
	}
	
	/**
	 * Closes the stream.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 **/
	function Close() {
		$this->_ressource->Close();
		unset($this->_ressource);
	}

	var $_ressource = null;
}


/* 
* jzStreamTar, written from pcltar by Vincent Blavet.
* Transforms a set of files or a single file into a streamable tar archive.
* 
* @author Laurent Perrin 
* @version 02/09/04
* @since 02/09/04
*/
class jzStreamTar
{
	/**
	 * Constructor.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 * @param mixed $files array of files to add to this tar archive,
	 * or a simple string of space separated filenames.
	 **/
	function jzStreamTar($files) {
		$this->_ressource = & new _jzRessource($files);
	}

	/**
	 * Gets the total size that this stream will send out.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 * @return integer size.
	 **/
	function FinalSize() {
		// tar content length = all files size rounded up to 512 + 512*nb_files + 512
		$total_size = 512;
		$files_size = $this->_ressource->getFilesSize();
	    foreach ($files_size as $size) {
			$total_size += 512 + 512*(int)(ceil($size/512));
		}
		return $total_size;
	}
	
	/**
	 * Opens the stream.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 * @return boolean false on error.
	 **/
	function Open() {
		return true;
	}
	
	/**
	 * Reads some data from the stream.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 * @param integer $size size of data to read, rounded to 512 bytes.
	 * @return string data red, or '' on end of file.
	 **/
	function Read($size) {
		//eof
		if ($this->_ressource->eofs()) return '';
	
		$v_binary_data = '';
	
		// open a new file
		if (!$this->_ressource->isOpen()) {
			$filename = $this->_ressource->OpenNext();
			$v_binary_data .= jzStreamTar::_tar_header(basename($filename),$filename);
		    
		}
	
		// ----- Read the file by 512 octets blocks
		for ($i=0;$i<$size/512;$i++)
		{
			if (($v_buffer = $this->_ressource->getData(512)) != '') {
			    $v_binary_data .= pack('a512', "$v_buffer");
			}
			else
			{
				if ( ($filename = $this->_ressource->OpenNext()) !== false )
				{
					$v_binary_data .= jzStreamTar::_tar_header(basename($filename),$filename);
					$i++;
				}
				else
				{
					$v_binary_data .= pack('a512', '');
					return $v_binary_data;
				}
			}
		}
	
		return $v_binary_data;
	}
	
	/**
	 * Closes the stream.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 **/
	function Close() {
		$this->_ressource->Close();
		unset($this->_ressource);
	}
	
	var $_ressource = null;
	
	/**
	 * Private static function to compute the header to be sent before a file in a tar archive.
	 * 
	 * @access private
	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 * @param string $archive_filename Filename as it will appear in the archive. Will be troncated at 99 chars.
	 * @param string $real_filename Real filename, to be passed to filesize(), stat(), and sileperms().
	 * @return string 512 byte long tar header for this file.
	 **/
	function _tar_header($archive_filename,$real_filename)
	{
		$archive_filename = substr($archive_filename,0,99);
		$header = '';
	
	    // ----- Get file info
	    $v_info = stat($real_filename);
	    $v_uid = sprintf("%6s ", DecOct($v_info[4]));
	    $v_gid = sprintf("%6s ", DecOct($v_info[5]));
	    $v_perms = sprintf("%6s ", DecOct(fileperms($real_filename)));
	
	    // ----- File mtime
	    $v_mtime_data = filemtime($real_filename);
	    $v_mtime = sprintf("%11s", DecOct($v_mtime_data));
	
	    // ----- File typeflag
	    // '0' or '\0' is the code for regular file
	    // '5' is directory
	    if (is_dir($real_filename))
	    {
	      $v_typeflag = '5';
	      $v_size = 0;
	    }
	    else
	    {
	      $v_typeflag = '';
	
	      // ----- Get the file size
	      $v_size = filesize($real_filename);
	    }
	
	    $v_size = sprintf("%11s ", DecOct($v_size));
	
	    // ----- Compose the binary string of the header in two parts arround the checksum position
	    $v_binary_data_first = pack("a100a8a8a8a12A12", $archive_filename, $v_perms, $v_uid, $v_gid, $v_size, $v_mtime);
	    $v_binary_data_last = pack("a1a100a6a2a32a32a8a8a155a12", $v_typeflag, '', '', '', '', '', '', '', '', '');
	
	    // ----- Calculate the checksum
	    $v_checksum = 0;
	    // ..... First part of the header
	    for ($i=0; $i<148; $i++)
	    {
	      $v_checksum += ord(substr($v_binary_data_first,$i,1));
	    }
	    // ..... Ignore the checksum value and replace it by ' ' (space)
	    for ($i=148; $i<156; $i++)
	    {
	      $v_checksum += ord(' ');
	    }
	    // ..... Last part of the header
	    for ($i=156, $j=0; $i<512; $i++, $j++)
	    {
	      $v_checksum += ord(substr($v_binary_data_last,$j,1));
	    }
	
	    // ----- Write the first 148 bytes of the header in the archive
	    $header .= $v_binary_data_first;
	
	    // ----- Write the calculated checksum
	    $v_checksum = sprintf("%6s ", DecOct($v_checksum));
	    $v_binary_data = pack("a8", $v_checksum);
	    $header .= $v_binary_data;
	
	
	    // ----- Write the last 356 bytes of the header in the archive
	    $header .= $v_binary_data_last;
		
		return $header;
	}
}


// 
/* 
* Writen from :
* Zip file creation class makes zip files on the fly...
* by Eric Mueller http://www.themepark.com
* initial version by Denis O.Philippov, webmaster@atlant.ru, http://www.atlant.ru
* 
* official ZIP file format: http://www.pkware.com/products/enterprise/white_papers/appnote.html
* ==== ==== ==== =====
* Compresses a set of files or a single file archive and stream it. Takes much less memory than the original, by compressing files one by one upon sending.
* 
* @author Laurent Perrin 
* @version 02/14/04
* @since 02/09/04
*/
class jzStreamZip
{
	/**
	 * Constructor.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 * @param mixed $files array of files to add to this tar archive,
	 * or a simple string of space separated filenames.
	 **/
	function jzStreamZip($files) {
		$this->_ressource = & new _jzRessource($files);
	}

	/**
	 * Gets the total size that this stream will send out.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 * @return integer size.
	 **/
	function FinalSize() {
		return 0;
	}
	
	/**
	 * Opens the stream.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 * @return boolean false on error.
	 **/
	function Open() {
		return true;
	}
	
	/**
	 * Reads some data from the stream.
	 * 
	 * To use compression, we would need to mess with non byte aligned data. I think it can be done, but it looks like quite difficult.
	 * Rough algorithm to do this:
	 * foreach file
	 * {
	 *    output header
	 *    while (!last block)
	 *    {
	 *       read a data block
	 *       gzdeflate it
	 *       if (!last block) clear the first bit of the compressed data
	 *                        remove the trailling 0 bits after the 8th
	 *       adds to compressed size
	 *       compute the crc
	 *       output compressed data
	 *    }
	 *    output data descriptor
	 *    update the central directory
	 * }
	 * output the central directory
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/14/04
	 * @since 02/09/04
	 * @param integer $size size of data to read, rounded to 512 bytes.
	 * @return string data red, or '' on end of file.
	 **/
	function Read($size) {
		//eof
		if ($this->_ressource->eofs()) return '';
	
		$v_binary_data = '';

		// open a new file
		if (!$this->_ressource->isOpen()) {
			// open the first file
			$filename = $this->_ressource->OpenNext();
			// write the local header
			$v_binary_data .= $this->_zip_header($filename);
		}

		// ----- Read the file by 512 octets blocks
		for ($i=0;$i<$size/512;$i++)
		{
			// read a block
			if (($v_buffer = $this->_ressource->getData(512)) != '') {
				// copy the block to the stream
				$v_binary_data .= $v_buffer;
			}
			else
			{
				// write the local footer
				$this->_zip_footer();
				// open a new file
				if ( ($filename = $this->_ressource->OpenNext()) !== false )
				{
					// write the local header for this new file
					$v_binary_data .= $this->_zip_header($filename);
				}
				else
				{
					// write the archive footer
					$ctrl_dir = implode("", $this->_ctrl_dir);
					$v_binary_data .= $ctrl_dir.		// all the central directory at once 
					"\x50\x4b\x05\x06\x00\x00\x00\x00".    //end of Central directory record
					pack("v", sizeof($this->_ctrl_dir)).     // total # of entries "on this disk"
					pack("v", sizeof($this->_ctrl_dir)).     // total # of entries overall
					pack("V", strlen($ctrl_dir)).             // size of central dir
					pack("V", $this->_old_offest ).          // offset to start of central dir
					"\x00\x00";                             // .zip file comment length
					return $v_binary_data;
				}
			}
		}
	
		return $v_binary_data;
	}
	
	/**
	 * Closes the stream.
	 * 
 	 * @author Laurent Perrin 
	 * @version 02/09/04
	 * @since 02/09/04
	 **/
	function Close() {
		$this->_ressource->Close();
		unset($this->_ressource);
		unset($this->_ctrl_dir);
		unset($this->_offset);
	}

	var $_ressource = null;

	var $_ctrl_dir = array();	// central directory    
	var $_old_offset = 0;		// offset of next local header
	var $_new_offset = 0;		// next offset of next local header
	
	var $_cur_crc = 0;			// current file crc
	var $_cur_size = 0;			// current file size
	var $_cur_name = '';		// current file name
	
	
	/**
	 * Computes the local header to be sent before a file. Updates archive offset.
	 * 
	 * @access private
  	 * @author Laurent Perrin 
	 * @version 02/14/04
	 * @since 02/10/04
	 * @param string $name name of the file in archive
	 * @return string full local header
	 **/
	function _zip_header($name)  
	{   
						
		// get its crc & size
		$this->_cur_crc = crc32(file_get_contents($name));
		$this->_cur_size = filesize($name);
		
		//get only the name
		$name = basename($name);
		$name = str_replace("\\", "/", $name); 
		
		// init current file data
		$this->_cur_name = $name;
	
		$header = "\x50\x4b\x03\x04";
		$header .= "\x14\x00";    // ver needed to extract
		$header .= "\x00\x00";    // gen purpose bit flag. bit3 set = sizes not in "header" but in "data descriptor"
		$header .= "\x00\x00";    // compression method
		
		$expd = explode (':',date ("Y:m:d:H:i"));
		$time = ((integer)$expd[3])<<11 | ((integer)$expd[4])<<5;
		$date = (((integer)$expd[0])-1980)<<9 | ((integer)$expd[1])<<5 | ((integer)$expd[2]);
		$header .= pack("v",(string)$time).pack("v",(string)$date); // last mod time and date

		$header .= pack("V",$this->_cur_crc); // crc32
		$header .= pack("V",$this->_cur_size); //compressed filesize
		$header .= pack("V",$this->_cur_size); //uncompressed filesize
		$header .= pack("v", strlen($name) ); //length of filename
		$header .= pack("v", 0 ); //extra field length
		$header .= $name;
		
		// update current writing offset
		$this->_new_offset += strlen($header);
		
		// end of "local file header" segment
		return $header;
	}

	/**
	 * Computes the local footer to be sent after a file. Updates central directory and archive offset.
	 * 
	 * @access private
  	 * @author Laurent Perrin 
	 * @version 02/14/04
	 * @since 02/10/04
	 * @return string full "data descriptor" aka local footer
	 **/
	function _zip_footer()
	{
		// update current writing offset
		$this->_new_offset += $this->_cur_size;
		
		// now add to central directory record
		$file_nb = count($this->_ctrl_dir);
		$this->_ctrl_dir[$file_nb] = "\x50\x4b\x01\x02";  
		$this->_ctrl_dir[$file_nb] .="\x14\x00";    // version made by
		$this->_ctrl_dir[$file_nb] .="\x14\x00";    // version needed to extract
		$this->_ctrl_dir[$file_nb] .="\x00\x00";    // gen purpose bit flag. bit3 set = size and crc not in "header" but in "data descriptor"
		$this->_ctrl_dir[$file_nb] .="\x00\x00";    // compression method
		
		$expd = explode (':',date ("Y:m:d:H:i"));
		$time = ((integer)$expd[3])<<11 | ((integer)$expd[4])<<5;
		$date = (((integer)$expd[0])-1980)<<9 | ((integer)$expd[1])<<5 | ((integer)$expd[2]);
		$this->_ctrl_dir[$file_nb] .= pack("v",(string)$time).pack("v",(string)$date); // last mod time and date
		
		$this->_ctrl_dir[$file_nb] .= pack("V",$this->_cur_crc); // crc32
		$this->_ctrl_dir[$file_nb] .= pack("V",$this->_cur_size); //compressed filesize
		$this->_ctrl_dir[$file_nb] .= pack("V",$this->_cur_size); //uncompressed filesize
		$this->_ctrl_dir[$file_nb] .= pack("v", strlen($this->_cur_name) ); //length of filename
		$this->_ctrl_dir[$file_nb] .= pack("v", 0 ); //extra field length    
		$this->_ctrl_dir[$file_nb] .= pack("v", 0 ); //file comment length
		$this->_ctrl_dir[$file_nb] .= pack("v", 0 ); //disk number start
		$this->_ctrl_dir[$file_nb] .= pack("v", 0 ); //internal file attributes
		$this->_ctrl_dir[$file_nb] .= pack("V", 32 ); //external file attributes - 'archive' bit set
		$this->_ctrl_dir[$file_nb] .= pack("V", $this->_old_offest ); //relative offset of local header
		$this->_ctrl_dir[$file_nb] .= $this->_cur_name;   
		// optional extra field, file comment goes here

		$this->_old_offest = $this->_new_offset;
	}
}


?>