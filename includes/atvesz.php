<?php
/**
* adatátvétel 
* a $recept és a $hozzavalok tömb mezőket tölti ki.
* képet másol az images könyvtárba.
* @param string $url; // recept képernyő a mindmegette.hu -n
* @param Rekord $recept
* @param array $hozzavalok
*/
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