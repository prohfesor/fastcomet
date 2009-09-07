<?php

	class flyFileUpload {

		var $aErrors = array();
		var $settings_replace = false;



		/*
		 * Class constructor
		 * Shouldnot becalledmanually
		 */
		function flyFileUpload($pass =null) {
			if(!$pass == "FileUploadPass") {
				new flyError("Unable to create new instance of singleton class! Use &getInstance() method.");
			}
		}


		function &getInstance() {
			global $instancefu;
			if ($instancefu === null) {
				$instancefu = new flyFileUpload("FileUploadPass");
			}

			return $instancefu;
		}


		/**
		 * Checks file uploading.
		 * Returns uploaded file name, or FALSE in wrong case. 
		 * @param $file_id
		 * @return string
		 */		
		function find($file_id, $type=null) {
			$file =& $_FILES[$file_id];
			$aErrors =& $this->aErrors; $aErrors = array();
			//check if such file was uploaded
			if(!is_array($file)) {
				//$aErrors[] = "1: File wasn\'t uploaded";
				return false;
			}
			if($file['error'] == UPLOAD_ERR_NO_FILE) {
				//$aErrors[] = "1: File wasn\'t uploaded";
				return false;
			}
			//check filetype?
			if(!empty($type) && $file['type']!==$type) {
				//TODO: check filetype of uploaded file correctly
				$aErrors[] = "2: Wrong file type";
				return false;
			}
			
			return $file;
		}
		
		
		/**
		 * Replace all characters in filename except letters, 
		 * digits or "_" with underscores.
		 * @param $filename
		 * @return string
		 */
		function namesafe($filename) {
			return ereg_replace('[^_A-z0-9]', '_', $filename);
		}
		
		
		/*
		 * Executes file uploading.
		 * @param $file_id - file id set in html input element
		 * @param $path - path for file to upload.
		 * If specified with filename, then this is a new name for file.
		 * If specified is a directory, then original filename saved.
		 * @param $type - if set, then file is moved only if
		 * filetype is correct.
		 */
		function get($file_id, $path, $type =null) {
			$file = $this->find($file_id, $type);
			if($file ===false){
				return false;
			}
			//save original filename?
			if(is_dir($path)) {
				$path = $path.$file['name'];
			}
			//replace old file?
 			if(is_file($path) && !$this->settings_replace) {
 				$aErrors[] = "3: File already exists";
 				return false;
 			} elseif ($this->settings_replace) {
 				@unlink($path);
 			}

			move_uploaded_file($file['tmp_name'] , $path);
			return $path;
		}


		function getErrors() {
			return $this->aErrors;
		}



	}

?>