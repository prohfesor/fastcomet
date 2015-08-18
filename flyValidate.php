<?php


class flyValidate {

	/*
	 * class constructor
	 */
	 function flyValidate(){

	 }


	/**
	 * determine if string is correct e-mail
	 * returns false if not
	 * Existance of domain and email adress is not checked
	 * Sure, it's imposiible ;)
	 */
	function isEmail($str) {
		if (preg_match('/\\b[A-Za-z0-9._-]+@[A-Za-z0-9._-]+\\.[A-Za-z]{2,4}\\b/', $str)) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * determine if string is correct variable name
	 * returns false if not
	 */
	function isCorrectVar($str) {
		if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $str)) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * validates array
	 * @aValidate array
	 * ('key' => condition,
	 *  ...
	 * )
	 * returns array of bool.
	 */
	function validateArray($aData, $aValidate) {
		$aResult = array();
		foreach($aValidate as $key=>$condition) {
			$bit = false;
			if(!isset($aData[$key])){
				continue;
			}
			$aCondParams = explode(":",$condition);
			switch ($aCondParams[0]) {
				case 'notnull':
					$bit = (!empty($aData[$key])) ? true : false;
					break;
				case 'email':
					$bit = (flyValidate::isEmail($aData[$key])) ? true : false;
					break;
				case 'eq':
					if(!isset($aCondParams[1])) {
						return new flyError("validateArray: Incorrect condition for 'eq' state. Use 'eq:\"string\"' statement");
					}
					if($aCondParams[1]{0}=='"' && substr($aCondParams[1],-1)=='"') {
						$aCondParams[1] = substr($aCondParams[1],1,-1);
					}
					$bit = ($aData[$key] == $aCondParams[1]) ? true : false;
					break;
				case '':
					$bit = true;
					break;
				default:
					$bit = true;
			}
			$aResult[$key] = $bit;
		}
		return $aResult;
	}


	/**
	 * validates object
	 * @aValidate array
	 * ('key' => condition,
	 *  ...
	 * )
	 * returns array of bool.
	 */
	function validateObject($oData, $aValidate) {
		$aResult = array();
		foreach($aValidate as $key=>$condition) {
			$bit = false;
			if(!isset($oData->$key)){
				continue;
			}
			$aCondParams = explode(":",$condition);
			switch ($aCondParams[0]) {
				case 'notnull':
					$bit = (!empty($oData->$key)) ? true : false;
					break;
				case 'email':
					$bit = (flyValidate::isEmail($oData->$key)) ? true : false;
					break;
				case 'eq':
					if(!isset($aCondParams[1])) {
						return new flyError("validateObject: Incorrect condition for 'eq' state. Use 'eq:\"string\"' statement");
					}
					if($aCondParams[1]{0}=='"' && substr($aCondParams[1],-1)=='"') {
						$aCondParams[1] = substr($aCondParams[1],1,-1);
					}
					$bit = ($oData->$key == $aCondParams[1]) ? true : false;
				case '':
					$bit = true;
					break;
				default:
					$bit = true;
			}
			$aResult[$key] = $bit;
		}
		return $aResult;
	}


	/** Helps to validate
	 *  an array of values and returns error messages.
	 *  See validateArray() and validateObject() description.
	 *  $aValidate - array('key' => 'condition')
	 *  $Data - array or object
	 *  $aMessages - array('key' => 'error message')
	 */
	function validateWithMessage($Data, $aValidate, $aMessages) {
		$aResult = array();
		$aErrors = array();
		if(is_array($Data)) {
			$aResult = flyValidate::validateArray($Data, $aValidate);
		}
		if(is_object($Data)) {
			$aResult = flyValidate::validateObject($Data, $aValidate);
		}
		foreach($aResult as $key=>$value) {
			if(!$value && isset($aMessages[$key])) {
				$aErrors[] = $aMessages[$key];
			}
		}
		return $aErrors;
	}


}


?>