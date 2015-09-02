<?php

require_once "../src/flyDbOrm/flyDbOrm.php";
require_once "../src/flyDbOrm/flyDbOrmPdo.php";

/**
 * author: @prohfesor
 * package: frm.local
 * Date: 02.09.2015
 * Time: 16:11
 */
class flyDbOrmPdoTest extends PHPUnit_Extensions_Database_TestCase
{

    /**
     * @var flyDbPdo
     */
    private $db = null;

    private $orm = null;

    private $dbFilename = null;

    private $dbTableName = "testOrm";


    public function getConnection()
    {
        $pdoDsn = 'sqlite:'.$this->getDbFilename();
        $pdo = new PDO($pdoDsn);
        $this->db = new flyDbPdo($pdoDsn);
        $this->orm = new flyDbOrmPdo($this->db, $this->dbTableName);
        return $this->createDefaultDBConnection($pdo);
    }


    public function getDataset() {
        $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->dbTableName} (id INT, name VARCHAR(50), phone DECIMAL(10), address TEXT)");
        $ds = $this->createArrayDataSet(array());
        return $ds;
    }

    public function getDbFilename() {
        if($this->dbFilename === null) {
            $tmpdir = sys_get_temp_dir();
            $this->dbFilename = $tmpdir . DIRECTORY_SEPARATOR . "flyDbOrmPdoTest" . ".sq3";
        }
        return $this->dbFilename;
    }


    public function __destruct() {
        $this->db = null;

        if(isset($this->dbFilename) && is_file($this->dbFilename)){
            @chmod($this->dbFilename, 0777);
            @unlink($this->dbFilename);
        }
    }


    public function testGet() {
        $this->assertNotFalse(false);
    }


    public function testGetFirst() {

    }

    public function testGetAll()
    {

    }

    public function testFindBy()
    {

    }

    public function testFindOneBy()
    {

    }

    public function testSet()
    {

    }

    public function testSave(){

    }


}


class testOrm {
    public $id, $name, $phone, $address;
}