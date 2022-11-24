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
* adatátvétel a topreceptek.hu -ról
* a $recept mezőit és a $hozzavalok tömb mezőket tölti ki.
* képet másol az images könyvtárba.
* @param string $url; // recept képernyő a cookpad.hu -n
* @param Rekord $recept
* @param array $hozzavalok
*/
function atvetel($url = 'https://www.topreceptek.hu/....',
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
	$html = implode("\n",file($url));
    $s = kiemel($html,'<body','</body>');
    
    // kép
    $kep = '';
    $s1 = kiemel($s,'<div class="main-imge">','</div>');
    if ($s1 != '') {
        $kep = 'https://topreceptek.hu/'.kiemel($s1,'src="','"');
    }

    // recep név
    $s1 = '';
    $cim = '';
    $s1 = kiemel($s,'<h1 class="title">','</h1>'); 
    $recept->nev = html_entity_decode($s1);

    // elkészítési idő (kettő is lehet)
    $s1 = '';
    $elkeszites = '';
    $s1 = kiemel($s,'<div class="times">','<div class="ingredients">'); 
    $s = '<div class="ingredients">'.$s;
    $s2 = kiemel($s1,'<strong>','</strong>');
    $elkeszites = (int)str_replace(' perc','',$s2);   
    // második idő... 
    $s3 = kiemel($s1,'<strong>','</strong>');
    if ($s3 != '') {
        $elkeszites2 = (int)str_replace(' perc','',$s3);    
        $elkeszites = $elkeszites + $elkeszites2;
    }    
    $recept->elkeszites = html_entity_decode($elkeszites);


    // adag nincs
    // $s1 = kiemel($s,'<span class="mise-icon-text">','</span>'); 
    $recept->adag = 4;


    // hozzávalók
    $hozzavalok = []; // [{mennyiseg, nev, me},...]
    $s1 = kiemel($s,'Alapanyagok</div>','<div class="instructions">'); 
    $s = '<div class="instructions">'.$s;
    while (($s1 != '') & (strlen($s1) > 10)){
        $hozzavalo = new \stdClass();
        $s2 = kiemel($s1,'<div class="key">','</div>');
        $w2 = explode(' ',$s2,2); // $w2[0] mennyiség, $w2[1] mértékegység
        $w2[] = ''; // hogy biztos legyen 2 elem
        $hozzavalo->nev = kiemel($s1,'<div class="value">','</div>');
        $hozzavalo->nev = trim(str_replace($mit,$mire,' '.$hozzavalo->nev.' '));
		if ($hozzavalo->nev == '') {
			break;
		}

        // $w2[1] valós mértékegység?
        $w2[1] = trim(str_replace($mit,$mire,' '.$w2[1].' '));
        if (count($w2) > 1) {
            if (in_array($w2[1],$mes)) {
                $hozzavalo->me = $w2[1];
            } else {
                $hozzavalo->me = '';
                $hozzavalo->nev = $w2[1].' '.$hozzavalo->nev;
            }    
        }
        // a $w2[0] mennyiség lehet 4-5 formában is
        $w3 = explode('-',$w2[0],2);
        if (count($w3) == 2) {
            $w2[0] = ((int)(trim($w3[0])) + (int)(trim($w3[1]))) / 2;
        }        
        // a $w2[0] lehet 1/2 -is
        if ($w2[0] == '1/2') {
            $w2[0] = 0.5;
        }
        $hozzavalo->mennyiseg = $w2[0];
        $hozzavalok[] = $hozzavalo;
    }


    // leírás
    $s1 = kiemel($s,'<div class="instructions">','<div class="interest int-15');
    $recept->leiras = strip_tags($s1,['ul','ol','li','p','br']);

    if ($kep != '') {
        $imageFileType = strtolower(pathinfo($kep,PATHINFO_EXTENSION));
        $imgFileName = 'images/'.$cim.'.'.$imageFileType;
        if (file_exists($imgFileName)) {
            unlink($imgFileName);			
        }
        copy($kep, $imgFileName);
    }

}	

?>
