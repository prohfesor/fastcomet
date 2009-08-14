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
			global $instance;
			if ($instance === null) {
				$instance = new flyFileUpload("FileUploadPass");
			}

			return $instance;
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
				$aErrors[] = "2: Wrong file type";
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

			return move_uploaded_file($file['tmp_name'] , $path);
		}


		function getErrors() {
			return $this->aErrors;
		}



	}


?>