
<?php					
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

/*

a githubról file elérési példa includes/napimenu.php:

https://raw.githubusercontent.com/utopszkij/szakacskonyv/main/includes/napimenu.php

a github/readm.md -t használja:
## erzió v#.#
... 
### változott fájlok
- index.php
- includes/..... 
- [del]includes/.... 

*/
class Upgrade {

	protected $github =       'https://raw.githubusercontent.com/utopszkij/szakacskonyv/main/';
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
		} catch (Exception $e) {	
			$this->errorCount++;
			$this->msg .= 'ERROR update '.$path.' '.JSON_encode($e).'<br>';
		}		
	}

	protected function downloadFile($path) {
		try {
			$lines = file($this->github.$path);		
			$fp = fopen(DOCROOT.'/'.$path.'.new','w+');
			fwrite($fp, implode("",$lines));
			fclose($fp);
		} catch (Exception $e) {	
			$this->errorCount++;
			$this->msg .= 'ERROR download '.$path.' '.JSON_encode($e).'<br>';
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
		$files = $this->getNewFilesList($this->githubReadme, $version);
		
		?>
		<div class="upgrade">
			<h2>Új verzió <?php echo $version; ?></h2>
			<div><?php echo $this->info; ?></div>
			<ul>
			<?php foreach ($files as $file) :?>
				<?php if (file_exists(DOCROOT.'/'.$file)) : ?>
					<li>változott <?php echo $file; ?></li> 
				<?php else : ?>
					<li>új file <?php echo $file; ?></li> 
				<?php endif; ?>		
			<?php endforeach; ?>	
			</ul>
			<?php if (count($files) > 0) : ?>
				<p>
					<a class="btn btn-secondary" href="index.php">Késöbb</a>&nbsp;
					<a class="btn btn-secondary" href="index.php?task=upgrade2&version=<?php echo $version; ?>">
						A fájlok frissitése most</a>
					<a class="btn btn-secondary" href="index.php?task=upgrade3&version=<?php echo $version; ?>">
						A fájlok frissitést megcsináltam</a>
				</p>
				<?php
				$this->echoWritable(DOCROOT);
				$this->echoWritable(DOCROOT.'/includes');
				$this->echoWritable(DOCROOT.'/vendor');
				$this->echoWritable(DOCROOT.'/images');
				$this->echoWritable(DOCROOT.'/includes/controllers');
				$this->echoWritable(DOCROOT.'/includes/models');
				$this->echoWritable(DOCROOT.'/includes/views');
				
				?>
				<p>A "fájlok frissitése most" funkció csak akoor használható, ha a web szervernek joga 
					van a könyvtárak, fájlok írásához, törléséhez!</p>
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
		$version = $_GET['version'];
		$files = $this->getNewFilesList($this->githubReadme, $version);
		foreach ($files as $file) {
			try {	
				if (substr($file,0,5) == '[del]') {
					$file = substr($file,5,200);
					if (file_exists(DOCROOT.'/'.$file)) {
						unlink(DOCROOT.'/'.$file);
					}
				} else if (file_exists(DOCROOT.'/'.$file)) {
					$this->updateFile($file); 
				} else {
					$this->downloadFile($file); 
				}		
			} catch (Exception $e) {	
				$this->errorCount++;
				$this->msg .= JSON_encode($e);
			}	
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
	 * file download kézzel meg lett csinálva
	 * GET param: version
	 */
	public function upgrade3() {
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
