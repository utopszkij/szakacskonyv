
<?php					
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

/*

a githubról file elérési példa includes/napimenu.php:

https://raw.githubusercontent.com/utopszkij/szakacskonyv/main/includes/napimenu.php

a github/readm.md -t használja:
## verzió v#.#
... 
### *************

*/
class Upgrade {

	protected $github =       'https://raw.githubusercontent.com/utopszkij/szakacskonyv/main/';
	protected $githubReadme = ''; // __constructor állítja be
	public $branch = 'main';
	protected $msg = '';
	protected $errorCount = 0;
	protected $info = '';

	function __construct() {
		// fejlesztő környezetben ?branch=xxx URL paraméterrel cserélhető
		// a github alapértelmezett "main" branch
		if (isset($_GET['branch'])) {
			$this->branch = $_GET['branch'];
			$_SESSION['branch'] = $this->branch;
			$this->github = 'https://raw.githubusercontent.com/utopszkij/szakacskonyv/'.$this->branch.'/';
		} else if (isset($_SESSION['branch'])) {
			$this->branch = $_SESSION['branch'];
			$_SESSION['branch'] = $this->branch;
			$this->github = 'https://raw.githubusercontent.com/utopszkij/szakacskonyv/'.$this->branch.'/';
		}
		$this->githubReadme = $this->github.'readme.md';
	}

	/**
	 * verzió átalakitása v##.##.## formára (az összehasonlíthatóság kedvéért)
	 */
	public static function versionAdjust(string $version):string {
		$w = explode('.',substr($version,1,100));
		foreach ($w as $i => $w1) {
			if (strlen($w1) < 2) {
				$w[$i] = ' '.$w1;
			}
		}
		return 'v'.implode('.',$w);
	}

	/**
	 * last verzio olvasása github readme -ből
	 */
	public function getLastVersion() {
		$result = 'v0.0';
		$lines = file($this->githubReadme);
		// keresi az új verio sort
		for ($i=0; (($i<count($lines)) & 
		            (strpos(strtolower($lines[$i]), '# verzió ') <= 0)); $i++) {
						//echo 'ciklusban '.$lines[$i].'<br>';
		}
		// echo 'ciklus után '.$lines[$i].'<br>'; exit();
		if ($i < count($lines)) {
			$w = explode(' ', strtolower($lines[$i]));
			if (count($w) > 2) {
				$result = trim($w[2]);
			}	
		}
		return $result;
	}
	
	protected function updateFile($path) {
		try {
			if (!is_dir(dirname(DOCROOT.'/'.$path))) {
				mkdir(dirname(DOCROOT.'/'.$path),0777);
			}
			if (file_exists(DOCROOT.'/'.$path.'.old')) {
				unlink(DOCROOT.'/'.$path.'.old');
			}	
			rename(DOCROOT.'/'.$path, 
			       DOCROOT.'/'.$path.'.old');
			$this->downloadFile($path);
			// végül is hiba esetén jól jöhet az old file... unlink(DOCROOT.'/'.$path.'.old');
		} catch (Exception $e) {	
			$this->errorCount++;
			$this->msg .= 'ERROR update '.$path.' '.JSON_encode($e).'<br>';
		}		
	}

	protected function downloadFile($path) {
		try {
			if (!is_dir(dirname(DOCROOT.'/'.$path))) {
				mkdir(dirname(DOCROOT.'/'.$path),0777);
			}
			$lines = file($this->github.$path);		
			$fp = fopen(DOCROOT.'/'.$path,'w+');
			fwrite($fp, implode("",$lines));
			fclose($fp);
		} catch (Exception $e) {	
			$this->errorCount++;
			$this->msg .= 'ERROR download '.$path.' '.JSON_encode($e).'<br>';
		}		
	}

	protected function delFile($path) {
		if ((strpos($path,'images/') === false) & (file_exists($path))) { 
			unlink($path);
		}
	}

	/**
	 * adatbázis verzió lekérdezése
	 * @return string
	 */
	public function getDBVersion():string {
		$q = new Query('receptek');
		$q->exec('create table if not exists dbverzio (
			verzio varchar(32)
		)');
		$q = new Query('dbverzio');
		$w = $q->first();
		if (isset($w->verzio)) {
			$dbverzio = $w->verzio;
		} else {
			$dbverzio = 'v0.0';
			$r = new Record();
			$r->verzio = 'v0.0';
			$q->insert($r);
		}
		return $dbverzio;
	}

	/**
	 * könyvtár írható? kiirása a képernyőre
	 */
	protected function echoWritable(string $path) {
		if (is_writable($path.'/index.php')) {
			echo $path.' írható<br />';
		} else {
			echo $path.' nem írható<br>';
		}
	}
	
	/**
	 * változott fájlok listázása
	 * GET param: version
 	*/
	public function upgrade1() {
		$version = $_GET['version'];
		// github -on lévő readme.md -ből változás infó olvasása
		$files = $this->getNewFilesList($this->githubReadme, $version);
		
		?>
		<div class="upgrade">
			<h2>Új verzió <?php echo $version; ?></h2>
			<div><?php echo $this->info; ?></div>
			<p> </p>
			<div class="changedFiles">
				<?php $actions = $this->listChangedFiles(); ?>
			</div>
			<div style="background-color:silver; padding:10px;">
				<?php
				$this->echoWritable(DOCROOT);
				$this->echoWritable(DOCROOT.'/includes');
				$this->echoWritable(DOCROOT.'/vendor');
				$this->echoWritable(DOCROOT.'/images');
				$this->echoWritable(DOCROOT.'/includes/controllers');
				$this->echoWritable(DOCROOT.'/includes/models');
				$this->echoWritable(DOCROOT.'/includes/views');
				?>
			</div>
			<p> </p>
			<?php if ($actions > 0) : ?>
				<p>
					<a class="btn btn-secondary" href="index.php">Késöbb</a>&nbsp;
					<a class="btn btn-secondary" href="index.php?task=upgrade2&version=<?php echo $version; ?>">
						A fájlok frissitése most</a>
					<a class="btn btn-secondary" href="index.php?task=upgrade3&version=<?php echo $version; ?>">
						A fájlok frissitést megcsináltam</a>
				</p>				
				<p><strong>A "fájlok frissitése most" funkció csak akkor használható, ha a web szervernek joga 
					van a könyvtárak, fájlok írásához, törléséhez! 
					Ennek a funkciónak a használata előtt csinálj mentést a működő program változatról!</strong>
				</p>
			<?php else : ?>
				<p>
					<a class="btn btn-secondary" href="index.php?task=upgrade3&version=<?php echo $version; ?>">
						OK
					</a>
				</p>
			<?php endif; ?>
		</div>	
		<?php
	}

	/**
	 * változott fájlok frissitése
	 * GET: version
	 * 
	 * TEST egyenlőre nem aktiv
	 * 
	 */
	public function upgrade2() {
		error_reporting(E_ERROR | E_PARSE);
		$this->errorCount = 0;
		try {	
			$this->upgradeChangedFiles();
		} catch (Exception $e) {	
			$this->errorCount++;
			$this->msg = JSON_encode($e);
		};	
		if ($this->errorCount == 0) {
			echo '
			<div>
				<p>Fájlok frissitése megtörtént</p>
				<a class="btn btn-secondary" href="index.php">
				Tovább
				</a>
			</div>
			';
		} else {
			echo '<p>Hiba lépett fel a fájlok frissitése közben!</p>'.$this->msg;
		}
	}

	/**
	 * file download kézzel meg lett csinálva
	 * GET param: version
	 */
	public function upgrade3() {
		?>
		<script>location="index.php";</script>
		<?php
	}


	/**
	 * Változott  infó olvasása a redame.md -ből
	 */
	protected function getNewFilesList(string $fileUrl, string $newVersion): array {
		$result = [];
		$lines = file($fileUrl);

		// keresi az új verzio sort
		for ($i=0; (($i<count($lines)) & 
		        (strpos(strtolower($lines[$i]), ' verzió '.strtolower($newVersion)) <= 0)); $i++) {
		}

		// keresi a ### sort
		for ($j=$i+1; (($j<count($lines)) & (substr($lines[$j],0,4) != '### ')); $j++) {
			$this->info .= $lines[$j].'<br />';
		}

		/* olvassa a változott fájlokat
		for ($k=$j+1; $k<count($lines); $k++) {
			if (substr($lines[$k],0,1)=='-') {
				$result[] = trim(substr($lines[$k],1,100));
			} else {
				$k = count($lines); // kiléptet a ciklusból
			}
		}
		*/
		return $result;
	} 

	protected function do_v1_4($dbverzio) {
		if ($this->versionAdjust($dbverzio) < 'v 1. 4') {
			$q = new Query('users');
			$q->exec('CREATE TABLE IF NOT EXISTS `cimkek` (
				`id` int NOT NULL AUTO_INCREMENT,
				`cimke` varchar(80),
				PRIMARY KEY (`id`)
			  ) DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci
			');
			if ($q->error != '') {
				echo $q->error; exit();
			}
			$q->exec("
				insert into cimkek (cimke) values
				('Desszert'),('Diétás'),('Előétel'),
				('Főétel'),('Főzelék'),('Glutén mentes'),
				('Hal'),('Italok'),('Köret'),
				('Leves'),('Reggeli'),('Saláta'),
				('Sertés'),('Sütemény'),('Szárnyas'),
				('Tészta'),('Marha'),('Vad'),
				('Vegán'),('Vegetáriánus');
			");
			if (file_exists('includes/cimkek.txt')) {
				unlink('includes/cimkek.txt');
			}	
			$q = new Query('dbverzio');
			$r = new Record();
			$r->verzio = 'v1.4';
			$q->where('verzio','<>','')->update($r);
			if ($q->error != '') {
				echo $q->error; exit();
			}
		}	
	}

	protected function do_v1_3($dbverzio) {
		if ($this->versionAdjust($dbverzio) < 'v 1. 3') {
			$q = new Query('dbverzio');
			$q->exec('CREATE TABLE IF NOT EXISTS `szinonimak` (
				`id` int NOT NULL AUTO_INCREMENT,
				`mit` varchar(80) comment "ezt cseréli",
				`mire` varchar(80) comment "erre",
				PRIMARY KEY (`id`),
				KEY `szinonimak_mit` (`mit`)
			  ) DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci
			');
			if ($q->error != '') {
				echo $q->error; exit();
			}
			$q->exec("INSERT INTO `szinonimak` VALUES 
			(2,'mokkáskanál','mk'),(3,'mokkás kanál','mk'),(4,'kávéskanál','kk'),
			(5,'kávés kanál','kk'),(6,'teáskanál','tk'),(7,'teás kanál','tk'),
			(8,'gyermekkanál','gyk'),(9,'gyermek kanál','gyk'),(10,'evőkanál','ek'),
			(11,'kávéscsésze','kcs'),(12,'kávés csésze','kcs'),(13,'teáscsésze','tcs'),
			(14,'teás csésze','tcs'),(15,'cs','csomag'),(16,'mély tányér','mélytányér'),
			(17,'púpozott evőkanál','púpozott_ek'),(18,'csapott evőkanál','csapott_ek'),
			(19,'krumpli','burgonya'),(20,'gyökér','fehér répa'),
			(21,'pirospaprika','fűszerpaprika'),(22,'piros paprika','fűszerpaprika'),
			(23,'fűszer paprika','fűszerpaprika'),(24,'őrölt paprika','fűszerpaprika'),
			(25,'kiskanál','tk'),(26,'kis kanál','tk'),(27,'darab','db');
			");
			if ($q->error != '') {
				echo $q->error; exit();
			}

			$q->exec('CREATE TABLE IF NOT EXISTS `mertekegysegek` (
				`id` int NOT NULL AUTO_INCREMENT,
				`nev` varchar(80) comment "mértékegység neve",
				PRIMARY KEY (`id`)
			  )  DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci
			');
			if ($q->error != '') {
				echo $q->error; exit();
			}
			$q->exec("INSERT INTO `mertekegysegek` VALUES 
			(4,'mk'),(5,'kk'),(6,'tk'),(7,'gyk'),(8,'ek'),(9,'kcs'),
			(10,'tcs'),(11,'csomag'),(12,'db'),(13,'csapott_ek'),
			(14,'púpozott_ek'),(15,'szál'),(16,'pár'),(17,'szelet'),
			(18,'csipet'),(19,'késhegynyi'),(20,'újnyi'),(21,'kg'),
			(22,'dkg'),(23,'g'),(24,'l'),(25,'dl'),(26,'ml'),(27,'bögre'),
			(28,'csésze'),(29,'kis'),(30,'közepes'),(31,'nagy');
			");
			if ($q->error != '') {
				echo $q->error; exit();
			}

			$q = new Query('dbverzio');
			$r = new Record();
			$r->verzio = 'v1.3';
			$q->where('verzio','<>','')->update($r);
			if ($q->error != '') {
				echo $q->error; exit();
			}
			$q = new Query('users');
			$q->exec('CREATE TABLE IF NOT EXISTS `profilok` (
				`id` int NOT NULL AUTO_INCREMENT,
				`avatar` varchar(80),
				`realname` varchar(80),
				`email` varchar(80),
				`phone` varchar(32),
				`group` varchar(80),
				PRIMARY KEY (`id`)
			  ) DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci
			');
			if ($q->error != '') {
				echo $q->error; exit();
			}
			$q = new Query('users');
			$q->exec('insert into profilok
			select id,"","","","","admin"
			from users
			where username = "'.ADMIN.'"
			');
			if ($q->error != '') {
				echo $q->error; exit();
			}
		}	
	}

	protected function do_v1_2($dbverzio) {	
		if ($this->versionAdjust($dbverzio) < 'v 1. 2') {
			$q = new Query('receptek');
			$q->exec('alter table hozzavalok 
				add szme varchar(8) comment "számítási alap me",
				add szmennyiseg decimal(10,5) comment "számítási mennyiség"
			');
			if ($q->error != '') {
				echo $q->error; exit();
			}
			$q->exec('update hozzavalok 
			set szme = me, szmennyiseg = mennyiseg');
			if ($q->error != '') {
				echo $q->error; exit();
			}
			$q->exec('CREATE TABLE IF NOT EXISTS `atvaltasok` (
				`id` int NOT NULL AUTO_INCREMENT,
				`nev` varchar(80),
				`szorzo` decimal(10,5) comment "szorzo * me = 1 szme",
				`me` varchar(8),
				`szme` varchar(8),
				PRIMARY KEY (`id`),
				KEY `atvaltasok_nev` (`nev`),
				KEY `atvaltasok_me` (`me`)
			  )  DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci
			');
			if ($q->error != '') {
				echo $q->error; exit();
			}
			
			$q = new Query('dbverzio');
			$r = new Record();
			$r->verzio = 'v1.2';
			$q->where('verzio','<>','')->update($r);
			if ($q->error != '') {
				echo $q->error; exit();
			}
		}
	}	

	protected function do_v1_1($dbverzio) {	
		if ($this->versionAdjust($dbverzio) < 'v 1. 1') {
			$q = new Query('receptek');
			$q->exec('CREATE TABLE IF NOT EXISTS `comments` (
				`id` int NOT NULL AUTO_INCREMENT,
				`recept_id` int,
				`msg` text CHARACTER SET utf8mb3 COLLATE utf8_hungarian_ci,
				`created_by` int DEFAULT NULL,
				`created_at` date DEFAULT NULL,
				`img0` varchar(80),
				`img1` varchar(80),
				`img2` varchar(80),
				PRIMARY KEY (`id`),
				KEY `comments_recept_id` (`recept_id`)
			  )  DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci
			');
			$q = new Query('dbverzio');
			$r = new Record();
			$r->verzio = 'v1.1';
			$q->where('verzio','<>','')->update($r);

		}
	}	

	/**
	 * szükség szerint adatbázis alterek, új táblák létrehozása
	 * adatbázisban tárolt dbverzio frissitése
	 * @param string $dbverzio jelenlegi telepitett adatbázis verzió
	 */
	public function dbUpgrade(string $dbverzio) {
		if ($this->versionAdjust($dbverzio) < 'v 0. 1') {
			$q = new Query('receptek');
			$q->exec('alter table receptek 
				add created_at date
			');
			$q = new Query('dbverzio');
			$r = new Record();
			$r->verzio = 'v0.1';
			$q->where('verzio','<>','')->update($r);
		}
		if ($this->versionAdjust($dbverzio) < 'v 0. 3') {
			$q = new Query('receptek');
			$q->exec('alter table receptek 
				add energia varchar(32),
				add elkeszites int,
				add adag int
			');
			$q->exec('update receptek set energia = 0, elkeszites = 0, adag = 4');
			$q->exec('create table if not exists recept_cimke ( 
				recept_id int,
				cimke varchar(64),
				KEY `recept_cimke_id` (`recept_id`),
				KEY `recept_cimke_cimke` (`cimke`)
				) DEFAULT CHARSET=utf8mb3 COLLATE=utf8_hungarian_ci
			');
			$q = new Query('dbverzio');
			$r = new Record();
			$r->verzio = 'v0.3';
			$q->where('verzio','<>','')->update($r);
		}
		$this->do_v1_1($dbverzio);
		$this->do_v1_2($dbverzio);
		$this->do_v1_3($dbverzio);
		$this->do_v1_4($dbverzio);
		// ide jönek a későbbi verziokhoz szükséges db alterek növekvő verzió szerint
	}

	/**
	 * files.txt fájl feldolgozása
	 * @param string $fileName
	 * @return array [relativfilePath => dátum,...]
	 */
	protected function processFilesTxt(string $fileName): array {
		$result = [];
		$lines = file($fileName);
		$path = '';
		$fname = '';
		$date = '';
		if (is_array($lines)) {
			foreach ($lines as $line) {
				if (substr($line,0,1) == '.') {
					$path = trim(str_replace(':','',$line)).'/';
					$path = str_replace('./','',$path);
				} else if (trim($line) != ''){
					$line = preg_replace('/\ +/i','|',trim($line),6);
					$line = str_replace("\n",'',$line);
					$line = str_replace('\n','',$line);
					$w = explode('|',$line,7);
					if (count($w) > 6) {
						if ((strpos($w[6],'.') > 0) & ($w[6] != 'config.php')) {
							$result[$path.$w[6]] = $w[5];
						}	
					}
				}
			}
		}
		return $result;
	} 

	/**
	 * A githubon lévő, -  és a telepitetten files.txt összehasonlitása, 
	 * eltérések képernyőre listázása
	 * @return elvégzendó müveletek száma
	 */
	public function listChangedFiles(): int {
		$result = 0;
		$myFiles = $this->processFilesTxt(DOCROOT.'/files.txt');
		if (is_array($myFiles)) {
			$gitFiles = $this->processFilesTxt($this->github.'files.txt');
			if (is_array($gitFiles)) {
				// új fájlok kivéve config.php
				foreach($gitFiles as $gitFile => $v) {
					if (!isset($myFiles[$gitFile]) & ($gitFile != 'config.php')) {
						echo '<p style="color:green"><em class="fas fa-plus-square"></em> Új '.$gitFile.'</p>';
						$result++;
					}
				}
				// változott fájlok kivéve config.php
				foreach($gitFiles as $gitFile => $v) {
					if (isset($myFiles[$gitFile]) & ($gitFile != 'config.php')) {
						if ($myFiles[$gitFile] != $gitFiles[$gitFile]) {
							echo '<p style="color:orange"><em class="fas fa-edit"></em> Változott '.$gitFile.'</p>';
							$result++;
						}	
					}
				}
				// törölt fájlok  kivéve config.php
				foreach($myFiles as $myFile => $v) {
					if (!isset($gitFiles[$myFile]) & ($myFile != 'config.php')) {
						echo '<p style="color:red"><em class="fas fa-minus-square"></em> Törölt '.$myFile.'</p>';
						$result++;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * A githubon lévő, -  és a telepitetten lévő files.txt összehasonlitása, 
	 * eltérések frissitése
	 */
	public function upgradeChangedFiles() {
		$myFiles = $this->processFilesTxt(DOCROOT.'/files.txt');
		if (is_array($myFiles)) {
			$gitFiles = $this->processFilesTxt($this->github.'files.txt');
			if (is_array($gitFiles)) {
				// új fájlok kivéve config.php
				foreach($gitFiles as $gitFile => $v) {
					if (!isset($myFiles[$gitFile]) & ($gitFile != 'config.php')) {
						$this->downloadFile($gitFile);
					}
				}
				// változott fájlok kivéve config.php
				foreach($gitFiles as $gitFile => $v) {
					if (isset($myFiles[$gitFile]) & ($gitFile != 'config.php')) {
						if ($myFiles[$gitFile] != $gitFiles[$gitFile]) {
							$this->updateFile($gitFile);
						}	
					}
				}
				// törölt fájlok  kivéve config.php
				foreach($myFiles as $myFile => $v) {
					if (!isset($gitFiles[$myFile]) & ($myFile != 'config.php')) {
						$this->delFile($myFile); 
					}
				}
			}
		}

	}	
}
?>
