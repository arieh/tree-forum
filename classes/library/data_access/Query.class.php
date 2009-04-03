<?php
interface Query{
	/**
	 * adds a select statment to the select stack
	 * 
	 * @param string|array $table a table name or a list of tabels and fields
	 * @param array        $field an array of fields
	 * 
	 * @comment for a list or fields, fields should be in either strings or arrays or pairs of
	 * 			field-names and aliases
	 * @comment for table arrays, an associative array, where the index is table name and the value is field-arrays as mentioned above
	 * 
	 * @access public  
	 */
	public function addSelect($table,$field=false);
	
	/**
	 * adds a select statment for using a function
	 * 
	 * @param string|array $function a function name|an array of function name and paramters for the function
	 * @param string       $alias    an alias for the function field in the result
	 * @param string       $table    table name
	 * @param string       $field    field name
	 * 
	 * @access public
	 */
	public function addSelectFunction($function,$alias='',$table=false,$field=false);
	
	/**
	 * adds an inner-join statment
	 * 
	 * @param array $table1 the main table. array of table name and field name
	 * @param array $table2 the to-be-joined table. array or table name and field name
	 * 
	 * @access public
	 */
	public function addInnerJoin($table1,$table2);
	
	/**
	 * creates a condition statment
	 * 
	 * @param string       $table    table name
	 * @param string       $field    field name
	 * @param string       $action   what comparison method to use
	 * @param mixed        $argument what to compare with. can be: int, string, array (for IN statements) and Query instance (for sub-queries)
	 * @param string|array $function a function name|an array of function name and paramters for the function
	 * 
	 * @access public
	 * @return condition a condition object
	 */
	public function createCondition($table,$field,$action,$argument);
	
	/**
	 * adds a set of conditions to a conditionset
	 * 
	 * each condition set is joined with AND, and seperated from other sets with OR
	 * 
	 * @param subCondition|array any number of condition objects and arrays of conditions (arrays will be handled as separate condition sets)
	 * 
	 * @access public 
	 */
	public function addConditionSet();
	
	/**
	 * generates an SQL statment
	 * 
	 * @access public
	 * @return string an SQL statment
	 */
	public function generate();
	
	/**
	 * adds a group by statment
	 * 
	 * @param string|array $table table name|associative array of table=>field
	 * @param string       $field if first var was table name, table field to group by
	 * 
	 * @access public 
	 */
	public function groupBy($table,$field='');
	
	/**
	 * adds a table-field set with which to order the result
	 * 	@param string|array $table table name|associative array of table=>field
	 * 	@param string       $field if first var was table name, table field to group by
	 * 
	 * @access public 
	 */
	public function orderBy($table,$field='');
	
	/**
	 * sets a limit to the results
	 * 	@param int $s what result to start from
	 * 	@param int $c how many results to fetch
	 * 
	 * @access public
	 */
	public function limit($s,$c=false);
}