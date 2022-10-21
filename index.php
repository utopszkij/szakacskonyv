<?php
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

$fw = new Fw();

//+ ----------- verzio kezelés start ------------
$fileVerzio = 'v2.0.4';
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
<html lang="en">
<head>
  <meta>
    <meta charset="UTF-8">
	<meta property="og:title"  content="<?php echo $title; ?>" />
	<base href="<?php echo SITEURL; ?>/">
	<link rel="icon" type="image/x-icon" href="images/szakacs.png">
    <title><?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	 <!-- bootstrap -->	
	 <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<!-- vue -->
    <script src="vendor/vue/vue.global.js"></script>
	<!-- axios -->
	<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
	<!-- fontawesome --> 
	<script src="vendor/fontawesome/js/all.min.js"></script>
	<link rel="stylesheet" href="vendor/fontawesome/css/all.min.css">

	<link rel="stylesheet" href="style.css?t=<?php echo $fileVerzio; ?>">
	<!-- multi language -->
	<?php
		if (defined('LNG')) {
			if (file_exists(__DIR__.'/languages/'.LNG.'.js')) {
				echo '<script src="languages/'.LNG.'.js"></script>';
			} else {
				echo '<script> tokens = {}; </script>';
			}	
		} else {
			if (file_exists(__DIR__.'/languages/hu.js')) {
				echo '<script src="languages/hu.js"></script>';
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

	<div class="container">
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
		<p style="text-align:center">
			<button class="btn btn-toggle btn-secondary" 
				type="button" onclick="themeTogle()">
				<em class="fas fa-adjust"></em>&nbsp;
				Világos/sötét mód váltás
			</button>
		</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
	</div>
</body>
<script type="text/javascript">
		// világos/sötét téma
		
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


</script>
</html>
