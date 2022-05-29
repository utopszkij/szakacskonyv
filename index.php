<?php
namespace RATWEB\DB;
session_start();
include_once 'config.php';
include_once 'vendor/database/db.php';
include_once(__DIR__.'/includes/views/view.php');
include_once(__DIR__.'/includes/models/model.php');

define('DOCROOT',__DIR__);

// egy felhasználós módban minen "0" user_id -hez rendelve 
// szerepel az adatbázisban
// több felhasználós változatban a bejelentkezési folyamatban
// beállított user.id -t használjuk
if (MULTIUSER) {
 if (!isset($_SESSION['loged'])) {
   $_SESSION['loged'] = -1;
   $_SESSION['logedName'] = 'guest';
 }
} else {
	$_SESSION['loged'] = 0;
	$_SESSION['logedName'] = 'admin';
}

$time = time();
if (isset($_SESSION['numDay'])) {
	$numDay = $_SESSION['numDay'];
	$numMonth = $_SESSION['numMonth'];
	$numYear = $_SESSION['numYear'];
} else { 
	$_SESSION['numDay'] = date('d', $time);
	$_SESSION['numMonth'] = date('m', $time);
	$_SESSION['numYear'] = date('Y', $time);
}

if (isset($_GET['task'])) {
	$task = $_GET['task'];
} else {
	$task = 'home';
}

global $components; // [[taskName, compName],....]
$components = [];

function importComponent($name) {
	global $components;
	include_once 'includes/controllers/'.strtolower($name).'.php';
	$methods = get_class_methods(ucFirst($name));
	foreach ($methods as $method) {
		$components[] = [$method,ucFirst($name)];
	}
}

importComponent('osszegzes');
importComponent('napimenu');
importComponent('recept');
importComponent('naptar');
importComponent('user');
importComponent('szovegek');
importComponent('upgrade');

// Facebbok/google loginból érkező hívás feldolgozása
if (isset($_GET['usercode'])) {
	$w = explode('-',$_GET['usercode']);
	$userName = 's_'.base64_decode($w[0]);
	$userId = $w[1];
	if ($w[2] == md5($userId.FB_SECRET)) {

		// van már ilyen user?
		$db = new Query('users');
		$db->where('username','=','"'.$userName.'"');
		$rec = $db->first();
		if ($db->error != '') {
			// nincs, létrehozzuk és az újra jelentkezünk be
			$r = new Record();
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
	}
}
//+ ----------- verzio kezelés start ------------

// -------------------
$fileVerzio = 'v0.3';
// -------------------

$upgrade = new \Upgrade();
$dbverzio  = $upgrade->getDBVersion();
$lastVerzio = $upgrade->getLastVersion();

if ($dbverzio < 'v0.1') {
	$q = new Query('receptek');
	$q->exec('alter table receptek 
		add created_at date
	');
	$q = new Query('dbverzio');
	$r = new Record();
	$r->verzio = 'v0.1';
	$q->where('verzio','<>','')->update($r);
}
if ($dbverzio < 'v0.3') {
	$q = new Query('receptek');
	$q->exec('alter table receptek 
		add energia varchar(32),
		add elkeszites int,
		add adag int
	');
	$q->exec('update receptek set energia = 0, elkeszites = 0, adag = 4');
	$q->exec('create table if not exists recept_cimke ( 
		recept_id int,
		cimke varchar(64),
		KEY `recept_cimke_id` (`recept_id`),
		KEY `recept_cimke_cimke` (`cimke`)
		) DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci
	');
	$q = new Query('dbverzio');
	$r = new Record();
	$r->verzio = 'v0.3';
	$q->where('verzio','<>','')->update($r);
}
// ide jönek a későbbi verziokhoz szükséges db alterek növekvő verzió szerint
//- ----------- verzio kezelés end ------------

?>

<html lang="en">
<head>
  <meta>
    <meta charset="UTF-8">
    <title>Hetimenü</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	 <!-- bootstrap -->	
	 <link rel="stylesheet" href="./vendor/bootstrap/css/bootstrap.min.css">
    <script src="./vendor/bootstrap/js/bootstrap.min.js"></script>
	 <!-- fontawesome --> 
	 <script src="./vendor/fontawesome/js/all.min.js"></script>
	 <link rel="stylesheet" href="./vendor/fontawesome/css/all.min.css">

	 <link rel="stylesheet" href="style.css">
	<script type="text/javascript">
		function popupConfirm(txt, yesfun) {
			document.getElementById('popupOkBtn').style.display="inline-block";
			document.getElementById('popupNoBtn').style.display='inline-block';
			document.getElementById('popup').className='popupSimple';
			document.getElementById('popupTxt').innerHTML = txt;
			document.getElementById('popupOkBtn').onclick=yesfun;
			document.getElementById('popup').style.display='block';
		}
		function popupClose() {
			document.getElementById('popup').style.display='none';
		}
		function popupMsg(txt,className) {
			if (className == undefined) {
				className = 'popupSimple';
			}
			document.getElementById('popupOkBtn').style.display="none";
			document.getElementById('popupNoBtn').style.display='none';
			document.getElementById('popup').className=className;
			document.getElementById('popupTxt').innerHTML = txt;
			document.getElementById('popup').style.display='block';
		}
	</script>	 
</head>	 
<body>
<div id="popup">
	<div style="text-align:right">
		<button type="button" onclick="popupClose()" 
			title="Bezár" style="margin:0px 0px 0px 0px; padding:0px 5px 0px 5px"
			class="btn btn-secondary">X</button>
	</div>
	<div id="popupTxt"></div>
	<div>
	<button type="button" id="popupOkBtn" class="btn btn-danger">Igen</button>
		&nbsp;
		<button type="button" id="popupNoBtn"class="btn btn-primary" onclick="popupClose()">Nem</button>
	</div>
</div>	 	
<div class="container">
	<div class="row" id="header">
	&nbsp;
	</div>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	  <div class="container-fluid">
	    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
	      <span class="navbar-toggler-icon"></span>
	    </button>
	    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
	      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
	        <li class="nav-item">
	          <a class="nav-link" href="index.php?task=home">
	          <em class="fas fa-home"></em>&nbsp;Napi menük</a>
	        </li>
	        <li class="nav-item">
	          <a class="nav-link" href="index.php?task=receptek&page=1">
	          <em class="fas fa-book"></em>&nbsp;Receptek</a>
	        </li>
	        <li class="nav-item">
	          <a class="nav-link" href="index.php?task=osszeg">
	          <em class="fas fa-plus"></em>&nbsp;Összesítés</a>
	        </li>
	      </ul>
	      <?php if (MULTIUSER) : ?>
	      <ul class="navbar-nav mb-2 mb-lg-0 text-right">
			  <?php if ($_SESSION['loged'] <= 0) : ?>
	        <li class="nav-item">
	          <a class="nav-link" href="index.php?task=login">
	          <em class="fas fa-sign-in-alt"></em>&nbsp;Bejelentkezés</a>
	        </li>
	        <?php endif; ?>
	        <?php if ($_SESSION['loged'] > 0) : ?>
	        <li class="nav-item">
	        		<a class="nav-link" href="#"><?php echo $_SESSION['logedName']; ?></a> 
	        </li>
	        <li class="nav-item">
	          <a class="nav-link" href="index.php?task=logout">
	          <em class="fas fa-sign-out-alt"></em>&nbsp;Kijelentkezés</a>
	        </li>
	        <?php endif; ?>
	        <li class="nav-item">
	          <a class="nav-link" href="index.php?task=regist">
	          <em class="fas fa-key"></em>&nbsp;Regisztrálás</a>
	        </li>
	      </ul>
			<?php endif; ?>
	      <!-- form class="d-flex">
	        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
	        <button class="btn btn-outline-success" type="submit">Search</button>
	      </form -->
	    </div>	<div class="upgrade">

	  </div>
	</nav>	
	<?php if ($lastVerzio > $fileVerzio) : ?>
	<div class="warning">
		<a href="index.php?task=upgrade1&version=<?php echo $lastVerzio; ?>" 
			class="btn btn-primary">
			Új verzó érhető el <?php echo $lastVerzio; ?>
		</a>
	</div>	
	<?php endif; ?>	
	<div class="page">
	<?php
		$compName = '';
		for ($i=0; $i<count($components); $i++) {
			if ($components[$i][0] == $task) {
				$compName = $components[$i][1];			
			}		
		} 
		if ($compName != '') {
			$comp = new $compName ();
			$comp->$task ();			
		} else {
			$task ();
		}	
	?>
	</div>
	<div id="footer">
		<a href="index.php?task=impresszum">Imresszum</a>&nbsp;&nbsp;&nbsp;
		<a href="index.php?task=adatkezeles">Adatkezelési leírás</a>&nbsp;&nbsp;&nbsp;
		<a href="index.php?task=visszaeles">Visszélés jelzése</a>&nbsp;&nbsp;&nbsp;
		<a href="index.php?task=licensz">Licensz</a>&nbsp;&nbsp;&nbsp;
		<a href="https://github.com/utopszkij/szakacskonyv" target="_new">Forrás program</a>&nbsp;&nbsp;&nbsp;
	</div>
</div>

</body>
</html>




