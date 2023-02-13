<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once __DIR__.'/../models/napimenumodel.php';

class NapiMenu extends Controller{
	
	protected $model;

	function __construct() {
		// hiánya esetén az object konstruálás behivná a "napimenu" funciót!
		// (classnévvel megegyegyező nevü method)
		parent::__construct();
		$this->model = new NapimenuModel();
	}
	
	// napi menü törlése (csak a saját maga által felvittet törölheti)
	// $_GET['id'] menu rekord id
	public function menuDelete() {
		$rec = $this->model->getById($this->request->input('id',0,INTEGER));
		$olds = $this->model->getByDate($rec->ev, $rec->ho, $rec->nap);
		foreach ($olds as $old) {
			$this->model->delById($old->id);
		}	
		$comp = new Naptar();
		$comp->home();	
	}
	
	// napimenü képernyő adatainak tárolása
	// $_POST['ev', 'ho', 'nap', .... képernyő mezők .... ] 
	// 0-2 reggeli, 3-7 ebéd, 8-12 vacsora
	public function menusave() {
		if (!$this->checkFlowKey('index.php')) {
			echo 'flowKey hiba'; exit();
		}		
		$ev = $this->request->input('ev',0,INTEGER);
		$ho = $this->request->input('ho',0,INTEGER);
		$nap = $this->request->input('nap',0,INTEGER);
		if ($ho < 10) $ho = '0'.$ho;
		if ($nap < 10) $nap = '0'.$nap;
		if ($this->loged < 0) {
			echo '<div class="alert alert-danger">Napi menü felviteléhez be kell jelentkezni!</div>';
			return;	
		}
		
		$olds = $this->model->getByDate($ev, $ho, $nap);
		for ($i=0; $i <= 12; $i++) {
			$r = new Record();
			if (count($olds) > 0) {
				$r->id = $olds[$i]->id;
			} else {
				$r->id = 0;
			}	
			$r->ev = $ev;	
			$r->ho = $ho;	
			$r->nap = $nap;	
			$r->datum = $ev.'-'.$ho.'-'.$nap;
			$r->sorszam = $i;	
			$r->recept = $this->request->input('recept'.$i);	
			$r->created_by = $this->loged;
			if ($i <= 2) {	
				$r->adag = $this->request->input('adag0',4,INTEGER);
			} else if ($i <= 7)	{
				$r->adag = $this->request->input('adag3',4,INTEGER);
			} else {
				$r->adag = $this->request->input('adag8',4,INTEGER);
			}
			if (!is_numeric($r->adag)) $r->adag = 4;	
			$this->model->save($r);
		}
		$comp = new Naptar();
		$comp->naptar();	
	}
	
	private function receptSelect($v, $a) {
			if ($v == $a) echo ' selected="selected"';
	}

	// napi menü felvivő/modsító képernyő
	// $_GET['nap'], $_SESSION['numYear'], $_SESSION['numMonth'],
	// 0-2 reggeli, 3-7 ebéd, 8-12 vacsora
	public function napimenu() {
		// get nap
		// sessionban a numYear és numMonth
		$nap = $this->request->input('nap',1,INTEGER);
		$ho = $this->session->input('numMonth',0);
		$ev = $this->session->input('numYear',0);
	
		// aktuális menu (virtuális rekord)
		$recs = $this->model->getByDate($ev,$ho,$nap);
		if (count($recs) == 0) {
			$rec = JSON_decode('{"id":0, 
				"recept0":0, 
				"recept1":0, 
				"recept2":0, 
				"recept3":0, 
				"recept4":0, 
				"recept5":0, 
				"recept6":0, 
				"recept7":0, 
				"recept8":0, 
				"recept9":0, 
				"recept10":0, 
				"recept11":0, 
				"recept12":0, 
				"adag0":4,
				"adag1":4,
				"adag2":4,
				"adag3":4,
				"adag4":4,
				"adag5":4,
				"adag6":4,
				"adag7":4,
				"adag8":4,
				"adag9":4,
				"adag10":4,
				"adag11":4,
				"adag12":4}');	
		} else {
			$rec = clone $recs[0];
			for ($i=0; $i <= 12; $i++) {
				$fn = 'adag'.$i;
				$rec->$fn = $recs[$i]->adag;
				$fn = 'recept'.$i;
				$rec->$fn = $recs[$i]->recept;
			}
		}

		// összes meglévő recept
		$receptek = $this->model->getAllRecept();

		view('napimenukep',["flowKey" => $this->newFlowKey(),
			"loged" => $this->loged,
			"nap" => $nap,
			"ho" => $ho,
			"ev" => $ev,
			"rec" => $rec,
			"receptek" => $receptek
		]);
	}
} // class

?>
