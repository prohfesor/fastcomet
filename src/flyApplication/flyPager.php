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
		
		
		function getPagesTotal(){
			return $this->pages_total;
		}
		
		
		/**
		 * Function tries to execute modified SQL query,
		 * if specified class (object) allows this.
		 * Also $object may be an array - then array is truncated.
		 * @param $object
		 */
		 function getObjects( &$object ) {
		 	$page 	  =& $this->page;
			$per_page =& $this->per_page;
			if(is_array($object)) {
				//truncate array
				$this->pages_total = ceil (sizeof($object) / $this->per_page);
				$aElems = $object;
				array_splice($aElems, 0, ($page-1)*$per_page);	//beginning
				array_splice($aElems, $per_page);				//trailing
				return $aElems;
			} elseif (is_object($object)) {
				//fetch limited
				if(!method_exists($object, 'getObjects')) {
					return array();
				} else {
					@$object->limits( ($page-1)*$per_page , $per_page);
					$aElems = $object->getObjects();
					if(is_int((int)$rowstotal = $object->rows_total))
						$this->pages_total = ceil($rowstotal / $this->per_page);
					@$object->removeLimits();
					return $aElems;
				}
			} else {

				return array();
			}
		 }


	}

?>