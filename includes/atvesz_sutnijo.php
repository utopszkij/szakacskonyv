<?php

/* kiemeli a $start - $end közti részt $s -ből
   @param string $s a vizsgálandó string (modosul $start...$end rész törölve)
   @param string $start
   @param string $end
   @return strinf a kiemelt string 
*/
function kiemel(string &$s, string $start, string $end): string {
    $result = '';
    $w = explode($start,$s,2);
    if (count($w) > 1) {
        $w = explode($end,$w[1],2);
        if (count($w) > 1) {
            $result = $w[0];
            $s = $w[1];
        }
    }
    return trim($result);
}

/**
* adatátvétel a sutnijo.hu -ról
* a $recept mezőit és a $hozzavalok tömb mezőket tölti ki.
* képet másol az images könyvtárba.
* @param string $url; // recept képernyő a cookpad.hu -n
* @param Rekord $recept
* @param array $hozzavalok
*/
function atvetel($url = 'https://www.sutnijo.hu/....',
    &$recept, &$hozzavalok) {	
    global $mes, $mit, $mire;
    if (isset($_SESSION['origImg'])) {
        unset($_SESSION['origImg']);
    }
    $cim = '';
	$kep = '';
	$elkeszites = '';
	$adag = 4;
	$elkeszitesiIdo = 0;
	$energia = 0;
	$hozza = '';


    // url feldolgozása
	$html = implode("\n",file($url));
    $s = kiemel($html,'<body','</body>');
    
    // kép
    $kep = '';
    $s1 = kiemel($s,'<div class="recipe-hero-image','</div>');
    if ($s1 != '') {
        $kep = kiemel($s1,"url(","'");
    }

    // recep név
    $s1 = '';
    $cim = '';
    $s1 = kiemel($s,'<h1 class="recipe-title">','</h1>'); 
    $recept->nev = html_entity_decode($s1);

    // elkészítési idő 
    $s1 = '';
    $elkeszites = '';
    $s1 = kiemel($s,'alt="Szükséges idő" width="16">','</div>');
    $recept->elkeszites = szam($s1);

    // adag 
    $s1 = '';
    $adag = '';
    $s1 = kiemel($s,'alt="Adag" width="16">','</div>'); 
    $recept->adag = szam($s1);
    // hozzávalók
    $hozzavalok = []; // [{mennyiseg, nev, me},...]
    $s1 = kiemel($s,'ingredients">','<h3');
    
    // a h4 -es sorokat is tekinsük hozzávalónak
    $s1 = str_replace('<h4 class="font-weight-bold">','<div>',$s1); 
    $s1 = str_replace('</h4>','</div>',$s1); 

    //$s1 =  ...<div>1 db zeller</div>....
    while (($s1 != '') & (strlen($s1) > 10)){
        $s2 = kiemel($s1,'<div>','</div>');
        if ($s2 == '') {
            break;
        }
        $s2 = strip_tags($s2);
        $hozzavalo = new \stdClass();
        $w = explode(' ',strip_tags($s2),3);
        if (count($w) < 3) {
            if (count($w) > 1) {
                // feltehetőleg me hiányzik
                $w[2] = $w[1];
                $w[1] = '';
            } else {
                // mennyiség és me is hiányzik
                $w[1] = '';
                $w[2] = $w[0];
                $w[0] = '';
            }
        }
        $mennyiseg = szam($w[0]);
        if ($mennyiseg == '') {
            $w[0] = '';
            $w[1] = '';
            $w[2] = $s2;
        }
        $hozzavalo->nev = $w[2];
        // $w[1] valós mértékegység?
        if (count($w) > 1) {
            $w[1] = trim(str_replace($mit,$mire,' '.$w[1].' '));
            $w[2] = trim(str_replace($mit,$mire,' '.$w[2].' '));
            if (in_array($w[1],$mes)) {
                $hozzavalo->me = $w[1];
            } else {
                $hozzavalo->me = '';
                $hozzavalo->nev = $w[1].' '.$hozzavalo->nev;
            }    
        }
        $hozzavalo->mennyiseg = $mennyiseg;
        $hozzavalok[] = $hozzavalo;
    }

    // leírás
    $s1 = kiemel($s,'<div class="recipe-step mb-3">','<div class="recipe-images');
    $recept->leiras = strip_tags($s1,['ul','ol','li','p','br']);

    if ($kep != '') {
        $_SESSION['origImg'] = $kep;
    } else {
        if (isset($_SESSION['origImg'])) {
            unset($_SESSION['origImg']);
        }	
    }

}	

?>
