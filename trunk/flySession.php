<?php

require_once("flyValidate.php");
require_once("flyDebug.php");

class flySession {


	var $connected =false;
	var $lazy =true;
	var $opened =false;
	var $name ="";
	var $id =0;
	var $cache_expire;
	var $cookie_path;
	var $cookie_domain;
	var $cookie_lifetime;
	var $aVars =array();


	/*
	 * Class constructor.
	 * Use &getInstance() instead.
	 */
	 function flySession($pass) {
	 	if($pass != "flySessionPass") {
	 		return new flyError("Unable to create instance of Singleton class. Use &getInstance metod.");
	 	}
	 }


	 /** Need to call this function, if $lazy is NOT active otherwise, it called automatically.
	  *  Also you can call it, but only immediatly after &getInstance()
	  *  in case you need to set different session params.
	  */
	 function open( $expire =null, $path =null, $domain =null) {
	 	$this->cache_expire    = (!empty($expire)) ? $expire : ini_get('session.cache_expire');
	 	$this->cookie_path	   = (!empty($path))   ? $path   : ini_get('session.cookie_path');
	 	$this->cookie_domain   = (!empty($domain)) ? $domain : ini_get('session.cookie_domain');
	 	$this->cookie_lifetime = (!empty($expire)) ? $expire : ini_get('session.cookie_lifetime');
		if(!$this->lazy) {
			$this->_do_connect();
		}
		$this->opened = true;
	 }


	 function close() {
	 	//check if connected
		$this->_do_connect();
	 	session_write_close();
	 	$this->connected = false;
	 }


	 function _do_connect() {
	 	if(!$this->connected) {
	 		//finish session if present (for previous)
	 		session_write_close();
	 		//set session name
			session_name($this->name);
			//set params
			session_set_cookie_params( $this->cookie_lifetime, $this->cookie_path, $this->cookie_domain );
			session_cache_expire( $this->cache_expire );
			//start session
			session_start();
			$this->connected = true;
			$this->id = session_id();
			$this->aVars =& $_SESSION;
	 	}
	 }


	 function get($key, $default =false) {
		if($this->lazy && !$this->connected) {
			$this->open( $this->cache_expire , $this->cookie_path , $this->cookie_domain );
			$this->_do_connect();
		}
		if(isset($_SESSION[$key])) {
			return $_SESSION[$key];
		} else {
			return $default;
		}
	 }


	 function put($key, $value) {
		if($this->lazy && !$this->connected) {
			$this->open();
			$this->_do_connect();
		}
		flyDebug::assert( flyValidate::isCorrectVar($key) , "flySession->put() - Incorrect name for session variable!" );
		$_SESSION[$key] = $value;
	 }
	 
	 
	 /*
	  * Destroy session variable.
	  * Returns variable value. 
	  * If no such variable - returns false.
	  */
	 function remove($key) {
	 	if(isset($_SESSION[$key])) {
	 		$old = $_SESSION[$key];
			unset($_SESSION[$key]);
			return $old;
		} else {
			return false;
		}
	 }


	 function clear() {
	 	//check if connected
		$this->_do_connect();
	 	session_unset();
	 }


	 /**
	  * Returns session instance.
	  * Multiple sessions can run at a time, returns session with $sess_id id.
	  * Empty $sess_id equal to system "PHPSESSID" etc.
	  */
	 function &getInstance($sess_id =null) {
		static $aSessId =array();
		if (empty($sess_id)) {
			$sess_id = ini_get("session.name");
		}
		if (!isset($aSessId[$sess_id])) {
			flyDebug::assert( flyValidate::isCorrectVar($sess_id) , "flySession::getInstance() - Incorrect session identifier" );
			$aSessId[$sess_id] = new flySession("flySessionPass");
			$aSessId[$sess_id]->name = $sess_id;
		}

		return $aSessId[$sess_id];
	 }


}

?>