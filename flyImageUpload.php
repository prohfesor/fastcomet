<?php 

require_once('flyFileUpload.php');
require_once('flyImage.php');

class flyImageUpload {

	
	var $oImage;
	
	
	/*
	 * Class constructor
	 * Should not be called manually
	 */
	function flyImageUpload($pass =null) {
		if(!$pass == "ImageImagePass") {
			new flyError("Unable to create new instance of singleton class! Use &getInstance() method.");
		}
	}


	function &getInstance() {
		global $instanceiu;
		if ($instanceiu === null) {
			$instanceiu = new flyImageUpload("ImageUploadPass");
		}

		return $instanceiu;
	}

	
	function get( $file_id, $path, $type =null ) {
		$fip = flyImageUpload::getInstance();
		$fup = flyFileUpload::getInstance();
		//file is loaded?
		$aFile = $fup->find($file_id);
		if(false=== $aFile){
			return false;
		}
		//file is image?
		$oImage = new flyImage( $aFile['tmp_name'] );
		if(!empty($oImage->aErrors)){
			$fup->aErrors = array_merge($fup->aErrors, $oImage->aErrors);
			return false;
		}
		$fip->oImage = $oImage;
		return flyFileUpload::get($file_id, $path, $oImage->get_ext_from_mime() );
	}
	
	
	function isError(){
		$fup = flyFileUpload::getInstance();
		return (bool)sizeof($fup->aErrors);
	}
		
		
	function getErrors() {
		$fup = flyFileUpload::getInstance();
		return $fup->aErrors;
	}
	
	
}

?>