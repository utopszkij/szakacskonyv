<?php
/**
* adatátvétel a mindmegette.hu -ról
* a $recept mezőit és a $hozzavalok tömb mezőket tölti ki.
* képet másol az images könyvtárba.
* @param string $url; // recept képernyő a mindmegette.hu -n
* @param Rekord $recept
* @param array $hozzavalok
*/
function atvetel($url = 'https://www.mindmegette.hu/sult-kacsacomb-kaposztas-tesztaval.recept/',
    &$recept, &$hozzavalok) {	
	global $mes, $mit, $mire;
	$cim = '';
	$kep = '';
	$elkeszites = '';
	$adag = 4;
	$elkeszitesiIdo = 0;
	$energia = 0;
	$hozza = '';
	$s = implode("\n",file($url));
	$w = explode('id="recipeAllDetails"',$s);
	
	if (count($w) > 1) {
		$w = explode('<h1 class="title">',$w[1]);

		if (count($w) > 1) {
			// adag
			$i = strpos($w[1],'adag</strong>');
			if ($i > 0) {
				$s = substr($w[1],$i-3,3);
				$s = trim(str_replace('>','',$s));
				$recept->adag = intval($s);
			}

			// elkészítési idő
			$i = strpos($w[1],'perc</strong>');
			if ($i > 0) {
				$s = substr($w[1],$i-4,4);
				$s = trim(str_replace('>','',$s));
				$s = trim(str_replace('g','',$s));
				$recept->elkeszites = intval($s);
			}

			$w = explode('</h1>',$w[1]);
			$recept->nev = html_entity_decode(trim($w[0]));


			if (count($w) > 1) {
				$w2 = explode('<ul class="shopingCart">',$w[1]);
				if (count($w2) > 1) {
					$w2 = explode('</ul>',$w2[1]);
					$hozza = $w2[0];	
					$hozza = str_replace("<span class='comment'>",';;;',$hozza);
					$hozza = str_replace('</span>',';',$hozza);
					$hozza = str_replace('</li>',"\n",$hozza);
					$hozza = strip_tags($hozza,['br']);
				}	
				// 2022.10.17 másik formátum
				$w2 = explode('<ul class="egyszeru-hozzavalok-lista">',$w[1]);
				if (count($w2) > 1) {
					$w2 = explode('</ul>',$w2[1]);
					$hozza = $w2[0];	
					$hozza = str_replace("<span class='comment'>",';;;',$hozza);
					$hozza = str_replace('</span>',';',$hozza);
					$hozza = str_replace('</li>',"\n",$hozza);
					$hozza = strip_tags($hozza,['br']);
				}	
							
			}

			if (count($w) > 1) {
				$w2 = explode('imageContainer',$w[1]);
				if (count($w2) > 1) {
					$w2 = explode('src="',$w2[1]);
					if (count($w2) > 1) {
						$w2 = explode('"',$w2[1]);
						$kep = 'https://mindmegette.hu'.$w2[0];
					}			
				}						
			}
			
			if (count($w) > 1) {
						$w2 = explode('<ol>',$w[1]);
						if (count($w2) > 1) {
							$w2 = explode('</ol>',$w2[1]);
							$elkeszites = $w2[0];	
							$w2 = explode('<div',$elkeszites);
							$elkeszites = '<ol>'.$w2[0].'</ol>';
						} else {
							$w2 = explode('<div class="instructions">',$w[1]);
							if (count($w2) > 1) {
								$w2 = explode('</div>',$w2[1]);
								$elkeszites = $w2[0];	
							}	
						}	
						$elkeszites = str_replace("\n",' ',$elkeszites);
						//$elkeszites = str_replace('</li>',"\n",$elkeszites);
						$elkeszites = str_replace("\r",' ',$elkeszites);
						$recept->leiras = trim(strip_tags($elkeszites,['ul','ol','li','p','br']));
			}
			if ($kep != '') {
				$imageFileType = strtolower(pathinfo($kep,PATHINFO_EXTENSION));
				$imgFileName = 'images/'.$cim.'.'.$imageFileType;
				if (file_exists($imgFileName)) {
					unlink($imgFileName);			
				}
				copy($kep, $imgFileName);
			}
		}
		
		$w1 = explode("\n",$hozza);
		$i = 0;
		foreach ($w1 as $hozzavalo) {
			$hozzavalo = str_replace("\r;",'',$hozzavalo);
            $hozzavalo = html_entity_decode($hozzavalo);
            $hozzavalo = trim(str_replace($mit, $mire, ' '.$hozzavalo.' '));
			if (strpos($hozzavalo,':') <= 0) {
				$w2 = explode(';',$hozzavalo);
				$w2[] = ' ';
				if (count($w2) > 3) {
					if (!in_array($w2[1],$mes)) {
						$w2[3] = $w2[1].' '.$w2[3];
						$w2[1] = '';
					}	
					$w2[0] = str_replace('1/2','0.5',$w2[0]);
					$hozzavalok[$i] = new \stdClass();
					$hozzavalok[$i]->nev = str_replace('&nbsp','',trim($w2[3]));
					$hozzavalok[$i]->mennyiseg = trim($w2[0]);
					$hozzavalok[$i]->me = trim($w2[1]);
				} else {	
					// 2022.10.17 másik formátum
						$hozzavalo = str_replace(';',' ',$hozzavalo);
						$w2 = explode(' ',$hozzavalo,3);
						$w2[] = ' ';
						if (count($w2) > 2) {
							if (!in_array($w2[1],$mes)) {
								$w2[2] = $w2[1].' '.$w2[2];
								$w2[1] = '';
							}	
							$hozzavalok[$i] = new \stdClass();
							$hozzavalok[$i]->nev = str_replace('&nbsp','',trim($w2[2]));
							$w2[0] = str_replace('1/2','0.5',$w2[0]);
							$hozzavalok[$i]->mennyiseg = trim($w2[0]);
							$hozzavalok[$i]->me = trim($w2[1]);
						}
				}	
				$i++;
			}
		}
	}
}	

?>
