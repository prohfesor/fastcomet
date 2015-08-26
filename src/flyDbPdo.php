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
        $result = $this->pdo->exec($query);
        if($result !== false){
            $this->lastAffectedRows = $result;
        } else {
            $this->error();
            return false;
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function insert($query)
    {
        $result = $this->pdo->exec($query);
        if($result !== false){
            $this->lastInsertId = $this->pdo->lastInsertId();
        } else {
            $this->error();
            return false;
        }
        return $this->lastInsertId;
    }

    /**
     * @inheritDoc
     */
    public function fetchAll($query)
    {
        $st = $this->pdo->prepare($query);
        $result = $st->execute();
        if($result === false){
            $this->error();
            return false;
        }
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    /**
     * @inheritDoc
     */
    public function fetchOne($query)
    {
        // TODO: Implement fetchOne() method.
    }

    /**
     * @inheritDoc
     */
    public function fetchColumn($query, $columnName = false)
    {
        // TODO: Implement fetchColumn() method.
    }

    /**
     * @inheritDoc
     */
    public function fetchKeyValue($query, $keyColumn = false, $valueColumn = false)
    {
        // TODO: Implement fetchKeyValue() method.
    }

    private function error(){
        $this->hasError = true;
        $this->error = $this->pdo->errorInfo();

        if($this->configThrowException){
            throw new Exception($this->getError());
        }
        return true;
    }

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