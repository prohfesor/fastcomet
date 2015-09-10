<?php

/**
 * author: @prohfesor
 * package: frm.local
 * Date: 20.08.2015
 * Time: 16:52
 */

require_once "../src/flyDb/flyDb.php";

class flyDbTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider flyDbMethodsProvider
     */
    public function testAllMethodsExists($method) {
        $this->assertTrue( method_exists("flyDb", $method) );
    }

    public function testSql() {
        $stub = $this->getMockForAbstractClass("flyDb");
        $rand = rand();
        $stub->expects($this->any())
            ->method('exec')
            ->will($this->returnValue( $rand ));

        $this->assertEquals( $stub->sql("query"), $rand);
    }

    public function testFetchRow() {
        $stub = $this->getMockForAbstractClass("flyDb");
        $arr = array(rand(), rand(), rand());
        $stub->expects($this->any())
            ->method('fetchOne')
            ->will($this->returnValue($arr));

        $this->assertEquals( $stub->fetchRow("another query"), $arr);
    }


    public function testEscape() {
        $stub = $this->getMockForAbstractClass("flyDb");

        $var = "ABCDEFGH:/MNRPQR/";
        $this->assertEquals("ABCDEFGH:/MNRPQR/", $stub->escape($var));

        $var = "Hello 'world'!!! \"BIG WORLD\"";
        $this->assertEquals('Hello \\\'world\\\'!!! \\"BIG WORLD\\"', $stub->escape($var));

        $queryWithParams = "SELECT * FROM sometable WHERE name=:? AND title LIKE :?";
        $params = array("John", "Mc'Crea%");
        $queryPrepared = 'SELECT * FROM sometable WHERE name="John" AND title LIKE "Mc\\\'Crea%"';
        $this->assertEquals($queryPrepared, $stub->escape($queryWithParams, $params));

        $queryWithParams = "SELECT * FROM sometable WHERE name=:name AND title LIKE :title";
        $params = array('name'=>"John", 'title'=>"Mc'Crea%");
        $this->assertEquals($queryPrepared, $stub->escape($queryWithParams, $params));

        $query = "SELECT :?, :?, :? FROM sometable";
        $params = array("A", "B");
        $this->assertEquals("SELECT \"A\", \"B\", \"\" FROM sometable", $stub->escape($query, $params));

        $query = "SELECT :name, :phone, :car FROM sometable";
        $params = array('name'=>"Andrei", 'car'=>"Bentley");
        $this->assertEquals("SELECT \"Andrei\", \"\", \"Bentley\" FROM sometable", $stub->escape($query, $params));
    }


    public function flyDbMethodsProvider() {
        return array(
            array("exec", "sql", "insert", "fetchAll", "fetchOne", "fetchRow", "fetchValue", "fetchColumn", "fetchKeyValue", "fetchObject", "fetchObjects", "escape")
        );
    }

}
