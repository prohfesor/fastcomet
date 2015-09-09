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

        $id = rand(1,sizeof($this->fixture));
        $className = "testOrmNo";
        $orm->setClassName($className);
        $result = $orm->get($id);
        $this->assertEquals($className, get_class($result));
        $this->assertEquals($result, $orm->get($id));
    }


    public function testGet() {
        $id = rand(1,sizeof($this->fixture));
        $result = $this->orm->get($id);
        $this->assertNotFalse($result);
        $this->assertEquals(get_class($this->orm), get_class($result));
        $this->assertEquals($id, $result->id);
    }


    public function testGetFirst() {
        $result = $this->orm->getFirst();
        $this->assertNotFalse($result);
        $this->assertEquals(get_class($this->orm), get_class($result));
        $this->assertObjectHasAttribute("id", $result);
        $this->assertObjectHasAttribute("name", $result);
        $this->assertObjectHasAttribute("address", $result);
    }


    public function testGetAll()
    {
        $id = rand(0,sizeof($this->fixture)-1);
        $result = $this->orm->getAll();
        $this->assertNotFalse($result);
        $this->assertInternalType("array", $result);
        $this->assertObjectHasAttribute("id", $result[$id]);
        $this->assertObjectHasAttribute("name", $result[$id]);
    }


    public function testFindBy()
    {
        $testRow = $this->fixture[ rand(0,2) ];
        $result = $this->orm->findBy(array('name'=>$testRow['name']));
        $this->assertInternalType("array", $result);
        $result = $result[0];
        $this->assertEquals($testRow['id'], $result->id);

        $result = $this->orm->findBy(array('name'=>$testRow['name'], 'phone'=>$testRow['phone']));
        $result = $result[0];
        $this->assertEquals($testRow['id'], $result->id);
    }


    public function testFindOneBy()
    {
        $testRow = $this->fixture[ rand(0,2) ];
        $result = $this->orm->findOneBy(array('phone'=>$testRow['phone'], 'address'=>$testRow['address']));
        $this->assertEquals(get_class($this->orm), get_class($result));
        $this->assertEquals($testRow['id'], $result->id);
    }


    public function testSet()
    {
        $row = rand(1, sizeof($this->fixture));
        $object = $this->orm->getTable($this->dbTableName)->get($row);
        $names = array("Andrei", "Nikolai", "Daniel");
        $newName = $names[ array_rand($names) ];
        $object->set('name', $newName);
        $this->assertEquals($newName, $object->name);

        $row = rand(1, sizeof($this->fixture));
        $newPhone = "232".rand("10000","70000");
        $object = $this->orm->getTable($this->dbTableName)->get($row);
        $object->set(array('name'=>$newName, 'phone'=>$newPhone));
        $this->assertEquals($newName, $object->name);
        $this->assertEquals($newPhone, $object->phone);
    }


    public function testSave()
    {
        $row = rand(1, sizeof($this->fixture));
        $newPhone = "232".rand("10000","70000");
        $object = $this->orm->getTable($this->dbTableName)->get($row);
        $object->set(array('phone'=>$newPhone));
        $object->save();

        $actor = $this->db->fetchOne("SELECT * FROM {$this->dbTableName} WHERE id={$row}");
        $this->assertNotFalse($actor);
        $this->assertEquals($object->phone, $actor['phone']);

        $new = new flyDbOrmPdo($this->db, $this->dbTableName);
        $new->set(array(
            "name" => "Richard Branson",
            "address" => "50 Brook Green\nHammersmith\nLondon, W6 7RR\nUK"
        ))->save();
        $this->assertTableRowCount($this->dbTableName, sizeof($this->fixture)+1);
    }


    public function testGetStructure() {
        $structure = $this->orm->getTable($this->dbTableName)->getStructure();
        $this->assertEquals(array_keys($this->fixture[0]), array_keys($structure));
    }


}


class testOrmNo extends flyDbOrmPdo {
    public $id, $name, $phone, $address;
}