<?php

define('RESIZE_MODE_STRICT', 0);
define('RESIZE_MODE_FIT', 1);
define('RESIZE_MODE_WIDTH', 2);
define('RESIZE_MODE_HEIGHT', 3);
define('RESIZE_MODE_CLIP', 4);

class flyImage {


	var $image_pixels;
	var $image_pixels_backup;
	var $filename;
	var $filename_save;
	var $type;
	var $mime;
	var $jpgquality =90;
	var $aErrors = array();
	var $extension_autodetect =1;
	var $duplicate_replace =0;
	var $duplicate_increment_suffix =1;


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
				return $filename;	
		}
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
	 * Saves picture with a filename specified in $filename.
	 * If no filename specified - original filename used.
	 */
	function save( $filename ='' , $jpgquality =null ) {
		if(is_dir($filename)) {
			$filename = $filename.$this->filename_save;
		}
		if(empty($jpgquality)){
			$jpgquality = $this->jpgquality;
		}
		if($this->extension_autodetect){
			$filename = $this->extension_detect($filename);
		}
		if($this->duplicate_replace && is_file($filename)){
			@unlink($filename);
		}
		if($this->duplicate_increment_suffix && is_file($filename)){
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
	 * Warning! This erases original picture, be careful with this option.
	 */
	function resize($width, $height, $mode =RESIZE_MODE_STRICT, $autosave =0) {
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
				$ratio = $width/$height;
				$new_width = $width;
				$new_height = $height;
				if($width/$ratio > $height_orig) {
					$height_orig = round($width/$ratio);
				} else {
					$width_orig = round($height*$ratio);
				}
				break;
		}
		$this->image_pixels_backup = $this->image_pixels;
		$image_p = imagecreatetruecolor($new_width, $new_height);
		$image_o = $this->image_pixels;
		imagecopyresampled($image_p, $image_o, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
		$this->image_pixels = $image_p;
		if($autosave){
			unlink($this->filename);
			$this->save($this->filename);
		}
	}


	/**
	 * Does the same as resize(), but prevents enlarging picture more
	 * than original resolution
	 */
	//TODO: recalc image downsample formulas
	function downsample($width, $height, $mode =RESIZE_MODE_STRICT, $autosave =0){
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
		}
		return $this->resize($width, $height, $mode, $autosave);
	}


	function revert(){
		$this->image_pixels = $this->image_pixels_backup;
	}

}

?>