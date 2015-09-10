<?php

/**
 * Class flyDb
 * Parent class for different db driver implementations
 */

 abstract class flyDb {

     /**
      * Execute database query.
      * Useful for queries returning no data.
      * Returns number of affected rows on success, or False on error
      * @param $query db query
      * @return mixed
      */
    abstract public function exec($query);

     /**
      * Alias for exec()
      * @param $query db query
      * @return mixed
      */
    public function sql($query) {
        return $this->exec($query);
    }

     /**
      * Execute database query,
      * Useful for "insert" queries.
      * Returns last autoincrement id, 0 if nothing inserted, or False on error
      * @param $query db query
      * @return mixed
      */
    abstract public function insert($query);

     /**
      * Returns array of all results
      * @param $query db query
      * @return mixed
      */
    abstract public function fetchAll($query);

     /**
      * Returns first row in results set
      * @param $query db query
      * @return mixed
      */
    abstract public function fetchOne($query);

     /**
      * Alias for fetchOne()
      * @param $query db query
      * @return mixed
      */
    public function fetchRow($query) {
        return $this->fetchOne($query);
    }

     /**
      * Returns first value of one cell from first row in result set.
      * If $columnName not specified - take first column.
      * @param $query
      * @param $columnName
      * @return mixed
      */
    abstract public function fetchValue($query, $columnName =false);

     /**
      * Returns array of one column of results set.
      * If $columnName not specified - take first column.
      * @param $query
      * @param $columnName
      * @return mixed
      */
    abstract public function fetchColumn($query, $columnName =false);

     /**
      * Returns key-value array with keys from $keyColumn column and values from $valueColumn.
      * If no $keyColumn - take first column
      * IF no $valueColumn - take next column after $keyColumn
      * @param $query
      * @param $keyColumn
      * @param $valueColumn
      * @return mixed
      */
    abstract public function fetchKeyValue($query, $keyColumn =false, $valueColumn =false);

     /**
      * Returns object from first row in result set.
      * If $className is not set - then uses stdClass
      * @param $query
      * @param bool|false $className
      * @return mixed
      */
    abstract public function fetchObject($query, $className =false, $arguments =array());

     /**
      * Returns array of objects.
      * If $className is not set - then uses stdClass
      * @param $query
      * @param bool|false $className
      * @return mixed
      */
    abstract public function fetchObjects($query, $className =false, $arguments =array());

     /**
      * Escapes var or query to be SQL injection safe.
      * Use question marks in query (":?") for placeholders.
      * Or use named placeholders (":title")
      * @param $query
      * @param array $params
      * @return string
      */
    public function escape($query, $params =array()) {
        if(empty($params)) {
            $query = $this->addslashes($query);
        } else {
            foreach($params as $key=>$param) {
                $param = "\"" . $this->addslashes($param) . "\"";
                if(is_numeric($key)) {
                    $query = substr_replace($query, $param, strpos($query, ":?"), 2);
                } else {
                    $query = preg_replace("/:{$key}/", $param, $query, 1, $count);
                    if(!$count){
                        $query = substr_replace($query, $param, strpos($query, ":?"), 2);
                    }
                }
            }
        }
        return $query;
    }

     /**
      * Escapes string, used internally in $this->escape()
      * @param $value
      * @return string
      */
     private function addslashes($value) {
         $string = stripslashes($value);
         $string = addslashes($string);
         return $string;
     }

}
