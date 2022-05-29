<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once __DIR__.'/../models/napimenumodel.php';

class NapiMenu {
	
	protected $model;

	function __construct() {
		// hiánya esetén az object konstruálás behivná a "napimenu" funciót!
		// (classnévvel megegyegyező nevü method)
		$this->model = new NapimenuModel();
	}
	
	// napi menü törlése (csak a saját maga által felvittet törölheti)
	// $_GET['id'] menu rekord id
	public function menuDelete() {
		$this->model->delById(intval($_GET['id']));
		$comp = new Naptar();
		$comp->home();	
	}
	
	// napimenü képernyő adatainak tárolása
	// $_GET['ev', 'ho', 'nap', .... képernyő mezők .... ] 
	public function menusave() {
		$ev = intval($_GET['ev']);
		$ho = intval($_GET['ho']);
		$nap = intval($_GET['nap']);
		if ($ho < 10) $ho = '0'.$ho;
		if ($nap < 10) $nap = '0'.$nap;
		if ($_SESSION['loged'] < 0) {
			echo '<div class="alert alert-danger">Napi menü felviteléhez be kell jelentkezni!</div>';
			return;	
		}
		
		$old = $this->model->getByDate($ev, $ho, $nap);
		if (isset($old->id)) {
			$id = $old->id;
		} else {
			$id = 0;
		}
		
		$r = new Record();
		$r->id = $id;
		$r->ev = $ev;	
		$r->ho = $ho;	
		$r->nap = $nap;	
		$r->datum = $ev.'-'.$ho.'-'.$nap;
		$r->recept1 = $_GET['recept1'];	
		$r->recept2 = $_GET['recept2'];	
		$r->recept3 = $_GET['recept3'];	
		$r->recept4 = $_GET['recept4'];
		$r->created_by = $_SESSION['loged'];	
		$r->adag = $_GET['adag'];
		if (!is_numeric($r->adag)) $r->adag = 4;	
		$this->model->save($r);
		
		$comp = new Naptar();
		$comp->home();	
	}
	
	private function receptSelect($v, $a) {
			if ($v == $a) echo ' selected="selected"';
	}

	// napi menü felvivő/modsító képernyő
	// $_GET['nap'], $_SESSION['numYear'], $_SESSION['numMonth'],
	public function napimenu() {
		// get nap
		// sessionban a numYear és numMonth
		$nap = intval($_GET['nap']);
		$ho = $_SESSION['numMonth'];
		$ev = $_SESSION['numYear'];
	
		// aktuális menu
		$rec = $this->model->getByDate($ev,$ho,$nap);
		if (!isset($rec->id)) {
			$rec = JSON_decode('{"id":0, "recept1":0, "recept2":0, "recept3":0, "recept4":0, "adag":4}');	
		}
	
		// összes meglévő recept
		$receptek = $this->model->getAllRecept();

		view('napimenukep',[
			"loged" => $_SESSION['loged'],
			"nap" => $nap,
			"ho" => $ho,
			"ev" => $ev,
			"rec" => $rec,
			"receptek" => $receptek
		]);
	}
} // class

?>
