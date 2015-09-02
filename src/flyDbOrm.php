<?php

/**
 * author: @prohfesor
 * package: frm.local
 * Date: 01.09.2015
 * Time: 23:48
 */
abstract class flyDbOrm
{

    abstract public function get();
    abstract public function getFirst();
    abstract public function getAll();
    abstract public function findBy();
    abstract public function findOneBy();
    abstract public function set();
    abstract public function save();

}