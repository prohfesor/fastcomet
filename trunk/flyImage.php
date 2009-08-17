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
	var $filename_original;
	var $type;
	var $mime;
	var $jpgquality =90;
	var $aErrors = array();

	
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
		$this->filename_original = basename($filename);
		$this->type = $aInfo[2];
		$this->mime = $aInfo['mime'];
		
		switch($this->type) {
			case IMG_JPG:
				$this->image_pixels = imagecreatefromjpeg($filename);
				break;
			case IMG_GIF:
				$this->image_pixels = imagecreatefromgif($filename);
				break;
			case IMG_PNG:
				$this->image_pixels = imagecreatefrompng($filename);
				break;		
			case IMG_WBMP:
				$this->image_pixels = imagecreatefromwbmp($filename);
				break;			
			default:
				$this->aErrors[] = "3: Unsupported image type";
				return false;	
		}
		
		return $filename;
	}
	
	
	/**
	 * Saves picture with a filename specified in $filename.
	 * If no filename specified - original filename used. 
	 */
	function save( $filename ='' , $jpgquality =null ) {
		if(is_dir($filename)) {
			$filename = $filename.$this->filename_original;
		}
		if(is_file($filename)){
			$this->aErrors[] = "4: Filename already exists";
			return false;
		}
		if(!is_writable(dirname($filename))) {
			$this->aErrors[] = "5: Unable to write specified file. Possible access denied";
			return false;
		}
		if(empty($jpgquality)){
			$jpgquality = $this->jpgquality;
		}
		
		switch($this->type) {
			case IMG_JPG:
				imagejpeg($this->image_pixels, $filename, $jpgquality);
				break;
			case IMG_GIF:
				imagegif($this->image_pixels, $filename);
				break;
			case IMG_PNG:
				imagepng($this->image_pixels, $filename);
				break;		
			case IMG_WBMP:
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
				die("Image clippin is not yet implemented");
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
				$new_width = $width;
				$new_height = $height;
				break;
			case RESIZE_MODE_FIT:
				if ($width/$height > $ratio_orig) {
					$new_width = round( $height*$ratio_orig );
					$new_height = $height;
				} else {
					$new_height = round( $width/$ratio_orig );
					$new_width  = $width;
				}
				break;
			case RESIZE_MODE_WIDTH:
				$new_height = round( $width/$ratio_orig );
				$new_width  = $width;
				break;
			case RESIZE_MODE_HEIGHT:
				$new_height = $height;
				$new_width  = round( $height*$ratio_orig );
				break;
			case RESIZE_MODE_CLIP:
				die("Image clippin is not yet implemented");
				break;
		}
		return $this->resize($width, $height, $mode, $autosave);
	}

	
	function revert(){
		$this->image_pixels = $this->image_pixels_backup;
	}

}

?>