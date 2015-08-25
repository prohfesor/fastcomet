<?php

class flyMail {

	var $from ="";
	var $to ="";
	var $subject ="";
	var $message ="";
	var $senderName ="";
	//var $recipientName ="";
	var $replyTo ="";
	var $xMailer ="";
	var $type ="txt";
	var $encoding ="";


	/**
	 * class constructor
	 */
	 function flyMail() {

	 }


	 /**
	  *  send plain-text message
	  *  Specify $this->to, $this->from, $this->subject, $this->SenderName, $this->ReplyTo, $this->message
	  */
	 function send($to =null, $subject =null, $message =null, $aHeaders =array()) {
	 	//flyDebug::assert( "!empty($this->to) &&  (!empty($this->subject)" );
		if(!empty($to)) $this->to = $to;
		if(!empty($subject)) $this->subject = $subject;
		if(!empty($message)) $this->message = $message;
		if(!empty($aHeaders['senderName'])) {
			$this->senderName = $aHeaders['senderName'];
			unset($aHeaders['senderName']);
		}	
		if(!empty($aHeaders['replyTo'])) {
			$this->replyTo = $aHeaders['replyTo'];
			unset($aHeaders['replyTo']);
		}
		if(!empty($aHeaders['xMailer'])) {
			$this->xMailer = $aHeaders['xMailer'];
			unset($aHeaders['xMailer']);
		}
		
		$subject = $this->subject;
		$senderName = $this->senderName;
		
		if(!empty($this->encoding)){
			$subject = "=?{$this->encoding}?B?".base64_encode($subject)."?=";
			$senderName = "=?{$this->encoding}?B?".base64_encode($senderName)."?=";
		}
		
		if(empty($this->from)) $this->from = "noreply@".$_SERVER['SERVER_NAME'];
		if(!empty($this->senderName)) {
			$headers = "From: $this->senderName <$this->from>"."\r\n";
		} else {
			$headers = "From: $this->from"."\r\n";
		}
		if(!empty($this->replyTo)) {
			$headers .= "Reply-To: $this->replyTo"."\r\n";
		}
		if(empty($this->xMailer)) $this->xMailer = "PHP/".phpversion();
			$headers .= "X-Mailer: ".$this->xMailer."\r\n";
		if($this->type == 'html')
			$headers .= "Content-type: text/html";
		else
			$headers .= "Content-type: text/plain";
		if(!empty($this->encoding))
			$headers .= "; charset=$this->encoding \r\n";
		else
			$headers .= "\r\n";
		
		if(sizeof($aHeaders)) foreach($aHeaders as $k=>$v){
			$headers .= "$k: $v\r\n";
		}
		
		return mail($this->to, $subject, $this->message, $headers);
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
		$aHdrs = array('from', 'to', 'subject', 'senderName', 'replyTo');
		foreach($aHdrs as $v) {
			if (isset($aHeaders[$v])) $this->$v = $aHeaders[$v];
		}
		$this->send();
	 }


}


?>