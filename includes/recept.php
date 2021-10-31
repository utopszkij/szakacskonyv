<?php
error_reporting(E_ALL);

// $_GET['id']
function receptdelete() {
	// normál user csak a saját maga által felvittet törölheti
	// system admin mindent törölhet
	$db = new \RATWEB\DB\Query('receptek');
	$db->where('id','=',$_GET['id']);
	$recept = $db->first();
	if (($recept->created_by != $_SESSION['loged']) &
	    ($_SESSION['logedName'] != ADMIN)) {
	   receptek();
	} else {
		$db = new \RATWEB\DB\Query('receptek');
		$db->where('id','=',$_GET['id'])->delete();
		$db = new \RATWEB\DB\Query('hozzavalok');
		$db->where('recept_id','=',$_GET['id'])->delete();
		receptek();
	}
}

function receptsave() {
	// get -ben: id, leiras, hozzvalok0, mennyiseg0, me0, hozzavalok1,....
	$receptId = $_POST['id'];
	if ($receptId == 0) {
		$db = new \RATWEB\DB\Query('receptek');
		$r = new \RATWEB\DB\Record();
		$r->nev = $_GET['nev'];
		$r->created_by = $_SESSION['loged'];
		$receptId = $db->insert($r);
	}


	// leírás tárolása, csak a saját maga által felvittet módosíthatja
	$db = new \RATWEB\DB\Query('receptek');
	$db->where('id','=',$receptId);
	$recept = $db->first();
	if ($recept->created_by == $_SESSION['loged']) {	
		$r = new \RATWEB\DB\Record();
		$r->leiras = $_POST['leiras'];
		$r->nev = $_POST['nev'];
		$db->where('id','=',$receptId)
			->where('created_by','=',$_SESSION['loged'])
		   ->update($r);
		
		// meglévő hozzávalók törlése
		$db = new \RATWEB\DB\Query('hozzavalok');
		if ($receptId > '0') {
			$db->where('recept_id','=',$receptId)->delete();	
		}
		
		// hozzávalók felvitele
		for ($i = 0; $i < 15; $i++) {
			if (isset($_POST['hozzavalo'.$i])) {
				if ($_POST['hozzavalo'.$i] != '') {
					$r = new \RATWEB\DB\Record();
					$r->recept_id = $receptId;
					$r->nev = $_POST['hozzavalo'.$i];
					$r->mennyiseg = $_POST['mennyiseg'.$i];
					if (!is_numeric($r->mennyiseg)) $r->mennyiseg = 0;
					$r->me = $_POST['me'.$i];
					$db->insert($r);
				}
			}	
		}
		// kép file feltöltése
		if (file_exists($_FILES['kepfile']['tmp_name'])) { 
			$target_dir = str_replace('/includes','',__DIR__)."/images/";
			$target_file = $target_dir . basename($_FILES["kepfile"]["name"]);
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			$target_file = $target_dir.$_POST['nev'].'.'.$imageFileType;		
			$uploadOk = '';
			
		   $check = getimagesize($_FILES["kepfile"]["tmp_name"]);
			if($check == false) {
			    $uploadOk = 'nem kép fájl';
			}
			
			if (file_exists($target_file)) {
			  unlink($target_file);
			}
			
			if ($_FILES["kepfile"]["size"] > 5000000) {
			  $uploadOk .= ' túl nagy fájl méret';
			}
			
			if($imageFileType != "jpg" && 
			   $imageFileType != "png" && 
			   $imageFileType != "jpeg" && 
			   $imageFileType != "gif" ) {
			  $uploadOk .= ' nem megengedett fájl kiterjesztés';
			}
			
			if ($uploadOk == '') {
			  if (!move_uploaded_file($_FILES["kepfile"]["tmp_name"], $target_file)) {
			    echo "Hiba a fájl feltöltés közben ".$target_file; exit();
			  }
			} else {
				echo $uploadOk; exit();			
			}
		} // van file upload
	} // jogosult a müveletre
	receptek();
}

function receptKep($recept) {
	$kep = 'images/etkeszlet.png'; 
	if (file_exists('images/'.$recept->nev.'.png')) {
		$kep = 'images/'.$recept->nev.'.png';
	} else if (file_exists('images/'.$recept->nev.'.jpg')) {
		$kep = 'images/'.$recept->nev.'.jpg';
	} else if ($recept->nev != '') {
	   	$receptNev = strtolower($recept->nev);
	   	$receptNev = urlencode($receptNev);
		   $sorok = file('https://www.picsearch.com/index.cgi?q='.$receptNev);
		   $s = implode("\n",$sorok);
		   // echo 'SSS '.$s.' SSS';
		   $w = explode('"result"',$s);
		   if (count($w) > 1) {
		   	$w = explode('src="',$w[1]);
			   if (count($w) > 1) {
			   	$w = explode('"',$w[1]);
			   	$kep = $w[0];
			   }	
		   }	
	}
	return $kep;
}

/**
* $_GET['id'] alapján recept képernyő
*/
function recept() {
	global $hozzavalok;
	$recept = JSON_decode('{"id":0, "leiras":"", "nev":""}');	
	$hozzavalok = [];	
	$receptId = $_GET['id'];
	$disable = '';

	// recept és hozzávalók beolvasása
	if ($receptId > '0') {
		$db = new \RATWEB\DB\Query('receptek');
		$db->where('id','=',$receptId);
		$recept = $db->first();	
		$db = new \RATWEB\DB\Query('hozzavalok');
		$hozzavalok = $db->where('recept_id','=',$receptId)->all();
		if (($recept->created_by != $_SESSION['loged']) &
		    ($_SESSION['logedName'] != ADMIN)) {
			$disable = ' disabled="disabled"';		
		}	
	}
	
	// meglévő recept nevek beolvasása
	$db = new \RATWEB\DB\Query('receptek');
	?>	
	<script>
	var receptNevek = <?php echo JSON_encode($db->select(['id','nev'])->all()); ?>;
	</script>	
	<?php	
	
	function meSelect($v,$i) {
		global $hozzavalok;
		if ($v == $hozzavalok[$i]->me) {
			echo ' selected="selected"';		
		}	
   }
	$kep = receptKep($recept);
	while (count($hozzavalok) < 15) {
			$hozzavalok[] = JSON_decode('{"mennyiseg":"", "me":0, "hozzavalo":""}');					
	}
	?>
	<div id="recept">
		<form id="receptForm" action="index.php?task=receptsave" method="post" enctype="multipart/form-data">
			<input type="hidden" value="receptsave" name="task" />			
			<input type="hidden" value="<?php echo $receptId; ?>" name="id" id = "id" />
			<div class="row">
				<div class="col-md-6">
					<div class="form-outline mb-4">
						<h2>Recept<h2>			
					</div>
					<div class="form-outline mb-4">
						<label>Recept megnevezése:</label>			
					</div>
					<div class="form-outline mb-4">
						<input type="text" value="<?php echo $recept->nev; ?>"
						 id="nev" name="nev" <?php echo $disable; ?>
						 class="form-control"  style="width:500px" />
					</div>
					<?php if ($disable == '') : ?>
					<div class="form-outline mb-4">
						A mindmegette.hu -n jelöld be a hozzávalókat és 
						másold az alábbi input mezőbe, majd kattints a "Feldolgoz" gombra!					</div>
					<div class="form-outline mb-4">
						<input type="text" id="paste" <?php echo $disable; ?> />
						<button type="button" class="btn btn-secondary"
						 onclick="processPaste()">Feldolgoz</button>
					</div>
					<?php endif; ?>
					
				</div>
				<div class="col-md-6">
					<?php 
					if ($kep != '') {
						echo '<img src="'.$kep.'" class="receptKep" class="receptKep" />';				
					}				
					?>
				</div>
			</div>

			<div class="row">
			<div class="col-md-6">
				<h3>Hozzávalók 4 főre</h3>
				<?php for ($i=0; $i < count($hozzavalok);  $i++) : ?>
				<div class="form-outline col-mb-10">
					<input type="text" <?php echo $disable; ?>
						value="<?php echo $hozzavalok[$i]->nev; ?>" 
						name="hozzavalo<?php echo $i; ?>" 
						id="hozzavalo<?php echo $i; ?>"/>			
					<input type="number" min="0" max="100" step="0.5" 
					   <?php echo $disable; ?>
						value="<?php echo $hozzavalok[$i]->mennyiseg; ?>"
						name="mennyiseg<?php echo $i; ?>" style="width:80px" 
						id="mennyiseg<?php echo $i; ?>" />			
					<select style="width:90px; height:30px"
					   <?php echo $disable; ?> 
						name="me<?php echo $i; ?>"
						id="me<?php echo $i; ?>">	
						<option value="?"<?php meSelect('?',$i); ?>>?</option>		
						<option value="db"<?php meSelect('db',$i); ?>>db</option>		
						<option value="csomag"<?php meSelect('csomag',$i); ?>>csomag</option>		
						<option value="tábla"<?php meSelect('tábla',$i); ?>>tábla</option>		
						<option value="fej"<?php meSelect('fej',$i); ?>>fej</option>		
						<option value="g"<?php meSelect('g',$i); ?>>g</option>		
						<option value="dkg"<?php meSelect('dkg',$i); ?>>dkg</option>		
						<option value="kg"<?php meSelect('kg',$i); ?>>kg</option>		
						<option value="ek"<?php meSelect('ek',$i); ?>>ek</option>		
						<option value="tk"<?php meSelect('tk',$i); ?>>tk</option>		
						<option value="kk"<?php meSelect('kk',$i); ?>>kk</option>		
						<option value="csipet"<?php meSelect('csipet',$i); ?>>csipet</option>		
						<option value="ml"<?php meSelect('ml',$i); ?>>ml</option>		
						<option value="dl"<?php meSelect('dl',$i); ?>>dl</option>		
						<option value="l"<?php meSelect('l',$i); ?>>l</option>
						<option value="bögre"<?php meSelect('bögre',$i); ?>>bögre</option>
						<option value="pohár"<?php meSelect('pohár',$i); ?>>pohár</option>
						<option value="pár"<?php meSelect('pár',$i); ?>>pár</option>		
						<option value="gerezd"<?php meSelect('gerezd',$i); ?>>gerezd</option>		
						<option value="fej"<?php meSelect('fej',$i); ?>>fej</option>		
						<option value="szelet"<?php meSelect('szelet',$i); ?>>szelet</option>		
						<option value=""<?php meSelect('',$i); ?>></option>		
					</select>
				</div>
				<?php endfor; ?>
				<p>Hozzávaló törléshez, töröld ki a nevét!</p>
			</div>
			<div class="col-md-6">
				<h3>Elkészítés</h3>			
				<textarea name="leiras" id="leiras"
				<?php echo $disable; ?> 
				cols="40" rows="11"><?php echo $recept->leiras; ?></textarea>
				<p>Kép fájl (jpg vagy png, nem kötelező)</p>
				<input type="file" name="kepfile" <?php echo $disable; ?> />			
			</div>
			
			</div>
				
			<div class="form-outline mb-4">
				<?php if (($recept->id == 0) | ($recept->created_by == $_SESSION['loged'])) : ?>
				<button type="button" class="btn btn-primary" onclick="okClick()">
				<em class="fas fa-check"></em>&nbsp;Tárolás</button>
				<?php endif; ?>
				&nbsp;
				<?php if ($recept->id > 0) : ?>
					<button type="button" class="btn btn-secondary"
					onclick="location='index.php?task=receptprint&id=<?php echo $receptId; ?>';">
					<em class="fas fa-print"></em>&nbsp;Nyomtatas</button>
					&nbsp;
					<?php if (($recept->created_by == $_SESSION['loged']) | 
					          ($_SESSION['logedName'] == ADMIN)) : ?>
						<button type="button" class="btn btn-danger"
						onclick="delClick()">
							<em class="fas fa-ban"></em>&nbsp;Recept törlése 
						</button>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</form>
	</div>
	<script>
	function okClick() {
		jo = true;
		id = document.getElementById('id').value;
		nev = document.getElementById('nev').value;
		var i = 0;
		for (i=0; i< receptNevek.length; i++) {
			if ((receptNevek[i].nev == nev) &
			    (receptNevek[i].id != id)) {
			    jo = false;
			    alert('Már van ilyen nevü recept!');	
			}		
		}
		if (jo) {
			document.getElementById('receptForm').submit();		
		}	
	}
	function delClick() {
		if (window.confirm('Bitos, törölni akarod ezt a receptet?')) {
			document.location = 'index.php?task=receptdelete&id=<?php echo $recept->id; ?>';		
		}	
	}
	function processPaste() {
		var i = 0;
		var j = 0;
		var szavak = []; // a nem TAB -al szeparált lista szavai
		var items = [];  // a TAB -al szeparált lista elemei
		var item = '';   // a TAB -al szeparált lista eleme (#me | hozzávaló neve)
		var m = '';      // mennyiség
		var me = '';     // mennyiség egység
		var h = '';      // hozzávaló neme
		var s = document.getElementById('paste').value;
		
		// a checkboxos hozzávaló lista TAB -al szeparátan jön át
		items = s.split(/\t/);
		
		// a nem checkboxos hozzávaló lista folyamatos szövegként jön át.
		// konvertáljuk TAB -al szeparált listára	
		if (items.length == 1) {
			szavak = s.split(' ');
			items = [];
			i = 0;
			j = 0;
			var item = '';
			while (i < szavak.length) {
				szavak[i] = szavak[i].replace('-','.');
				szavak[i] = szavak[i].replace(',','.');
				if (isNaN(szavak[i]) == false) {
					if (item != '') {
						items.push(item.trim());
						item = '';			
					}
					item = szavak[i]+szavak[i+1];
					i = i + 1;
					items.push(item);
					item = '';
				} else {
					item = item+' '+szavak[i];			
				}
				i = i + 1;		
			}
		}
		console.log(szavak);
		console.log(items);
	
		// items -ek feldolgozása
		i = 0; // pointer a feldolgozandó szóra
		j = 0; // ponter a hozzávalókra
		while (i < items.length) {
		  item = items[i].trim();
		  item = item.replace('-','.');
		  item = item.replace(',','.');
		  me = item.replace(/[0123456789\.]*/,'').trim();
		  m = item.replace(me,'').trim();
		  if (m != '') {
		     i = i + 1
		  	  h = items[i];	
		  } else {
		  	  me = ''; 
			  h = item;		  
		  }
		  // beír a képernyőre
		  document.getElementById('mennyiseg'+j).value = m.trim();
		  document.getElementById('me'+j).value = me.trim();
		  document.getElementById('hozzavalo'+j).value = h.trim();
		  j = j + 1;
		  i = i + 1;
		}
	}
	</script>
	
	<?php
}

/**
* $_GET['id'] alapján recept nyomtatás 
*/
function receptprint() {
	global $hozzavalok;
	$recept = JSON_decode('{"id":0, "leiras":"", "nev":""}');	
	$hozzavalok = [];	
	$receptId = $_GET['id'];
	$db = new \RATWEB\DB\Query('receptek');
	if ($receptId > '0') {
		$db->where('id','=',$receptId);
		$recept = $db->first();	
	}
	$db = new \RATWEB\DB\Query('hozzavalok');
	if ($receptId > '0') {
		$hozzavalok = $db->where('recept_id','=',$receptId)->all();	
	}
	$recept->leiras = str_replace("\n",'<br />',$recept->leiras);
	?>
	<div class="row">
		<div class="col-md-12">
			<strong><?php echo $recept->nev; ?></strong><br /><br />
			<img src="<?php echo receptKep($recept); ?>" class="receptKep" />
			<strong>Hozzávalók</strong><br />		
			<?php foreach($hozzavalok as $hozzavalo) : ?>
			<div><?php echo $hozzavalo->nev.' '.$hozzavalo->mennyiseg.' '.$hozzavalo->me; ?></div>
			<?php endforeach; ?>
		</div>
		<div class="col-md-12">
			<br /><strong>Elkészítés</strong><br />
			<?php echo $recept->leiras; ?>
		</div>			
	</div>
	<script>
		window.print();
	</script>
	<?php	
}	


function receptek() {
	$db = new \RATWEB\DB\Query('receptek');
	$db->exec('CREATE TABLE IF NOT EXISTS receptek (
		    id int AUTO_INCREMENT,
		    nev varchar(80),
		    leiras text,
		    PRIMARY KEY (id),
		    created_by int,
		    KEY (nev)
		)');
	$db->exec('CREATE TABLE IF NOT EXISTS hozzavalok (
		    id int AUTO_INCREMENT,
		    recept_id int,
		    nev varchar(80),
		    mennyiseg numeric(10,2),
		    me varchar(8),
		    PRIMARY KEY (id),
		    KEY (recept_id)
		)');
	$db = new \RATWEB\DB\Query('receptek');
	$db->orderBy('nev');
	$list = $db->all();
	?>
	<div id="receptList">
		<img src="https://cdn.pixabay.com/photo/2014/09/17/20/26/restaurant-449952_960_720.jpg"
		class="dekorImg" />
		<div class="row">
			<h2>Receptek</h2>
		</div>		
		<div id="receptListTable">
			<table style="width:100%">
				<thead>
					<tr><td>ID</td><td>Megnevezés</td></tr>		
				</thead>
				<tbody>
					<?php
					foreach ($list as $item) {
						$trClass = '';
						if ($item->created_by == $_SESSION['loged']) {
							$trClass = 'sajat';
						} else {
							$trClass = 'idegen';
						}
						echo '<tr class="'.$trClass.'">'.
						'<td><a href="?task=recept&id='.$item->id.'">'.$item->id.'</a></td>'.
						'<td><a href="?task=recept&id='.$item->id.'">'.$item->nev.'</a></td></tr>';
					}
					?>
				</tbody>
			</table>
		</div>	
		<p>Kattints a recept nevére a megtekintéséhez, modosításhoz, törléséhez!</p>
		<div style="text-align:center">
			<a href="?task=recept&id=0" class="btn btn-primary">
				<em class="fas fa-plus"></em>&nbsp;Új recept</a>
		</div>
	</div>	
	<?php
}

?>