<?php

require_once "../src/flyDb.php";
require_once "../src/flyDbPdo.php";

/**
 * author: @prohfesor
 * package: frm.local
 * Date: 23.08.2015
 * Time: 15:31
 */
class flyDbPdoTest extends PHPUnit_Extensions_Database_TestCase
{

    /**
     * @var flyDbPdo
     */
    private $db = null;

    private $dbFilename = null;


    public function getConnection()
    {
        $pdoDsn = 'sqlite:'.$this->getDbFilename();
        $pdo = new PDO($pdoDsn);
        $this->db = new flyDbPdo($pdoDsn);
        return $this->createDefaultDBConnection($pdo);
    }


    public function getDataset() {
        $ds = $this->createArrayDataSet(array());
        return $ds;
    }


    public function getDbFilename() {
        if($this->dbFilename === null) {
            $tmpdir = sys_get_temp_dir();
            $this->dbFilename = $tmpdir . DIRECTORY_SEPARATOR . "flyDbPdoTest" . ".sq3";
        }
        return $this->dbFilename;
    }


    public function __destruct() {
        $this->db = null;

        if(is_file($this->dbfile)){
            @chmod($this->dbfile, 0777);
            @unlink($this->dbfile);
        }
    }


    public function testExec()
    {
        $table = "testExec";

        $result = $this->db->exec("DROP TABLE IF EXISTS {$table}");
        $this->assertNotFalse($result);

        $result = $this->db->exec("CREATE TABLE {$table} (id INT, name VARCHAR(20))");
        $this->assertNotFalse($result);
        $this->assertEquals(0, $this->getConnection()->getRowCount($table));
    }


    public function testInsert() {
        $table = "testExec";

        //number of inserted row ok
        $result = $this->db->insert("INSERT INTO {$table} VALUES (1, 'abcd')");

        $this->assertEquals(1, $result);
        $this->assertEquals(1, $this->getConnection()->getRowCount($table));
        $this->assertEquals(false, $this->db->getError());

        $result = $this->db->insert("INSERT INTO {$table} VALUES (1, 'efgh')");

        $this->assertEquals(2, $result);
        $this->assertEquals(2, $this->getConnection()->getRowCount($table));
        $this->assertEquals(false, $this->db->getError());
    }


    public function testFetchAll() {
        $table = "testFetchAll";

        $result = $this->db->exec("DROP TABLE IF EXISTS {$table}");
        $this->assertNotFalse($result);

        $result = $this->db->exec("CREATE TABLE {$table} (id INTEGER PRIMARY KEY, title VARCHAR(30), number DECIMAL(10))");
        $this->assertNotFalse($result);

        //each insert returns autoincrement
        $rows = rand(5,20);
        for($i=1;$i<=$rows;$i++){
            $title = flyFunc::generate_string();
            $number = flyFunc::generate_string( rand(5,10), "0123456789");
            $result = $this->db->insert("INSERT INTO {$table} (title, number) VALUES ('{$title}', '{$number}')");
            $this->assertGreaterThan(0, $result);
        }

        //number of rows in result
        $result = $this->db->fetchAll("SELECT * FROM {$table}");
        $this->assertNotFalse($result);
        $this->assertEquals($rows, sizeof($result));
    }


    public function testFetchOne() {
        $table = "testFetchAll";

        $result = $this->db->fetchOne("SELECT * FROM {$table}");
        $this->assertNotFalse($result);
        $this->assertEquals( 3, sizeof($result));
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('number', $result);
        $this->assertNotEmpty($result['title']);
        $this->assertNotEmpty($result['number']);
    }


    public function testFetchColumn() {
        $table = "testFetchAll";

        $result = $this->db->fetchColumn("SELECT title FROM {$table} LIMIT 5");
        $this->assertNotFalse($result);
        $this->assertEquals(5, sizeof($result));
        $this->assertEquals("array", gettype($result));
        $this->assertEquals("string", gettype($result[1]));
    }


    public function testFetchKeyValue() {
        $table = "testFetchAll";

        $array = $this->db->fetchAll("SELECT id, title FROM {$table} LIMIT 5");
        $hash = $this->db->fetchKeyValue("SELECT id, title FROM {$table} LIMIT 5");
        $this->assertNotFalse($hash);
        $this->assertEquals(5, sizeof($hash));
        $this->assertEquals("array", gettype($hash));
        $this->assertEquals($array[0]['title'], $hash[$array[0]['id']]);
        $this->assertEquals($array[3]['title'], $hash[$array[3]['id']]);
    }


    public function testErrors() {
        $table = "testFetchAll";
        $this->db->setConfigThrowException(false);

        //wrong table name
        $result = $this->db->exec("DROP TABLE {$table}Wrong (id INT, wrong_name VARCHAR(20))");
        $this->assertFalse($result);

        //wrong column count
        $result = $this->db->insert("INSERT INTO {$table} VALUES ('RRR', 'OOO', 'AAA', 'RRR')");
        $this->assertFalse($result);
        $this->assertNotFalse( $this->db->getError() );

        //wrong column
        $result = $this->db->fetchAll("SELECT name, phone FROM {$table}");
        $this->assertFalse($result);

        //wrong column
        $result = $this->db->fetchOne("SELECT name, phone FROM {$table}");
        $this->assertFalse($result);

        //wrong column
        $result = $this->db->fetchColumn("SELECT name, phone FROM {$table}");
        $this->assertFalse($result);

        //wrong column
        $result = $this->db->fetchColumn("SELECT * FROM {$table}", "name");
        $this->assertFalse($result);

        //throw exception
        $this->db->setConfigThrowException(true);
        $this->setExpectedException("Exception");
        $this->db->fetchOne("SELECT name, phone FROM {$table}");
        $this->db->insert("INSERT INTO {$table} VALUES ('RRR', 'OOO', 'AAA', 'RRR')");
    }


}
