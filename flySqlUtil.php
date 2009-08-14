<?php


 class flySqlUtil {


	/*
	 *  Protect string for including into query.
	 *  Quotes at the biginning and the end are added,
	 *  except if $addQuotes == false.
	 *  @access public
	 *  @access static
	 */
 	function prepareString($string ="", $addQuotes=1) {
		$result = addslashes($string);
		if($addQuotes){
			$result = '"'.$result.'"';
		}
		return $result;
 	}


 }


?>