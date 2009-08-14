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
 *
 * @access private
 */
function flyDebug_notifyAssertion($file, $line, $code)
{
    flyDebug::notifyAssertion($file, $line, $code);
}

assert_options(ASSERT_CALLBACK, "flyDebug_notifyAssertion");
assert_options(ASSERT_WARNING, 0);