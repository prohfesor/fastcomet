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
        $this->getConnection()->createDataSet();

        $table = "testTbl";

        $result = $this->db->exec("DROP TABLE IF EXISTS {$table}");
        $this->assertNotFalse($result);

        $result = $this->db->exec("CREATE TABLE {$table} (id INT, name VARCHAR(20))");

        $this->assertNotFalse($result);
        $this->assertEquals(0, $this->getConnection()->getRowCount($table));
    }


    public function testInsert() {
        $table = "testTbl";

        $result = $this->db->insert("INSERT INTO {$table} VALUES (1, 'abcd')");

        $this->assertEquals(1, $result);
        $this->assertEquals(1, $this->getConnection()->getRowCount($table));
        $this->assertEquals(false, $this->db->getError());

        $result = $this->db->exec("INSERT INTO {$table} VALUES ('RRR', 'OOO', 'AAA', 'RRR')");
        $this->assertFalse($result);
        $this->assertNotFalse( $this->db->getError() );
    }


}
