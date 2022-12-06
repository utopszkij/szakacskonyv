<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
	<meta property="og:title"  content="<?php echo $title; ?>">
	<base href="<?php echo SITEURL; ?>/">
	<link rel="icon" type="image/x-icon" href="<?php echo SITEURL; ?>/images/szakacs.png" />
    <title><?php echo $title; ?></title>
	<meta property="og:image" content="<?php echo SITEURL; ?>/images/fejlec.png">
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
 	<meta name="keywords" content="recept, receptek, szakács, szakácskönyv, napi menü, összegit checkout -b sítés, bevásárló lista, étel, ételek, sütemény, sütemények">
  	<meta name="author" content="Fogler Tibor">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

	<!-- vue -->
    <script src="<?php echo SITEURL; ?>/vendor/vue/vue.global.prod.js"></script>
	<!-- axios -->
	<script src="<?php echo SITEURL; ?>/vendor/axios/axios.js"></script>

    <!-- Core Stylesheet -->
	<link rel="stylesheet" href="styles/delicious/css/style.css">
	<link rel="stylesheet" href="styles/delicious/index.css">
	<link rel="stylesheet" href="styles/default/admin.css">
	
	<!-- fontawesome --> 
	<script src="<?php echo SITEURL; ?>/vendor/fontawesome/js/all.min.js"></script>
	<link rel="stylesheet" href="<?php echo SITEURL; ?>/vendor/fontawesome/css/all.min.css">
	
	<!-- multi language -->
	<?php
	if (!defined('LNG')) {
		define('LNG','hu');
	}
	if (file_exists('languages/'.LNG.'.js')) {
			echo '<script src="languages/'.LNG.'.js"></script>';
	} else {
			echo '<script> tokens = {}; </script>';
	}	
	?>
	<script type="text/javascript">
		var rewrite = <?php echo (int)REWRITE; ?>;
        var siteurl = "<?php echo SITEURL; ?>"; 
	</script>	
	<script src="index.js"></script>
</head>

<body>
	<div id="scrolltotop"></div>
	<div id="popup" style="display:none">
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
	if (file_exists('includes/extras/'.$task.'.html')) {
		include 'includes/extras/'.$task.'.html';
	}
	?>
	
    <!-- Preloader -->
    <div id="preloader">
        <i class="circle-preloader"></i>
        <img src="styles/delicious/img/core-img/salad.png" alt="">
    </div>
    
    <!-- Search Wrapper -->
    <div class="search-wrapper">
        <!-- Close Btn -->
        <div class="close-btn"><i class="fa fa-times" aria-hidden="true"></i></div>

        <div class="container">
            <div class="row">
                <div class="col-12">
                    <form action="#" method="post">
                        <input type="search" name="search" placeholder="Type any keywords...">
                        <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ##### Header Area Start ##### -->
    <header class="header-area" id="header">

        <!-- Top Header Area -->
        <div class="top-header-area">
            <div class="container h-100">
                <div class="row h-100 align-items-center justify-content-between">
                    <!-- Breaking News -->
                    <div class="col-12 col-sm-6">
                        <div class="breaking-news">
                            <div id="breakingNewsTicker" class="ticker">
                                <ul>
                                    <li><a href="#">Hello!</a></li>
                                    <li><a href="#">Üdvözöllek a web oldalunkon.</a></li>
                                    <li><a href="#">Használd egészséggel!</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Top Social Info -->
                    <div class="col-12 col-sm-6">
                        <div class="top-social-info text-right">
                            <a href="#" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+location,'_blank'); false">
								<i class="fab fa-facebook" aria-hidden="true"></i></a>
                            <a href="#" onclick="window.open(https://twitter.com/intent/tweet?&url='+location, '_blank'); false">
								<i class="fab fa-twitter" aria-hidden="true"></i></a>
                            <a href="#" onclcik="window.open(https://www.linkedin.com/cws/share?url='+location, '_blank'); false">
								<i class="fab fa-linkedin" aria-hidden="true"></i></a>
                            <a href="#" onclick="window.open('https://mail.google.com/mail/?view=cm&body='+location,'_blank'); false">
								<i class="fa fa-envelope" aria-hidden="true"></i></a>
                            <a target="_blank" href="https://github.com/utopszkij/szakacskonyv"><i class="fab fa-github" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navbar Area -->
        <div class="delicious-main-menu">
            <div class="classy-nav-container breakpoint-off">
                <div class="container">
                    <!-- Menu -->
                    <nav class="classy-navbar justify-content-between" id="deliciousNav">

                        <!-- Logo -->
                        <a class="nav-brand" href="index.php">
							<img src="images/fejlec.png" alt="">
							<br /><var>BEFALOM</var>
						</a>

                        <!-- Navbar Toggler -->
                        <div class="classy-navbar-toggler">
                            <span class="navbarToggler"><span></span><span></span><span></span></span>
                        </div>

                        <!-- Menu -->
                        <div class="classy-menu">

                            <!-- close btn -->
                            <div class="classycloseIcon">
                                <div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
                            </div>

                            <!-- Nav Start -->
                            <div class="classynav">
                                <ul>
                                    <li><a href="<?php echo SITEURL; ?>/">Kezdőlap</a></li>
                                    <li><a href="<?php echo SITEURL; ?>/task/receptek/page/1">Receptek</a></li>
                                    <li><a href="<?php echo SITEURL; ?>/task/naptar">Napi menük</a></li>
                                    <li><a href="<?php echo SITEURL; ?>/task/osszeg">Összegzés</a></li>
                                    <li><a href="<?php echo SITEURL; ?>/task/blogs/page/1">Cikkek</a></li>
                                    <li><a href="<?php echo SITEURL; ?>/task/admin">Beállítások</a></li>
                                    <?php if ($_SESSION['loged'] > 0) : ?>
										<li><a href="#">
												<?php 
												if ($_SESSION['logedAvatar'] == '') {
													$_SESSION['logedAvatar'] = 'noavatar.png';
												}
												echo $_SESSION['logedName']; 
												?>
												<img class="avatar" src="images/users/<?php echo $_SESSION['logedAvatar']; ?>" />
											</a>
											<ul class="dropdown">
												<li><a href="<?php echo SITEURL; ?>/task/useredit/id/<?php echo $_SESSION['loged']; ?>">Profil</a></li>
												<li><a href="<?php echo SITEURL; ?>/task/logout">Kijelentkezés</a></li>
											</ul>
										</li>
                                    <?php else :?>
										<li><a href="<?php echo SITEURL; ?>/task/login">Belépés</a></li>
									<?php endif; ?>
                                </ul>
                            </div>
                            <!-- Nav End -->
                        </div>
                    </nav>
                </div>
            </div>
        </div>
        
        <!-- Navbar Area End -->
    </header>
    <!-- ##### Header Area End ##### -->
	
		<div class="page">
			<?php
				$comp->$task ();			
			?>
		</div>
	
    <!-- ##### Footer Area Start ##### -->
	<div id="footer">
					<!-- Bottom Social Info -->
                    <div class="col-12 col-sm-6">
                        <div class="top-social-info text-right">
                            <a href="#" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+location,'_blank'); false">
								<i class="fab fa-facebook" aria-hidden="true"></i></a>
                            <a href="#" onclick="window.open(https://twitter.com/intent/tweet?&url='+location, '_blank'); false">
								<i class="fab fa-twitter" aria-hidden="true"></i></a>
                            <a href="#" onclcik="window.open(https://www.linkedin.com/cws/share?url='+location, '_blank'); false">
								<i class="fab fa-linkedin" aria-hidden="true"></i></a>
                            <a href="#" onclick="window.open('https://mail.google.com/mail/?view=cm&body='+location,'_blank'); false">
								<i class="fa fa-envelope" aria-hidden="true"></i></a>
                            <a target="_blank" href="https://github.com/utopszkij/szakacskonyv"><i class="fab fa-github" aria-hidden="true"></i></a>
                        </div>
                    </div>

					<div style="text-align:right"><?php echo $fileVerzio; ?>&nbsp;</div>
					<div style="text-align:center">
						<hr>
						<a href="<?php echo SITEURL; ?>/task/impresszum">Imresszum</a>&nbsp;&nbsp;&nbsp;
						<a href="<?php echo SITEURL; ?>/task/blog/blog_id/9">Segédlet</a>&nbsp;&nbsp;&nbsp;
						<a href="<?php echo SITEURL; ?>/task/adatkezeles">Adatkezelési leírás</a>&nbsp;&nbsp;&nbsp;
						<a href="<?php echo SITEURL; ?>/task/visszaeles">Visszaélés jelzése</a>&nbsp;&nbsp;&nbsp;
						<a href="https://gnu.hu/gplv3.html" >Licensz</a>&nbsp;&nbsp;&nbsp;
						<a href="<?php echo SITEURL; ?>/task/velemenyek">Vélemények</a>&nbsp;&nbsp;&nbsp;
						<a href="https://github.com/utopszkij/szakacskonyv" target="_new" >
							Forrás program</a>&nbsp;&nbsp;&nbsp;
							<a href="<?php echo SITEURL; ?>/task/sponzor">Támogatás</a>
						<!-- div style="color:white;">Ⓒ 2022 {{ lng('COPYRIGHT') }}.&nbsp;&nbsp;&nbsp;</div -->
						<hr>
					</div>
					<div>
						Dizájn: 
						<img src="https://themewagon.com/wp-content/uploads/2021/10/colorlib-logo.png" style="width:32px;" />
						&nbsp;<a href="https://themewagon.com/author/kimlabs/">Colorlib </a>
					</div>
                    
  				   <!-- div style="text-align:center">
					  <button class="btn btn-toggle btn-secondary" 
						 type="button" onclick="themeTogle()">
						 <em class="fas fa-adjust"></em>&nbsp;
						 Világos/sötét mód váltás
					  </button>
				   </div -->
				   <div>&nbsp;</div>
				   <div>&nbsp;</div>
	</div>
	
    <!-- ##### All Javascript Files ##### -->
    <script src="styles/delicious/js/jquery/jquery-2.2.4.min.js"></script>
    <script src="styles/delicious/js/bootstrap/bootstrap.min.js"></script>
    <script src="styles/delicious/js/bootstrap/popper.min.js"></script>
    <script src="styles/delicious/js/plugins/plugins.js"></script>
    <script src="styles/delicious/js/active.js"></script>
	
    <script>
		if (window.self == window.top) {
			// az admin oldalon vannak iframe -be hivva, ilyenkor ez nem kell
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
		}
	</script>	
</body>
<script type="text/javascript">
		// check in iframe 
		// az admin oldalon vannak iframe -be hivva, itt mindig a light téma kell és
		// a page header, footer nem kell
		if (window.self !== window.top) {
			// document.body.className = 'light';
			document.body.className = getCookie('theme');
			document.getElementById('header').style.display="none";
			document.getElementById('footer').style.display="none";
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
		
		// set activ menu item
		for(var i = 0; i < document.links.length; i++) {
			if (document.links[i].href == document.location.href) {
				document.links[i].className = 'active';
			}
		}
</script>
</html>
