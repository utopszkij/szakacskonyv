<?php

/* kiemeli a $start - $end közti részt $s -ből
   $s -ből törli a kiemelt részt
   @param string $s a vizsgálandó string (modosul)
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
    return $result;
}

/**
* adatátvétel a cookpad.hu -ról
* a $recept mezőit és a $hozzavalok tömb mezőket tölti ki.
* képet másol az images könyvtárba.
* @param string $url; // recept képernyő a cookpad.hu -n
* @param Rekord $recept
* @param array $hozzavalok
*/
function atvetel($url = 'https://www.cookpad.hu/....',
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
	//$html = implode("\n",file($url));
    
	$s = kiemel($html,'<body','</body>');
    // kép
    $kep = '';
    $s1 = kiemel($s,'<picture','</picture>');
    $kep = kiemel($s1,'src="','"');

    // recep név
    $s1 = '';
    $cim = '';
    $s1 = kiemel($s,'<h1','</h1>'); // .....dir='auto">cím</h1>
    $cim = kiemel($s1,'dir="auto">','</h1>');    
    $recept->nev = html_entity_decode($cim);

    // elkészítési idő
    $s1 = '';
    $elkeszites = '';
    $s1 = kiemel($s,'<span class="mise-icon-text">','</span>'); 
    $elkeszites = str_replace(' perc','',$s1);    
    $recept->elkeszites = html_entity_decode($elkeszites);

    // adag
    $s1 = '';
    $adag = '';
    $s1 = kiemel($s,'<span class="mise-icon-text">','</span>'); 
    $adag = str_replace(' fő részére','',$s1);    
    $recept->adag = html_entity_decode($adag);



    // hozzávalók
    $hozzavalok = []; // [{mennyiseg, nev, me},...]
    $s1 = kiemel($s,'<ol','</ol>'); // ....><li..>...</li> 
    $w = explode('<l1',$s1); // ['...','..>elem</li>',...]
    if (count($w) > 1) {
        for ($i=1; $i<count($w); $i++) {
            $elemStr = $w[1]; // ...><bdi..>...<bdi>...</li>
            // mennyiség és mennyiség egység
            $s2 = kiemel($elemStr, '<bdi class=""font-semi-bold">','</bdi>');
            $w2 = explode($s2,' '); // $w2[0] mennyiség, $w2[1] mennyiségegység
            $hozzavalo = new \stdClass();
            $hozzavalo->nev = html_entity_decode($elemStr);
            if (in_array($w2[1],$mes)) {
                $hozzavalo->me = $w2[1];
            } else {
                $hozzavalo->me = '';
            }    
            // mennyiség #-# értelmezéssel
            $w3 = explode('-',$w2[0],2);
            if (count($w3) == 2) {
                $w2[0] = ((int)(trim($w3[0])) + (int)(trim($w3[1]))) / 2;
            }        
            $hozzavalo->mennyiseg = $w2[0];
        }
    }    



    // leírás
    $s1 = kiemel($s,'<span>Elkészítés</span>','<span>Reakciók</span>');
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
