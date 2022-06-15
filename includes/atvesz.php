<?php
/**
* adatátvétel 
* a $recept és a $hozzavalok tömb mezőket tölti ki.
* képet másol az images könyvtárba.
* @param string $url; // recept képernyő a mindmegette.hu -n
* @param Rekord $recept
* @param array $hozzavalok
*/

function isMe(string $s):bool {
	$result = true;
	if ($s == 'TV') { $result = false; }
	if (trim($s) == '') { $result = false; }
	return $result;
}

function szinonima(string $s): string {
	$result = $s;
	if ($s == 'evőkanál') { $result = 'ek'; }
	if ($s == 'kiskanál') { $result = 'kk'; }
	if ($s == 'teás kanál') { $result = 'tk'; }

	return $result;
}

function defMe(string $s): string {
	echo 'defMe '.$s.'<br>';
	$result = '';
	if (trim($s) == "SZABADTARTÁSOS TOJÁS") { $result = 'db'; }
	if (trim($s) == "TV paprika") { $result = 'db'; }
	echo 'defMe '.$s.' result='.$result.'<br>';
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
			
}	

?>