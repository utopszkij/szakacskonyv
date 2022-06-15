<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once __DIR__.'/controller.php';
include_once __DIR__.'/../atvesz.php';
include_once __DIR__.'/../models/receptmodel.php';
include_once __DIR__.'/../models/commentmodel.php';

class Recept extends Controller{
	protected $model;

	function __construct() {
		parent::__construct();
		$this->model = new ReceptModel();
	}	
	
	// $_GET['id']
	public function receptdelete() {
		// normál user csak a saját maga által felvittet törölheti
		// system admin mindent törölhet
		$recept = $this->model->getById($this->request->input('id',0,INTEGER));
		if (($recept->created_by != $this->session->input('loged')) &
		    (!$this->logedAdmin)) {
		    $this->receptek();
		} else {
			$this->model->delById($this->request->input('id',0,INTEGER));
			$this->model->deleteHozzavalok($this->request->input('id'));
			$this->model->deleteCimkek($this->request->input('id'));
			$this->receptek();
		}
	}
	
	public function receptsave() {
		// get -ben: id, leiras, hozzvalok0, mennyiseg0, me0, hozzavalok1,....
		if ($this->session->input('loged') < 0) {
			echo '<div class="alert alert-danger">Recept felviteléhez be kell jelentkezni!</div>';
			return;	
		}
	
		// összes cimke listája
		$cimkek = file(DOCROOT.'/includes/cimkek.txt');
		foreach ($cimkek as $fn => $fv) {
			$cimkek[$fn] = trim(str_replace("\n","",$fv));
		}
		
		$receptId = $_POST['id'];
		if ($receptId == 0) {
			$r = new Record();
			$r->id = 0;
			$r->nev = $_POST['nev'];
			if ($this->session->input('loged') > 0) {
				$r->created_by = $this->session->input('loged');
			} else {
				$r->created_by = 0;
			}	
			$r->created_at = date('Y.m.d');
			$receptId = $this->model->save($r);
		} else {
			$recept = $this->model->getById($this->model->sqlValue($receptId));
			if (($recept->created_by != $this->session->input('loged')) & 
			    (!$this->logedAdmin)) {
				echo '<div class="alert alert-danger">Hozzáférés megtagadva!</div>';
				return;
			}
		}
		if ($this->model->errorMsg != '') {
			echo ' error in insert '.$this->model->errorMsg; exit();
		}
	
		$r = new Record();
		$r->id = $this->model->sqlValue($receptId);
		$r->leiras = $_POST['leiras'];
		$r->nev = $_POST['nev'];
		$r->adag = intval($_POST['adag']);
		$r->elkeszites = intval($_POST['elkeszites']);
		$r->energia = intval($_POST['energia']);
		
		$this->model->save($r);
		
		// meglévő hozzávalók törlése
		$this->model->deleteHozzavalok($r->id);
		if ($this->model->errorMsg != '') {
			echo ' error in del hozzavalok '.$this->model->errorMsg; exit();
		}
		// hozzávalók felvitele
		for ($i = 0; $i < 30; $i++) {
			if (isset($_POST['hozzavalo'.$i])) {
				if ($_POST['hozzavalo'.$i] != '') {
					$r = new Record();
					$r->recept_id = $receptId;
					$r->nev = $_POST['hozzavalo'.$i];
					if (isset($_POST['mennyiseg'.$i])) {
						$r->mennyiseg = $_POST['mennyiseg'.$i];
					} else {
						$r->mennyiseg = 0;
					}	
					if (!is_numeric($r->mennyiseg)) $r->mennyiseg = 0;
					if (isset($_POST['me'.$i])) {
						$r->me = $_POST['me'.$i];
					} else {
						$r->me = '';				
					}
					$this->model->insertHozzavalok($r);
					if ($this->model->errorMsg != '') {
						echo ' error in insert hozzavalok '.$this->model->errorMsg; exit();
					}
				}
			}	
		}
		
		// kép file feltöltése

		if (file_exists($_FILES['kepfile']['tmp_name'])) { 
			$target_dir = DOCROOT.'/images/';
			$target_file = $target_dir . basename($_FILES["kepfile"]["name"]);
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			$target_file = $target_dir.$_POST['nev'].'.'.$imageFileType;		
			$uploadOk = '';
			
		    $check = getimagesize($_FILES["kepfile"]["tmp_name"]);
			if($check == false) {
			    $uploadOk = 'nem kép fájl';
			}
			
			if (file_exists($target_dir.$_POST['nev'].'.jpg')) {
				unlink($target_dir.$_POST['nev'].'.jpg');
			}
			if (file_exists($target_dir.$_POST['nev'].'.png')) {
				unlink($target_dir.$_POST['nev'].'.png');
			}
			if (file_exists($target_dir.$_POST['nev'].'.jpeg')) {
				unlink($target_dir.$_POST['nev'].'.jpeg');
			}
			if (file_exists($target_dir.$_POST['nev'].'.gif')) {
				unlink($target_dir.$_POST['nev'].'.gif');
			}
			
			if ($_FILES["kepfile"]["size"] > 5000000) {
			  $uploadOk .= ' túl nagy fájl méret';
			}
			
			if($imageFileType != "jpg" && 
			   $imageFileType != "png" && 
			   $imageFileType != "jpeg" && 
			   $imageFileType != "gif" ) {
			  $uploadOk .= ' nem megengedett fájl kiterjesztés';
			}
			
			if ($uploadOk == '') {
			  if (!move_uploaded_file($_FILES["kepfile"]["tmp_name"], $target_file)) {
			    echo "Hiba a fájl feltöltés közben ".$target_file; exit();
			  }
			} else {
				echo $uploadOk; exit();			
			}
		} // van file upload

		// receptCimkek tárolása
		foreach ($cimkek as $cimke) {
			if (isset($_POST[$cimke])) {
				$this->model->saveReceptCimke($receptId, $cimke);
			} else {
				$this->model->delReceptCimke($receptId, $cimke);
			}	
		}

		$this->receptek();
	}
	
	/**
	 * kép url képzése
	 * 1. van az images könyvtárban?
	 * 2. ha nincs megpróbál név alapján  net-től keresni
	 */
	private function receptKep($recept) {
		$kep = 'images/etkeszlet.png'; 
		if (file_exists('images/'.$recept->nev.'.png')) {
			$kep = 'images/'.$recept->nev.'.png';
		} else if (file_exists('images/'.$recept->nev.'.jpg')) {
			$kep = 'images/'.$recept->nev.'.jpg';
		} else {
		// adat lekérés a google -ról	
			$receptNev = urlencode($recept->nev);
			$ch =
			curl_init("https://www.google.es/search?q=".$receptNev."&safe=off&source=lnms&tbm=isch");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_HEADER, 0);  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
			$r = curl_exec($ch);
			curl_close($ch);
			$te=explode('https://encrypted-tbn0.gstatic.com/images?q=tbn:', $r,2);
			for($i=1;$i<count($te);$i++) {
				$tedd=explode('"', $te[$i], 2);
				$url = "https://encrypted-tbn0.gstatic.com/images?q=tbn:".$tedd[0];
				$image = @file_get_contents($url);
				if(!$image)
					break;
				$kep = HtmlSpecialChars($url);
			}

		}
		return $kep;
	}

	public function recept() {	
		global $hozzavalok;
		$recept = JSON_decode('{"id":0, "leiras":"", "nev":"", "created_by":0, 
			"created_at":"2022.01.01",
			"adag":4,
			"energia":0,
			"elkeszites":0
		}');	
		$hozzavalok = [];	
		$receptId = $this->request->input('id',0,INTEGER);
		$disable = '';
	
		// aktuális recept és hozzávalók beolvasása
		if ($receptId > '0') {
			$recept = $this->model->getById($receptId);	
			$hozzavalok = $this->model->getHozzavalok($receptId);

			if (($recept->created_by != $this->session->input('loged')) &
			    (!$this->logedAdmin)) {
				$disable = ' disabled="disabled"';		
			}	
			
			// creator hozzáolvasása
			$creator = $this->model->getCreator($recept);
		} else {
			$creator = new Record();
			$creator->id = $this->session->input('loged');
			$creator->username = $this->session->input('logedName');
		}

		// commentek olvasása
		if ($this->request->isset('page')) {
			$page = $this->request->input('page',0,INTEGER);
		} else {
			$page = 1;
		}
		$commentModel = new CommentModel();
		$comments = $commentModel->getComments($recept->id, $page);
		$commentsTotal = $commentModel->getCommentsTotal($recept->id);
		$pages = [];
		for ($p=1; (($p-1)*20) < $commentsTotal; $p++) {
			$pages[] = $p;
		}

		// meglévő recept nevek beolvasása
		$receptNevek = $this->model->getReceptNevek();

		?>	
		<script>
		var receptNevek = <?php echo JSON_encode($receptNevek); ?>;
		</script>	
		<?php	
		
		// meglévő hozzávaló nevek beolvasása
		$nevek = $this->model->getHozzavaloNevek();
		
		$kep = $this->receptKep($recept);
		while (count($hozzavalok) < 15) {
			$hozzavalok[] = $this->model->emptyHozzavalo();
		}

		$cimkek = file(DOCROOT.'/includes/cimkek.txt');
		foreach ($cimkek as $fn => $fv) {
			$cimkek[$fn] = trim(str_replace("\n","",$fv));
		}

		$receptCimkek = $this->model->getReceptCimkek($receptId);

		if ($this->request->isset('url')) {
			atvesz($this->request->input('url'),$recept,$hozzavalok);	
		}

		view('receptkep',[
			"loged" => $this->session->input('loged'),
			"logedName" => $this->session->input('logedName'),
			"receptId" => $receptId,
			"kep" => $kep,
			"recept" => $recept,
			"disabled" => $disable,
			"hozzavalok" => $hozzavalok,
			"nevek" => $nevek,
			"receptNevek" => $receptNevek,
			"cimkek" => $cimkek,
			"receptCimkek" => $receptCimkek,
			"ADMIN" => ADMIN,
			"logedAdmin" => $this->logedAdmin,
			"creator" => $creator,
			"comments" => $comments,
			"total" => $commentsTotal,
			"page" => $page,
			"pages" => $pages,
			"UPLOADLIMIT" => UPLOADLIMIT,
			"task" => 'recapt&id='.$recept->id 

		]);
		
	}
	
	/**
	* $_GET['id'] alapján recept nyomtatás 
	*/
	public function receptprint() {
		global $hozzavalok;
		$recept = JSON_decode('{"id":0, "leiras":"", "nev":""}');	
		$hozzavalok = [];	
		$receptId = $this->request->input('id');
		$db = new Query('receptek');
		if ($receptId > '0') {
			$db->where('id','=',$db->sqlValue($receptId));
			$recept = $db->first();	
		}
		$db = new Query('hozzavalok');
		if ($receptId > '0') {
			$hozzavalok = $db->where('recept_id','=',$db->sqlValue($receptId))->all();	
		}
		$recept->leiras = str_replace("\n",'<br />',$recept->leiras);

		$db = new Query('recept_cimke');
		$receptCimkek = $db->where('recept_id','=',$receptId)->orderBy('cimke')->all();
		?>
		<div class="row">
			<div class="col-md-12">
				<strong><?php echo $recept->nev; ?></strong><br /><br />
				<img src="<?php echo $this->receptKep($recept); ?>" class="receptKep" />
				<strong>Hozzávalók <?php echo $recept->adag; ?> személyre</strong><br />		
				<?php foreach($hozzavalok as $hozzavalo) : ?>
				<div><?php echo $hozzavalo->nev.' '.$hozzavalo->mennyiseg.' '.$hozzavalo->me; ?></div>
				<?php endforeach; ?>
			</div>
			<div class="col-md-12">
				<br /><strong>Elkészítés</strong><br />
				<?php echo $recept->leiras; ?>
			</div>
			<div class="col-md-12">
				<br /><strong>Elkészítési idő</strong>:
				<?php echo $recept->elkeszites; ?> perc
				&nbsp;Energia tartalom:
				<?php echo $recept->energia; ?>kalória
			</div>
			<div class="col-md-12">
				<?php foreach ($receptCimkek as $receptCimke) : ?>
					<strong><?php echo $receptCimke->cimke; ?></strong>,&nbsp;&nbsp; 
				<?php endforeach; ?>	
			</div>						
		</div>
		<script>
			window.print();
		</script>
		<?php	
	}
	
	protected function getParam(string $name): string {
		$result = '';
		if ($this->session->isset($name)) {
			$result = $this->session->input($name);
		}
		if ($this->request->isset($name)) {
			$result = $this->request->input($name);
		}
		$this->session->set($name, $result);
		return $result;
	}
	
	protected function buildQuery():Query {
		$filterStr = $this->getParam('filterstr');
		$filterCreator = $this->getParam('filtercreator');
		$filterCreated = $this->getParam('filtercreated');
		$filterCimke = $this->getParam('filtercimke');
		$filterCreatorId = -1;
		if ($filterCreator != '') {
			$db = new Query('users');
			$r = $db->where('username','=',$filterCreator)->first();
			if (isset($r->id)) {
				$filterCreatorId = $r->id;
			} else {
				$filterCreatorId = 999999999;
			}
		}
		$db = new Query('receptek');
		$db->select(['id','nev'])
			->join('left outer','recept_cimke','c','c.recept_id','=','receptek.id')
			->where('nev','<>','');	
		if ($filterStr != '') {
			$db->where('nev','like','%'.$filterStr.'%');
		}
		if ($filterCreated != '') {
			$db->where('created_at','>=',$filterCreated);
		}
		if ($filterCreatorId >= 0) {
			$db->where('created_by','=',$filterCreatorId);
		}
		if ($filterStr != '') {
			$db->where('nev','like','%'.$filterStr.'%');
		}
		if ($filterCreated != '') {
			$db->where('created_at','>=',$filterCreated);
		}
		if ($filterCreatorId >= 0) {
			$db->where('created_by','=',$filterCreatorId);
		}
		if ($filterCimke != '') {
			$db->where('cimke','=',$filterCimke);
		}
		$db->groupBy(['id','nev']);
		return $db;
	}

	public function receptek() {
		$filterStr = $this->getParam('filterstr');
		$filterCreator = $this->getParam('filtercreator');
		$filterCreated = $this->getParam('filtercreated');
		$filterCimke = $this->getParam('filtercimke');
		$filterCreatorId = -1;
		if ($filterCreator != '') {
			$db = new Query('users');
			$r = $db->where('username','=',$filterCreator)->first();
			if (isset($r->id)) {
				$filterCreatorId = $r->id;
			} else {
				$filterCreatorId = 999999999;
			}
		}
		if ($this->request->isset('page')) {
			$page = $this->request->input('page');
			$offset = (20 * $page) - 20;
		} else if ($this->session->isset('page')) {
			$page = $this->session->input('page');
			$offset = (20 * $page) - 20;
		} else {
			$page = 1;
			$offset = 0;
		}
		$this->session->set('page',$page);
		$db = new Query('receptek');
		$db->exec('CREATE TABLE IF NOT EXISTS receptek (
			    id int AUTO_INCREMENT,
			    nev varchar(80),
			    leiras text,
			    PRIMARY KEY (id),
			    created_by int,
			    KEY (nev)
			)');
		$db->exec('CREATE TABLE IF NOT EXISTS hozzavalok (
			    id int AUTO_INCREMENT,
			    recept_id int,
			    nev varchar(80),
			    mennyiseg numeric(10,2),
			    me varchar(8),
			    PRIMARY KEY (id),
			    KEY (recept_id)
			)');
		$db = $this->buildQuery();
		$list = $db->all();
		$total = $db->count();
		$db = $this->buildQuery();
		$list = $db->offset($offset)->limit(20)->all();

		$pages = [];
		for ($p=1; (($p - 1)*20) < $total; $p++) {
			$pages[] = $p;
		}

		$cimkek = file(DOCROOT.'/includes/cimkek.txt');
		foreach ($cimkek as $fn => $fv) {
			$cimkek[$fn] = trim(str_replace("\n","",$fv));
		}

		view('receptek',[
			"filterStr" => $filterStr,
			"filterCreator" => $filterCreator,
			"filterCreated" => $filterCreated,
			"filterCimke" => $filterCimke,
			"list" => $list,
			"page" => $page,
			"pages" => $pages,
			"total" => $total,
			"loged" => $this->session->input('loged'),
			"cimkek" => $cimkek,
			"task" => 'receptek'
		]); 

	}
} // class

?>