<?php


 class flyEnv {


	/*
	 * class constructor
	 */
	function flyEnvironment( $pass =null ) {
		if(!$pass == "EnvironmentPass") {
			new flyError("Unable to create new instance of singleton class! Use &getInstance() method.");
		}

		$this->initialize();
	}


	/*
	 * @access private
	 * Defines internal constants.
	 */
	function initialize() {
		define('FLY_REQUEST_ALL',  0);
		define('FLY_REQUEST_GET',  1);
		define('FLY_REQUEST_POST', 2);
		define('FLY_REQUEST_COOKIE', 3);
		define('FLY_REQUEST_SERVER', 4);
	}



	/**
	 * Return specified key from request arrays
	 * (FLY_REQUEST_ALL, FLY_REQUEST_GET, FLY_REQUEST_POST, FLY_REQUEST_COOKIE).
	 * If no such key, return $default or FALSE.
	 * Request arrays have next priority: see $_REQUEST priority
	 * (EGPCS (Environment, GET, POST, Cookie, Server) by default)
	 * This is defined by php value "variables_order".
	 */
	function get($key, $default =false, $area =0) {
		switch($area) {
		  case(0):
		  	$aRequest =& $_REQUEST;
		  	break;
		  case(FLY_REQUEST_GET):
			$aRequest =& $_GET;
			break;
		  case(FLY_REQUEST_POST):
			$aRequest =& $_POST;
			break;
		  case(FLY_REQUEST_COOKIE):
			$aRequest =& $_COOKIE;
			break;
		  case(FLY_REQUEST_SERVER):
		  	$aRequest =& $_SERVER;
            break;
		  default:
			 return false;
		}

		if (isset($aRequest[$key]) ) {
			return $aRequest[$key];
		} else {
			return $default;
		}
	}


	/**
	 * Checks if specified key exists in request arrays and is integer value.
	 * Returns $default or FALSE, if not.
	 * See get() description.
	 */
	function getInt($key, $default =0, $area =0) {
		$value = $this->get($key, $default, $area);
		if(is_int( (int)$value )) {
			return (int)$value;
		} else {
			return $default;
		}
	}


	/**
	 * Checks if specified key exists in request arrays and is float value.
	 * Returns $default or FALSE, if not.
	 * See get() description.
	 */
	function getFloat($key, $default =0, $area =0) {
		$value = $this->get($key, $default, $area);
		if(is_float((float)$value)) {
			return $value;
		} else {
			return $default;
		}
	}


	/**
	 * Checks if specified key exists in request arrays and is numeric value.
	 * Returns $default or FALSE, if not.
	 * See get() description.
	 */
	function getNumeric($key, $default =0, $area =0) {
		$value = $this->get($key, $default, $area);
		if(is_numeric($value)) {
			return $value;
		} else {
			return $default;
		}
	}


	/**
	 * Checks if specified key exists in request arrays and is string value.
	 * Returns $default or FALSE, if not.
	 * See get() description.
	 */
	function getString($key, $default ='', $area =0) {
		$value = $this->get($key, $default, $area);
		if(is_string( (string)$value )) {
			return $value;
		} else {
			return $default;
		}
	}


	/**
	 * Checks if specified key exists in request arrays and is boolean value.
	 * Returns $default or FALSE, if not.
	 * See get() description.
	 */
	function getBool($key, $default =false, $area =0) {
		$value = $this->get($key, $default, $area);
		switch($value){
			case "true": $value = true; break;
			case "false": $value = false; break;
			default: $value = (bool)$value;
		}
		if(is_bool($value)) {
			return $value;
		} else {
			return $default;
		}
	}


	/**
	 * Checks if specified key exists in request arrays and is object.
	 * Returns FALSE, if not.
	 * See get() description.
	 */
	function getObject($key, $default, $area =0) {
		$value = $this->get($key, $default, $area);
		if(is_object($value)) {
			return $value;
		} else {
			return $default;
		}
	}


	/**
	 * Checks if specified key exists in request arrays and is array.
	 * Returns $default or FALSE, if not.
	 * See get() description.
	 */
	function getArray($key, $default =array(), $area =0) {
		$value = $this->get($key, $default, $area);
		if(is_array($value)) {
			return $value;
		} else {
			return $default;
		}
	}


	/**
	 *  Determines if there are data in the POST
	 *  @param bool $manual - don't trust PHP, determine internally
	 */
	function isPost($manual =false) {
		if (!$manual) {
			if ('POST' == $_SERVER['REQUEST_METHOD']) {
				return true;
			} else {
				return false;
			}
		} else {
			if (!empty($_POST)) {
				return true;
			} else {
				return false;
			}
		}
		//return false;
	}


	/**
	 *  Redirects user to another page.
	 *  Uses "Location: " header; if $by_meta = TRUE
	 *  then <meta> tag used for redirection.
	 */
	function redirect($to, $by_meta =false) {
		if(!$by_meta) {
			header("Location: $to");
		} else {
			echo("<meta http-equiv=\"refresh\" content=\"0; url=$to\">");
		}
	}


	function &getInstance() {
		global $instance;
		if ($instance === null) {
			$instance = new flyEnv("EnvironmentPass");
		}

		return $instance;
	}


 }


?>