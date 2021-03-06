<?php

/**
 * Helper functions for filesystem
 * author: @prohfesor
 * package: frm.local
 * Date: 02.09.2015
 * Time: 15:39
 */
class flyHelperFilesystem
{

    /**
     * Read gzip'ed file and get its contents.
     */
    function gzfile_get_contents($file) {
        if(!$file = gzfile($file)) return false;
        if(!$file = implode('', $file)) return false;
        return $file;
    }


    /**
     * Write file archived in gzip.
     * Function similar to func::file_write_contents
     */
    function gzfile_put_contents($file, $text, $mode = 'w+') {
        while(is_file($file . '.lock') && !@IGNORE_LOCK_FILES){
            //Wait for lock to release
        }
        chmod($file, 0777);
        $fp = fopen($file . '.lock', 'w+'); fwrite($fp, 'lock'); fclose($fp);
        if($fp = gzopen($file, $mode)) {
            if(!empty($text) && !gzwrite($fp, $text)) return false;
            gzclose($fp);
        } else return false;
        unlink($file . '.lock');
        return true;
    }


    /**
     * Reads file into array.
     * Each line is an array element.
     * This function is similar to file(), but it
     * removes end-of-line characters.
     */
    function file_get_lines($file) {
        $aResult = array();

        function filegetarraytrim(&$item1, $key) {
            $item1 = rtrim($item1);
        }

        if(file_exists($file)) {
            $aResult = file($file);
            array_walk($aResult, "filegetarraytrim");
        }
        return $aResult;
    }


    /**
     * Creates *.ini file.
     * @mixed $data - Associative array which would be saved
     * @string $filename
     * @bool $process_sections - Define, if '[ ]' brackets would be added to params or not. By default - not added.
     * @return mixed
     * Copyright by reloadcms.
     */
    function write_ini_file($data, $filename, $process_sections = false){
        $ini = '';
        if(!$process_sections){
            if(is_array($data)){
                foreach ($data as $key => $value){
                    $ini .= $key . ' = "' . str_replace('"', '&quot;', $value) . "\"\n";
                }
            }
        } else {
            if(is_array($data)){
                foreach ($data as $key => $value){
                    $ini .= '[' . $key . ']' . "\n";
                    foreach ($value as $ekey => $evalue){
                        $ini .= $ekey . ' = "' . str_replace('"', '&quot;', $evalue) . "\"\n";
                    }
                }
            }
        }
        return func::file_write_contents($filename, $ini);
    }


    /**
     * Read ini file and return parameters as associative array
     * $key -> $value
     * @string $filename
     * @bool $blocks - get keys from "[ ]" brackets. By default - no brackets.
     * @return mixed
     * Copyright by reloadcms.
     */
    function read_ini_file($filename, $blocks = false){
        if(!is_file($filename)) {
            return array();
        }
        $array1 = file($filename);
        $section = '';
        foreach ($array1 as $filedata) {
            $dataline = trim($filedata);
            $firstchar = substr($dataline, 0, 1);
            if ($firstchar != ';' && !empty($dataline)) {
                if ($blocks && $firstchar == '[' && substr($dataline, -1, 1) == ']') {
                    $section = strtolower(substr($dataline, 1, -1));
                } else {
                    $delimiter = strpos($dataline, '=');
                    if ($delimiter > 0) {
                        preg_match("/^[\s]*(.*?)[\s]*[=][\s]*(\"|)(.*?)(\"|)[\s]*$/", $dataline, $matches);
                        $key = $matches[1];
                        $value = $matches[3];

                        if($blocks){
                            if(!empty($section)){
                                $array2[$section][$key] = stripcslashes($value);
                            }
                        } else {
                            $array2[$key] = stripcslashes($value);
                        }
                    } else {
                        if($blocks){
                            if(!empty($section)){
                                $array2[$section][trim($dataline)] = '';
                            }
                        } else {
                            $array2[trim($dataline)] = '';
                        }
                    }
                }
            }
        }
        return (!empty($array2)) ? $array2 : false;
    }


    /**
     * Files list in directory
     * @string $folder
     * @string $filter - condition in format "FILE != smth", where FILE is a placeholder for each entry. PHP eval() is used to handle this condition.
     * @return mixed
     */
    function files_list($folder, $filter ="") {
        $res = array();
        if(!is_dir($folder)) {
            return $res;
        }
        if(empty($filter)) {
            $filter = "1";
        }
        $dir = dir($folder);
        while ( $entry = $dir->read() ) {
            $iFilter = str_replace("FILE", $entry, $filter);
            //echo("Entry: $entry;  Filter: $iFilter; <br>");
            if(($entry!='.') AND ($entry != '..') AND (eval("return (bool)(".$iFilter.");"))) {
                $res[] = $entry;
            }
        }
        return $res;
    }


    /**
     * Directory listing
     * @string $folder
     * @string $filter
     * @return mixed
     */
    function dirs_list($folder, $filter ="") {
        $newfilter = 'is_dir("'.$folder.'FILE")';
        $newfilter = (empty($filter))  ?	$newfilter  :  $newfilter." AND ($filter)" ;
        return func::files_list($folder, $newfilter);
    }


    /**
     * Recursive directory listing
     * @string $folder
     * @string $filter
     * @return mixed
     */
    function files_list_recursive($folder, $filter =""){
        $res = array();
        $cutoff = strlen($folder);
        foreach($aFiles=func::files_list($folder, $filter) as $file){
            $res[] = $file;
            if(is_dir($folder.'/'.$file))
                foreach(func::files_list_recursive($folder.'/'.$file, $filter) as $file_r)
                    $res[] = substr($folder.$file.'/'.$file_r, $cutoff);
        }
        return $res;
    }

}