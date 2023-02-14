<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once __DIR__.'/../atvesz.php';
include_once __DIR__.'/../models/receptmodel.php';
include_once __DIR__.'/../models/commentmodel.php';
include_once __DIR__.'/../models/likemodel.php';
include_once __DIR__.'/../models/cimkemodel.php';
include_once __DIR__.'/../models/statisticmodel.php';
include_once __DIR__.'/../urlprocess.php';
include_once __DIR__.'/../uploader.php';

class Recept extends Controller{
	protected $model;

	function __construct() {
		parent::__construct();
		$this->model = new ReceptModel();
		$this->dataBrowser = 'index.php?task=receptek';
		$this->browserTask = 'receptek';
	}	
	
	public function getTitle(string $task) {
		$result = 'Szakácskönyv';
		if ($task == 'recept') {
			$this->wrecept = $this->model->getById( (int) $this->request->input('id',0));
			if (isset($this->wrecept->nev)) {
				$result = $this->wrecept->nev;
			}
		}
		return $result;
	}

	public function getFbImage(string $task) {
		$result = SITEURL.'/images/fejlec.png';
		if (isset($this->wrecept)) {
			if (isset($this->wrecept->id)) {
				$result = $this->receptKep($this->wrecept);
			}	
		}
		return $result;
	}
	
	
	// $_GET['id']
	public function receptdelete() {
		if (isset($_SESSION['origImg'])) {
			unset($_SESSION['origImg']);
		}	
		// normál user csak a saját maga által felvittet törölheti
		// system admin mindent törölhet
		$recept = $this->model->getById($this->request->input('id',0,INTEGER));
		if (($recept->created_by != $this->session->input('loged')) &
		    (!$this->logedAdmin)) {
			// nics extras js betöltve! $this->receptek();
			echo '<script>location="index.php?task=receptek";</script>';
		} else {
			$this->model->delById($this->request->input('id',0,INTEGER));
			$this->model->deleteHozzavalok($this->request->input('id'));
			$this->model->deleteCimkek($this->request->input('id'));
			$likeModel = new LikeModel();
			$likeModel->deleteLikes('recept',$this->request->input('id'));
			echo '<script>location="index.php?task=receptek";</script>';
			// nics extras js betöltve! $this->receptek();
		}
	}

	protected function delReceptImg(int $receptId) {
		if (file_exists('images/recept'.$receptId.'.jpg')) {
			unlink('images/recept'.$receptId.'.jpg');
		}
		if (file_exists('images/recept'.$receptId.'.jpeg')) {
			unlink('images/recept'.$receptId.'.jpeg');
		}
		if (file_exists('images/recept'.$receptId.'.png')) {
			unlink('images/recept'.$receptId.'.png');
		}
		if (file_exists('images/recept'.$receptId.'.gif')) {
			unlink('images/recept'.$receptId.'.gif');
		}
		if (file_exists('images/recept'.$receptId.'.url')) {
			unlink('images/recept'.$receptId.'.url');
		}
	}	
	
	public function receptsave() {
        if (!$this->checkFlowKey('index.php?task=receptek')) {
            $this->session->set('flowKey','used');
            echo 'flowKey error! Lehet, hogy túl hosszú várakotzás miatt lejárt a munkamenet?'; exit();
			$this->receptek();
        }
		$this->session->set('flowKey','used');
		// get -ben: id, leiras, hozzvalok, mennyiseg0, me0, hozzavalok1,....
		if ($this->session->input('loged') < 0) {
			echo '<div class="alert alert-danger">Recept felviteléhez be kell jelentkezni!</div>';
			return;	
		}
	
		// összes cimke listája
		$cimkek = [];
		$q = new Query('cimkek');
		$recs = $q->all();
		foreach ($recs as $rec) {
			$cimkek[] = $rec->cimke;
		}
		
		$receptId = $this->request->input('id');
		if ($receptId == 0) {
			$r = new Record();
			$r->id = 0;
			$r->nev = $this->request->input('nev');
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
			    (!$this->logedAdmin) & ($this->logedGroup != 'moderator')) {
				echo '<div class="alert alert-danger">Hozzáférés megtagadva!</div>';
				return;
			}
		}
		// most már $receptId -nek van értéke (akkor is ha új felvitel)
		if ($this->model->errorMsg != '') {
			echo ' error in insert '.$this->model->errorMsg; exit();
		}
	
		$r = new Record();
		$r->id = $this->model->sqlValue($receptId);
		$r->leiras = $this->request->input('leiras','',HTML);
		$r->nev = $this->request->input('nev');
		$r->adag = intval($this->request->input('adag'));
		$r->elkeszites = intval($this->request->input('elkeszites'));
		$r->energia = intval($this->request->input('energia'));
		$this->model->save($r);
		// meglévő hozzávalók törlése
		$this->model->deleteHozzavalok($r->id);
		if ($this->model->errorMsg != '') {
			echo ' error in del hozzavalok '.$this->model->errorMsg; exit();
		}
		// hozzávalók felvitele
		for ($i = 0; $i < 30; $i++) {
			if (isset($_POST['hozzavalo'.$i])) {
					$r = new Record();
					$r->recept_id = $receptId;
					$r->nev = $this->request->input('hozzavalo'.$i);
					if (isset($_POST['mennyiseg'.$i])) {
						$r->mennyiseg = $this->request->input('mennyiseg'.$i);
					} else {
						$r->mennyiseg = 0;
					}	
					if (!is_numeric($r->mennyiseg)) $r->mennyiseg = 0;
					if (isset($_POST['me'.$i])) {
						$r->me = $this->request->input('me'.$i);
					} else {
						$r->me = '';				
					}
					$this->model->insertHozzavalok($r);
					if ($this->model->errorMsg != '') {
						echo ' error in insert hozzavalok '.$this->model->errorMsg; exit();
					}
			}	
		}
		
		// kép file feltöltése
		if (file_exists($_FILES['kepfile']['tmp_name'])) { 
			// képernyőn megadott képfile
			$this->delReceptImg($receptId);
			$uploadRes = Uploader::doImgUpload('kepfile',
			DOCROOT.'/images',
			'recept'.$receptId.'.*');
			if ($uploadRes->error != '') {
				echo '<div class="alert alert-danger">'.$uploadRes->error.'</div>';
			}
		} else if (isset($_SESSION['origImg'])) {
			// átvételnél talált eredeti képfájl a forrás oldalon
			$kep = $_SESSION['origImg'];
			$imageFileType = strtolower(pathinfo($kep,PATHINFO_EXTENSION));
			$imgFileName = 'images/recept'.$receptId.'.'.$imageFileType;
			if (file_exists($imgFileName)) {
				unlink($imgFileName);			
			}
			copy($kep, $imgFileName);
		}
		if ($this->request->input('kepurl','') != '') {
			$this->delReceptImg($receptId);
			$fp = fopen('images/recept'.$receptId.'.url','w+');
			fwrite($fp,$this->request->input('kepurl',''));
			fclose($fp);
		}
		if (isset($_SESSION['origImg'])) {
			unset($_SESSION['origImg']);
		}
		// receptCimkek tárolása
		foreach ($cimkek as $cimke) {
			if (isset($_POST[str_replace(' ','_',$cimke)])) {
				$this->model->saveReceptCimke($receptId, $cimke);
			} else {
				$this->model->delReceptCimke($receptId, $cimke);
			}	
		}
		// extras nincs betöltve! $this->receptek();
		echo '<script>location="index.php?task=receptek";</script>';
	}
	
	/**
	 * kép url képzése
	 * 1. van az images könyvtárban? (lehet url is!)
	 * 2. ha nincs megpróbál név alapján  net-től keresni
	 */
	private function receptKep($recept) {
		$kep = 'images/noimage.png'; 
		if (file_exists('images/recept'.$recept->id.'.jpg')) {
			$kep = 'images/recept'.$recept->id.'.jpg';
		} else if (file_exists('images/recept'.$recept->id.'.jpeg')) {
			$kep = 'images/recept'.$recept->id.'.jpeg';
		} else if (file_exists('images/recept'.$recept->id.'.png')) {
			$kep = 'images/recept'.$recept->id.'.png';
		} else if (file_exists('images/recept'.$recept->id.'.gif')) {
			$kep = 'images/recept'.$recept->id.'.gif';
		} else if (file_exists('images/'.$recept->nev.'.png')) {
			$kep = 'images/'.$recept->nev.'.png';
		} else if (file_exists('images/'.$recept->nev.'.jpg')) {
			$kep = 'images/'.$recept->nev.'.jpg';
		} else if (file_exists('images/'.$recept->nev.'.jpeg')) {
			$kep = 'images/'.$recept->nev.'.jpeg';
		} else if (file_exists('images/'.$recept->nev.'.gif')) {
			$kep = 'images/'.$recept->nev.'.gif';
		} else if (file_exists('images/recept'.$recept->id.'.url')) {
			$kep = implode('', file('images/recept'.$recept->id.'.url'));
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
				if (isset($tedd[0])) {
					$url = "https://encrypted-tbn0.gstatic.com/images?q=tbn:".$tedd[0];
					$image = @file_get_contents($url);
					if(!$image) {
						$kep = 'images/noimage.png';
					} else {
						$kep = HtmlSpecialChars($url);
						// kép mentése az images könyvtárba
						$kep = 'images/recept'.$recept->id.'.png';
						try {
							$fp = fopen($kep,'w+');
							fwrite($fp, $image);
							fclose($fp);
						} catch(exception $e) {
							$kep = 'images/noimage.png';
						}	
					}
				}	
			}
		}
		return $kep;
	}

	public function recept() {	
		global $hozzavalok;
		$statisticModel = new StatisticModel();
		if (isset($_SESSION['origImg'])) {
			unset($_SESSION['origImg']);
		}	
		$recept = JSON_decode('{"id":0, "leiras":"", "nev":"", "created_by":0, 
			"created_at":"2022.01.01",
			"adag":4,
			"energia":0,
			"elkeszites":0,
			"lattak":0
		}');	
		$hozzavalok = [];	
		$receptId = $this->request->input('id',0,INTEGER);
		$adag = $this->request->input('adag',0,INTEGER);
		$disable = '';

	
		// aktuális recept és hozzávalók beolvasása
		if ($receptId > '0') {

			$recept = $this->model->getById($receptId);	
			$hozzavalok = $this->model->getHozzavalok($receptId);
			$statisticModel->setShow($recept, $this->session->input('logedName'));

			if (($recept->created_by != $this->session->input('loged')) &
			    (!$this->logedAdmin) &
				($this->logedGroup != 'moderator')) {
				$disable = ' disabled="disabled"';		
			}	
			
			// creator hozzáolvasása
			$creator = $this->model->getCreator($recept);
			// kedvenc?
			$isFavorit = $this->model->isFavorit($this->session->input('loged'), $receptId);
		} else {
			$creator = new Record($this->session->input('loged'));
			$creator->id = $this->session->input('loged');
			$creator->username = $this->session->input('logedName');
			$isFavorit = false;
		}

		// a $recept->leiras -banlévő url -eket feldolgozza
		$recept->leirasHtml = urlprocess($recept->leiras);

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
		foreach($hozzavalok as $hozzavalo) {
			if (is_numeric($hozzavalo->mennyiseg)) {
				$hozzavalo->mennyiseg = Round($hozzavalo->mennyiseg * 10) / 10;
				$hozzavalo->menny = Round($hozzavalo->mennyiseg * 10) / 10;
			}
			if ($hozzavalo->mennyiseg == 0) {
				$hozzavalo->menny = '';
			}
		}
		// összes cimke listája
		$cimkek = [];
		$q = new Query('cimkek');
		$recs = $q->all();
		/* 2023.01.03 cimke fa struktura
		foreach ($recs as $rec) {
			$cimkek[] = $rec->cimke;
		}
		*/
		$cimkek = $recs;

		$receptCimkek = $this->model->getReceptCimkek($receptId);

		if ($this->request->isset('url')) {
			atvesz($this->request->input('url'),$recept,$hozzavalok);	
			// fogalmam sincs róla miért, de a mennyiseg nevü memzőt NaN -re cseréli valami ????
			// ugyamigy az adag mező is elromlik ????
			$recept->a = $recept->adag;
			foreach ($hozzavalok as $hozzavalo) {
				$hozzavalo->menny = $hozzavalo->mennyiseg;
			}
			if (isset($_SESSION['origImg'])) {
				$kep = $_SESSION['origImg'];
			} else {
				$kep = $this->receptKep($recept);
			}	
		}

		// likes infok a $recept -be
		$likeModel = new LikeModel();
		$recept->likeCount = $likeModel->getLikesTotal('recept', $recept->id);
		$recept->userLike = $likeModel->userLiked('recept',$recept->id, $this->session->input('loged'));

		view('receptkep',[
			"flowKey" => $this->newFlowKey(),
			"loged" => $this->session->input('loged'),
			"logedName" => $this->session->input('logedName'),
			"logedAdmin" => $this->logedAdmin,
			"logedGroup" => $this->logedGroup,
			"receptId" => $receptId,
			"adag" => $adag,
			"kep" => $kep,
			"recept" => $recept,
			"isFavorit" => $isFavorit,
			"disabled" => $disable,
			"hozzavalok" => $hozzavalok,
			"nevek" => $nevek,
			"receptNevek" => $receptNevek,
			"cimkek" => $cimkek,
			"receptCimkek" => $receptCimkek,
			"ADMIN" => ADMIN,
			"creator" => $creator,
			"comments" => $comments,
			"total" => $commentsTotal,
			"page" => $page,
			"pages" => $pages,
			"UPLOADLIMIT" => UPLOADLIMIT,
			"LIKESIZE" => LIKESIZE,
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
	
	/*
	* lekérdező sql kialakitása 
	* figyelem a filterCimke -nél lehet fa szerkezet is!
	*/
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
		$db->select(['id','nev','lattak'])
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
			$cimkeModel = new CimkeModel();
			$cimkek = $cimkeModel->getBy('cimke',$filterCimke);
			if (count($cimkek) > 0) {
				$cimke0 =$cimkek[0]; // a képernyőre beirt cimke rekord
				$w = [];
				$cimkeModel->getItems1($cimke0->id, 0, $w); 
				// most $w a beirt alrekordjait tartalmazza
				$cimkeList = [$cimke0->cimke];
				foreach ($w as $w1) {
					$cimkeList[] = $w1->cimke;
				}
				// most CimkeList a kiválasztott cimke és annak alrekordjai
				$db->where('cimke','in',$cimkeList);
			}
		}	
		$db->groupBy(['id','nev','lattak']);
		$db->orderBy('nev');
		//echo $db->getSql();
		return $db;
	}

	public function receptek($task = 'receptek') {
		// $pageSize = round((int)$_SESSION['screen_height'] / 80);
		$pageSize = 15;
		$filterStr = $this->getParam('filterstr');
		$filterCreator = $this->getParam('filtercreator');
		$filterCreated = $this->getParam('filtercreated');
		$filterCimke = $this->getParam('filtercimke');
		$filterCreatorId = -1;
		if (isset($_SESSION['origImg'])) {
			unset($_SESSION['origImg']);
		}	
		
		$q = new Query('receptek');
		$news = $q->orderBy('id')->orderDir('DESC')->limit(8)->all();
		
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
			$offset = ($pageSize * $page) - $pageSize;
		} else if ($this->session->isset('page')) {
			$page = $this->session->input('page');
			$offset = ($pageSize * $page) - $pageSize;
		} else {
			$page = 1;
			$offset = 0;
		}
		if ($page < 1) {
			$page = 1;
			$offset = 0;
		}
		$db = new Query('receptek');
		$db = $this->buildQuery();
		$list = $db->all();

		$total = $db->count();
		// lapok tömb kialakitása a paginátor számáta
		$pages = [];
		for ($p=1; (($p - 1)*$pageSize) < $total; $p++) {
			$pages[] = $p;
		}
		// esetleges hibás page korrigálása
		$p = $p - 1;
		if ($p < 1) {
			$p = 1;
		}
		if ($page > $p ) {
			$page = $p;
			$offset = ($p - 1)*$pageSize;
		}
		// $page tárolása sessionba
		$this->session->set('page',$page);

		// rekordok lekérése
		$db = $this->buildQuery();
		$list = $db->offset($offset)->limit($pageSize)->all();
		$likeModel = new LikeModel();
		foreach ($list as $item) {
			$item->favorit = $this->model->isFavorit($this->session->input('loged',0), $item->id);
			// likes infok a $recept -be
			$item->likeCount = $likeModel->getLikesTotal('recept', $item->id);
			$item->userLiked = $likeModel->userLiked('recept',$item->id, $this->session->input('loged'));
		}
		foreach ($news as $new) {
			// likes infok a $recept -be
			$new->likeCount = $likeModel->getLikesTotal('recept', $new->id);
		}


		// összes cimke listája
		$cimkeModel = new CimkeModel();
		$cimkek = $cimkeModel->getItems(0,0,'','');

		view('receptek',[
			"filterStr" => $filterStr,
			"filterCreator" => $filterCreator,
			"filterCreated" => $filterCreated,
			"filterCimke" => $filterCimke,
			"newsOpened" => false,
			"list" => $list,
			"news" => $news,
			"page" => $page,
			"pages" => $pages,
			"total" => $total,
			"loged" => $this->session->input('loged'),
			"cimkek" => $cimkek,
			"task" => $task,
			"LIKESIZE" => LIKESIZE
		]); 

	}

	/**
	 * api backend funcion
	 * GET page
	 * return items
	 */
	public function apiReceptekList() {
		$pageSize = 15;
		$filterStr = $this->getParam('filterstr');
		$filterCreator = $this->getParam('filtercreator');
		$filterCreated = $this->getParam('filtercreated');
		$filterCimke = $this->getParam('filtercimke');
		$filterCreatorId = -1;
		if (isset($_SESSION['origImg'])) {
			unset($_SESSION['origImg']);
		}	
		
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
			$offset = ($pageSize * $page) - $pageSize;
		} else if ($this->session->isset('page')) {
			$page = $this->session->input('page');
			$offset = ($pageSize * $page) - $pageSize;
		} else {
			$page = 1;
			$offset = 0;
		}
		if ($page < 1) {
			$page = 1;
			$offset = 0;
		}
		$db = new Query('receptek');
		$db = $this->buildQuery();
		$list = $db->all();

		// $page tárolása sessionba
		$this->session->set('page',$page);

		// rekordok lekérése
		$db = $this->buildQuery();
		$list = $db->offset($offset)->limit($pageSize)->all();
		$likeModel = new LikeModel();
		foreach ($list as $item) {
			$item->favorit = $this->model->isFavorit($this->session->input('loged',0), $item->id);
			// likes infok a $recept -be
			$item->likeCount = $likeModel->getLikesTotal('recept', $item->id);
			$item->userLiked = $likeModel->userLiked('recept',$item->id, $this->session->input('loged'));
		}
		$result = '[';
		for ($i=0; $i<count($list); $i++) {
			if ($i > 0) $result .= ',';
			$result .= JSON_encode($list[$i]);
		}
		$result .= ']';
		header('Content-Type: application/json; charset=utf-8');
		echo $result;
	}

	/**
	 * kedvencek megjelenítése
	 */
	public function favorites() {
		$pageSize = round((int)$_SESSION['screen_height'] / 80);
		if ($this->request->isset('page')) {
			$page = $this->request->input('page');
			$offset = ($pageSize * $page) - $pageSize;
		} else if ($this->session->isset('page')) {
			$page = $this->session->input('page');
			$offset = ($pageSize * $page) - $pageSize;
		} else {
			$page = 1;
			$offset = 0;
		}
		if ($page < 1) {
			$page = 1;
			$offset = 0;
		}
		$db = new Query('kedvencek','k');
		$db->select(['r.id, r.nev'])
			->where('k.user_id','=',$this->session->input('loged',0))
			->join('LEFT','receptek','r','r.id','=','k.recept_id')
			->orderBy('r.nev');
		$list = $db->all();

		$total = $db->count();
		// lapok tömb kialakitása a paginátor számáta
		$pages = [];
		for ($p=1; (($p - 1)*$pageSize) < $total; $p++) {
			$pages[] = $p;
		}
		// esetleges hibás page korrigálása
		$p = $p - 1;
		if ($page > $p ) {
			$page = $p;
			$offset = ($p - 1)*$pageSize;
		}
		// $page tárolása sessionba
		$this->session->set('page',$page);

		// rekordok lekérése
		$db = new Query('kedvencek','k');
		$db->select(['r.id, r.nev'])
			->where('user_id','=',$this->session->input('loged',0))
			->join('LEFT','receptek','r','r.id','=','k.recept_id')
			->orderBy('r.nev');
		$list = $db->offset($offset)->limit($pageSize)->all();
		$likeModel = new LikeModel();
		foreach ($list as $item) {
			$item->favorit = true;
			// likes infok a $recept -be
			$item->likeCount = $likeModel->getLikesTotal('recept', $item->id);
			$item->userLiked = $likeModel->userLiked('recept',$item->id, $this->session->input('loged'));
		}
		view('kedvencek',[
			"list" => $list,
			"page" => $page,
			"pages" => $pages,
			"total" => $total,
			"loged" => $this->session->input('loged'),
			"task" => 'favorites'
		]); 
	}

    /**
    * recpt kép url id alapján (axios backend)
    */
    public function getImage() {
		$id = $this->getParam('id');
        $q = new \RATWEB\DB\Query('receptek');
        $rec = $q->where('id','=',$id)->first();
        $imgurl = $this->receptkep($rec);
        echo $imgurl;
    }

	/**
	 * recept hozzáadása a kedvencekhez
	 */
	public function addtofavorit() {
		$id = $this->getParam('recept_id');
		$this->model->addToFavorit($this->session->input('loged'), $id);
		$this->request->set('id',$id);
		$this->recept();
	}

	/**
	 * recept törlése a kedvencek közül
	 */
	public function delfromfavorit() {
		$id = $this->getParam('recept_id');
		$this->model->delfromFavorit($this->session->input('loged'), $id);
		$this->request->set('id',$id);
		$this->recept();
	}


} // class

?>
