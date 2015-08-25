<?php

	class flyFileUpload {

		var $aErrors = array();
		var $settings_replace = false;
		var $settings_namesafe = true;



		/*
		 * Class constructor
		 * Should not be called manually
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
		 * Returns uploaded file name, or FALSE otherwise. 
		 * @param $file_id
		 * @param $type - allowed file extensions, lowercase, comma-separated, e.g. 'jpg,gif,png'
		 * @return string
		 */		
		function find($file_id, $type =null) {
			$file =& $_FILES[$file_id];
			$aErrors =& $this->aErrors;
			//check if such file was uploaded
			if(!is_array($file)) {
				//$aErrors[] = "1: File wasn\'t uploaded";
				return false;
			}
			if($file['error'] == UPLOAD_ERR_NO_FILE) {
				//$aErrors[] = "1: File wasn\'t uploaded";
				return false;
			}
			//split name and extension
			$pos = strrpos($file['name'] , '.');
			if($pos){
				$ext = substr($file['name'], $pos+1);
				$name2 = substr($file['name'], 0, $pos);
			} else {
				$ext = '';
				$name2 = $file['name'];
			}
			$file['ext']   = $ext;
			$file['name2'] = $name2;
			//check filetype?
			if(!empty($type)) {
				if($pos){
					$aExtAllow = explode(',' , strtolower(str_replace(' ','',$type)));
				} 
				if(!$pos || !in_array(strtolower($ext), $aExtAllow)){
					$aErrors[] = "2: Wrong file type";
					return false;
				}
			}
			
			return $file;
		}
		
		
		/**
		 * Replace all unsafe characters in filename except letters, 
		 * digits, minus or "_" with underscores.
		 * If filename is empty, then returns "_".
		 * @access static
		 * @param $filename
		 * @return string
		 */
		function namesafe($filename) {
			if(empty($filename)) $filename="_";
			return ereg_replace('[^_A-z0-9\-]', '_', $filename);
		}
		
		
		/**
		 * Determines extension from filename.
		 * Depends on letters after last dot.
		 */
		function getFileExtension($filename){
			$pos = strrpos($filename , '.');
			if(!$pos)
				return false;
			return substr($filename, $pos+1);
		}
		
		
		/**
		 * Executes file uploading.
		 * If specified with filename, then this is a new name for file.
		 * If specified is a directory, then original filename saved.
		 * @access static
		 * @param $file_id - file id set in html input element
		 * @param $path - path for file to upload.
		 * @param $type - if set, then file is moved only if filetype is correct.
		 * @return string
		 */
		function get($file_id, $path, $type =null) {
			$fup = flyFileUpload::getInstance();
			$file = $fup->find($file_id, $type);
			//not loaded?
			if($file ===false){
				return false;
			}
			//save original filename?
			if(is_dir($path)) {
				$f_dir = $path;
				$f_name = $file['name2'];
				$f_ext = $file['ext'];
				$path = $path.$f_name.'.'.$f_ext;
			} else {
				$f_dir = dirname($path).'/';
				$f_ext = $fup->getFileExtension(basename($path));
				$f_name = substr(basename($path), 0, -1*(strlen($f_ext))-1);
				$path = dirname($path) .'/'. $f_name .'.'. $f_ext;
			}
			//check namesafe?
			if($fup->settings_namesafe){
				$path = $f_dir . $fup->namesafe($f_name) .'.'. $f_ext;
			}
			//replace old file?
 			if(is_file($path) && !$fup->settings_replace) {
 				$fup->aErrors[] = "3: File already exists";
 				return false;
 			} elseif ($fup->settings_replace) {
 				@unlink($path);
 			}
			move_uploaded_file($file['tmp_name'] , $path);
			return $path;
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