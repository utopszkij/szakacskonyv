<?php
namespace RATWEB\DB;
session_start();
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
	          <a class="nav-link" href="index.php?task=receptek">
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




