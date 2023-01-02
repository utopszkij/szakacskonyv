<?php
if (isset($_COOKIE['sid'])) {
	session_id($_COOKIE['sid']);
}
session_start();
global $components;

// server infok hozzáférhetővé tétele a php számára
define('DOCROOT',__DIR__);
$w1 = (int) str_replace('M', '', ini_get('post_max_size'));
$w2 = (int) str_replace('M','',ini_get('upload_max_filesize'));
define('UPLOADLIMIT',min($w1,$w2));
include_once 'config.php';
include_once 'vendor/database/db.php';
include_once('vendor/model.php');
include_once('vendor/view.php');
include_once('vendor/controller.php');
include_once('vendor/fw.php');
include_once('includes/models/statisticmodel.php');

if (!defined('STYLE')) {
	define('STYLE','delicious');
}
if (!defined('LIKESIZE')) {
	define('LIKESIZE',1); // ennyi like jelent egy csillagot
}

importComponent('osszegzes');
importComponent('napimenu');
importComponent('recept');
importComponent('naptar');
importComponent('user');
importComponent('szovegek');
importComponent('comment');
importComponent('atvaltasok');
importComponent('szinonima');
importComponent('mertekegyseg');
importComponent('cimkek');
importComponent('upgrade');
importComponent('blog');
importComponent('like');
importComponent('admin');

// statisztikai adatgyüjtés
$statisticModel = new StatisticModel();
$statisticModel->saveStatistic();

$fw = new Fw();

//+ ----------- verzio kezelés start ------------
$fileVerzio = 'v2.2.0';
$upgrade = new \Upgrade();
$dbverzio  = $upgrade->getDBVersion();
$lastVerzio = $upgrade->getLastVersion();
$upgrade->dbUpgrade($dbverzio);
$branch = $upgrade->branch;
//- ----------- verzio kezelés end ------------

// képernyő méretek elérése
if (isset($_COOKIE['screen_width'])) {
	$_SESSION['screen_width'] = $_COOKIE['screen_width'];
} else {
	$_SESSION['screen_width'] = 1024;
}
if (isset($_COOKIE['screen_height'])) {
	$_SESSION['screen_height'] = $_COOKIE['screen_height'];
} else {
	$_SESSION['screen_height'] = 800;
}

$task = $fw->task;
$comp = $fw->comp;
$title = 'Szakácskönyv';
if (method_exists($comp, 'getTitle')) {
	$title = $comp->getTitle($task);
} 

// execute API backends
if (in_array($fw->compName.'.'.$fw->task,
    ['Recept.getImage'])) {
	$comp->$task ();
    exit();
}
include_once 'styles/'.STYLE.'/main.php';
?>
