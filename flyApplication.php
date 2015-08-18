<?php

require_once("flyInit.php");
require_once("flyRouting.php");
require_once("flyView.php");

class flyApplication
{
    var $config =array();
    var $controller;
    var $action;
    var $view;

    function run(){
        //load configs
        $this->config = array_merge( $this->config, json_decode(file_get_contents("config/routing.json"), 1) );
        $this->config = array_merge( $this->config, json_decode(file_get_contents("config/view.json"), 1) );
        //init view
        $classname = (!empty($this->config['view']['class'])) ? $this->config['view']['class'] : "flyView";
        if(class_exists($classname)){
            $this->view = new $classname;
            $this->view->init($this->config['view']['parameters']);
        } else {
            die("Cannot load view class $classname ;(");
        }
        //do routing
        $requested = (!empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : $_SERVER['PATH_INFO'];
        $routing = new flyRouting( $this->config['routing'] );
        $aRoute = $routing->match( $requested );
        if($aRoute){
            $controller = (!empty($aRoute[0])) ? $aRoute[0] : "index";
            $action = (!empty($aRoute[1])) ? $aRoute[1] : "index";
            $action_params = array_slice($aRoute , 2);
        } else {
            die("404 Not found");
        }
        //load controller
        $classname = "{$controller}Controller";
        $classfile = "controller/{$classname}.php";
        if(file_exists($classfile)){
            require_once($classfile);
            $this->controller = new $classname;
        } else {
            die("Unable to load controller $classname from $classfile ;(");
        }
        $this->controller->view = $this->view;
        //launch action
        if(method_exists($this->controller, $action)){
            //$this->controller->$action( $action_params[0] );
            call_user_func_array( array($this->controller,$action) , $action_params );
        } else {
            die("Not found method $action in $classname ;(");
        }
    }
}
