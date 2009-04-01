<?php
$GLOBALS['included'] = array();
function __autoload($class_name){
	if (!defined(_SEP_)) define('_SEP_',DIRECTORY_SEPARATOR);
	global $included;
	if (@file_exists('records.txt'))
		$record = unserialize(file_get_contents('records.txt'));
	else
		$record = array(); 
	if (!is_array($record)){
		base::log("bad record:".$class_name);
		$record = array();
	} 
	if (in_array($class_name,$included)) return;
	if (array_key_exists($class_name,$record) && !is_null($record[$class_name])){
		if (file_exists($record[$class_name]."class.php")){
			require_once($record[$class_name]."class.php");
			return;	
		}else{
			unset($record[$class_name]);			
		}
	}
	
	$folders = array(
		".."._SEP_."classes"._SEP_."library"._SEP_,
		".."._SEP_."classes"._SEP_."library"._SEP_."models"._SEP_,
		".."._SEP_."classes"._SEP_."library"._SEP_."data_access"._SEP_,
		".."._SEP_."classes"._SEP_."app"._SEP_,
		".."._SEP_."classes"._SEP_."app"._SEP_."models"._SEP_,
	);
	
	foreach ($folders as $folder){
		if (file_exists($folder.$class_name.".class.php")){
			require_once($folder.$class_name.".class.php");
			set_record($record,$class_name,$folder.$class_name.".class.php");
			return;
		}
	}
}

/**
 * sets a classe's location to the recordset and writes it to file
 * 	@param array $record an associative array of classname=>location
 * 	@param string $cname class name
 * 	@param string #dir location of class file
 */
function set_record($record,$cname,$dir){
	$record[$cname] = $dir;
	$str = serialize($record);
	$file = @fopen('records.txt','w+');
	@fwrite($file,$str);
	@fclose($file);
}
?>