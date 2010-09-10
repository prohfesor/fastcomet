<?php

define('RESIZE_MODE_STRICT', 0);
define('RESIZE_MODE_FIT', 1);
define('RESIZE_MODE_WIDTH', 2);
define('RESIZE_MODE_HEIGHT', 3);
define('RESIZE_MODE_CLIP', 4);
define('RESIZE_MODE_BORDERS', 5);

class flyImage {


	var $image_pixels;
	var $image_pixels_backup;
	var $filename;
	var $filename_save;
	var $type;
	var $mime;
	var $aErrors = array();
	var $settings_jpg_quality =90;
	var $settings_extension_autodetect =1;
	var $settings_duplicate_replace =0;
	var $settings_duplicate_increment_suffix =0;
	var $settings_undo =1;
	var $settings_resizeborders_color = "#ffffff";


	// constructor
	function flyImage( $filename ='') {
		if(!empty($filename)){
			return $this->load($filename);
		}
	}


	/**
	 * Reads image file into $this->image_pixels
	 * Computes image dimensions in $this->aDimensions
	 * If file has unknown image format, or no such file - returns false
	 * @param $filename
	 * @return bool
	 */
	function load( $filename ) {
		if(!is_file($filename)) {
			$this->aErrors[] = "1: File not exists";
			return false;
		}

		$aInfo = getimagesize($filename);

		if (false == $aInfo) {
			$this->aErrors[] = "2: Unrecognized image format";
			return false;
		}

		$this->filename = $filename;
		$this->filename_save = basename($filename);
		$this->type = $aInfo[2];
		$this->mime = $aInfo['mime'];

		switch($this->mime) {
			case 'image/jpeg':
				$this->image_pixels = imagecreatefromjpeg($filename);
				break;
			case 'image/gif':
				$this->image_pixels = imagecreatefromgif($filename);
				break;
			case 'image/png':	
				$this->image_pixels = imagecreatefrompng($filename);
				break;
			case 'image/vnd.wap.wbmp':
				$this->image_pixels = imagecreatefromwbmp($filename);
				break;
			default:
				$this->aErrors[] = "3: Unsupported image type";
				return false;
		}

		return $filename;
	}


	function get_ext_from_mime($mime =null){
		if(empty($mime)) $mime = $this->mime;
		switch($this->mime) {
			case 'image/jpeg':
				$extension = "jpg";
				break;
			case 'image/gif':
				$extension = "gif";
				break;
			case 'image/png':
				$extension = "png";
				break;
			case 'image/vnd.wap.wbmp':
				$extension = "bmp";
				break;
			default:
				$this->aErrors[] = "3: Unsupported image type";
				return false;	
		}
		return $extension;
	}
	
	
	/**
	 * Replaces filename extension with new extension, according to detected $this->type.
	 * Adds extension, if not present.
	 * @param $filename
	 * @return string
	 */
	function extension_detect($filename =''){
		if(empty($filename)) {
			$filename = $this->filename;
		}
		$extension = $this->get_ext_from_mime();
		if(!$extension)
			return $filename;
		$aPathInfo = pathinfo($filename);
		$filename = $aPathInfo['dirname']."/".$aPathInfo['filename'].".".$extension;
		return $filename;
	}

	
	function duplicate_increment_suffix($filename){
		$aPathInfo = pathinfo($filename);
		$suffix =1;
		while(is_file($filename)){
			$filename = "{$aPathInfo['dirname']}/{$aPathInfo['filename']}_{$suffix}.{$aPathInfo['extension']}";
			$suffix++;
		}
		return $filename;
	}
	

	/**
	 * Saves picture from memory with a filename specified in $filename.
	 * If no filename specified - original filename used.
	 */
	function save( $filename ='' , $jpgquality =null ) {
		if(is_dir($filename)) {
			$filename = $filename.$this->filename_save;
		}
		if(empty($jpgquality)){
			$jpgquality = $this->settings_jpg_quality;
		}
		if($this->settings_extension_autodetect){
			$filename = $this->extension_detect($filename);
		}
		if($this->settings_duplicate_replace && is_file($filename)){
			@unlink($filename);
		}
		if($this->settings_duplicate_increment_suffix && is_file($filename)){
			$filename = $this->duplicate_increment_suffix($filename);	
		}
		if(is_file($filename)){
			$this->aErrors[] = "4: Filename already exists";
			return false;
		}
		if(!is_writable(dirname($filename))) {
			$this->aErrors[] = "5: Unable to write specified file. Possible access denied";
			return false;
		}
		switch($this->mime) {
			case 'image/jpeg':
				imagejpeg($this->image_pixels, $filename, $jpgquality);
				break;
			case 'image/gif':
				imagegif($this->image_pixels, $filename);
				break;
			case 'image/png':
				imagepng($this->image_pixels, $filename);
				break;
			case 'image/vnd.wap.wbmp':
				imagewbmp($this->image_pixels, $filename);
				break;
			default:
				$this->aErrors[] = "3: Unsupported image type";
				return false;
		}
		return $filename;
	}


	/**
	 * Resize picture, loaded by load(), applying desired resize mode.
	 * If $autosave is set to TRUE - automatically saves image after resize.
	 * Warning! This erases original picture, be careful with this option (off by default).
	 * Resize methods:
	 *  RESIZE_MODE_STRICT	(default) width and height will be exactly as specified. Aspect ratio isn't kept.
	 *  RESIZE_MODE_FIT		resize with keeping aspect ratio, by width or height, whichever would fit in specified borders
	 *  RESIZE_MODE_WIDTH	resize by width (height ignored)
	 *  RESIZE_MODE_HEIGHT	resize by height
	 *  RESIZE_MODE_CLIP	completely fit in specified borders but crop outside inage data to keep aspect ratio
	 *  RESIZE_MODE_BORDERS	completely fit image, adding borders to save original aspect ratio
	 */
	function _resize($width, $height, $mode =RESIZE_MODE_STRICT, $autosave =0, $jpgquality =null) {
		$width_orig  = imagesx($this->image_pixels);
		$height_orig = imagesy($this->image_pixels);
		switch ($mode) {
			case RESIZE_MODE_STRICT:
				$new_width = $width;
				$new_height = $height;
				break;
			case RESIZE_MODE_FIT:
				$ratio_orig = $width_orig/$height_orig;
				if ($width/$height > $ratio_orig) {
					$new_width = round( $height*$ratio_orig );
					$new_height = $height;
				} else {
					$new_height = round( $width/$ratio_orig );
					$new_width  = $width;
				}
				break;
			case RESIZE_MODE_WIDTH:
				$ratio_orig = $width_orig/$height_orig;
				$new_height = round( $width/$ratio_orig );
				$new_width  = $width;
				break;
			case RESIZE_MODE_HEIGHT:
				$ratio_orig = $width_orig/$height_orig;
				$new_height = $height;
				$new_width  = round( $height*$ratio_orig );
				break;
			case RESIZE_MODE_CLIP:
				$ratio_orig = $width_orig/$height_orig;
				$ratio = $width/$height;
				if ($width/$height < $ratio_orig) {
					$new_width = round( $height*$ratio_orig );
					$width_orig = round( $height_orig*$ratio );					
				} else {
					$new_height = round( $width/$ratio_orig );
					$height_orig = round( $width_orig/$ratio );
				}
				$new_height = $height;
				$new_width	= $width;
				break;
			case RESIZE_MODE_BORDERS:
				$ratio_orig = $width_orig/$height_orig;
				if ($width/$height > $ratio_orig) {
					$new_width = round( $height*$ratio_orig );
					$new_height = $height;
				} else {
					$new_height = round( $width/$ratio_orig );
					$new_width  = $width;
				}
				$image_p = imagecreatetruecolor($new_width, $new_height);
				$image_o = $this->image_pixels;
				imagecopyresampled($image_p, $image_o, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
				$this->image_pixels = $image_p;
				$image_p = imagecreatetruecolor($width, $height);
				$image_o = $this->image_pixels;
				$bg_color =& $this->settings_resizeborders_color;
				imagefill($image_p, 0, 0, imagecolorallocate($image_p, hexdec(substr($bg_color,1,2)), hexdec(substr($bg_color,3,2)), hexdec(substr($bg_color,5,2))) );
				$this->image_pixels = $image_p;
				imagecopy($image_p, $image_o, round(($width-$new_width)/2), round(($height-$new_height)/2), 0, 0, $new_width, $new_height);
				$this->image_pixels = $image_p;
				$new_width  =$width_orig;
				$new_height =$height_orig;
				break;	
		}
		if($this->settings_undo){
			$this->image_pixels_backup = $this->image_pixels;
		}
		if($new_width!=$width_orig && $new_height!=$height_orig) {	
			$image_p = imagecreatetruecolor($new_width, $new_height);
			$image_o = $this->image_pixels;
			imagecopyresampled($image_p, $image_o, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
			$this->image_pixels = $image_p;
		}	
		if($autosave){
			$savename = ($autosave===1 || $autosave===true) ? $this->filename : $autosave;
			if($savename==$this->filename && is_file($savename)) unlink($this->filename);
			return $this->save($savename, $jpgquality);
		}
	}
	
	
	/**
	 * Does the same as resize(), but prevents enlarging picture more
	 * than original resolution
	 */
	//TODO: recalc image downsample formulas
	function _downsample($width, $height, $mode =RESIZE_MODE_STRICT, $autosave =0, $jpgquality =null){
		$width_orig  = imagesx($this->image_pixels);
		$height_orig = imagesy($this->image_pixels);
		$ratio_orig = $width_orig/$height_orig;
		switch ($mode) {
			case RESIZE_MODE_STRICT:
				//no resize
				if($width<$new_width || $height<$new_height){
					$new_width = $width_orig;
					$new_height= $height_orig;
				} else {
					$new_width  = $width;
					$new_height = $height;					
				}
				break;
			case RESIZE_MODE_FIT:
				if ($width/$height > $ratio_orig) {
					$new_width = round( $height*$ratio_orig );
					$new_height = $height;
				} else {
					$new_height = round( $width/$ratio_orig );
					$new_width  = $width;
				}
				//no resize
				if($width<$new_width || $height<$new_height){
					$new_width = $width_orig;
					$new_height= $height_orig;
				} 
				break;
			case RESIZE_MODE_WIDTH:
				$new_height = round( $width/$ratio_orig );
				$new_width  = $width;
				//no resize
				if($width<$new_width || $height<$new_height){
					$new_width = $width_orig;
					$new_height= $height_orig;
				}
				break;
			case RESIZE_MODE_HEIGHT:
				$new_height = $height;
				$new_width  = round( $height*$ratio_orig );
				//no resize
				if($width<$new_width || $height<$new_height){
					$new_width = $width_orig;
					$new_height= $height_orig;
				}
				break;
			case RESIZE_MODE_CLIP:
				$ratio = $width/$height;
				$new_width = $width;
				$new_height = $height;
				if($width/$ratio > $height_orig) {
					$height_orig = round($width/$ratio);
				} else {
					$width_orig = round($height*$ratio);
				}
				//no resize
				if($width<$new_width || $height<$new_height){
					$new_width = $width_orig;
					$new_height= $height_orig;
				} 
				break;
			case RESIZE_MODE_BORDERS:
				$new_width = $width;
				$new_height = $height;
				break;	
		}
		return $this->resize($width, $height, $mode, $autosave, $jpgquality);
	}

	
	/**
	 * Load file and resize it.
	 */
	function resize($filename, $width, $height, $mode =RESIZE_MODE_STRICT, $autosave =0, $jpgquality =null){
		if(!$this->load($filename))	return false;
		return $this->_resize($width, $height, $mode, $autosave, $jpgquality);
	}
	
	
	/**
	 * Load file and resize, but prevent enlarging.
	 */
	function downsample($filename, $width, $height, $mode =RESIZE_MODE_STRICT, $autosave =0, $jpgquality =null){
		if(!$this->load($filename))	return false;
		return $this->_downsample($width, $height, $mode, $autosave, $jpgquality);
	}
	

	function &revert(){
		if(!$this->settings_undo) return false;
		$this->image_pixels = $this->image_pixels_backup;
		return $this;
	}

}

?>