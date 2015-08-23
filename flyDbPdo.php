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

    public function __construct($pdoDsn, $user, $pass, $options =array()) {
        $this->pdo = new PDO($pdoDsn, $user, $pass, $options);
    }

    /**
     * @inheritDoc
     */
    public function exec($query)
    {
        // TODO: Implement exec() method.
    }

    /**
     * @inheritDoc
     */
    public function insert($query)
    {
        // TODO: Implement insert() method.
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


}