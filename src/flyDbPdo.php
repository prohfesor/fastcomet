<?php

/**
 * author: @prohfesor
 * package: frm.local
 * Date: 23.08.2015
 * Time: 15:24
 */
class flyDbPdo extends flyDb
{

    /**
     * Connection link
     */
    private $pdo;

    private $lastInsertId;
    private $lastAffectedRows;
    private $hasError =false;
    private $error;

    private $configThrowException =true;


    public function __construct($pdoDsn, $user ='', $pass ='', $options =array()) {
        $this->pdo = new PDO($pdoDsn, $user, $pass, $options);
    }

    /**
     * @inheritDoc
     */
    public function exec($query)
    {
        $this->errorReset();
        $result = $this->pdo->exec($query);
        if($result !== false){
            $this->lastAffectedRows = $result;
        } else {
            return $this->error();
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function insert($query)
    {
        $result = $this->exec($query);
        if($result !== false){
            $this->lastInsertId = $this->pdo->lastInsertId();
        } else {
            return $this->error();
        }
        return $this->lastInsertId;
    }

    /**
     * @inheritDoc
     */
    public function fetchAll($query)
    {
        $this->errorReset();
        $st = $this->pdo->prepare($query);
        if($st) {
            $result = $st->execute();
        }
        if($st === false || $result === false){
            return $this->error();
        }
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    /**
     * @inheritDoc
     */
    public function fetchOne($query)
    {
        $this->errorReset();
        $st = $this->pdo->prepare($query);
        if($st) {
            $result = $st->execute();
        }
        if($st === false || $result === false){
            return $this->error();
        }
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * @inheritDoc
     */
    public function fetchColumn($query, $columnName = false)
    {
        $result = $this->fetchAll($query);
        if($result === false){
            return $this->error();
        }
        $column = array();
        if(!$columnName && isset($result[0])){
            $keys = array_keys($result[0]);
            $columnName = (isset($keys[0])) ? $keys[0]  :  false;
        }
        if($columnName && isset($result[0]) && !isset($result[0][$columnName])){
            $columnName = false;
        }
        if(!$columnName) {
            return $this->error("Column not found!");
        }
        foreach($result as $row){
            $column[] = $row[$columnName];
        }
        return $column;
    }

    /**
     * @inheritDoc
     */
    public function fetchKeyValue($query, $keyColumn = false, $valueColumn = false)
    {
        $result = $this->fetchAll($query);
        if($result === false) {
            return $this->error();
        }
        $aKeyValue = array();
        if(!$keyColumn && isset($result[0])){
            $keys = array_keys($result[0]);
            $keyColumn =  $keys[0];
        }
        if(!$valueColumn && isset($result[0])){
            $keys = array_keys($result[0]);
            foreach($keys as $key){
                if(isset($previous) && $previous==$keyColumn) {
                    $valueColumn = $key;
                }
                $previous = $key;
            }
        }
        if(!$keyColumn || !$valueColumn) {
            return $this->error("Column not found!");
        }
        if($keyColumn && !isset($result[0][$keyColumn])){
            return $this->error("Column '{$keyColumn}' not found!");
        }
        if($valueColumn && !isset($result[0][$valueColumn])){
            return $this->error("Column '{$valueColumn}' not found!");
        }
        foreach($result as $row){
            $aKeyValue[ $row[$keyColumn] ] = $row[$valueColumn];
        }
        return $aKeyValue;
    }

    /**
     * Get error message from pdo and set error flag.
     * Throws exception unless $this->configThrowException is false
     * Always returns false.
     * @return bool
     * @throws Exception
     */
    private function error($message =null){
        $this->hasError = true;
        $this->error = (empty($message))  ?  $this->pdo->errorInfo()  :  $message;

        if($this->configThrowException){
            throw new Exception($this->getError());
        }

        return false;
    }

    private function errorReset() {
        $this->hasError = false;
        $this->error = null;
    }

    /**
     * Get error message.
     * Returns false if no error.
     * @return bool|string
     */
    public function getError() {
        if(!$this->hasError){
            return false;
        }
        $message = "";
        if(!empty($this->error[0])){
            $message .= "[{$this->error[0]}]";
        }
        if(!empty($this->error[1])){
            $message .= "[{$this->error[1]}]";
        }
        if(!empty($this->error[2])){
            $message .= " ". $this->error[2];
        }
        return $message;
    }


    /**
     * @return boolean
     */
    public function getConfigThrowException()
    {
        return $this->configThrowException;
    }

    /**
     * @param boolean $configThrowException
     */
    public function setConfigThrowException($configThrowException)
    {
        $this->configThrowException = $configThrowException;
    }

    /**
     * @return mixed
     */
    public function getLastAffectedRows()
    {
        return $this->lastAffectedRows;
    }

    /**
     * @return mixed
     */
    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }


}