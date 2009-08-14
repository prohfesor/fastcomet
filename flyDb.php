<?php
/*
 * Created on 16.07.2008
 *
 * Author: Pavel Dvoynos (prohfesor@gmail.com)
 * FlyToolkit framework
 *
 *
 */

 require_once('flyDbMysql.php');
 require_once('flySqlUtil.php');
 require_once('flyDebug.php');

 class flyDb {

    /**
     * @access private
     */
    function flyDb($secret = null)
    {
		if ($secret != "singletonpass") {
            return new flyError("Unable to call private constructor of Singleton class. Use &getInstance() call instead!");
        }
    }


	/**
     * Get instance of Singleton class.
     *
     * Returns link to DbConnection.
     * Supports multiple connections, but only one can be used at a time.
     * Therefore, connection is keeping active.
     * If no DbDriver specified - MySql used by default.
     * If you are using single DB connection at runtime - empty $connectionName
     * allowed, which is equal to "" connection.
     * Class for db driver must be named as "flyDb{Dbdriver}.php".
     *
     * @access public
     * @return flyDb
     * @static
     */
    function & getInstance($connectionName =null, $dbDriver ="Mysql")
    {
        if ($connectionName === null) {
            $connectionName = "";
        }

        $dbDriver = ucfirst(strtolower($dbDriver));

        static $aInstances =array();

        if (!isset($aInstances[$connectionName])) {
            $className = "flyDb$dbDriver";
            flyDebug::assert(class_exists($className));

        	$aInstances[$connectionName] = new $className("fly".$dbDriver."pass");
        }

        return $aInstances[$connectionName];
    }


}


?>
