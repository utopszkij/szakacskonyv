<?php

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
    return $result;
}

/**
* adatátvétel a nosalty.hu -ról
* a $recept mezőit és a $hozzavalok tömb mezőket tölti ki.
* képet másol az images könyvtárba.
* @param string $url; // recept képernyő a mindmegette.hu -n
* @param Rekord $recept
* @param array $hozzavalok
*/
function atvetel($url = 'https://www.nosalty.hu/....',
    &$recept, &$hozzavalok) {	
    global $mes, $mit, $mire;
    $cim = '';
	$kep = '';
	$elkeszites = '';
	$adag = 4;
	$elkeszitesiIdo = 0;
	$energia = 0;
	$hozza = '';


    // url feldolgozása
	$s = implode("\n",file($url));

    // recep név
    $w = $s;
    $s1 = kiemel($w,'<h1','/h1>');
    $cim = kiemel($s1,'>','<');
    $recept->nev = html_entity_decode(str_replace('recept','',$cim));
    
    // kép
    $kep = '';
    $w = $s;
    $s1 = kiemel($w,'<picture','</picture>');
    $kep = kiemel($s1,'srcset="',',');

    // mennyiség
    $recept->adag = (int)kiemel($s,'<time class="mr-2">','</time>');

    // elkészítési idő
    $ido = 0;
    $w = $s;
    $s1 = kiemel($w,'p-recipe__details','Hozzávalók');
    $s1 = kiemel($s1,'Összesen','/time');
    $ido = (int)kiemel($s1,'<time class="mr-2">','<');
    if ($ido == 0) {
        $w = $s;
        $ido = (int)kiemel($w,'<time class="mr-2">','<');
        echo 'ido2='.$ido.'<br>';    
    } 
    $recept->elkeszites = (int)$ido;
    
    // energia
    $w = $s;
    $s1 = kiemel($w,'id="calories-btn"','/span>');
    $recept->energia = (float)kiemel($s1,'<span>','<') * 4.187; // átszámolja J -ra
    $recept->energia = round($recept->energia);  // kerekit egészre

    // hozzávalók
    $hozzavalok = [];
    $w = $s;
    $s1 = kiemel($w,'Hozzávalók','Elkészítés');

    $liStr = kiemel($s1,'<li','</li>');
    while ($liStr != '') {
        $w = $liStr;
        $menny_me = kiemel($w,'<span>','</span>');
        $menny_me = trim(str_replace($mit,$mire,' '.trim($menny_me).' '));
        $w2 = explode(' ',$menny_me,2);
        if (count($w2)==2) {
            $mennyiseg = (float)$w2[0];
            $me = $w2[1];
        } else {
            $mennyiseg = 0;
            $me = '';
        }    
        $s2 = kiemel($liStr,'<a','/a>');
        $nev =  kiemel($s2,'>','<');
        for ($i=0; $i<10;$i++) {
            $nev = str_replace('  ',' ',$nev);
        }    
        $nev = str_replace("\n",'',$nev);
        $nev = str_replace("\r",'',$nev);
        $nev = trim(str_replace($mit, $mire, $nev));
        if (!in_array($me,$mes)) {
            $nev = $me.' '.$nev;
            $me = '';            
        }    
        if ($mennyiseg == 'islés') {
            $nev = 'izlés '.$nev;
            $mennyiseg = 0;
        }
        $hozzavalo = new \stdClass();
        $hozzavalo->mennyiseg = $mennyiseg;
        $hozzavalo->nev = $nev;
        $hozzavalo->me = trim(str_replace($mit,$mire,' '.trim($me).' '));
        $hozzavalok[] = $hozzavalo;

        $liStr = kiemel($s1,'<li','</li>');
    }

    // leírás
    $w = $s;
    $s1 = kiemel($w, 'Elkészítés</h3>','<div class="d-print-none');
    $s1 = str_replace('</li>','[br]',$s1);
    $s1 = str_replace("\n",'',$s1);
    $s1 = str_replace("\r",'',$s1);
    for ($i=0; $i<10;$i++) {
        $s1 = str_replace('  ',' ',$s1);
    }    
    $s1 = strip_tags($s1);
    $recept->leiras = str_replace('[br]',"\n\n",trim($s1));

    /*
    if ($kep != '') {
        $imageFileType = strtolower(pathinfo($kep,PATHINFO_EXTENSION));
        $imgFileName = 'images/'.$cim.'.'.$imageFileType;
        if (file_exists($imgFileName)) {
            unlink($imgFileName);			
        }
        copy($kep, $imgFileName);
    }
    */
}    

?>