<?php

global $components; // [[taskName, compName],....]
$components = [];

class Fw {
	public $task = '';
	function __construct() {
		// SEO barát URL kezelés
		// url = ....../task/xxx/parname/parValue....
		$w = explode('/',$_SERVER['REQUEST_URI']);
		$i = 0;
		while ($i < count($w)) {
			if ($w[$i] == 'task') {
				$_GET['task'] = $w[$i+1];
				$i = $i + 2;
				while ($i < count($w)) {
					$_GET[$w[$i]] = $w[$i+1];
					$i = $i + 2;
				}
			}
			$i++;
		}

		// screen méret hozzáférhetővé tétele a php számára
		if (isset($_REQUEST['width'])) {
			$_SESSION['screen_width'] = $_REQUEST['width'];
			$_SESSION['screen_height'] = $_REQUEST['height'];
			header('Location: ' . $_SERVER['PHP_SELF']);
		} else if(!isset($_SESSION['screen_width']) OR !isset($_SESSION['screen_height'])){
			echo '<script type="text/javascript">window.location = "' . $_SERVER['PHP_SELF'] . '?width="+screen.width+"&height="+screen.height;</script>';
		}	

		// server infok hozzáférhetővé tétele a php számára
		define('DOCROOT',__DIR__);
		$w1 = (int) str_replace('M', '', ini_get('post_max_size'));
		$w2 = (int) str_replace('M','',ini_get('upload_max_filesize'));
		define('UPLOADLIMIT',min($w1,$w2));

		// egy felhasználós módban minen "0" user_id -hez rendelve 
		// szerepel az adatbázisban
		// több felhasználós változatban a bejelentkezési folyamatban
		// beállított user.id -t használjuk
		if (MULTIUSER) {
		 if (!isset($_SESSION['loged'])) {
		   $_SESSION['loged'] = -1;
		   $_SESSION['logedName'] = 'guest';
		   $_SESSION['logedAvatar'] = '';
		   $_SESSION['logedGroup'] = '';
		 }
		} else {
			$_SESSION['loged'] = 1;
			$_SESSION['logedName'] = 'user';
			$_SESSION['logedAvatar'] = '';
			$_SESSION['logedGroup'] = 'admin';
		}

		// Facebbok/google loginból érkező hívás feldolgozása
		if (isset($_GET['usercode'])) {
			$w = explode('-',$_GET['usercode']);
			$userName = 's_'.base64_decode($w[0]);
			$userId = $w[1];
			if ($w[2] == md5($userId.FB_SECRET)) {
				// van már ilyen user?
				$db = new \RATWEB\DB\Query('users');
				$db->where('username','=','"'.$userName.'"');
				$rec = $db->first();
				if ($db->error != '') {
					// nincs, létrehozzuk és az újra jelentkezünk be
					$r = new \RATWEB\DB\Record();
					$r->username = $userName;
					$r->password = $w[2];
					$userId = $db->insert($r);
				} else {
					// van erre jelentkezünk be
					$userId = $rec->id;
				}
				// bejelentkeztetés
				$_SESSION['loged'] = $userId;
				$_SESSION['logedName'] = $userName;
				$_SESSION['logedAvatar'] = '';
				$_SESSION['logedGroup'] = '';
				$db = new \RATWEB\DB\Query('profilok');
				$profil = $db->where('id','=',$userId)->first();
				if (isset($profil->avatar)) {
					$_SESSION['logedAvatar'] = $profil->avatar;
					$_SESSION['logedGroup'] = $profil->group;
				}
			} else {
				echo 'kodolási hiba userId='.$userId.' userName='.$userName; exit();	
			}
		}

		// task kezelés
		if (isset($_GET['task'])) {
			$this->task = $_GET['task'];
		} else {
			$this->task = 'home';
		}
		if (strpos($this->task,'.')) {
			$w = explode('.',$this->task);
			$compName = $w[0];
			$this->task = $w[1];
			importComponent($compName); 
		}
	}
	
	/**
	* controller betöltése
	* globalis funkcióként is hívható
	* @param string $name
	*/
	public static function importComponent(string $name) {
		global $components;
		include_once 'includes/controllers/'.strtolower($name).'.php';
		$methods = get_class_methods(ucFirst($name));
		foreach ($methods as $method) {
			$components[] = [$method,ucFirst($name)];
		}
	}

	/**
	* a bejelentkezett user admin?
	* globalis funkcióként is hívható 
	* @return bool
	*/
	public static function isAdmin() {
		return (($_SESSION['logedGroup'] == 'admin') | ($_SESSION['logedName'] == ADMIN));
	}
	
	/**
	* viewer hívása  
	* global funkccióként is hívható
	* @param string $viewName
	* @param array $params ["pname" => $pvalue,...]
	* @param string $appName
	*/
	public static function view(string $viewname, array $params, string $appName = 'add') {
		view($viewname, $params, $appName); 
	}
}

function importComponent(string $name) {
	Fw::importComponent($name);
} 

function isAdmin() {
	return Fw::isAdmin();
}

?>