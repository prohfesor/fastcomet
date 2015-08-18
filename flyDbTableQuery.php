<?php 

class flyDbTableQuery extends flyDb {
	
	
	var $select;
	var $from;
	var $aDefFilters =array();
	var $aFilters    =array();
	var $aLimits     =array();
	var $aSortOrder  =array();
	var $count_rows  =true;
	var $group_having_count =false;
	var $rows_total;
	
	
	function flyDbTableQuery($from, $select ='', $aDefFilters ='', $aSortOrder ='', $connectionName =null){
		$this->from = $from;
		$this->select = $select;
		$this->aDefFilters = (!empty($aDefFilters)) ? (array)$aDefFilters : array();
		$this->aSortOrder =  (!empty($aSortOrder))  ? (array)$aSortOrder  : array();
		$this->db = flyDb::getInstance($connectionName);
	}
	
	
	function addFilters($aFilters){
		$this->aFilters = array_merge($this->aFilters, (array)$aFilters);
	}

	
	function defFilters($aFilters =null){
		$old = $this->aDefFilters;
		if(null !== $aFilters){
			$this->aDefFilters = $aFilters;			
		}
		return $old;
	}
	
	
	function resetFilters() {
		$this->aFilters = array();
	}
	
	
	function limits($from, $count =null){
		$old = $this->aLimits;
		$this->aLimits = array($from, $count);
		return $old;
	}
	
	
	function removeLimits(){
		$this->aLimits =array();
	}
	
	
	function setSortOrder($aSortOrder){
		$old = $this->aSortOrder;
		$this->aSortOrder = (array)$aSortOrder;
		return $old;
	}
	
	
	/**
	 * @access private
	 * @return string
	 */
	function _createQuery(){
		//from
		$from = $this->from;
		if(empty($from))
			return false;
		// select
		$select = $this->select;
		if(empty($select))
			$select = '*';
		// where
		$where = '';
		if(!empty($this->aDefFilters) || !empty($this->aFilters)) {
			$where = "WHERE ";
		}	
		if(!empty($this->aDefFilters)) {
			$where .= "(" . implode(") AND(", $this->aDefFilters) . ")";	
		}
		if(!empty($this->aFilters)) {
			if(!empty($this->aDefFilters)) 
				$where .= " AND ";
			$where .= "(" . implode( ") AND(" , $this->aFilters ) . ")";
		}
		//limits
		if(!empty($this->aLimits[0]) || !empty($this->aLimits[1])) {
			$limits  = "LIMIT ";
			$limits .= (!empty($this->aLimits[0])) ? (int)$this->aLimits[0] : "0";
		}		
		if(!empty($this->aLimits[1])) {
			$limits .= ",".(int)$this->aLimits[1];	
		}
		//sort order
		$order = '';
		if(!empty($this->aSortOrder)){
			$aSortOrder = array();
			foreach($this->aSortOrder as $k=>$v){
				if ($v{0}=='-') {
					$v = "$v DESC";
				} else {
					$v = "$v ASC";
				}
				if ($v{0}=='-' || $v{0}=='+') {
					$v = substr($v, 1);
				}
				$aSortOrder[$k] = $v;
			}
			$order = "ORDER BY ". implode(", ", $aSortOrder);
		}
		//query
		$query = "SELECT $select FROM $from $where $order $limits";
		
		return $query;
	}
	
	
	/**
	 * @access private 
	 * @return int
	 */
	function _countableQuery(){
		//from
		$from = $this->from;
		if(empty($from))
			return false;
		// select
		$select = $this->select;
		if(empty($select))
			$select = '*';	
		// where
		$where = '';
		if(!empty($this->aDefFilters) || !empty($this->aFilters)) {
			$where = "WHERE ";
		}	
		if(!empty($this->aDefFilters)) {
			$where .= "(" . implode(") AND(", $this->aDefFilters) . ")";	
		}
		if(!empty($this->aFilters)) {
			if(!empty($this->aDefFilters)) 
				$where .= " AND ";
			$where .= "(" . implode( ") AND(" , $this->aFilters ) . ")";
		}
		//query
		$query = "SELECT count(*) AS flydbtablequery_numrows FROM $from $where";
		//but we cannot use count() when grouping functions present
		$lquery = strtolower($query);
		if(strpos($lquery, "having") || strpos($lquery, "group by")){
			$alternateQuery = "SELECT count(*) AS flydbtablequery_numrows FROM (
								 SELECT $select FROM $from $where
								) AS flydbtablequery_full";
			return (!$this->group_having_count) ? false : $alternateQuery;
		} 
		return $query;
	}
	
	
	/**
	 * Retrieve total rows count
	 * (limits ignored)
	 * Useful for using with pagers
	 * @return int
	 */
	function rowsCount(){
		if(!$query= $this->_countableQuery()){
			new flyError("Unable to use countable query with grouping functions! Zero rows returned.");
			return 0;
		}
		return $this->db->fetch_value($query, 'flydbtablequery_numrows');
	}
	
	
	function getObjects() {
		$query = $this->_createQuery();
		$aResult = $this->db->fetch_all_rows($query);
		if($this->count_rows)
		 $this->rows_total = $this->rowsCount();
		return $aResult;
	}
	
	
	function getFirst($return_array =0) {
		$limits = $this->limits(1);
		$query = $this->_createQuery();
		$this->limits($limits);
		return $this->db->fetch_row($query, $return_array);
	}
	
	
}

?>