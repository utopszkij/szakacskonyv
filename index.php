<?php
session_start();
// namespace \RATWEB\DB;
include_once 'config.php';
include_once 'vendor/database/db.php';

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

include_once 'includes/osszegzes.php';
include_once 'includes/napimenu.php';
include_once 'includes/recept.php';
include_once 'includes/naptar.php';
include_once 'includes/user.php';
include_once 'includes/szovegek.php';
?>

<html lang="en">
<head>
  <meta>
    <meta charset="UTF-8">
    <title>Hetimenü</title>
    <meta name="viewport" content="width=1240px, initial-scale=1">
	 <!-- bootstrap -->	
	 <link rel="stylesheet" href="./vendor/bootstrap/css/bootstrap.min.css">
    <script src="./vendor/bootstrap/js/bootstrap.min.js"></script>
	 <!-- fontawesome --> 
	 <script src="./vendor/fontawesome/js/all.min.js"></script>
	 <link rel="stylesheet" href="./vendor/fontawesome/css/all.min.css">

	 <link rel="stylesheet" href="style.css">
</head>	 
<body>
<div class="container">
	<div class="row" id="header">
	&nbsp;
	</div>
	<nav>
		<div>
			    <div class="row">
			      <ul class="col-md-7">
			        <li>
			          <a class="nav-link" href="?task=home" v-on:click="click('home')">
			            <em class="fas fa-home"></em>&nbsp;
			          	Napi menük</a>
			        </li>
			        <li>
			          <a class="nav-link" href="?task=receptek" v-on:click="click('description')">
			            <em class="fas fa-book"></em>&nbsp;
			          	Receptek</a>
			        </li>
			        <li>
			          <a class="nav-link" href="?task=osszeg" v-on:click="click('description')">
			            <em class="fas fa-plus"></em>&nbsp;
			          	Összesítés</a>
			        </li>
					</ul>
					<?php if (MULTIUSER) : ?>
			      <ul class="col-md-5" style="text-align:right">
			        <li>	
		           	<?php if ($_SESSION['loged'] < 0) : ?>
		           </li>	
			        <li>
			          <a class="nav-link" href="?task=login" v-on:click="click('login')">
			            <em class="fas fa-sign-in-alt"></em>&nbsp;
			          	Bejelentkezés</a>
			        </li>
			        <?php endif; ?>
		           <?php if ($_SESSION['loged'] >= 0) : ?>
			           <li>
				          <var><?php echo $_SESSION['logedName']; ?></var>&nbsp;
				        </li>  
				        <li>
				          <a class="nav-link" href="?task=logout" v-on:click="click('logout')">
				            <em class="fas fa-sign-out-alt"></em>&nbsp;
				          	Kijelentkezés</a>
				        </li>
			        <?php endif; ?>
			        <li>
			          <a class="nav-link" href="?task=regist" v-on:click="click('regist')">
			            <em class="fas fa-key"></em>&nbsp;
			          	Regisztrálás</a>
			        </li>
					</ul>	
					<?php endif; ?>	    
			    </div>
		</div>	    
	</nav>
	<div class="page">
	<?php 
		$task ();
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




