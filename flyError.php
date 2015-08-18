<?php

define("ERROR_RETURN", 1);
define("ERROR_TRIGGER", 2);
define("ERROR_TRIGGER_HTML", 3);
define("ERROR_DIE", 4);

 class flyError {


	var $message =null;
	var $file =null;
	var $line =null;
	var $stackDump =null;


	/*
	 *  class Constructor
	 *  raises custom error
	 */
    function flyError($message, $file = null, $line = null)
    {
        $this->message   = (string)$message;
        $this->file      = $file;
        $this->line      = $line;
        $this->stackDump = function_exists('debug_backtrace') ? debug_backtrace() : array();

        $this->raise();
    }


	/*
	 *  Error raiser
	 */
	function raise()
    {
        $settings =& flyErrorSettings::getInstance();

        switch ($settings->handlingMode) {
            case ERROR_RETURN:
                break;

            case ERROR_TRIGGER:
                if (error_reporting() != 0) {
                    trigger_error($this->toString(false), E_USER_WARNING);
                }
                break;

            case ERROR_TRIGGER_HTML:
                if (error_reporting() != 0) {
                    fly_error_handler( E_USER_ERROR, $this->toString(true) , null, null);
                }
                break;

            case ERROR_DIE:
                die("<pre style=\"font-family: Arial;\">\n<b>Execution died</b><p>\n".$this->_toString()."\n</p></pre>");
                break;

            default:
                die();
                break;
        }
    }


	/*
	 *  Get error message, with file and line (if specified)
	 */
	function _toString() {
        $result = $this->message;
        if ($this->file !== null) {
            $result .= "\n    (".$this->file;
            if ($this->line !== null) {
                $result .= ":".$this->line;
            }
            $result .= ")";
        }
        return $result;
    }


	/*
	 *  Get error message with full error dump
	 */
	function toString($toHtml = false)    {
        $result = $this->_toString($toHtml);
        $result .= "<br><b>Stack dump:</b> <br/>".$this->stackDumpToString($toHtml);

        return $result;
    }


    function stackDumpToString($toHtml = false)   {
        if (empty($this->stackDump)) {
            return "No stack dump.";
        }

        $call_stack = "";
        foreach ($this->stackDump as $call) {
            $call_stack .= " \x95 ";

            if (isset($call['class'])) {
                if (isset($call['type']))
                    $call_stack .= $call['class'].$call['type'];
                else
                    $call_stack .= $call['class']."::";
            }

            $call_stack .= "$call[function](";
            if (isset($call['args'])) {
                $args = array ();
                foreach ($call['args'] as $arg) {
                    $type = gettype($arg);
                    switch ($type) {
                        case 'boolean':
                            $arg = $arg ? 'true' : 'false';
                            break;
                        case 'null':
                            $arg = 'null';
                            break;
                        case 'integer':
                        case 'double':
                            break;
                        case 'string': {
                            $maxchars = 50;
                            $arg = strlen($arg) > $maxchars ? '"'.substr($arg, 0, $maxchars).'"'."..." : '"'.$arg.'"';
                            break;
                        }
                        case 'array':
                            $arg = 'array';
                            break;
                        case 'object':
                            $arg = 'object:'.get_class($arg);
                            break;
                    }
                    $args[] = $arg !== null ? "($type)$arg" : $type;
                }
                $call_stack .= implode(', ', $args);
            }
            $call_stack .= "); \n";
            if (isset($call['file'])) {
				$call_stack .= "    $call[file]:$call[line]\n\n";
            }
        }

        if($toHtml) {
        	$call_stack = str_replace( "\n", " <br>\n", $call_stack );
        } else {
        	$call_stack = "<pre>$call_stack</pre>";
        }

        return $call_stack;
    }


	 static function outHTML($message, $title ="ERROR") {
		$settings = flyErrorSettings::getInstance();
		$D = rand(100,500);
		$pre = str_replace( array("errD","%ERROR%") , array("err$D",$title) , $settings->pre_errmsg);

		if(!$settings->pre_cnt) {
			print_r($settings->pre_errs);
			$settings->pre_cnt =+ 1;
		}
		$pre  = "<script>document.getElementById('div_errS').innerHTML = document.getElementById('div_errS').innerHTML+'" . addslashes($pre);
		$post = "' </script>" .$settings->post_errmsg;
		$message = str_replace( array("\r","\n"), "", $message );
		$message = addslashes($message);
		print_r($pre.$message.$post);
	}


    static function setErrorHandling($handlingMode)  {
        $settings =& flyErrorSettings::getInstance();
        $settings->handlingMode = $handlingMode;

		if($settings->handle_all_errors) {
			set_error_handler('fly_error_handler');
		}
    }



 }



class flyErrorSettings {

    var $handlingMode;
    var $handle_all_errors;
    var $halt_php_handling;
    var $pre_errmsg;
    var $post_errmsg;


	function flyErrorSettings($pass) {
		if($pass!="ErrorSettingsPass"){
			return new flyError("Prohibited to call private constructor of Singleton class!");
		}
		$this->handlingMode = ERROR_TRIGGER_HTML;
    	$this->handle_all_errors = 1;
    	$this->halt_php_handling = 0;
    	$this->pre_cnt = 0;
    	$this->pre_errs   = '^<div id="div_errS" style="position: absolute; left: 0; top: 0; width: 100%; z-index: 9999;"></div>';
    	$this->pre_errmsg = ' <div id="div_errD" style="padding: 0 10px; background-color: #ffd; border-bottom: 1px solid #999;">' .
    						'<b>%ERROR%</b> <span id="errDshw">[<a href="#" onclick="document.getElementById(\'errD\').style.display=\'\'; document.getElementById(\'errDhde\').style.display=\'\'; document.getElementById(\'errDshw\').style.display=\'none\'; return false">show</a>]</span>' .
    						 '<span id="errDhde" style="display: none;">[<a href="#" onclick="document.getElementById(\'errD\').style.display=\'none\'; document.getElementById(\'errDshw\').style.display=\'\'; document.getElementById(\'errDhde\').style.display=\'none\'; return false">hide</a>]</span>' .
    						 '<span id=""> | [<a href="#" onclick="document.getElementById(\'div_errD\').style.display=\'none\'; return false">close</a>]</span>' .
    						'<pre id="errD" style="font-family: Arial; display: none">';
    	$this->post_errmsg ='</pre></div>';
	}

    /*
     * @return flyErrorSettings
     * @access private
     */
    static function getInstance()
    {
        static $instance;
        if ($instance === null) {
            $instance = new flyErrorSettings("ErrorSettingsPass");
        }
        return $instance;
    }
}



function fly_error_handler($err_no, $err_str, $err_file, $err_line) {
    $aErrortype = array (
                E_ERROR           => "Error",
                E_WARNING         => "Warning",
                E_PARSE           => "Parsing Error",
                E_NOTICE          => "Notice",
                E_CORE_ERROR      => "Core Error",
                E_CORE_WARNING    => "Core Warning",
                E_COMPILE_ERROR   => "Compile Error",
                E_COMPILE_WARNING => "Compile Warning",
                E_USER_ERROR      => "User Error",
                E_USER_WARNING    => "User Warning",
                E_USER_NOTICE     => "User Notice",
                2048			  => "Strict Runtime Notice" //E_STRICT
                );
    $error_rep = ini_get("error_reporting");
    if($error_rep==0) {
    	return true;
    }

	if (isset($aErrortype[$err_no])) {
		$err_message = "<b>$aErrortype[$err_no]</b>: <br>$err_str";
		$err_title = $aErrortype[$err_no];
		if(!empty($err_file) && !empty($err_line)) {
			$err_message .= " <br>$err_file [$err_line] <br>";
		}
	} else {
		$err_message = "<b>ERROR!</b>: <br>$err_str";
		$err_title = null;
		if(!empty($err_file) && !empty($err_line)) {
			$err_message .= " <br>$err_file [$err_line] <br>";
		}
	}

	flyError::outHTML($err_message, $err_title);

    $settings =& flyErrorSettings::getInstance();
    if (!$settings->halt_php_handling) {
    	return false;
    }
}

?>