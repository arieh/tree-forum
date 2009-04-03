<?php

class NewDaoException extends Exception{}
/**
 * this class handles a router for database calls and basic functions
 */
class NewDao{
	/** 
	 * 	@var string name of the database error function
	 *  @access protected
	 */	
	protected $_errorFunc='mysql_error';
	/** 
	 * 	@var string name of the result class
	 * 	@access protected
	 */
	protected $_resultClass = 'daoMySQLresult';
	/** 
	 * 	@var string database type
	 * 	@access protected
	 */
	protected $_type = 'mysql';

	/**
	 * @param array a list of query results
	 * @access protected
	 */	
	protected $_resultList = array();
	
	/**
	 * @param NewDao a NewDao singeton
	 * @access private
	 * @static
	 */
	static private $_instance = false;
	
	/**
	 * @param string a logging function
	 * @access private
	 * @static
	 */
	static private $_logger = false;
	
	/** 
	 * 	@var database-link a holder of the database link
	 *  @access private
	 * 	@static
	 */	
	static private $_link  = false;
	
	/**
	 * @var bool whether a connection has been established
	 * @access private
	 * @static
	 */
	static private $_connected = false;
	
	/** 
	 * __construct a constructor for the object
	 * @access private
	 * @return void 
	 */
	private function __construct(){
		$this->checkLinkType();
	}
	
	
	public function __destruct(){
		foreach ($this->_resultList as $res) $res->__destruct();
	}

/*
 * ==========================
 * 		Static Methods
 * ==========================
 */
	/**
	 * connect to a database
	 * 	@param string $type database type
	 * 	@param string $host host name
	 * 	@param string $user user name
	 * 	@param string $pass password
	 * 	@param string $db   database name
	 * 
	 * @access public 
	 * @static
	 * @return bool true if connection was successful;
	 */
	static public function connect($type,$host,$user,$pass,$db){
		switch ($type){
    		case "mysql":
    			$link = mysql_connect($host,$user,$pass);
    			mysql_select_db($db,$link);
    			if (mysql_error($link)){
    				throw new NewDaoException("Mysql Error:".mysql_error($link),"MysqlConnectionError",__LINE__,__FILE__);
    			}
    			self::$_connected = true;
    			self::$_link = $link;
    			return true;
    		break;
    		default:
    			$link = new mysqli($host,$user,$pass,$db);
				if (mysqli_errno($link)){
					throw new NewDaoException(mysqli_error($link),"MysqliConnectionError",__LINE__,__FILE__);
				}
				self::$_connected = true;
				self::$_link = $link;
				return true;
    		break;
    	}
    	throw new NewDaoException("Bad Database Type");
	}
	
	/**
	 * if a database connection has been established elsewhere, set the link to that database connection
	 * 	@param mixed $link a database link
	 * @access public
	 * @static
	 */
	static public function setLink($link){
		if (self::$_instance instanceof NewDao) self::$_instance->__destruct();
		self::$_link = $link;
	}
	
	/**
	 * checkes wether a database connection has been established
	 * @access public
	 * @static
	 * @return bool	
	 */
	static public function connected(){return (self::$_connected==true);}
	
	/**
	 * a factory for this class
	 * @access public
	 * @static
	 * @return NewDao	
	 */
	static public function getInstance(){
		if (!self::$_link || !self::connected()) throw new NewDaoException('not connected');
		if (!self::$_instance){
			self::$_instance = new NewDao();
		}
		return self::$_instance;
	}
	
	/**
	 * a factory for an appropriate Query generator
	 * @access public
	 * @static
	 * @return Query a query class relevant for the database type. if not connected, assumes MysqlQuery
	 * 
	 * @comment note: if now connected, query will not escape string!
	 */
	static public function getGenerator(){
		if (self::connected()==false) return new MysqlQuery(); 
		
		switch (self::getInstance()->getType()){
			case 'mysql':
				return new MysqlQuery();
			break;
		}
	}
	
	/**
	 * sets the logging function
	 * 	@param string $func name of logging function
	 * @access public
	 * @static
	 */
	static public function setLogger($func){
		if (!function_exists($func)) throw new NewDaoException("Function $func does not exists");
		self::$_logger = $func;
	}

/*
 * ================
 * 	   /statics
 * ================
 */
	
	/** checkLinkType() sets the Link type for the used database
	 *  @access private
	 *  @return void
	 */
	private function checkLinkType(){
		if (self::$_link instanceof mysqli){
		}else{
			if (!is_resource(self::$_link)) throw new NewDaoException("The database link is not a valid database link");
			switch (get_resource_type(self::$_link)){
				case 'mysql link':
				case 'mysql link persistent':
					$this->_errorFunc = 'mysql_errno';
					$this->_resultClass = 'DaoMySQLresult';
					$this->_type = 'mysql';
					return;
				break;	
			}
		}
		if (is_resource(self::$_link)) $type = get_resource_type(self::$_link);
		else $type = gettype(self::$_link);
		throw new NewDaoException("The database link is not a valid database link:$type");
	}
	
	/**
	 * an accessor for database type
	 * @access public
	 * @return string database type name
	 */
	public function getType(){return $this->_type;}
	
	
	public function getLastId(){
		switch ($this->_type){
			case 'mysqli':
				return self::$_link->insert_id;
			break;
			case 'mysql':
				return mysql_insert_id(self::$_link);
			break;
		}
	}
	
	
/*
 * ============================
 * 		quering methods
 * ============================
 */	
 /** 
  * send a query to the database
  * 	@param string $sql an SQL query
  * 	@param bool   $log whether to log query or not
  * @access protected
  * @return DaoResult
  */
	protected function &query($sql,$log=false){
		$class = $this->_resultClass;
		
		$type = (substr($sql,0,1)=='S') ? 'select' : 'update';
		
		if ($log && self::$_logger) call_user_func_array(self::$_logger,$sql);
	
		switch ($this->_type){
			case 'mysql':
				$result = mysql_query($sql,self::$_link);
				if ($err = mysql_error(self::$_link)){
					$error = "Error from DB: $err";
					if ($sql) $error.="\n( $sql )";
					throw new NewDaoException($error);
				}

				$res = new $class($result,$type,$sql);
			break;
		}	
		
		$this->_resultList[] = &$res;
		return $res;
	}	
	
	/**
	 * returns an array represantation of query result
	 * 	@param string $sql an SQL query
  	 * 	@param bool   $log whether to log query or not
  	 * @access public
  	 * @return array
	 */
	public function queryArray($sql,$log=false){
		$res = $this->query($sql,$log);
		$results = array();
		while ($row=$res->fetch_assoc()){
			$results[]=$row;
		}
		return $results;
	}
	/** 
	 * counts result fields for a speceific argument
	 * 		@param string $table  the table to select from
	 * 		@param array  $conditions  the conditions to check (more ditails on api file)
	 * 		@param bool   $log    whether to log the query or not
	 * @access public
	 * @return int  
	 */
	public function countFields($table,$conditions,$log=false){
		$query = self::getGenerator();
		$arr=array();
		$f = '*';
		foreach($conditions as $c=>$v){
			if ($f=='*') $f = $c; 
			$arr[] = $query->createCondition($table,$c,"=",$v);
		}
		
		$query->addConditionSet($arr);
		
		$query->addSelectFunction('COUNT','c',$table,$c);
		$res = $this->queryArray($query->generate(),$log);
		return $res[0]['c'];
	}
	
	/** 
	 * counts result fields for a speceific argument for lower-cased strings
	 * 		@param string $table : the table to select from
	 * 		@param array  $conditions : the conditions to check (more ditails on api file)
	 * 		@param bool   $log   : whether to log the query or not
	 * @access public
	 * @return int  
	 */
	public function countFieldsLCASE($table,$conditions,$log=false){
		$query = self::getGenerator();
		$query->addSelectFunction('COUNT','c',$table);
		foreach($conditions as $c=>$v){
			$arr[] = $query->createCondition($table,$c,"=",$v,'LCASE');
		}
		$query->addConditionSet($arr);
		$res = $this->query($query->generate(),$log);
		$result = $res->fetch_assoc();
		return $result['c'];
	}	
	
	
	/** 
	 * a simple select operation
	 * 		@param string $table   table name
	 * 		@param array  $fields  which fields to select 
	 * 		@param array  $conditions  which contions to use
	 *		@param bool   $min     whether or not to return a row aor an array holding a row when reciving only 1 row as a result
	 * 		@param bool   $log     whether to logg query or not
	 * @access public
	 * @return database result object  
	 */
	public function select($table='',$fields=array(),$conditions=array(),$min=true,$log=false){
		$query = self::getGenerator();
		$query->addSelect($table,$fields);
		$arr=array();
		foreach($conditions as $c=>$v){
			$arr[] = $query->createCondition($table,$c,"=",$v);
		}
		$query->addConditionSet($arr);
		$res = $this->queryArray($query->generate(),$log);
		if ($min && count($res)==1) return $res[0];
		
		return $res;
	}
	
	/** 
	 * a simple select operation, using lower cased comparison
	 * 		@param string $table   table name
	 * 		@param array  $fields  which fields to select 
	 * 		@param array  $conditions  which contions to use
	 *		@param bool   $min     whether or not to return a row aor an array holding a row when reciving only 1 row as a result
	 * 		@param bool   $log     whether to logg query or not
	 * @access public
	 * @return database result object  
	 */
	public function selectLCASE($table,$fields=array(),$conditions=array(),$min=true,$log=false){
		$query = NewDao::getGenerator();
		$query->addSelect($table,$fields);
		
		$conds = array();
		foreach ($conditions as $field => $value ){
			if (is_numeric($value)){
				$conds[]=$query->createCondition($table,$field,'=',$value);
			}else{
				$conds[]=$query->createCondition($table,$field,'=',strtolower($value),'LCASE');
			}	
		}
		$query->addConditionSet($conds);
		$res = $this->queryArray($query->generate(),$log);
		if ($min && count($res)==1) return $res[0];
		
		return $res;
	}
	

	/** 
	 * a method for updating values in the database
	 * 		@param string $table   the table to update
	 * 		@param array  $fields  the fields to update and their new values
	 *      @param array  $conditions  conditions for the update
	 * 		@param bool   $log     whether to logg SQL statement or not
	 * @access public
	 * @return bool whether success or not 
	 */	
	public function update($table,$fields,$conditions=array(),$log=false){
		$sql = $this->generateUpdateSQL($table,$fields,$conditions);
		$this->query($sql,$log);
		return true;
	}

	/** 
	 * a method for inserting values into DB
	 * 		@param string $table   the table for insert
	 * 		@param array  $fields  the fields to insert and their new values
	 * 		@param bool   $log     whether to logg SQL statement or not
	 * @access public
	 * @return bool whether success or not 
	 */	
	public function insert($table,$fields,$log=false){
		$sql = $this->generateInsertSQL($table,$fields);
		$this->query($sql,$log);
		return;
	}
	
	/**
	 * performs a delete action on the database
	 * 	@param string $table what table to delete from
	 * 	@param array  $conditions a set of conditions ($field=>$value)
	 * 	@param bool   $log wether to log query or not
	 * 
	 * @return bool wether query was successful
	 * @access public 
	 */
	public function delete($table,$conditions,$log=false){
		$sql = "DELETE FROM `$table` WHERE ";
		$sep = '';
		foreach ($conditions as $f => $v){
			$sql.= "$sep `$f`=`$v` ";
			$sep= 'AND';
		}
		return $this->query($sql,$log);
	}	

/* 
 * ==========================
 *     SQL Generators
 * ========================== 
 */
	protected function generateInsertSQL($table,$fields){
		switch ($this->_type){
			default:
				$sql = 'INSERT INTO `'.$table.'`'
						.'(`'.implode("`,`",array_keys($fields)).'`)'
						.'VALUES (';
			
				$sep="";
				$vals = array_values($fields);
				
				$funcs = array('CURDATE()','NOW()'); 
				
				for ($i=0;$i<count($vals);$i++){
					if (in_array($vals[$i],$funcs)) $sur='';
					elseif (is_string($vals[$i])) $sur="'";
					else $sur="";
					$sql.=$sep.$sur.$vals[$i].$sur;
					$sep=",";
				}	
						
				$sql.=")";
				return $sql;
			break;
		}
	}

	
	protected function generateUpdateSQL($table,$fields,$conditions){
		switch ($this->_type){
			default:
				$sql = 'UPDATE `'.$table.'` ';
				$sql .= "SET ";			
				$sep="";
				$sur = '';
				foreach ($fields as $key=>$value){
					$sur = (is_numeric($value))? '' : "'";
					$sql.=$sep."`$key`=".$sur.$value.$sur;
					$sep = ',';
				}		
				if (count($conditions)==0) return $sql;
				$sql.=" WHERE ";
				$sep = '';
				foreach ($conditions as $field => $value){
					$sur = (is_numeric($value)) ? '' : "'";
					$sql.= "$sep `$field` = $sur".$value.$sur;
					$sep = " AND ";
				}
				return $sql;
			break;
		}
	}	
}