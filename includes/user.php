<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

class User {

	function __construct() {
	}
	
	public function login() {
		if (isset($_GET['msg'])) {
			echo '<div class="alert alert-danger">'.$_GET['msg'].'</div>';	
		}
		?>
		<div class="form">
			<h1>Bejelentkezés</h1>
			<form name="login" action="index.php?task=dologin" method="post">
			<input type="hidden" name="task" value="dologin" />
			<input type="text" name="username" placeholder="Bejelentkezési név" required="true" />
			<br /><br />
			<input type="password" name="password" placeholder="Jelszó" required="true" />
			<br /><br />
			<input type="submit" class="btn btn-primary" name="submit" value="Küld" />
			<br /><br />
<!--				
			<a href="https://netpolgar.hu/fblogin.php?state=<?php echo urlencode(SITEURL); ?>" class="btn" 
			   style="background-color:blue; color:white">
				<strong>f</strong> Belépés Facebook -al			
			</a>
			<br /><br />
			<a href="https://netpolgar.hu/googlelogin.php?state=<?php echo urlencode(SITEURL); ?>" class="btn" 
			   style="background-color:blue; color:white">
				<strong>g</strong> Belépés Google -al			
			</a>
-->
			</form>
		</div>
		<?php	
	}
	
	public function logout() {
		$_SESSION['loged'] = -1;
		$_SESSION['logedName'] = 'guest';
		?>
		<script>
				document.location="index.php";		
		</script>
		<?php			
	}
	
	public function regist() {
		if (isset($_GET['msg'])) {
			echo '<div class="alert alert-danger">'.$_GET['msg'].'</div>';	
		}
		?>
		<div class="form">
			<h1>Regisztrálás</h1>
			<form name="registration" action="index.php?task=doregist" method="post">
			<input type="hidden" name="task" value="doregist" />
			<input type="text" name="username" placeholder="Bejelentkezési név" required="true" />
			<br /><br />
			<input type="password" name="password" placeholder="Jelszó" required="true" />
			<br /><br />
			<input type="password" name="password2" placeholder="Jelszó ismét" required="true" />
			<br /><br />
			<input type="submit" class="btn btn-primary" name="submit" value="Küld" />
			</form>
			<p>Figyelem! e-mail címet nem tárolunk. 
			Így a rendszer semmilyen személyes adatot nem kezel, a GDPR hatályán kivül esik.
			Viszont ennek következtében jelszó emlékeztető küldésére nincs lehetőség,
			tehát a jelszót jól jegyezd meg!</p>
		</div>
		<?php	
	}
	
	public function dologin() {
		$db = new Query('users');
		$db->exec('CREATE TABLE IF NOT EXISTS users (
			    id int AUTO_INCREMENT,
			    username varchar(32),
			    password varchar(128),
			    PRIMARY KEY (id),
			    KEY (username)
		)');
		$userName = $_POST['username'];
		$password = $_POST['password'];
		$error = '';
		$db->where('username','=','"'.$userName.'"');
		$rec = $db->first();
		
		// echo $db->getSql(); exit();
		
		if ($db->error != '') {
				$error = 'Nincs ilyen néven fiók! '.$db->error;
				?>
				<script>
					document.location="index.php?task=login&msg=<?php echo $error; ?>";		
				</script>
				<?php			
		} else {
			if ($rec->password != md5($password)) {
				$error = 'Nem jó jelszó!';
				?>
				<script>
					document.location="index.php?task=login&msg=<?php echo $error; ?>";		
				</script>
				<?php			
			} else {
				$_SESSION['loged'] = $rec->id;
				$_SESSION['logedName'] = $rec->username;
				?>
				<script>
					document.location="index.php";		
				</script>
				<?php			
			} 
		}	
	}
	
	public function doregist() {
		$db = new Query('users');
		$db->exec('CREATE TABLE IF NOT EXISTS users (
			    id int AUTO_INCREMENT,
			    username varchar(32),
			    password varchar(128),
			    PRIMARY KEY (id),
			    KEY (username)
		)');
		$userName = $_POST['username'];
		$password = $_POST['password'];
		$password2 = $_POST['password2'];
		$error = '';
		if ($password != $password2) {
			$error = 'A két jelszó nem azonos!';
			?>
			<script>
				document.location="index.php?task=regist&msg=<?php echo $error; ?>";		
			</script>
			<?php			
		} else {
			$db->where('username','=','"'.$userName.'"');
			$rec = $db->first();
			if ($db->error == '') {
				$error = 'Már van ilyen néven fiók!';
				?>
				<script>
					document.location="index.php?task=regist&msg=<?php echo $error; ?>";		
				</script>
				<?php			
			
			} else {
				$r = new Record();
				$r->username = $userName;
				$r->password = md5($password);
				$userId = $db->insert($r);
				$_SESSION['loged'] = $userId;
				$_SESSION['logedName'] = $userName;
				?>
				<script>
					document.location="index.php";		
				</script>
				<?php			
			}			
		}	
	}
}


?>