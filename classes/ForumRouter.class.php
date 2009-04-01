<?php
class ForumRouterException extends Exception{}

class ForumRouter {
	public function __construct($options=array()){
		if (isset($options['link'])){
			NewDao::setLink($options['link']);
			
		}elseif (isset($options['database-ini'])){
			if (file_exists($options['database-ini'])){
				$ini = new IniObject($options['database-ini']);
				NewDao::connect($ini->host,$ini->uname,$ini->pass,$ini->name);	
			}
			
		}elseif (NewDao::connected()==false){
			throw new ForumRouterException("You must connect to the database");
		}
		
		if (!isset($options['decode-input']) || $options['decode-input']==true){
			$array = array($_GET,$_POST,$_COOKIE);
			foreach ($array as &$arr) $arr = self::decodeInput($arr);
		}
		
		if (isset($options['base_uri'])){
			$baseURI = $options['base_uri'];
		}else $baseURI = '';
		
		if (strlen($baseURI)>0){
			$split_uri = explode($baseURI,$_SERVER['REQUEST_URI']);
			$split_uri = explode('/',$split_uri);	
		}else{
			$split_uri = explode('/',$_SERVER['REQUEST_URI']);
		}
		
		if (count($split_uri)>0){
			$controller = $split_uri[0];
			array_pop($split_uri);	
		}else $controlelr = $this->_defaultController;
		
		if (count($split_uri)>0){
			$model = $split_uri[0];
			array_pop($split_uri);
		}else $model = $this->_defaultModel;
		
		$cont = new $controllers($model,$split_uri);
		$cont->execute();
			
		$actions = $split_uri;
	}    
	
	static public function decodeInput($arr){
		foreach ($arr as &$value){
			if (is_string($value) && is_numeric($value)==false){
				$value = rawurldecode($value);
				$value = htmlspecialchars($value);
			}elseif (is_array($value)) $value = self::decodeInput();
		}
		return $arr;
	}
}
?>