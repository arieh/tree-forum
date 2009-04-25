<?php
interface TFView {
	public function addPath($type, $path);
	public function display($tpl = null);
	public function fetch($tpl = null);
	public function assign($arg0=null,$arg1=null);    
}
?>