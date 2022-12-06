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
* adatátvétel a receptneked.hu -ról
* a $recept mezőit és a $hozzavalok tömb mezőket tölti ki.
* képet másol az images könyvtárba.
* @param string $url; // recept képernyő a mindmegette.hu -n
* @param Rekord $recept
* @param array $hozzavalok
*/
function atvetel($url = 'https://www.receptneked.hu/....',
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
	$s = implode("\n",file($url));
	$w = explode('id="mainbar"',$s,2);

    // recep név
	if (count($w) > 1) {
        $s = $w[1];
        $cim = kiemel($s,'id="recipe-title">','</h1>');
        $recept->nev = html_entity_decode(str_replace('recept','',$cim));
    }

    // kép
    $kep = '';
    $w = explode('id="main-image"',$s,2);
    if (count($w) > 1) {
        $s = $w[1];
        $kep = kiemel($s,'src="','"');
    }

    // elkészítési idő
    $w = explode('Elkészítési idő:',$s,2);
    if (count($w) > 1) {
        $s = $w[1];
        $elkeszitesiIdo = kiemel($s,'-bottom">',' perc');
        $recept->elkeszites = $elkeszitesiIdo;
    }

    // mennyiség
    $w = explode('Mennyiség:',$s,2);
    if (count($w) > 1) {
        $s = $w[1];
        $recept->adag = kiemel($s,'yield">',' fő');
    }

    // hozzávalók
    $hozzavalok = [];
    $w = explode('Hozzávalók',$s,2);
    if (count($w) > 1) {
        $s = $w[1];
        if (stripos('Elkészitese',$s) > 0) {
            $hozzaStr = kiemel($s,'<ul','Elkészítése'); // <li>...</li> -k vannak benne
        } else {
            $hozzaStr = kiemel($s,'<ul','Elkészítés'); // <li>...</li> -k vannak benne
        }    
        $s1 = kiemel($hozzaStr,"recipeIngredient'>",'</li>');
        while ($s1 != '') {
            $s1 = str_replace('</span>','',$s1);
            // most $s1-ben a hozzávlaó lista egy sora van string formában
            if (strpos($s1,'</a>') > 0) {
                // reklám link van benne; el kell távolítani
                $w2 = explode('<a',$s1,2);
                $s1 = $w2[0];
                $w2 = explode('</a>',$w2[1],2);
                $s1 .= $w2[1];
            } 
            // ne legyenek benne html elemek
            $i = stripos($s1,'<');
            if ($i > 0) {
                $s1 = substr($s1,0,$i);
            }
            $s1 = html_entity_decode($s1);
            $s1 = str_replace($mit, $mire, ' '.$s1.' ');
            $s1 = trim($s1);
            $hozzavalo = new \stdClass();
            $hozzavalo->mennyiseg = 0;
            $hozzavalo->nev = '';
            $hozzavalo->me = '';

            $w = explode(' ',$s1,3);
            // alap esetben w[0] = mennyiség, w[1] = me, w[2]= név
            if (count($w) == 2) {
                // feltehetőleg a me hiányzik
                $w[2] = $w[1];
                $w[1] = '';
            }
            if (count($w) == 3) {
                if (!in_array($w[1],$mes)) {
                    // $w[1] nem mértékegység
                    $w[2] = $w[1].' '.$w[2];
                    $w[1] = '';
                }
                // mennyiség #-# és #/# értelmezéssel
                $w2 = explode('-',$w[0],2);
                if (count($w2) == 2) {
                    $w[0] = ((int)(trim($w2[0])) + (int)(trim($w2[1]))) / 2;
                }        
                $w2 = explode('/',$w[0],2);
                if (count($w2) == 2) {
                    $w[0] =  (int)(trim($w2[0])) / (int)(trim($w2[1]));
                }    
                $w[0] = str_replace(',','.',$w[0]);

                if (is_numeric($w[0])) {
                    $hozzavalo->mennyiseg = $w[0];
                    $hozzavalo->me = $w[1];
                    $hozzavalo->nev = $w[2];
                } else {
                    $hozzavalo->nev = $s1;
                }
            } else {
                $hozzavalo->nev = $s1;
            }
            $hozzavalok[] = $hozzavalo;
            $s1 = kiemel($hozzaStr,"recipeIngredient'>",'</li>');
        }
    }

    // leírás

    // a.eset van amikor  egy P -ben van az egész így:<p> Elkeszítése:<br>......</p>
    // b.eset van amikor  igy van: <div....<p>Elkeszítése:</p><p>......</p>...</div>

    // most az $s -ben a ':' -al kezdődő string részlet van
    $i = stripos($s,'<p');
    $j = stripos($s,'</p>');
    if (($j > $i) | ($j < 20)) {
        $s1 = kiemel($s, '<p>','</div>');  //b.eset
        // $recept->leiras = str_replace('>','',$s1);
    } else {
        $s1 =kiemel($s, ':','</p>');     //a.eset
    }   

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
