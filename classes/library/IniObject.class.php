<?php
class IniObjectException extends Exception{}

/** 
 * this class is intended to create an object representation of an INI file
 */
class IniObject{
	
	/**
	 * the cunstruction method of this class
	 * 
	 * @param string $file the location of the ini file to be parsed
	 * @param string $section optional - a specific section of the file to parse
	 * @param string $name a name for the object
	 * @param bool $debug whether to run in debug mode or not
	 * @param bool $global whether to register the object globaly;
	 * 
	 * @access public
	 */
	public function __construct($file,$section=false){
		
		if (false==file_exists($file)){
			throw new IniObjectException('INI file '.$file.' Does Not Exists');
		}
		$this->getIniFile($file,$section);
	}
	
	/**
	 * parses the ini file into class members
	 * 
	 * @param string $file the location of the ini file to be parsed
	 * @param string $section optional - a specific section of the file to parse
	 * 
	 * @access protected 
	 */
	protected function getIniFile($file,$sec=false){
		
		$array = parse_ini_file($file,true);
		
		if ($sec && array_key_exists($sec,$array)){
		//if a specific section was requested that exists in the file
			foreach($array[$sec] as $key=>$value){
				$this->$key=$value;
			}
		}elseif($sec && array_key_exists($sec,$array)==false) 
		//if the requested section does not exists	
			$this->generateError("No Section Exists by the name $section",$this->getObjectName()."::noSection",__LINE__,__FILE__);
		
		foreach($array as $section=>$defs){
			if (is_array($defs)){
				foreach($defs as $key=>$value){
					$this->$section->$key=$value;
				}
			}else{
				$this->$section=$defs;
			}
		}
	}
}
