<?php
class DaoResultException extends Exception{}

/**
 * an interface for accessing database results
 */
interface DaoResult{
	
	/**
	 * fetches a result row as an assocciative array
	 * @access public
	 * @return array 
	 */
	public function fetch_assoc();
	
	/**
	 * returns how many rows were modified at update or fetched at select
	 * @access public
	 * @return int
	 */
	public function num_rows();
	
	/**
	 * returns how many rows were modified at update or fetched at select
	 * @access public
	 * @return int
	 */
	public function field_count();
}