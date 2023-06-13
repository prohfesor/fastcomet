<?php
/**
 * author: @prohfesor
 * package: frm.local
 * Date: 17.04.13
 * Time: 22:20
 */

class flyRouting {

    var $aRoutes = array();
    var $matchedRoute = false;
    var $matchedParams = array();

    function flyRouting($aRoutes =array())
    {
        $this->setRoutes($aRoutes);
    }


    function setRoutes($aRoutes)
    {
        $this->aRoutes = $aRoutes;
    }


    /**
     * Search for matching route for $path
     * Returns array of params or false if nothing matched
     * @param $path
     * @return array|bool
     */
    function match($path)
    {
	$get_params = "";
        $is_get = strpos($path, "?");
        if($is_get){
            $get_params = substr($path, $is_get+1);
            $path = substr($path, 0, $is_get);
        }
        $path = ("/"!=substr($path,0,1)) ? "/".$path : $path ;
        foreach($this->aRoutes as $route=>$params){
            $route = ("/"!=substr($route,0,1)) ? "/".$route : $route ;
            $regexp = "@^{$route}$@i";
            if( preg_match($regexp, $path) ){
                foreach($params as $k=>$v){
                    $this->matchedParams[$k] = preg_replace($regexp, $v, $path);
                }
                $this->matchedRoute = $route;
                return $this->matchedParams;
            }
        }
        return false;
    }

}
