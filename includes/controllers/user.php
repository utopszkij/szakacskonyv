<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once __DIR__.'/controller.php';
include_once __DIR__.'/../models/usermodel.php';

class User extends Controller {

	function __construct() {
		parent::__construct();
		$this->model = new UserModel();
        $this->name = "user";
        $this->browserURL = 'index.php?task=userek';
        $this->addURL = 'index.php?task=regist';
        $this->editURL = 'index.php?task=useredit';
        $this->browserTask = 'userek';
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
			<a href="https://netpolgar.hu/auth/facebook?state=<?php echo urlencode(SITEURL); ?>" class="btn" 
			   style="background-color:blue; color:white">
				<strong>f</strong> Belépés Facebook -al			
			</a>
			<br /><br />
			<a href="https://netpolgar.hu/auth/google?state=<?php echo urlencode(SITEURL); ?>" class="btn" 
			   style="background-color:blue; color:white">
				<strong>g</strong> Belépés Google -al			
			</a>
			</form>
			<div class="alert alert-info">
				Bejelentkezés után a felső menöben lévő belépési nevedre kattintva,  
				a "profil" képernyőn jelszót változtathatsz, avatar képet tölthetsz fel.
			</div>
		</div>
		<?php	
	}
	
	public function logout() {
		$_SESSION['loged'] = -1;
		$_SESSION['logedName'] = 'guest';
		$_SESSION['logedAvatar'] = '';
		$_SESSION['logedGroup'] = '';
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
			<p><strong>Figyelem! e-mail címet nem tárolunk. 
			Így a rendszer semmilyen személyes adatot nem kezel, a GDPR hatályán kivül esik.
			Viszont ennek következtében jelszó emlékeztető küldésére nincs lehetőség,
			tehát a jelszót jól jegyezd meg!</strong></p>
			<div class="alert alert-info">
				Bejelentkezés után a felső menöben lévő belépési nevedre kattintva,  
				a "profil" képernyőn jelszót változtathatsz, avatar képet tölthetsz fel.
			</div>
		</div>
		<?php	
	}
	
	public function dologin() {
		$userName = $_POST['username'];
		$password = $_POST['password'];
		$recs = $this->model->getBy('username',$userName);
		if (count($recs) == 0) {
				$error = 'Nincs ilyen néven fiók! '.$db->error;
				?>
				<script>
					document.location="index.php?task=login&msg=<?php echo $error; ?>";		
				</script>
				<?php			
		} else {
			$rec = $recs[0];
			if ($rec->password != md5($password)) {
				$error = 'Nem jó jelszó!';
				?>
				<script>
					document.location="index.php?task=login&msg=<?php echo $error; ?>";		
				</script>
				<?php			
			} else {
				$rec = $this->model->getById($recs[0]->id);
				$_SESSION['loged'] = $rec->id;
				$_SESSION['logedName'] = $rec->username;
				$_SESSION['logedAvatar'] = $rec->avatar;
				$_SESSION['logedGroup'] = $rec->group;
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
				$r->id = 0;
				$r->username = $userName;
				$r->password = md5($password);
				$r->avatar = '';
				$r->realname = '';
				$r->email = '';
				$r->phone = '';
				$r->group = '';
				$userId = $this->model->save($r);
				$_SESSION['loged'] = $userId;
				$_SESSION['logedName'] = $userName;
				$_SESSION['logedAvatar'] = '';
				$_SESSION['logedGroup'] = '';
				?>
				<script>
					document.location="index.php";		
				</script>
				<?php			
			}			
		}	
	}

	// === profil kezelés v1.3 ===
  
	/**
     * rekord ellenörzés a profil modositásnál van hivva
     * @param Record $record
     * @return string üres vagy hibaüzenet
     */
    protected function validator($record):string {
        $result = '';
		if ($record->password == '') {
			$result = 'A jelszó nem lehet üres';
		} else if ($record->password != $record->password2) {
			$result = 'A két jelszó nem egyforma';
		}
        return $result;
    }


    /**
     * bejelentkezett user jogosult erre?
	 * a forman vannak szükség esetén letiltva a modositó mezők 
     * @param string $action new|edit|delete
     * @return bool
     */
    protected function  accessRight(string $action, $record):bool {
		$result = true;
        return $result;
    }

	
	/**
     * user browser GET -ben: page, order, filter
	 * - adminok a névre kattintva modosithatnak,
	 * - mások a sajátjukra kattintva modosithatnak, másra kattintva csak megnézhetnek
	 */
    public function userek() {
        $this->items('username');
    }
    
    /**
     * user editor/show képernyő GET -ben id
	 * a userform képernyő oldja meg:
	 * - saját adataiból password, password2 modositható
	 * - admin modosithat password, password2, group
	 * - mások semmit nem modosithatnak
     */
    public function useredit() {
        $this->edit();
    }     

    /**
     * user tárolása POST -ban: user és profil adatok
	 * - group -ot csak admin modosithatja
	 * - password adatokat admin és a record->id user modosithatja
     */
    public function usersave() {
		$id = $this->request->input('id',0);
		if ($id > 0) {
			$record = $this->model->getById($id);
			$record->password2 = $record->password;
		} else {
			$record = $this->model->emptyRecord();
			$record->password2 = '';
		}
        $record->id = $id;
        $password = trim($this->request->input('password',$record->password));
		if (($password != '') & 
		    ((isAdmin() | ($record->id == $this->loged)))) {
			$record->password = md5($password);
			$record->password2 = md5($this->request->input('password2',''));
		}	
		if (isAdmin()) {
			$record->group = trim($this->request->input('group',$record->group));
		}	
        $record->realname = trim($this->request->input('realname',$record->realname));
        $record->email = trim($this->request->input('email',$record->email));
        $record->phone = trim($this->request->input('phone',$record->phone));
		if ((isAdmin() | $this->loged == $record->id)) {
        	$this->save($record); 
		}	
    }
  
    /**
     * szinonima törlése GET-ben: id
     */
    public function userdelete() {
		$record = $this->model->getById($id);
		$record->username = 'törölt'.$record->id;
		$record->password= md5(rand(100000,9999999));
		$record->avatar = '';
		$record->realname = '';
		$record->phone = '';
		$record->email = '';
        $this->save($record); 
    }    


}


?>