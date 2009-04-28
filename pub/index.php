<?php
ob_start();
session_start();
session_regenerate_id();

define ("_SEP_", DIRECTORY_SEPARATOR);
define ("_DEBUG_",true);
require_once 'autoloader.php';
require_once 'errorHandler.php';
require_once ".." . _SEP_ . "classes" . _SEP_ . "library" . _SEP_ .'firePHP' . _SEP_ . "fb.php";

NewDao::connect('mysql',".." . _SEP_ . "configs" . _SEP_ . "db.ini");
NewDao::setLogger(array('FB','log'));

$conf = new IniObject(".." . _SEP_ . "configs" . _SEP_ . "view.ini");

TFRouter::route($conf->base_path,'',$conf);
ob_flush();