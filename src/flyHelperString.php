<?php

/**
 * Helper set of static functions.
 * General purpose functions to increase usability.
 * Usage: func::function_name($param)
 */

class flyHelperString {

	/**
	 * Generate random string
	 * If string $symbols passed - uses only specified symbols then
	 * @param int $length
	 * @param null $symbols
	 * @return string
	 */
	static function generate_string($length =16, $symbols =null) {
		$string = '';
		if(empty($symbols)) {
			$symbols = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		}
        if(is_array($symbols)){
            $symbols = implode('', $symbols);
        }
		for($i=1;$i<=$length;$i++){
			$string .= $symbols{rand(0,strlen($symbols)-1)};
		}
		return $string;
	}


}
