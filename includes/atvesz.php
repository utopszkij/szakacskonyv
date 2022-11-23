<?php
/**
* adatátvétel  sajnos a cookpad.hu letiltotta az átvételt :(
*	
* a $recept és a $hozzavalok tömb mezőket tölti ki.
* képet másol az images könyvtárba.
* @param string $url; // recept képernyő a mindmegette.hu -n
* @param Rekord $recept
* @param array $hozzavalok
*/
use \RATWEB\Model;
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

//include_once __DIR__.'/../vendor/database/db.php';
global $mes, $mit, $mire;

// mértékegységek
$db = new Query('mertekegysegek');
$recs = $db->all();
$mes = [];
foreach ($recs as $rec) {
	$mes[] = trim($rec->nev);
}

// szinonima értelmezés
$db = new Query('szinonimak');
$recs = $db->all();
$mit = [];
$mire =[];
$mit[] = '    '; $mire[] = ' ';
$mit[] = '   '; $mire[] = ' ';
$mit[] = '  '; $mire[] = ' ';
foreach ($recs as $rec) {
    $mit[] = ' '.trim($rec->mit).' ';
    $mire[] = ' '.trim($rec->mire).' ';
}

function atvesz($url = 'https://www.mindmegette.hu/sult-kacsacomb-kaposztas-tesztaval.recept/',
    &$recept, &$hozzavalok) {	

	if (strpos($url,'mindmegette.hu') > 0) {
		include_once (__DIR__.'/atvesz_mindmegette.php');
		atvetel($url, $recept, $hozzavalok);
	}	

	if (strpos($url,'receptneked.hu') > 0) {
		include_once (__DIR__.'/atvesz_receptneked.php');
		atvetel($url, $recept, $hozzavalok);
	}	

	if (strpos($url,'nosalty.hu') > 0) {
		include_once (__DIR__.'/atvesz_nosalty.php');
		atvetel($url, $recept, $hozzavalok);
	}	

	/*
	if (strpos($url,'cookpad.com') > 0) {
		include_once (__DIR__.'/atvesz_cookpad.php');
		atvetel($url, $recept, $hozzavalok);
	}	
	*/

	if (strpos($url,'topreceptek.hu') > 0) {
		include_once (__DIR__.'/atvesz_topreceptek.php');
		 atvetel($url, $recept, $hozzavalok);
	}	

	if (strpos($url,'sutnijo.hu') > 0) {
		include_once (__DIR__.'/atvesz_sutnijo.php');
		atvetel($url, $recept, $hozzavalok);
	}	

	$recept->leiras .= '<p>&nbsp;</p><p>Forrás:'.$url.'</p>';

}	

?>