<?php
/**
 * author: @prohfesor
 * package: frm.local
 * Date: 04.12.13
 * Time: 13:20
 */

require_once '../src/flyApplication/flyRegistry.php';

class flyRegistryTest extends PHPUnit_Framework_TestCase
{

    public function testSet()
    {
        $str = "some string";
        $key = "key";
        flyRegistry::set($key, $str);
        $this->assertEquals($str, flyRegistry::get($key));

        $str = "some string INT";
        $key = 1;
        flyRegistry::set($key, $str);
        $this->assertEquals($str, flyRegistry::get($key));

        $str = "some string INT";
        $key = 2;
        flyRegistry::set($key, $str);
        $this->assertEquals($str, flyRegistry::get($key));

        $str = "some string BOOL";
        $key = true;
        flyRegistry::set($key, $str);
        $this->assertEquals($str, flyRegistry::get($key));

        $str = "some string EMPTY";
        $key = "";
        flyRegistry::set($key, $str);
        $this->assertEquals($str, flyRegistry::get($key));

        $str = "some string NULL";
        $key = null;
        flyRegistry::set($key, $str);
        $this->assertEquals($str, flyRegistry::get($key));
    }


    public function testGet()
    {
        $str = "some string INT";
        $key = 2;
        $this->assertEquals($str, flyRegistry::get($key));

        $str = "some string";
        $key = "key";
        $this->assertEquals($str, flyRegistry::get($key));

        $str = "some other string";
        $key = "key";
        flyRegistry::set($key, $str);
        $this->assertEquals($str, flyRegistry::get($key));
    }

}