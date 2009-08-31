<?php

	class flyPager {


		var $page = 1;
		var $pages_total = 1;
		var $per_page = 10;


		/*
		 * Class constructor
		 * Should not be called manually
		 */
		function flyPager($singletonPass ="") {
			if($singletonPass !="PagerPass") {
				return new flyError("Trying to call constructor of singleton class. Use &getInstance() method!");
			}
		}


		/**
		 * Multiple pagers can be used.
		 * Only one pager can be active.
		 * @param Pager identifier - $instance
		 */
		function &getInstance($instance =null) {
			if($instance === null) {
				$instance = "";
			}

			static $aPagers =array();

			if(!isset($aPagers[$instance])) {
				$aPagers[$instance] =& new flyPager("PagerPass");
			}
			return $aPagers[$instance];
		}
		
		
		function setPage($page){
			$this->page = $page;
		}
		
		
		function setPerPage($per_page){
			$this->per_page = $per_page;
		}


		/**
		 * Function tries to execute modified SQL query,
		 * if specified class (object) allows this.
		 * Also $object may be an array - then array is truncated.
		 * @param $object
		 */
		 function getObjects( &$object ) {
			if(is_array($object)) {
				//truncate array
				$this->pages_total = ceil (sizeof($object) / $this->per_page);
				$aElems = $object;
				$page =&	$this->page;
				$per_page=&	$this->per_page;
				array_splice($aElems, 0, ($page-1)*$per_page);	//beginning
				array_splice($aElems, $per_page);				//trailing
				return $aElems;
			} elseif (is_object($object)) {
				//fetch limited
				if(!method_exists($object, 'get_objects')) {
					return array();
				} else {
					// ... something TODO: get objects
				}
			} else {

				return array();
			}
		 }


	}

?>