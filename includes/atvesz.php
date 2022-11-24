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

/**
 * szám string értelmezés (###-###, #/#)
 * @param string $s
 * @return float | ''
 */
function szam(string $s) {
	$result = '';
	$s = str_replace('kb.','',$s);
	$s = str_replace('kb','',$s);
	$s = trim(str_replace(' ','',$s));
	$w = explode('-',$s);
	if (count($w) > 1) {
		$result = round(10*((float)$w[0]+(float)$w[1])/2)/10;
	} else {
		$w = explode('/',$s);
		if (count($w) > 1) {
			$result = round(10*float($w[0])/float($w[1]))/10;
		} else if (is_numeric($s)){
			$result = (float)$s;
		} else {
			$result = '';
		} 
	} 
	return $result;
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

	// $recept->leiras .= '<p>&nbsp;</p><p>Forrás:'.$url.'</p>';
	$recept->leiras .= '<p>&nbsp;</p><p>Feltöltő:'.$_SESSION['logedName'].'</p>';

}	

?>