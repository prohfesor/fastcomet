<?php

require_once '../src/flyHelperString.php';

class flyHelperStringTest extends PHPUnit_Framework_TestCase
{

    public function testGenerateString() {
        $str = flyHelperString::generate_string();
        $this->assertNotEmpty($str);

        $length = rand(1, 100);
        $str = flyHelperString::generate_string($length);
        $this->assertEquals( strlen($str), $length);

        $symbols = array("a", "B", "0");
        $str = flyHelperString::generate_string($length, $symbols);
        $this->assertEquals( strlen($str), $length);
        $this->assertRegExp("/^[aB0]+$/", $str);
    }
}