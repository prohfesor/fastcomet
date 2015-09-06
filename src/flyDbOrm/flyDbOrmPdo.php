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
    private $tableStructure;


    public function __construct($db, $tableName) {
        $this->db = $db;
        $this->tableName = $tableName;
        return $this;
    }


    /**
     * Get this class for specified table - useful for fetching rows.
     * @param $tableName
     * @return flyDbOrmPdo
     */
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


    public function getStructure() {
        $this->db->setConfigThrowException(false);
        if(!$this->tableStructure) {
            //general sql query
            $query = "SHOW COLUMNS FROM " . $this->db->escape($this->tableName);
            $result = $this->db->fetchAll($query);
            if ($result) {
                $this->tableStructure = array();
                foreach($result as $row) {
                    $this->tableStructure[ $row['Field'] ] = array(
                        'type' => $row['Type'] ,
                        'null' => ($row['Null']=="YES") ? true : false ,
                        'default' => ($row['Default']=="NULL") ? null : $row['Default']
                    );
                }
            }
        }
        if(!$this->tableStructure) {
            //sqlite
            $query = "PRAGMA table_info(".$this->db->escape($this->tableName).")";
            $result = $this->db->fetchAll($query);
            if($result) {
                $this->tableStructure = array();
                foreach($result as $row) {
                    $this->tableStructure[ $row['name'] ] = array(
                        'type' => $row['type'],
                        'null' => !(bool)$row['notnull'],
                        'default' => $row['dflt_value']
                    );
                }
            }
        }
        if(!$this->tableStructure) {
            //last chance - try only column titles
            $result = $this->db->fetchOne("SELECT * FROM ".$this->db->escape($this->tableName));
            if($result) {
                $this->tableStructure = array();
                foreach($result as $column=>$value) {
                    $this->tableStructure[ $column ] = array();
                }
            }
        }
        return $this->tableStructure;
    }

}