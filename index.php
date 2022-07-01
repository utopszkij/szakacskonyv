<?php
// hibát okoz!   namespace RATWEB\DB;
session_start();
include_once 'config.php';
include_once 'vendor/database/db.php';
include_once(__DIR__.'/includes/views/view.php');
include_once(__DIR__.'/includes/models/model.php');

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

function isAdmin() {
	return (($_SESSION['logedGroup'] == 'admin') | ($_SESSION['logedName'] == ADMIN));
}

importComponent('osszegzes');
importComponent('napimenu');
importComponent('recept');
importComponent('naptar');
importComponent('user');
importComponent('szovegek');
importComponent('upgrade');
importComponent('comment');
importComponent('atvaltasok');
importComponent('szinonima');
importComponent('mertekegyseg');
importComponent('cimkek');

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
//+ ----------- verzio kezelés start ------------

$fileVerzio = 'v1.5.2';

$upgrade = new \Upgrade();
$dbverzio  = $upgrade->getDBVersion();
$lastVerzio = $upgrade->getLastVersion();
$upgrade->dbUpgrade($dbverzio);
$branch = $upgrade->branch;
//- ----------- verzio kezelés end ------------

?>

<html lang="en">
<head>
  <meta>
    <meta charset="UTF-8">
	<link rel="icon" type="image/x-icon" href="images/szakacs.png">
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
		function submenuToggle() {
			var submenu = document.getElementById('submenu');
			if (submenu.style.display == 'block') {
				submenu.style.display = 'none';
			} else {
				submenu.style.display = 'block';
			}
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
	<div class="row" id="header" onclick="document.location='index.php';"></div>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	  <div class="container-fluid">
	    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
	      <span class="navbar-toggler-icon"></span>
	    </button>
		<?php if ($_SESSION['logedAvatar'] != '') : ?>
			<a class="nav-link navbar-toggler" 
				href="index.php?task=useredit&id=<?php echo $_SESSION['loged']; ?>">
				<img src="images/users/<?php echo $_SESSION['logedAvatar']; ?>"
					style="height:34px; margin:0px; float:right" />
				<var class="<?php echo $_SESSION['logedGroup']; ?>">
					<?php echo $_SESSION['logedName']; ?>
				</var>
			</a>
		<?php endif; ?>
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
			<li class="nav-item">
				<a class="nav-link" href="#" onclick="submenuToggle()">
					 <em class="fas fa-tools"></em>&nbsp;Beállítások
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown" id="submenu">
						<?php if (MULTIUSER) : ?>
						<a class="dropdown-item" href="index.php?task=userek">
							Felhasználók</a>
						<?php endif; ?>	
						<a class="dropdown-item" href="index.php?task=atvaltasok">Átváltások</a>
						<a class="dropdown-item" href="index.php?task=szinonimak">Szinonimák</a>
						<a class="dropdown-item" href="index.php?task=mertekegysegek">Mértékegységek</a>
						<a class="dropdown-item" href="index.php?task=cimkek">Cimkék</a>
					</div>
			</li>	
	      </ul>
	      <?php if (MULTIUSER) : ?>
	      <ul class="navbar-nav mr-auto mb-2 mb-lg-0 text-right">
			<?php if ($_SESSION['loged'] <= 0) : ?>
	        	<li class="nav-item">
	          		<a class="nav-link" href="index.php?task=login">
	          			<em class="fas fa-sign-in-alt"></em>&nbsp;Bejelentkezés</a>
	        	</li>
	        <?php endif; ?>
	        <?php if ($_SESSION['loged'] > 0) : ?>
				<li class="nav-item">
					<a class="nav-link" 
						href="index.php?task=useredit&id=<?php echo $_SESSION['loged']; ?>">
						<em class="fas fa-address-card"></em>	
						<var class=" <?php echo $_SESSION['logedGroup'] ?>">
							<?php echo $_SESSION['logedName']; ?>
							<?php if ($_SESSION['logedAvatar'] != '') : ?>
								<img src="images/users/<?php echo $_SESSION['logedAvatar']; ?>"
									style="height:34px; margin:0px;" />
							<?php endif; ?>		
						</var>	
					</a>
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

	<?php if ((trim($lastVerzio) > trim($fileVerzio))  & 
			  ((MULTIUSER == false) | (isAdmin()))) : ?>
	<div class="warning">
		<a href="index.php?task=upgrade1&version=<?php echo $lastVerzio; ?>" 
			class="btn btn-primary">
			Új verzó érhető el "<?php echo $lastVerzio; ?>" 
		</a>
	</div>	
	<?php endif; ?>	
	<?php echo '<div style="text-align:right">'.$fileVerzio.'&nbsp;</div>'; ?> 

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




