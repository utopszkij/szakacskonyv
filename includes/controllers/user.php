<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

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
		view('login',["msg" => $this->request->input('msg',''),
					  "SITEURL" => SITEURL,
					  "redirect" => $this->request->input('redirect','')]);
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
		view('regist',["flowKey" => $this->newFlowKey(),
						"msg" => $this->request->input('msg',''),
					   "SITEURL" => SITEURL,
					   "redirect" => $this->request->input('redirect','')]);
	}
	
	public function dologin() {
		$userName = $this->request->input('username');
		$password = $this->request->input('password');
		$redirect = $this->request->input('redirect');
		$recs = $this->model->getBy('username',$userName);
		if (count($recs) == 0) {
				$error = 'Nincs ilyen néven fiók! ';
				?>
				<script>
					document.location="index.php?task=login&msg=<?php echo $error; ?>&redirect=<?php echo urlencode($redirect) ?>";		
				</script>
				<?php			
		} else {
			$rec = $recs[0];
			//+ Tervezett funkció 
			$rec->locked = '';
			$rec->errorcount = 0;
			//- Tervezett funkció
			if ($rec->locked < (time()-(10*60))) {
				/* zárolás idő lejárt
				$rec->locekd = '';
				$rec->errorcount = 0;
				$this->model-save($rec);
				*/
			}	
			if ($rec->locked > (time()-(10*60))) {
				$error = '5 hibás belépési kisérlet miatt a fiók 10 percre zárolva van!';
				?>
				<script>
					document.location="index.php?task=login&msg=<?php echo $error; ?>&redirect=<?php echo urlencode($redirect) ?>";		
				</script>
				<?php			
			}
			if ($rec->password != md5($password)) {
				$error = 'Nem jó jelszó!';
				/* hiba számláló növelése a rekordban
				$rec->errorcount = $rec->errorcount + 1;
				if ($rec->errorcount == 5) {
					$rec->locked = time();
				}
				$this->model-save($rec);
				*/
				?>
				<script>
					document.location="index.php?task=login&msg=<?php echo $error; ?>&redirect=<?php echo urlencode($redirect) ?>";		
				</script>
				<?php			
			} else {
				$rec = $this->model->getById($recs[0]->id);
				/* adatbázisban hibaszámláló és locked nulázása
					$rec->errorcount = 0;
					$rec->locked='';
					$this->model-save($rec);
				*/

				$_SESSION['loged'] = $rec->id;
				$_SESSION['logedName'] = $rec->username;
				$_SESSION['logedAvatar'] = $rec->avatar;
				$_SESSION['logedGroup'] = $rec->group;
				?>
				<script>
					document.location="<?php echo SITEURL.'/'.base64_decode($redirect); ?>";		
				</script>
				<?php			
			} 
		}	
	}
	
	public function doregist() {
return;
		$db = new Query('users');
		$userName = $this->request->input('username');
		$password = $this->request->input('password');
		$password2 = $this->request->input('password2');
		$redirect = base64_decode($this->request->input('redirect'));
		$error = '';
		if (!$this->checkFlowKey($redirect)) {
			echo 'flowKey error'; exit();
		}
		if ($password != $password2) {
			$error = 'A két jelszó nem azonos!';
			?>
			<script>
				document.location="index.php?task=regist&msg=<?php echo $error; ?>&redirect=<?php echo urlencode($redirect) ?>";		
			</script>
			<?php
		} else if (($userName == '') | ($password == '')) {
			$error = 'Névet és jelszót meg kell adni!';
			?>
			<script>
				document.location="index.php?task=regist&msg=<?php echo $error; ?>&redirect=<?php echo urlencode($redirect) ?>";		
			</script>
			<?php
		} else {
			$db->where('username','=','"'.$userName.'"');
			$rec = $db->first();
			if (isset($rec->id)) {
				$error = 'Már van ilyen néven fiók!';
				?>
				<script>
					document.location="index.php?task=regist&msg=<?php echo $error; ?>&redirect=<?php echo urlencode($redirect) ?>";		
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
				if ($userName == ADMIN) {
					$r->group = 'admin';
				} else {
					$r->group = '';
				}	
				$userId = $this->model->save($r);
				$_SESSION['loged'] = $userId;
				$_SESSION['logedName'] = $userName;
				$_SESSION['logedAvatar'] = $r->avatar;
				$_SESSION['logedGroup'] = $r->group;
				?>
				<script>
					document.location="<?php echo SITEURL.'/'.base64_decode($redirect); ?>";		
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
		if (($record->password == '') & ($record->id == 0)) {
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
		if ($action == 'delete') {
			if (($_SESSION['loged'] <= 0) | 
				(($_SESSION['logedGroup'] != 'admin') & ($_SESSION['loged'] != $record->id))) {
				$result = false;  // nincs bejelentkezve, vagy nem jogosult erre
			}
		} else if ($action == 'edit') {
			if (($_SESSION['loged'] <= 0) | 
				(($_SESSION['logedGroup'] != 'admin') & ($_SESSION['loged'] != $record->id))) {
					$result = false;  // nincs bejelentkezve, vagy nem jogosult erre
			}
		} else {
			$result = true;
		}	
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
     * user tárolása POST -ban: user és profil adatok és browserUrl
	 * - group -ot csak admin modosithatja
	 * - password adatokat admin és a record->id user modosithatja
     */
    public function usersave() {
		if (!$this->checkFlowKey('index.php')) {
			echo 'flowKey error'; exit();
		}
		$id = $this->request->input('id',0);
		if ($id > 0) {
			$record = $this->model->getById($id);
			if (!isset($record->id)) {
				return; // nincs ilyen rekord
			}
			if (!$this->accessRight('edit',$record)) {
				return; // nem jogosult erre
			}
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
        	$this->model->save($record); 
		}	
		if ($this->session->input('loged') == $id) {
			$record = $this->model->getById($id);
			$this->session->set('logedAvatar',$record->avatar);
		}
		$this->userek();
    }
  
    /**
     * user törlése GET-ben: id
     */
    public function userdelete() {
		$id = $this->request->input('id',0, INTEGER);
		$record = $this->model->getById($id);
		if (!isset($record->id)) {
			return; // nincs ilyen user
		}
		if (!$this->checkFlowKey('index.php')) {
			echo 'flowKey error'; exit();
		}
		if (!$this->accessRight('delete',$record)) {
			echo 'accssRight error'; exit();
			return;  // nincs bejelentkezve, vagy nem jogosult erre
		}
		$record->username = 'törölt'.$record->id;
		$record->password= md5(rand(100000,9999999));
		$record->avatar = '';
		$record->realname = '';
		$record->phone = '';
		$record->email = '';
		$record->password = md5(rand(10000,99000));
		$record->password2 = $record->password;
		$this->model->save($record); 
		if ($_SESSION['loged'] == $id) {
			$_SESSION['loged'] = -1;
			$_SESSION['logedName'] = 'guest';
			$_SESSION['logedAvatar'] = '';
			$_SESSION['logedGroup'] = '';
			echo '<script>
				document.location="index.php";
			</script>';
		} else{
			$this->userek();
		}

    }    
}


?>
