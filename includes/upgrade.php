<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

/*
a githubról file elérési példa includes/napimenu.php:

https://raw.githubusercontent.com/utopszkij/szakacskonyv/main/includes/napimenu.php

*/
class Upgrade {

	protected $github = 'https://raw.githubusercontent.com/utopszkij/szakacskonyv/main/';
	protected $githubReadme = 'https://raw.githubusercontent.com/utopszkij/szakacskonyv/main/readme.md';
	protected $msg = '';
	protected $errorCount = 0;
	protected $info = '';

	public function getLastVersion() {
		$result = 'v0.0';
		$lines = file($this->githubReadme);
		// keresi az új verio sort
		for ($i=0; (($i<count($lines)) & 
		            (strpos(strtolower($lines[$i]), '# verzió ') <= 0)); $i++) {
		}
		if ($i < count($lines)) {
			$w = explode(' ', strtolower($lines[$i]));
			if (count($w) > 2) {
				$result = $w[2];
			}	
		}
		return $result;
	}

	/**
	 * változott fájlok listázása
	 * GET param: version
	 */
	public function upgrade1() {
		$version = $_GET['version'];
		$files = $this->getNewFilesList($this->githubReadme, $version);
		?>
		<div class="upgrade">
			<h2>Új verzió <?php echo $version; ?></h2>
			<div><?php echo $this->info; ?></div>
			<ul>
			<?php foreach ($files as $file) :?>
				<?php if (file_exists(__DIR__.'/../'.$file)) : ?>
					<li>változott <?php echo $file; ?></li> 
				<?php else : ?>
					<li>új file <?php echo $file; ?></li> 
				<?php endif; ?>		
			<?php endforeach; ?>	
			</ul>
			<?php if (count($files) > 0) : ?>
				<p>
					<a class="btn btn-secondary" href="index.php">Késöbb</a>&nbsp;
					<a class="btn btn-secondary" href="index.php?task=upgrade3&version=<?php echo $version; ?>">
						A fájlok frissitést megcsináltam</a>
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
	 * Könyvtár irhatóvá tétele
	 */
	protected function setWritableDir($path) {
/*
		try {
			$w = fileperms($path);
			chmod($path, ($w || 0070)); // group irhatja
		} catch (Exception $e) {	
		}	
*/
	}

	/**
	 * könyvtár olvashatóvá tétele 
	 */
	protected function setReadonlyDir($path) {
/*
		try {
			$w = fileperms($path);
			chmod($path, ($w && 0755)); // group nem irhatja
		} catch (Exception $e) {	
		}	
*/
	}
	
	protected function updateFile($path) {
		try {
			$w = fileperms(__DIR__.'/../'.$path);
			chmod(__DIR__.'/../'.$path,($w || 0070)); // group irhatja
			//unlink(__DIR__.'/../'.$path);
			$this->downloadFile($path);
		} catch (Exception $e) {	
			$this->errorCount++;
			$this->msg .= 'ERROR update '.$path.' '.JSON_encode($e).'<br>';
		}		
	}

	protected function downloadFile($path) {
		try {
			$owner = stat(__DIR__.'/../index.php')['uid'];
			$lines = file($this->github.$path);		
			$fp = fopen(__DIR__.'/../'.$path.'.new','w+');
			fwrite($fp, implode("",$lines));
			fclose($fp);
			chown (__DIR__.'/../'.$path.'.new', $owner); // tulajdonosa ugyanaz mint az index.php -nek
			chmod(__DIR__.'/../'.$path.'.new',0555); // senki nem irhatja
		} catch (Exception $e) {	
			$this->errorCount++;
			$this->msg .= 'ERROR download '.$path.' '.JSON_encode($e).'<br>';
		}		
	}

	/**
	 * változott fájlok frissitése
	 * GET: version
	 */
	public function upgrade2() {
		error_reporting(E_ERROR | E_PARSE);
		$this->errorCount = 0;
		$this->msg = '';
		$lastVersion = $_GET['version'];
		$files = $this->getNewFilesList($this->githubReadme, $lastVersion);
		try {
			$this->setWritableDir('.');
			$this->setWritableDir('..');
			foreach ($files as $file) {
				if (file_exists(__DIR__.'/../'.$file)) {
					$this->updateFile($file); 
				} else {
					$this->downloadFile($file); 
				}		
			}
			$this->setReadonlyDir('.');
			$this->setReadonlyeDir('..');
		} catch (Exception $e) {	
			$this->errorCount++;
			$this->msg .= JSON_encode($e);
		}	
		if ($this->errorCount == 0) {
			?>
			<div>
			<h3><?php echo $lastVersion; ?></h3>
				<p>Fájlok frissitése megtörtént</p>
				<a class="btn btn-secondary" href="index.php?task=upgrade3&version=<?php echo $lastVersion; ?>">
				Tovább
				</a>
			</div>
			<?php
		} else {
			echo '<h3>'.$lastVersion.'</h3>
			<p>Hiba lépett fel a fájlok frissitése közben!</p>'.$msg;
		}
	}

	/**
	 * változás tárolása az adatbázisba
	 * GET param: version
	 */
	public function upgrade3() {
		$lastVerzio = $_GET['version'];
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
		
		// v.0.1 created_at mező a receptek -be
		if ($dbverzio < 'v0.1') {
			$q->exec('alter table receptek 
				add created_at date
			');
		}
		// ide jönek a későbbi verziokhoz szükséges db alterek növehvő verzió szerint

		// frissitett verzio szám tárolása az adatbázisba
		if ($lastVerzio > $dbverzio) {
			$q = new Query('dbverzio');
			$r = new Record();
			$r->verzio = $lastVerzio;
			$q->where('verzio','<>','')->update($r);
		}

		// redirect
		?>
		<script>location="index.php";</script>
		<?php
	}


	protected function getNewFilesList(string $fileUrl, string $newVersion): array {
		$result = [];
		$lines = file($fileUrl);

		// keresi az új verio sort
		for ($i=0; (($i<count($lines)) & 
		            (strpos(strtolower($lines[$i]), ' verzió '.strtolower($newVersion)) <= 0)); $i++) {
		}

		// keresi a ### sort
		for ($j=$i; (($j<count($lines)) & (substr($lines[$j],0,4) != '### ')); $j++) {
			$this->info .= $lines[$j].'<br />';
		}

		// olvassa a változott fájlokat
		for ($k=$j+1; $k<count($lines); $k++) {
			if (substr($lines[$k],0,1)=='-') {
				$result[] = trim(substr($lines[$k],1,100));
			} else {
				$k = count($lines); // kiléptet a ciklusból
			}
		}
		return $result;
	} 
}
?>
