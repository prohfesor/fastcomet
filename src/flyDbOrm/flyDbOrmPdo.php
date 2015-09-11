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
    private $className;
    private $idColumn;


    public function __construct($db, $tableName =null, $className =null) {
        $this->db = $db;
        $this->tableName = $tableName;
        $this->setClassName($className);
        return $this;
    }


    public function getClassName() {
        if(empty($this->className) || !class_exists($this->className)){
            return __CLASS__;
        }
        return $this->className;
    }

    public function setClassName($className) {
        if (!empty($className) && class_exists($className) && is_subclass_of($className, __CLASS__)) {
            $this->className = $className;
        } else {
            $this->className = null;
        }
        return $this->className;
    }


    public function create($params =array()){
        $object = new self($this->db, $this->tableName, $this->getClassName());
        if(!empty($params)){
            $object->set($params);
        }
        return $object;
    }


    /**
     * Get this class for specified table - useful for fetching rows.
     * @param $tableName
     * @return flyDbOrmPdo
     */
    public function getTable($tableName) {
        $className = __CLASS__;
        if(class_exists($tableName)) {
            $className = $this->setClassName($tableName);
        }
        return new flyDbOrmPdo($this->db, $tableName, $className);
    }


    public function getIdColumn() {
        return (!empty($this->idColumn)) ? $this->idColumn : "id";
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
        $this->db->setConfigThrowException(true);
        return $this->tableStructure;
    }


    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $query = $this->db->escape("SELECT * FROM {$this->tableName} WHERE id=:?", array($id));
        return $this->db->fetchObject($query, $this->getClassName(), array($this->db, $this->tableName));
    }


    /**
     * @inheritDoc
     */
    public function getFirst()
    {
        $query = $this->db->escape("SELECT * FROM {$this->tableName}");
        return $this->db->fetchObject($query, $this->getClassName(), array($this->db, $this->tableName));
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        $query = $this->db->escape("SELECT * FROM {$this->tableName}");
        return $this->db->fetchObjects($query, $this->getClassName(), array($this->db, $this->tableName));
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
        return $this->db->fetchObjects($query, $this->getClassName(), array($this->db, $this->tableName));
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
        return $this->db->fetchObject($query, $this->getClassName(), array($this->db, $this->tableName));
    }

    /**
     * @inheritDoc
     */
    public function set($values = array(), $value =null)
    {
        $structure = $this->getStructure();
        if(!is_array($values)){
            $values = array($values=>$value);
        }
        foreach($values as $key=>$value){
            if(isset($structure[$key])){
                $this->$key = $value;
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        $set = "";
        $insert = "";
        $params = array();
        $keys = array_keys($this->getStructure());
        $idColumn = $this->getIdColumn();
        foreach($keys as $key) {
            if(!isset($this->$key) || $key==$idColumn){
                continue;
            }
            if(!empty($set)) {
                $set .= ", ";
                $insert .= ", ";
            }
            $set .= "{$key}=:{$key}";
            $insert .= ":{$key}";
            $params[$key] = $this->$key;
        }
        if(!empty($this->$idColumn)){
            $query = "UPDATE {$this->tableName} SET $set WHERE {$idColumn}={$this->$idColumn}";
        } else {
            $insertKeys = array_keys($params);
            $insertKeys = implode(",", $insertKeys);
            $query = "INSERT INTO {$this->tableName} ({$insertKeys}) VALUES ({$insert})";
        }
        $query = $this->db->escape($query, $params);
        return (bool)$this->db->exec( $query );
    }

}