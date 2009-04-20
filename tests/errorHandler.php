<?php
function TF_Error_Handler($errno,$str,$file,$line){
	
		$stack = debug_backtrace();	
	
	$error = "\nERROR: "
			.$str." \n"
			."On Line: $line "
			."On File: $file "
			."\n";

	if (function_exists('fb')){
		fb($error);
	}
	
	
  		
  		array_shift($stack);
  		$stackstr = '';
  		$sep = '>> ';
  		$tabs = "\n";
  		while ($ins = array_pop($stack)){
  			$stackstr .=$tabs.$sep;
  			if (isset($ins['class'])) $stackstr.=$ins['class'].$ins['type'];
  			if (isset($ins['function'])){
  				$stackstr .= $ins['function'].'()';
  				$stackstr .= " args:(";
  				foreach ($ins['args'] as $key=>$value){
  					$stackstr .= "[".$key."::";
  					$stackstr .= "(".gettype($value).")";
  					switch (gettype($value)){
  						case 'integer':
  						case 'string':
  						break;
  						case 'boolean':
  							$stackstr .= ($value) ? 'TRUE' : 'FALSE';
  						break;
  						case 'object':
  							$stackstr.=(isset($ins['object'])) ? get_class($ins['object']) : get_class($value)."::";
  						case 'array':
  							
  						default:
  							$stackstr.=json_encode($value);
  							//$stackstr.=serialize($value);
  						break;
  					}
  					$stackstr.=']';
  				} 
  				$stackstr .=")";
  				$stackstr .=" On line ".$ins['line'];
  				$stackstr .=" From file ".$ins['file'];
  			} 
  			$tabs.="\t";
  		}
  	if (file_exists('errors.log')){
  		$log = fopen('errors.log','a');
  	}else{
  		$log = fopen('errors.log','a+');
  	}
  	fwrite($log,$error."Stack:\n".$stackstr."\n=======================================\n");
  	fclose($log);
}

function TF_Exception_Handler($ex){
	
	$stack = $ex->getTrace();	
	
	$error = "\nUncaught-Exception: <<<[ "
			.$ex->getMessage()." ]>>>\n"
			."On Line: {$ex->getLine()} "
			."On File: {$ex->getFile()} "
			."\n";

	if (function_exists('fb')){
		fb($error);
	}
	
	
  		$stackstr = '';
  		$sep = '>> ';
  		$tabs = "\n";
  		
  		while ($ins = array_pop($stack)){
  			$stackstr .=$tabs.$sep;
  			if (isset($ins['class'])) $stackstr.=$ins['class'].$ins['type'];
  			if (isset($ins['function']) && isset($ins['file'])){
  				$stackstr .= $ins['function'].'()';
  				$stackstr .=" On line ".$ins['line'];
  				$stackstr .=" In file ".$ins['file'];
  			}elseif (isset($ins['function']) && isset($ins['class'])){
  				$stackstr .= $ins['function']. "(";
  				$sep ='';
  				
  				foreach ($ins['args'] as $arg){
  					$stackstr.=$sep . $arg;
  					$sep = ','; 	
  				} 
  				$stackstr .= ')';
  			} 
  			$tabs.="\t";
  		}
  	if (file_exists('errors.log')){
  		$log = fopen('errors.log','a');
  	}else{
  		$log = fopen('errors.log','a+');
  	}
  	fwrite($log,$error."Stack:\n".$stackstr."\n=======================================\n");
  	fclose($log);
}

set_error_handler("TF_Error_Handler");
set_exception_handler("TF_Exception_Handler");