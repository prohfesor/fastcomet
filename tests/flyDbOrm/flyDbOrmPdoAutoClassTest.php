<?php

require_once "../src/flyDbOrm/flyDbOrm.php";
require_once "../src/flyDbOrm/flyDbOrmPdo.php";

/**
 * author: @prohfesor
 * package: frm.local
 * Date: 02.09.2015
 * Time: 16:11
 */
class flyDbOrmPdoAutoClassTest extends PHPUnit_Extensions_Database_TestCase
{

    /**
     * @var flyDbPdo
     */
    private $db = null;

    /**
     * @var flyDbOrmPdo
     */
    private $orm = null;

    private $dbFilename = null;

    private $dbTableName = "testOrm";

    private $fixture = array(
        array( 'id'=>1 , 'name'=>'John Doe', 'phone'=>'223322', 'address'=>'5th Avenue 123/4 5 NY CA 66555'),
        array( 'id'=>2 , 'name'=>'Leonardo Di Caprio', 'phone'=>'2774485', 'address'=>"9255 Sunset Blvd.\n Suite 615\nWest Hollywood, CA 90069"),
        array( 'id'=>3 , 'name'=>'Jason Statham', 'phone'=>'3238229300', 'address'=>"8409 Santa Monica Blvd\nLos Angeles, CA 90069"),
    );


    public function getConnection()
    {
        $pdoDsn = 'sqlite:'.$this->getDbFilename();
        $pdo = new PDO($pdoDsn);
        $this->db = new flyDbPdo($pdoDsn);
        $this->orm = new flyDbOrmPdo($this->db, $this->dbTableName);
        return $this->createDefaultDBConnection($pdo);
    }


    public function getDataset() {
        $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->dbTableName} (id INT PRIMARY KEY, name VARCHAR(50), phone DECIMAL(10), address TEXT)");
        $ds = $this->createArrayDataSet(array(
            $this->dbTableName => $this->fixture
        ));
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


    public function testGetTable() {
        $id = rand(1,sizeof($this->fixture));
        $orm = $this->orm->getTable($this->dbTableName);
        $result = $this->orm->get($id);
        $this->assertEquals($result, $orm->get($id));
    }


    public function testGet() {
        $id = rand(1,sizeof($this->fixture));
        $result = $this->orm->get($id);
        $this->assertNotFalse($result);
        $this->assertEquals(get_class($this->orm), $this->dbTableName);
        $this->assertEquals($id, $result->id);
    }


}


class testOrm extends flyDbOrmPdo {
    public $id, $name, $phone, $address;
}