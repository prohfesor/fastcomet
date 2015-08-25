<?php

/**
 * author: @prohfesor
 * package: frm.local
 * Date: 20.08.2015
 * Time: 16:52
 */

require_once "../src/flyDb.php";

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

    public function flyDbMethodsProvider() {
        return array(
            array("exec", "sql", "insert", "fetchAll", "fetchOne", "fetchRow", "fetchColumn", "fetchKeyValue")
        );
    }

}
