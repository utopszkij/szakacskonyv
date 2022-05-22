<?php
/**
* adatátvétel a mindmegette.hu -ról
* a képernyőn lévő nev. leiras, hozzavalo0, mennyiseg0, me0,.... mezőket tölti ki.
* képet másol az images könyvtárba.
* @param string $url; // recept képernyő a mindmegette.hu -n
*/
function atvesz($url = 'https://www.mindmegette.hu/sult-kacsacomb-kaposztas-tesztaval.recept/') {	
	$cim = '';
	$kep = '';
	$hozzavalok = '';
	$elkeszites = '';
	
	$s = implode("\n",file($url));
	$w = explode('id="recipeAllDetails"',$s);
	
//	echo $w; exit();
	
	if (count($w) > 1) {
		$w = explode('<h1 class="title">',$w[1]);
		if (count($w) > 1) {
			$w = explode('</h1>',$w[1]);
			$cim = $w[0];

			if (count($w) > 1) {
						$w2 = explode('<ul class="shopingCart">',$w[1]);
						if (count($w2) > 1) {
							$w2 = explode('</ul>',$w2[1]);
							$hozzavalok = $w2[0];	
							$hozzavalok = str_replace("<span class='comment'>",';;;',$hozzavalok);
							$hozzavalok = str_replace('</span>',';',$hozzavalok);
							$hozzavalok = str_replace('</li>',"\n",$hozzavalok);
							$hozzavalok = strip_tags($hozzavalok,['br']);
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
							$elkeszites = $w2[0];
							$elkeszites = str_replace('</li>','\n',$elkeszites);
							$elkeszites = str_replace("\n",'',$elkeszites);
							$elkeszites = str_replace("\r",'',$elkeszites);
							$elkeszites = str_replace('"','\"',$elkeszites);
							$elkeszites = strip_tags($elkeszites);
						}				
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
	}	
	?>
	<script>
		document.getElementById('nev').value = "<?php echo $cim; ?>";
		<?php 
			$w = explode("\n",$hozzavalok);
			for ($i = 0; ($i < count($w) & $i < 15); $i++) {
				$w2 = explode(';',$w[$i]);
				if (count($w2) > 3) {
					echo 'document.getElementById("hozzavalo'.$i.'").value = "'.$w2[3].'";'."\n";
					echo 'document.getElementById("mennyiseg'.$i.'").value = "'.$w2[0].'";'."\n";
					echo 'document.getElementById("me'.$i.'").value = "'.$w2[1].'";'."\n";
				}
			}		
		?>
		document.getElementById('leiras').value = "<?php echo $elkeszites; ?>";	
	</script>
	<?php	
	
}	

?>