<?php

define ("DEBUG_IGNORE", 1);
define ("DEBUG_TRIGGER_WARNING", 2);
define ("DEBUG_TRIGGER_ERROR", 4);


 class flyDebug{

	var $assertReaction =DEBUG_TRIGGER_WARNING;
	var $failedMessage  ="";

	
    function assert($condition, $err_message ="") {
        if (!is_bool($condition)) {
        	$message = "Condition must only have boolean result.";
            trigger_error("flyDebug::assert(): $message (<code>$condition</code>)", E_USER_ERROR);
        }

        $callingPlace = flyDebug::getCallingPlace(true);
        if (empty($callingPlace)) {
            $callingPlace = array ("unknown", "unknown");
        }

        if (!$condition) {
        	$debug =& flyDebug::getInstance();
        	$debug->failedMessage = $err_message;
            flyDebug::notifyAssertion($callingPlace[0], $callingPlace[1], "");
        }
    }


    function notifyAssertion($file, $line, $code)
    {
        $debug =& flyDebug::getInstance();

        switch ($debug->assertReaction) {
            case DEBUG_IGNORE:
                break;
            case DEBUG_TRIGGER_WARNING:
				flyError::outHTML( "Assertion failed <br>". ($debug->failedMessage!="" ? $debug->failedMessage."<br>" : "") .$file."[".$line."]".($code!="" ? "for code $code" : ""), "Assertion failed" );
            	break;
            case DEBUG_TRIGGER_ERROR:
                trigger_error("Assertion failed ".$file."[".$line."] ".($code != "" ? "for code $code" : "") . ($debug->failedMessage!="" ? " Reason: ".$debug->failedMessage : ""), E_USER_ERROR);
                break;
            default:
                trigger_error("Unknown reaction to assert() call.", E_USER_ERROR);
                break;
        }
    }



    function dump($value, $caption = "", $escape = true, $return = false) {
    	$debug =& flyDebug::getInstance();
        ob_start();
         print_r($value);
        $content = ob_get_contents();
        ob_end_clean();
        $callingPlace = flyDebug::getCallingPlace() . "\n\n";
        $result = "<pre class='dump'>" . htmlspecialchars($callingPlace) . htmlspecialchars($caption).' '.($escape ? htmlspecialchars($content) : $content)."</pre>";
        if (!$return) {
        	if($debug->assertReaction == DEBUG_TRIGGER_WARNING) {
        		flyError::outHTML( str_replace("\n","<br>",$result) , "Variable dump" );
        	} else {
        		echo $result;
        	}
        }
        else {
            return $result;
        }
    }


    function getCallingPlace($returnArray = false)
    {
        if (!$returnArray) {
            $result = "";
        }
        else {
            $result = array ();
        }

        if (function_exists("debug_backtrace")) {
            $backtrace = debug_backtrace();
            if (count($backtrace) > 1) {
                if ($returnArray) {
                    $result = array ($backtrace[1]['file'], $backtrace[1]['line']);
                }
                else {
                    $result = $backtrace[1]['file'].":".$backtrace[1]['line'];
                }
            }
        }

        return $result;
    }


    /**
     * Call this to display script timing. Uses tick function to log stats.
	 * Code evaluation script which uses debug_backtrace() to get execution time in ns, relative current line number, function, file, and calling function info on each tick, and shove it all in $script_stats array.  See debug_backtrace manual to customize what info is collected.
	 *
	 * Warning: this will exhaust allowed memory very easily, so adjust tick counter according to the size of your code.  Also, array_key_exists checking on debug_backtrace arrays is removed here only to keep this example simple, but should be added to avoid a large number of resulting PHP Notice errors.
	 * code from php.net
	 * @param int ticks
     */
    function benchmark($ticks =1){
    	$debug = flyDebug::GetInstance();
		$debug->script_stats = array();
		$debug->script_time = microtime(true);

		function track_stats(){
			$debug = flyDebug::GetInstance();
		    $trace = debug_backtrace();
		    $exe_time = (microtime(true) - $debug->script_time) * 1000;
		    foreach($trace[1]["args"] as $k=>$arg){
		    	if('string'==gettype($arg) && 60<=strlen($arg))
		    		$trace[1]["args"][$k] = substr($arg,0,45) . '...' . substr($arg,-15);
		    }
		    $func_args = implode(", ",$trace[1]["args"]);
		    $stat = array(
		        "current_time" => date("H:i:") . ((microtime(true)-time())+(int)date("s")),
		        "memory" => memory_get_usage(true),
		        "function" => $trace[1]["function"].'('.$func_args.')',
		        );
		    if(!empty($trace[1]["file"]))
		    	$stat["file"] = $trace[1]["file"].': '.$trace[1]["line"];
		    if(!empty($trace[2]))
		    	$stat["called_by"] = $trace[2]["function"];
		    if(!empty($trace[2]["file"]))	 
		    	$stat["called_by"] .= ' in '.$trace[2]["file"].': '.$trace[2]["line"];
		    $stat["ns"] = $exe_time; 
		    
		    $debug->script_stats[] = $stat;
			$debug->script_time = microtime(true);
		}

		//TODO: add tmp dir detection
		$temp_file = ini_get('upload_tmp_dir').'/flydebug_ticks.php';
    	file_put_contents($temp_file,"<? declare(ticks=$ticks) ?>");
    	require_once($temp_file);
    	unlink($temp_file);
		
		register_tick_function("track_stats");
		register_shutdown_function('flyDebug_helper_benchmark');
    }


    function &getInstance() {
        static $instance = null;

        if ($instance === null) {
            $instance = new flyDebug();
        }

        return $instance;
    }

}



/**
 * Helper function to call flyDebug::notifyAssert()
 * @access private
 */
function flyDebug_notifyAssertion($file, $line, $code)
{
    flyDebug::notifyAssertion($file, $line, $code);
}


/**
 * Helper function to display benchmark information on shutdown
 * @access private
 */
function flyDebug_helper_benchmark(){
	$debug = flyDebug::GetInstance();
	$debug->dump($debug->script_stats, 'Benchmark stats');
}

assert_options(ASSERT_CALLBACK, "flyDebug_notifyAssertion");
assert_options(ASSERT_WARNING, 0);