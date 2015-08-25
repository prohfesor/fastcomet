<?php


 class flySqlUtil {


	/**
	 *  Protect string for including into query.
	 *  Quotes at the biginning and the end are added,
	 *  except if $addQuotes == false.
	 *  @access public
	 *  @access static
	 */
 	function prepareString($string ="", $addQuotes =1) {
		$result = addslashes($string);
		if($addQuotes){
			$result = '"'.$result.'"';
		}
		return $result;
 	}
 	
 	
 	/**
 	 * Prepare array of strings for db query.
 	 * Applies prepareString method. 
 	 * @param $array
 	 * @param $addQuotes
 	 * @return mixed
 	 * @access public
	 * @access static
 	 */
 	function prepareArrayString($array =array(), $addQuotes =1){
 		$result = array();
 		foreach($array as $k=>$v){
 			$result[$k] = $this->prepareString($v, $addQuotes);
 		}
 		return $result;
 	}
 	
 	
 	/**
 	 * Ensures every array element to be integer.
 	 * Otherwise - will be replaced with zero (0). 
 	 * @param $array
 	 * @return mixed
 	 * @access public
	 * @access static
 	 */
 	function prepareArrayInt($array =array()){
 		$result = array();
 		foreach($array as $k=>$v){
 			$result[$k] = (int)$v;
 		}
 		return $result;
 	}


 }


?>