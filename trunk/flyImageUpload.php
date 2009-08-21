<?php 

require_once('flyFileUpload.php');
require_once('flyImage.php');

class flyImageUpload extends flyFileUpload {

	var $image;
	
	
	function flyImageUpload($pass =null) {
		flyFileUpload::getInstance();
	}

	
	function get( $id ) {
		$aFile = $this->find($id);
		if(false === $aFile){
			return false;
		}
		$this->image = new flyImage( $aFile['tmp_name'] );
		$this->image->filename_save = $aFile['name'];
		return $this->image;
	}
	
	
}

?>