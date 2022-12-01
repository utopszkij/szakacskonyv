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
$fileVerzio = 'v2.1.6';
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

?>
<html>
<head>
    <meta charset="UTF-8">
	<meta property="og:title"  content="<?php echo $title; ?>">
	<base href="<?php echo SITEURL; ?>/">
	<link rel="icon" type="image/x-icon" href="<?php echo SITEURL; ?>/images/szakacs.png" />
    <title><?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="A programba étel recepteket és napi menüket lehet kezelni.
Ezek alapján a program adott időszak összesített anyagszükségleteit tudja meghatározni. 
Ebből bevásárló listát lehat a program segitségével készíteni.
Tulajdonságok Recepthez hozzávalók, elkészítési leírás és kép vihető fel,
egy recepthez max 30 hozzávaló adható meg, a program támogatja a mindmegette.hu, receptneked.hu, 
topreceptek.hu, sutnijo.hu -ról történő adatátvételt, a receptek módosíthatóak, törölhetőek, ha 
a recepthez képet nem adunk meg akkor a program a recept neve alapján megpróbál a net-en képet 
keresni, a receptek kinyomtathatóak, napi menübe naponta max. 4 fogás vihető fel, megadható 
hány főre főzünk aznap, a napi menük módosíthatóak, törölhetőek, a számított hozzávaló összesítés 
(bevásárló lista), nyomtatás előtt módosítható (pl. törölhető amiből 'van a spájzban').
A receptekhez hozzászólást lehet írni (pl: megfőztem, jó ), a hozzászóláshoz max 3 db kép is 
csatolható (pl a saját 'alkotásom' fényképei). A hozzászólások és csatolt képek minden látogató 
számára láthatóak. Törölni, modosítani csak a feltöltő, a moderátorok és a rendszer adminisztrátorok 
tudják őket. Az össesítés optimális müködése érdekében a program egy 'szinonima szótárat' és 
'átváltási adatbázist' kezel. Ezek tartalmát csak a rendszer adminisztrátorok módosíthatják.
A program konfigurálható egyfelhasználós vagy többfelhasználós módba.
Több felhasználós módban mindenki csak a sajátmaga által felvitt napi menüket látja és ezeket 
kezelheti, az összesítés is ezek alapján készül. A recepteknél látja, használhatja a mások által 
felvitteket is, de modosítani, törölni csak a sajátmaga által felvitteket tudja. Illetve a 
rendszer adminisztrátorok és moderátorok módosíthatják, törölhetik az összes receptet. 
A hozzászólások mindenki számára láthatóak">
 	<meta name="keywords" content="recept, receptek, szakács, szakácskönyv, napi menü, összesítés, bevásárló lista, étel, ételek, sütemény, sütemények">
  	<meta name="author" content="Fogler Tibor">
	<!-- bootstrap -->	
	<link rel="stylesheet" href="<?php echo SITEURL; ?>/vendor/bootstrap/css/bootstrap.min.css" />
    <script src="<?php echo SITEURL; ?>/vendor/bootstrap/js/bootstrap.min.js"></script>
	<!-- vue -->
    <script src="<?php echo SITEURL; ?>/vendor/vue/vue.global.js"></script>
	<!-- axios -->
	<script src="<?php echo SITEURL; ?>/vendor/axios/axios.js"></script>
	<!-- fontawesome --> 
	<script src="<?php echo SITEURL; ?>/vendor/fontawesome/js/all.min.js"></script>
	<link rel="stylesheet" href="<?php echo SITEURL; ?>/vendor/fontawesome/css/all.min.css" />

	<link rel="stylesheet" href="<?php echo SITEURL; ?>/styles/admin.css?t=<?php echo $fileVerzio; ?>" />
	<link rel="stylesheet" href="<?php echo SITEURL; ?>/styles/style.css?t=<?php echo $fileVerzio; ?>" />
	<!-- multi language -->
	<?php
		if (defined('LNG')) {
			if (file_exists(__DIR__.'/languages/'.LNG.'.js')) {
				echo '<script src="'.SITEURL.'/languages/'.LNG.'.js"></script>';
			} else {
				echo '<script> tokens = {}; </script>';
			}	
		} else {
			if (file_exists(__DIR__.'/languages/hu.js')) {
				echo '<script src="'.SITEURL.'/languages/hu.js"></script>';
			} else {
				echo '<script> tokens = {}; </script>';
			}	
		}
	?>
	<script type="text/javascript">
		var rewrite = <?php echo (int)REWRITE; ?>;
        var siteurl = "<?php echo SITEURL; ?>"; 
	</script>	
	<script src="index.js"></script>
</head>	 
<body>
	<button onclick="topFunction()" id="scrolltotop" title="Fel a tetejére">
		<em class="fa fa-arrow-up"></em>
	</button>
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

	<?php
	// extra html -ek betöltése (pl extra js -ek belodolása)
	if (file_exists(__DIR__.'/includes/extras/'.$task.'.html')) {
		include __DIR__.'/includes/extras/'.$task.'.html';
	}
	?>

	<div class="container" id="container">
		<div class="row" id="header" onclick="document.location='index.php';"></div>
		
		<?php 
			if (($_SESSION['loged'] > 0) & ($_SESSION['logedAvatar'] == '')) {
				$_SESSION['logedAvatar'] = 'noavatar.png';
			}
			view('mainmenu',[
				'MULTIUSER' => MULTIUSER,
				'loged' => $_SESSION['loged'],
				'logedAvatar' => $_SESSION['logedAvatar'],
				'logedName' => $_SESSION['logedName'],
				'logedGroup' => $_SESSION['logedGroup'],
				'isAdmin' => isAdmin(),
				'lastVerzio' => Upgrade::versionAdjust($lastVerzio),
				'fileVerzio' => Upgrade::versionAdjust($fileVerzio)
				],'mainmenu'); 
		?>

		<div class="page">
			<?php
				$comp->$task ();			
			?>
		</div>

		<?php 
			view('footer',[
				'fileVerzio' => Upgrade::versionAdjust($fileVerzio)
			],'footer'); 
		?>
	</div>
	<script>
		if (document.cookie.search('cookieEnabled=2') >= 0) {
			document.write('<p id="cookieEnabled">"Süti" kezelés engedélyezve van. Letiltásához kattints ide:'+
			'<a href="index.php" onclick="setCookie(\'cookieEnabled\',0,100);">Letilt</a></p>');
		} else if (document.location.href.search('adatkezeles') < 0) {
			popupConfirm('Ennek a web oldalnak a használatához un. "munkamenet sütik" használtata szükséges.'+
			'<br />Lásd: <a href="index.php?task=adatkezeles">Adatkezelési leírás</a>'+
			'<br />Kérjük engedélyezd a "sütik" kezelését!',
			function() {
				setCookie('cookieEnabled',2,100);
				document.location='index.php';
			})
		}
	</script>	
</body>
<script type="text/javascript">
		// check in iframe 
		// az admin oldalon vannak iframe -be hivva, itt mindig a light téma kell és
		// apage header, footer nem kell
		if (window.self !== window.top) {
			// document.body.className = 'light';
			document.body.className = getCookie('theme');
			document.getElementById('header').style.display="none";
			document.getElementById('mainmenu').style.display="none";
			document.getElementById('footer').style.display="none";
			document.getElementById('scrolltotop').style.display="none";
			document.getElementById('cookieEnabled').style.display="none";
			document.getElementById('container').className="inIframe";
		} else {
			const currentTheme = getCookie("theme");
			var theme = '';
			if (currentTheme == "dark") {
				document.body.className = 'dark';
				theme = 'dark';
			} else if (currentTheme == "light") {
				document.body.className = 'light';
				theme = 'light';
			} else {
				document.body.className = 'light';
				theme = 'light';
			}
			setCookie("theme", theme,100);
		}

		// világos/sötét téma váltás
		function themeTogle() {
			const currentTheme = getCookie("theme");
			var theme = getCookie("theme");
			if (currentTheme == "dark") {
				document.body.className = 'light';
				theme = 'light';
			} else if (currentTheme == "light") {
				document.body.className = 'dark';
				theme = 'dark';
			} else {
				document.body.className = 'dark';
				theme = 'dark';
			}
			setCookie("theme", theme,100);
		}

		// mozgatható elemek
		dragElement(document.getElementById("popup"));

		// sessionId csokiba
		window.sessionId = "<?php echo session_id(); ?>";
		setCookie("sid","<?php echo session_id(); ?>", 500);

		// iframe elemek átméretezése a parent div mérethez
		var frames = document.getElementsByTagName("iframe");
		var sz = 0, max = 0;
		for (var i = 0; i < frames.length; i++) {
			max = frames[i].parentNode.getBoundingClientRect().width * 0.9;
			if (frames[i].width > max) {
				sz = max / frames[i].width;
				frames[i].width = Math.round(max);
				frames[i].height = Math.round(frames[i].height * sz);
			}
		}
</script>
</html>
