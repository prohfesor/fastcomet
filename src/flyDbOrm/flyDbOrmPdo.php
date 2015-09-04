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
        return $this;
    }


    public function getTable($tableName) {
        return new flyDbOrmPdo($this->db, $tableName);
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
    public function getFirst()
    {
        $query = $this->db->escape("SELECT * FROM {$this->tableName}");
        return $this->db->fetchObject($query);
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        $query = $this->db->escape("SELECT * FROM {$this->tableName}");
        return $this->db->fetchObjects($query);
    }

    /**
     * @inheritDoc
     */
    public function findBy($criteria = array())
    {
        $paramString = "";
        foreach($criteria as $k=>$v){
            if(!empty($paramString)){
                $paramString .= " AND ";
            }
            $paramString .= "{$k} = :?";
        }
        $query = $this->db->escape("SELECT * FROM {$this->tableName} WHERE {$paramString}", $criteria);
        return $this->db->fetchObjects($query);
    }

    /**
     * @inheritDoc
     */
    public function findOneBy($criteria = array())
    {
        $paramString = "";
        foreach($criteria as $k=>$v){
            if(!empty($paramString)){
                $paramString .= " AND ";
            }
            $paramString .= "{$k} = :?";
        }
        $query = $this->db->escape("SELECT * FROM {$this->tableName} WHERE {$paramString}", $criteria);
        return $this->db->fetchObject($query);
    }

    /**
     * @inheritDoc
     */
    public function set($values = array(), $value =null)
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