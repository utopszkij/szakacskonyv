<?php
use RATWEB\DB\Query;
include_once './config_test.php'; 
define('DOCROOT',str_replace('/tests','',__DIR__));
define('UPLOADLIMIT',2000);
include_once './vendor/database/db.php';
include_once('./vendor/model.php');
include_once('./vendor/controller.php');
$_SESSION = [];
$_GET = [];
$_POST = [];
global $viewData;

function isAdmin() {
   return  false;
}

function view($name, $params, $appName='app') {
	global $viewData;
	$viewData = ["name" => $name, "params" => $params, "appname" => $appName];
}

function checkView() {
	global $viewData;
	return $viewData;
}

$q = new Query('dbverzio');


?>
