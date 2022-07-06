<?php
session_start();

include_once 'config.php';
include_once 'vendor/database/db.php';
include_once('vendor/model.php');
include_once('vendor/view.php');
include_once('vendor/controller.php');
include_once('vendor/fw.php');
$fw = new Fw();

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

//+ ----------- verzio kezelés start ------------
importComponent('upgrade');
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
	<base href="<?php echo SITEURL; ?>/">
	<link rel="icon" type="image/x-icon" href="images/szakacs.png">
    <title>Hetimenü</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	 <!-- bootstrap -->	
	 <link rel="stylesheet" href="./vendor/bootstrap/css/bootstrap.min.css">
    <script src="./vendor/bootstrap/js/bootstrap.min.js"></script>
	<!-- vue -->
    <script src="./vendor/vue/vue.global.js"></script>
	<!-- fontawesome --> 
	<script src="./vendor/fontawesome/js/all.min.js"></script>
	<link rel="stylesheet" href="./vendor/fontawesome/css/all.min.css">

	<link rel="stylesheet" href="style.css?t=<?php echo $fileVerzio; ?>">
	
	<script type="text/javascript">
	    const { createApp } = Vue; 
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
		<?php 
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
				$task = $fw->task;
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
		<?php 
			view('footer',[],'footer'); 
		?>
	</div>
</body>
</html>
