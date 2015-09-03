<?php

/**
 * author: @prohfesor
 * package: frm.local
 * Date: 01.09.2015
 * Time: 23:48
 */
abstract class flyDbOrm
{

    /**
     * Return object by Id
     * @return mixed
     */
    abstract public function get($id);

    /**
     * Return first object in result set
     * @param $criteria
     * @return mixed
     */
    abstract public function getFirst();

    /**
     * Return array of all results set
     * @return mixed
     */
    abstract public function getAll();

    /**
     * Return array of all results by search criteria
     * @param array $criteria
     * @return mixed
     */
    abstract public function findBy($criteria =array());

    /**
     * Return first row from result set.
     * @param array $criteria
     * @return mixed
     */
    abstract public function findOneBy($criteria =array());

    /**
     * Set new values
     * @param array $values
     * @return mixed
     */
    abstract public function set($values =array());

    /**
     * Save object
     * @return mixed
     */
    abstract public function save();

}