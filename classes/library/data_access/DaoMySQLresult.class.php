<?php
require_once('DaoResult.class.php');

class DaoMySQLresultException extends DaoResultException{}

class DaoMySQLresult implements daoResult{
	/**
	 * @param mixed a database result resource
	 * @access private
	 */
	private $_result = false;
	
	/**
	 * @param int rows affected/fetched by query 
	 */
	protected $_row_count = 0;
	
	/**
	 * constructor
	 * 	@param mixed $res a database resoult resource
	 * 	@param string $type whether query was a select or update(/delete)
	 * 	@param string $sql  the sql statment
	 * @access public
	 */
	public function __construct($res,$type='select',$sql=''){
		$this->_result = $res;
		$row = ($type!='select') ? 1 : mysql_num_rows($res);
		$this->_row_count = $row;
		$this->_sql = $sql;
	}
	
	public function __destruct(){
		if (isset($this->_result) && gettype($this->_result)=='resource'){
			mysql_free_result($this->_result);
		}
	}
	
	/**
	 * fetches a result row as an assocciative array
	 * @access public
	 * @return array 
	 */
	public function fetch_assoc(){
		return mysql_fetch_assoc($this->_result);
	}
	
	/**
	 * returns how many rows were modified at update or fetched at select
	 * @access public
	 * @return int
	 */
	public function num_rows(){return $this->_row_count;}
	
	/**
	 * returns how many rows were modified at update or fetched at select
	 * @access public
	 * @return int
	 */
	public function field_count(){return $this->_row_count;}
}