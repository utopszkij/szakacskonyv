<?php
class Szovegek {
	
	function __construct() {
	}	
	
	public function impresszum() {
		view('impressum',[]);
	}
	
	public function adatkezeles() {
		view('policy',[]);
	}
	
	public function licensz() {
		view('licence',[]);
	}
	
	public function visszaeles() {
		view('protest',[]);
	}
}

?>