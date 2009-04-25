<?php
class TFRouter {
	
	static private $_params = array();
	
	static private $_default_controller = '';
	
	static private $_def_view_conf = '';
	
	static private $_env = 'xhtml';
	
	static private $_default_view_conf ='';
	
	static public function route($route='',$def_router='',$viewConf=''){
		self::$_default_view_conf = '..' . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'view.ini';
		
		$view_conf = (strlen($viewConf)>0 && file_exists($viewConf)) ? new IniObject($viewConf) : new IniObject(self::$_def_view_conf); 
		
		$savant = new Savant3();
		$savant->addPath('template',$view_conf->base_dir . $view_conf->templates);
		$savant->assign('bPath',$view_conf->base_path);
		
		if (strlen($route)>0){
			if (substr($route,-1)!='/') $route.='/';
			$route = explode($route,$_SERVER['REQUEST_URI']);
			$route = $route[1];	
		}
		
		$inputs = array($_GET,$_POST,$_COOKIE);
		foreach ($inputs as $input) self::correctInput($input);
		$route = explode('/',$route);
		
		$controller = ucwords(strtolower(array_shift($route)))."C";
		if (file_exists("../classes/app/controllers/$controller.class.php")){
			$control = new $controller($route,$savant);
		}
		
		if (!isset($control)){
			if (strlen($def_router)>0 && class_exists($def_router)) $control = new $def_router($route,$savant);
			//elseif (strlen($def_router)>0 && class_exists($this->_default_router)) $control = new {$this->_default_router}($route,$savant);
		}
		
		if (!isset($control) || !$control instanceof TFController) throw new TFRouterException ('No Controller Was Set'); 
	}
	
	static private function correctInput(&$arr){
		$gpc = get_magic_quotes_gpc();
		foreach ($arr as &$var){
			if (is_array($var)) self::correctInput($var);
			elseif (is_string($var)){
				if ($gpc) stripslashes($var);
				$var = urldecode($var);
			}
		} 
	}
	
	static public function getParam($name){
		if (array_key_exists($name,self::$_params)) return self::$_params[$name];
		$inputs = array($_POST,$_GET);
		foreach ($inputs as $input) {
			if (array_key_exists($name,$input)) return $input[$name];
		}
		return false;
	}
	
	static public function setParam($name,$value=''){
		if (is_string($name)) self::$_params[$name] = $value;
		else throw new TFRouteException('Bad Paramater Name');
	}
	
	static public function setEnv($env){
		self::$_env = $env;
	}
	
	static public function getEnv(){
		return self::$_env;
	}	
}

class TFRouterException extends Exception {}