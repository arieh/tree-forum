<?php

abstract class TFController {
    
    /**
     * @param TFModel holder of controller's model
     * @access protected
     */
    protected $_model = null;
    
    /**
     * @param string name of model class to use
     * @access protected
     */
    protected $_model_name = '';
    
    /**
     * @param Savant3 holder of Savant3 instance
     * @access protected
     */
    protected $_view = null;
    
    /**
     * @param array holder of controller options to be sent to the model and the view
     * @access protected 
     */
    protected $_options = array();
    
    /**
     * @param array variables sent through router
     * @access protected
     */
    protected $_vars = array();
    
    /**
     * @param string default template folder
     * @access protected
     */
    protected $_default_tpl_folder = '';
    
    /**
     * @param array a list of template folders to user per action. can be a set of action=>folder
     */
    protected $_tpl_folders = array();
    
    /**
     * @param array a list of allowed enviorments for the controller. note that each enviorment MUST have
     *              represantaion via the action's template folder
     */
    protected $_envs = array('xhtml');
    
    /**
     * @param string default enviorment
     */
    protected $_def_env = 'xhtml';
    
    /**
     * @param current action
     */
    protected $_action = '';
	
	/**
	 * @param bool whether or not to render view
	 */
	protected $_show_view = true;    
    
    /**
     * @param string the final string to output when controller is done
     */
    protected $_output = '';
    
    /**
     * 	@param array $vars variables passed by router
     * 	@param TFView $view a view instance
     * @access public
     */
    public function __construct($vars, TFView $view){
    	if (!defined('_SEP_')) define (_SEP_,DIRECTORY_SEPARATOR); 
    	
    	if (count($vars)>0 && !is_numeric($vars[0])){
    		$this->_action = $this->setOption('action',strtolower(array_shift($vars)));
    	}
    	
    	$this->_vars = $vars;
    	$this->_view = $view;
    	
    	$this->setOption('user',TFUser::getInstance()->getId());
    	$this->setOption('permissions',TFUser::getInstance()->getPermissionIds(false));
    	if (defined('_DEBUG_')) $this->setOption('debug',_DEBUG_);
    	
    	$this->setOptions();
    	
    	$this->setModel();
    	
    	$this->executeBefore();
    	
    	$this->setView();
    	
		$this->executeAfter();
		
		if ($this->_show_view) echo $this->_output;
    }
    
    /**
     * a helper method, used to let develpors run operations before calling main view
     * @access protected
     */
    protected function executeBefore(){}
    
    /**
     * a helper method, used to let develpors run operations after calling main view
     * @access protected
     */
    protected function executeAfter(){}
    
    /**
     * add an option to the controller's option list (which will be passed to the model and view)
     * 	@param string $name option name
     * 	@param miced  $value a value to that option
     * @access protected
     */
    protected function setOption($name,$value){
    	$this->_options[$name]=$value;
    	return $value;
    }
    
    /**
     * get the options array
     * @access protected
     * @return array 
     */
    protected function getOptions(){
    	return $this->_options;
    }
    
    /**
     * check to see if a specific option was set
     * 	@param string $name option name
     * @access protected
     * @return bool 
     */
    protected function isOptionSet($name){
    	if (array_key_exists($name,$this->_options)) return true;
    	return false;
    }
    
    /**
     * sets the options for the controller
     * 
     * as a default, it will simply set all get and post vars. this should be extended for each controller,
     * as these options are the ones sent to the model and view
     * 
     * @access protected
     */
    protected function setOptions(){
    	$inputs = array($_GET,$_POST);
    	foreach ($inputs as $input){
    		foreach ($input as $key=>$var){
    			$this->setOption($key,$var);
    		}
    	}
    }
    
    /**
     * sets the model
     * @access protected
     */
    protected function setModel(){
    	$name = $this->_model_name;
    	
    	if (strlen($name)==0) return;
    	
    	if (!is_subclass_of($name,'TFModel')) throw new TFControllerException('model must be a subclass of TFModel');
    	
    	$this->_model = new $name($this->getOptions());
		
		$this->_model->execute();	
    }
    
    /**
     * sets the view
     * @access protected
     */
    protected function setView(){
    	if (!$this->_show_view) return;
    	
    	if ($this->_model) $this->_view->assign('model',$this->_model);
    	$this->_view->assign('options',$this->_options);
    	
    	if (in_array(TFRouter::getEnv(),$this->_envs)) $folder = TFRouter::getEnv();
		else $folder = $this->_def_env;
		
		if (strlen($this->_action)>0){
			if ( in_array($this->_action,$this->_tpl_folders)) 
				$location = $this->_template_dir . _SEP_ . $this->_action . _SEP_ . $folder . _SEP_;
			elseif ( array_key_exists($this->_action,$this->_tpl_folders) ){
				$location = $this->_template_dir . _SEP_ . $this->_tpl_folders[$this->_action] . _SEP_ . $folder . _SEP_;
			}
		} else{
			$location = $this->_template_dir . _SEP_ . $this->_default_tpl_folder . _SEP_ . $folder . _SEP_;
		} 	
		
		$file = ((is_null($this->_model)==false) && $this->_model->isError()) ? 'errors.tpl.php' : 'main.tpl.php';
		
		$this->_output .= $this->_view->fetch($location . $file);
    }
}

class TFControllerException extends Exception{}