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
            $this->hasError = true;
            $this->error = $this->pdo->errorInfo();
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function insert($query)
    {
        $this->lastInsertId = $this->pdo->exec($query);
        return $this->lastInsertId;
    }

    /**
     * @inheritDoc
     */
    public function fetchAll($query)
    {
        // TODO: Implement fetchAll() method.
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

}