<?php


class flyMail {

	var $from ="";
	var $to ="";
	var $subject ="";
	var $message ="";
	var $SenderName ="";
	var $RecipientName ="";
	var $ReplyTo ="";
	var $XSender ="";


	/**
	 * class constructor
	 */
	 function flyMail() {

	 }


	 /**
	  *  send plain-text message
	  *  Specify $this->to, $this->from, $this->subject, $this->SenderName, $this->ReplyTo, $this->message
	  */
	 function send() {
	 	//flyError::raise("TO: $this->to; SUBJECT: $this->subject; MESSAGE: $this->message; HEADERS: $headers");
	 	//flyDebug::assert( "!empty($this->to) &&  (!empty($this->subject)" );

		if(empty($this->from)) $this->from = "noreply@".$_SERVER['SERVER_NAME'];
		if(!empty($this->SenderName) && !empty($this->from)) {
			$headers = "From: $this->SenderName <$this->from>"."\r\n";
		} else {
			$headers = "From: $this->from"."\r\n";
		}
		if(!empty($this->ReplyTo)) {
			$headers .= "Reply-To: $this->ReplyTo"."\r\n";
		}

			$headers .= "X-Mailer: PHP/".phpversion()."\r\n";

		mail($this->to, $this->subject, $this->message, $headers);
	 }


	 /**
	  * Get text file template into $this->message
	  */
	 function load_template($file) {
	 	if(!is_file($file)) {
	 		return false;
	 	}
	 	$this->message = file_get_contents($file);
	 	return $this->message;
	 }


	 /**
	  * Parse template vars
	  * @mixed $vars array("varname"=>"varvalue")
	  * @bool $casesensitive
	  */
	 function parse_vars($vars, $casesensitive =0) {
	 	$message =& $this->message;
		foreach($vars as $key=>$value){
			if(!$casesensitive){
				$message = preg_replace("/{%$key%}/i", $value, $message);
			} else {
				$message = str_replace("{%$key%}", $value, $message);
			}
		}
	 }


	 /**
	  *  Sends plain-text message.
	  *  Specify "to", "sender", "subject", etc. in $headers array.
	  *  Optional - assoc. array of tpl vars names=>values, parsed
	  *  with case insensitive.
	  */
	 function sendsmart($aHeaders, $message, $tplvars =array()) {
		$this->message = $message;
		$this->parse_vars($tplvars);
		$aHeaders = array_merge( array('to'=>'','subject'=>'') , $aHeaders );
		$aHdrs = array('from', 'to', 'subject', 'SenderName', 'ReplyTo');
		foreach($aHdrs as $v) {
			if (isset($aHeaders[$v])) $this->$v = $aHeaders[$v];
		}
		$this->send();
	 }


}


?>