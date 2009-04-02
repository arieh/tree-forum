<?php
require_once('Query.class.php');

class MysqlQuery implements Query{
	/**
	 * @var array holder of select fields
	 */
	private $_selects = array();
	/**
	 * @var array holder of select fields with functions 
	 */
	private $_selectFuncs = array();
	/**
	 * @var array holder of inner-joins
	 */
	private $_innerJoins   = array();
	/**
	 * @var array holder of condition sets
	 */
	private $_conditionSets = array();
	/**
	 * @var array a list of allowed comparison actions
	 */
	private $_actions = array('=','!=','>','<','LIKE','IN','= ANY','=ANY');
	/**
	 * @var array a list of allowed functions
	 */
	private $_functions = array('SHA1','LCASE','SUBSTRING','COUNT','DATE_FORMAT');
	
	/**
	 * @var array a group-by order hodler
	 * @access private 
	 */
	private $_group = array();
	
	/**
	 * @var array holds the order-by order
	 * @access private
	 */
	private $_order = array();
	
	/**
	 * @var array holds the limit paramaters
	 * @access private
	 */
	private $_limit = array();
	
	/**
	 * @var bool whether to order results descending (true) or asscending(false)
	 * @access private
	 */
	private $_orderDesc = true;
	
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
	public function addSelect($table,$field=false){
		if (is_string($table) && is_array($field)){
			if (count($field)==0) $this->_selects[$table]=array();
			foreach ($field as $fn=>$alias){
				if (is_numeric($fn)) $this->_selects[$table][]=$alias;
				else $this->_selects[$table][$fn]=$alias;
			}
		}elseif (is_array($table)){
			foreach ($table as $tbl=>$fields){
				$this->addSelect($tbl,$fields);
			}
		}
	}
	
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
	public function addSelectFunction($function,$alias='',$table=false,$field=false){
		if (in_array($function,$this->_functions)){
			$func[0]=$function;
			$func[1]='';
		}elseif (is_array($function)) $func=$function;
		$alias = (is_string($alias) && strlen($alias)>0) ? $alias : '';
		if(in_array($func[0],$this->_functions)){
			$this->_selectFuncs[]=array($func,$alias,$table,$field);
		}
	}
	
	/**
	 * adds an inner-join statment
	 * 
	 * @param array $table1 the main table. array of table name => field name
	 * @param array $table2 the to-be-joined table. array or table name => field name
	 * 
	 * @access public
	 */
	public function addInnerJoin($table1,$table2){
		array_push($this->_innerJoins,array($table1,$table2));
	}
	
	/**
	 * creates a condition statment
	 * 
	 * @param string       $table    table name
	 * @param string       $field    field name
	 * @param string       $action   what comparison method to use
	 * @param string       $argument what to compare with
	 * @param string|array $function a function name|an array of function name and paramters for the function
	 * 
	 * @access public
	 * @return subCondition a condition object
	 */
	public function createCondition($table,$field,$action,$argument,$function=''){
		$cond = array();
		
		if (in_array($action,$this->_actions)==false) $cond['action'] = '=';
		else $cond['action']=$action;
		
		
		if (is_array($function) && in_array($function[0],$this->_functions)){
			$cond['func']=$function;
		}elseif (is_string($function) && in_array($function,$this->_functions)){
			$cond['func'][0]=$function;
			$cond['func'][1]='';
		}else{
			$cond['func']=false;
		};
		
		if ($argument instanceof query) $cond['argument'] = $argument;
		elseif (is_numeric($argument)===false) $cond['argument'] = mysql_real_escape_string($argument);	
		else $cond['argument'] = $argument;	
		
		$cond['fields'] = array($table,$field);
		
		return new subCondition($cond);
	}
	
	/**
	 * adds a set of conditions to a conditionset
	 * 
	 * each condition set is joined with AND, and seperated from other sets with OR
	 * 
	 * @param subCondition|array any number of condition objects and arrays of conditions 
	 * 
	 * @access public 
	 */
	public function addConditionSet(){
		$conds = func_get_args();
		$arr = array();
		foreach ($conds as $cond){
			if (is_array($cond)){
				foreach ($cond as $c){
					if ($c instanceof subCondition){
						$arr[]=$c;
					}
				}	
			}
			if ($cond instanceof subCondition){
				$arr[]=$cond;
			}
		}
		array_push($this->_conditionSets,$arr);
	}
	
	/**
	 * adds a group by statment
	 * 
	 * @param string|array $table table name|associative array of table=>field
	 * @param string       $field if first var was table name, table field to group by
	 * 
	 * @access public 
	 */
	public function groupBy($table,$field=''){
		if (is_string($table)){
			$this->_group[] = array($table=>$field);
		}elseif (is_array($table)){
			$args = func_get_args();
			foreach ($args as $value){
				$this->_group[]=$value;
			}			
		}
	}
	
	/**
	 * adds a table-field set with which to order the result
	 * 	@param string|array $table table name|associative array of table=>field
	 * 	@param string       $field if first var was table name, table field to group by
	 * 
	 * @access public 
	 */
	public function orderBy($table,$field=''){
		if (is_string($table)){
			$this->_order[] = array($table=>$field);
		}elseif (is_array($table)){
			$args = func_get_args();
			foreach ($args as $value){
				$this->_order[]=$value;
			}			
		}
	}
	
	/**
	 * sets the ordering order to Ascending
	 * @access public
	 */
	public function orderAsc(){$this->_orderDesc = false;}
	
	/**
	 * sets the ordering order to descending
	 * @access public
	 */
	public function orderDesc(){$this->_orderDesc = true;}
	
	/**
	 * sets a limit to the results
	 * 	@param int $s what result to start from
	 * 	@param int $c how many results to fetch
	 * 
	 * @access public
	 */
	public function limit($s,$c=false){
		if (!is_numeric($s)) return false;
		if ($c && is_numeric($c)) $this->_limit = array((int)$s,(int)$c);
		else $this->_limit = array((int)$s);
	}
	
	/**
	 * generates an SQL statment
	 * 
	 * @access public
	 * @return string an SQL statment
	 */
	public function generate(){
		$sql ='';
		$tables = array();
		$sql = "\nSELECT \n";
		$sep = '';
		foreach ($this->_selects as $table=>$fields){
			$tables[]=$table;
			if (count($fields)==0){
				$sql.=$sep."\t"."`$table`.*";
				$sep=',';
			}else{
				foreach ($fields as $fn=>$alias){
					if (is_numeric($fn)==false){
						$sql.=$sep."\t"."`$table`.`$fn` as `$alias`";
					}elseif ($alias==='*'){
						$sql.=$sep."\t"."`$table`.*";
					}else{
						$sql.=$sep."\t"."`$table`.`$alias`";
					}
					$sep=",\n";
				}
			}
		}
		foreach ($this->_selectFuncs as $select){
			if (strlen($select[0][1])>0){
				$args = ",".$select[0][1];
			}else $args='';
			$alias = (strlen($select[1])>0) ? " as `".$select[1]."` " : '';
			$table = ($select[2] && strlen($select[2])>0) ? "`".$select[2]."`" : '';
			if ($table) $tables[]=$select[2];
			if ($select[3] && strlen($select[3])>0){
				$field ="`".$select[3]."`"; 
				$tSep = ".";
			}else{
				$table = '';
				$tSep  = '';
				$field = '*';
			}
			
			$sql.=$sep."\t".$select[0][0]."($table $tSep $field $args) $alias";
			$sep = ",\n";
		}
		
		$sql.="\nFROM `".$tables[0]."`\n";
		
		if (count($this->_innerJoins)>0){
			foreach ($this->_innerJoins as $tables){
				$mTable = $tables[0];
				$keys = array_keys($mTable);
				$mtname =  $keys[0];
				$sTable = $tables[1];
				$keys = array_keys($sTable);
				$stname =  $keys[0];
				$sql.="\tInner Join `".$stname."` ON `".$mtname."`.`".$mTable[$mtname]."` = `".$stname."`.`".$sTable[$stname]."`\n";
			}
		}
		
		if (count($this->_conditionSets)>0){
			$sql.="WHERE \n";
			$sep="\t";
			foreach($this->_conditionSets as $condset){
				foreach($condset as $cond){
					$field = $cond->getFields();
					if ($func = $cond->getFunc()){
						$args = (strlen($func[1])>0) ? ",".$func[1] : '';
						$sql.=$sep."(".$func[0]."(`".$field[0]."`.`".$field[1]."`$args) ";
					}else{
						$sql.=$sep."(`".$field[0]."`.`".$field[1]."` ";
					}
					$sql.= $cond->getAction()." ";
					$arg = $cond->getArgument();
					if ($arg instanceof query) $sql.="(".$arg->generate()."))";
					elseif (!is_numeric($arg) && $cond->getAction()!='IN') $sql.="'$arg')";
					else $sql.=$arg.")";
					$sep="\n\tAND\n\t";
				}
				$sep="\n OR \n\t";
			}
		}
		if (count($this->_group)>0){
			$sql.="\nGROUP BY ";
			$sep = "";
			foreach ($this->_group as $table){
				$tname = array_keys($table);
				$tname = $tname[0];
				$sql.="$sep `$tname`.`$table[$tname]`";
			}
		}
		
		if (count($this->_order)>0){
			$sql.="\nORDER BY ";
			$sep = "";
			foreach ($this->_order as $table){
				$tname = array_keys($table);
				$tname = $tname[0];
				$sql.="$sep `$tname`.`$table[$tname]`";
			}
			$sql.=($this->_orderDesc) ? " DESC" : " ASC";
		}
		
		if (count($this->_limit)>0){
			$sql.="\nLIMIT ";
			$sql.=$this->_limit[0];
			if (isset($this->_limit[1])) $sql.=",".$this->_limit[1];
		}
		
		return $sql;
	}
	
	/**
	 * resets all condition sets
	 * @access public
	 */
	public function resetConditions(){
		$this->_conditionSets = array();
	}
	
	/**
	 * resets all query paramaters
	 * @access public
	 */
	public function reset(){
		$this->_selectFuncs = array();
		$this->_selects = array();
		$this->_innerJoins   = array();
		$this->_conditionSets = array();
		$this->_group = array();
		$this->_order = array();
		$this->_limit = array();
		$this->_orderDesc = true;
	}
}

class subCondition{
/**
	 * @param array the array containing passed variables
	 * @access private
	 */
	private $_array = array();
	
	/**
	 * a cunstructor function
	 * 
	 * @param array $arr a 1 dimentional assotiative array
	 * 
	 * @access public
	 */
	public function __construct($arr){
		$this->_array = $arr;
	}
	
	/**
	 * an access function to unknown functions
	 * 
	 * it recives funtions with the folowing name-pattern "getParamnane" where param name a
	 * wanted paramater that was recived from the database
	 * 
	 * @param string function name
	 * @param mixed  passed arguments
	 * 
	 * @access public
	 * @return mixed whatever the paramater of the name is
	 */
	public function __call($name,$pars){
		if (substr($name,0,3)=='get'){
			if (array_key_exists(strtolower(substr($name,3)),$this->_array)){
				return $this->_array[strtolower(substr($name,3))];
			}
		}
		return false;
	}	
}