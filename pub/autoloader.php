<?php

function __autoload($class_name){
	TreeForumAutoload($class_name);
}

/**
 * searches for a class's file
 * 	@param string $class_name class name
 */
function TreeForumAutoload($class_name){
	if (!defined('_SEP_')) define('_SEP_',DIRECTORY_SEPARATOR);
	if (@file_exists('records.txt'))
		$record = unserialize(file_get_contents('records.txt'));
	else
		$record = array(); 

	if (array_key_exists($class_name,$record) && !is_null($record[$class_name])){
		if (file_exists($record[$class_name]."class.php")){
			require_once($record[$class_name]."class.php");
			return;	
		}else{
			unset($record[$class_name]);			
		}
	}
	if (!file_exists('folder_list.txt')) return;
	
	$folders = fopen('folder_list.txt','r');
	
	while ($folder = trim(fgets($folders))){
		if (_SEP_=='/') $folder = str_replace("\\",'/',$folder);
		else $folder = str_replace('/',"\\",$folder);
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