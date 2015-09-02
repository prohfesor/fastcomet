<?php

/**
 * author: @prohfesor
 * package: frm.local
 * Date: 02.09.2015
 * Time: 13:19
 */
class flyDbOrmPdo extends flyDbOrm
{

    /**
     * @var flyDbPdo
     */
    private $db;
    private $tableName;

    public function __construct($db, $tableName) {
        $this->db = $db;
        $this->tableName = $tableName;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $query = $this->db->escape("SELECT * FROM {$this->tableName} WHERE id=:?", array($id));
        return $this->db->fetchObject($query);
    }


    /**
     * @inheritDoc
     */
    public function getFirst($criteria = array())
    {
        // TODO: Implement getFirst() method.
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        // TODO: Implement getAll() method.
    }

    /**
     * @inheritDoc
     */
    public function findBy($criteria = array())
    {
        // TODO: Implement findBy() method.
    }

    /**
     * @inheritDoc
     */
    public function findOneBy($criteria = array())
    {
        // TODO: Implement findOneBy() method.
    }

    /**
     * @inheritDoc
     */
    public function set($values = array())
    {
        // TODO: Implement set() method.
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        // TODO: Implement save() method.
    }

}