<?php

require_once '../src/flyFunc.php';

class funcTest extends PHPUnit_Framework_TestCase
{

    public function testGenerateString() {
        $str = flyFunc::generate_string();
        $this->assertNotEmpty($str);

        $length = rand(1, 100);
        $str = flyFunc::generate_string($length);
        $this->assertEquals( strlen($str), $length);

        $symbols = array("a", "B", "0");
        $str = flyFunc::generate_string($length, $symbols);
        $this->assertEquals( strlen($str), $length);
        $this->assertRegExp("/^[aB0]+$/", $str);
    }
}