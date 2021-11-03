<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

/*
hozzávalók választhatóak is lehetnek:

<input type="text" list="cars" />
<datalist id="cars">
  <option>Volvo</option>
  <option>Saab</option>
  <option>Mercedes</option>
  <option>Audi</option>
</datalist>
*/

include_once 'atvesz.php';

// $_GET['id']
function receptdelete() {
	// normál user csak a saját maga által felvittet törölheti
	// system admin mindent törölhet
	$db = new Query('receptek');
	$db->where('id','=',$db->sqlValue($_GET['id']));
	$recept = $db->first();
	if (($recept->created_by != $_SESSION['loged']) &
	    ($_SESSION['logedName'] != ADMIN)) {
	   receptek();
	} else {
		$db = new Query('receptek');
		$db->where('id','=',$db->sqlValue($_GET['id']))->delete();
		$db = new Query('hozzavalok');
		$db->where('recept_id','=',$db->sqlValue($_GET['id']))->delete();
		receptek();
	}
}

function receptsave() {
	// get -ben: id, leiras, hozzvalok0, mennyiseg0, me0, hozzavalok1,....
	
	
	if ($_SESSION['loged'] < 0) {
		echo '<div class="alert alert-danger">Recept felviteléhez be kell jelentkezni!</div>';
		return;	
	}

	
	$receptId = $_POST['id'];
	if ($receptId == 0) {
		$db = new Query('receptek');
		$r = new Record();
		$r->nev = $_POST['nev'];
		$r->created_by = $_SESSION['loged'];
		$receptId = $db->insert($r);
	} else {
		$db = new Query('receptek');
		$db->where('id','=',$db->sqlValue($receptId));
		$recept = $db->first();
		if (($recept->created_by != $_SESSION['loged']) & 
		    ($_SESSION['logedName'] != ADMIN)) {
			echo '<div class="alert alert-danger">Hozzáférés megtagadva!</div>';
			return;
		}
	}

	// leírás és név tárolása
	$db = new Query('receptek');
	$db->where('id','=',$db->sqlValue($receptId));
	$recept = $db->first();
	$r = new Record();
	$r->leiras = $_POST['leiras'];
	$r->nev = $_POST['nev'];
	$db->where('id','=',$db->sqlValue($receptId))
	   ->update($r);
	
	// meglévő hozzávalók törlése
	$db = new Query('hozzavalok');
	if ($receptId > '0') {
		$db->where('recept_id','=',$db->sqlValue($receptId))->delete();	
	}
	
	// hozzávalók felvitele
	for ($i = 0; $i < 15; $i++) {
		if (isset($_POST['hozzavalo'.$i])) {
			if ($_POST['hozzavalo'.$i] != '') {
				$r = new Record();
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
	$recept = JSON_decode('{"id":0, "leiras":"", "nev":"", "created_by":0}');	
	$hozzavalok = [];	
	$receptId = $_GET['id'];
	$disable = '';

	// aktuális recept és hozzávalók beolvasása
	if ($receptId > '0') {
		$db = new Query('receptek');
		$db->where('id','=',$db->sqlValue($receptId));
		$recept = $db->first();	
		$db = new Query('hozzavalok');
		$hozzavalok = $db->where('recept_id','=',$db->sqlValue($receptId))->all();
		if (($recept->created_by != $_SESSION['loged']) &
		    ($_SESSION['logedName'] != ADMIN)) {
			$disable = ' disabled="disabled"';		
		}	
	}
	
	// meglévő recept nevek beolvasása
	$db = new Query('receptek');
	?>	
	<script>
	var receptNevek = <?php echo JSON_encode($db->select(['id','nev'])->all()); ?>;
	</script>	
	<?php	
	
	// meglévő hozzávaló nevek beolvasása
	$db = new Query('hozzavalok');
	$db->select(['distinct nev'])
		->orderBy('nev');
	$nevek = $db->all();
	
	
	function meSelect($v,$i) {
		global $hozzavalok;
		if ($v == $hozzavalok[$i]->me) {
			echo ' selected="selected"';		
		}	
   }
	$kep = receptKep($recept);
	while (count($hozzavalok) < 15) {
			$hozzavalok[] = JSON_decode('{"mennyiseg":"", "me":0, "nev":""}');					
	}
	
	if ($_SESSION['loged'] < 0) {
		echo '<div class="alert alert-danger">Recept felviteléhez, modosításhoz, törléséhez be kell jelentkezni!</div>';	
	}

	?>
	<div id="recept">
		<form id="receptForm" action="index.php?task=receptsave" method="post" enctype="multipart/form-data">
			<input type="hidden" value="receptsave" name="task" />			
			<input type="hidden" value="<?php echo $receptId; ?>" name="id" id = "id" />
			<div class="row">
				<div class="col-md-8">
					<div class="form-outline mb-4">
						<h2>Recept<h2>			
					</div>
					<div class="form-outline mb-4 d-inline d-md-none">
						<?php if ($kep != '') : ?>
						<img src="<?php echo $kep; ?>" class="receptKep" class="receptKep" />				
						<?php endif; ?>			
					</div>
					<div class="form-outline mb-4">
						<label>Recept megnevezése:</label>			
					</div>
					<div class="form-outline mb-4">
						<input type="text" value="<?php echo $recept->nev; ?>"
						 id="nev" name="nev" <?php echo $disable; ?>
						 class="form-control"  />
					</div>
					<?php if ($disable == '') : ?>
					<div class="form-outline mb-4">
						A mindmegette.hu -n nyisd meg a recept oldalát, 
						a web címét másold az alábbi input mezőbe, majd kattints a "Feldolgoz" gombra!
					</div>
					<div class="form-outline mb-4">
						<input type="text" id="paste" <?php echo $disable; ?> />
						<button type="button" class="btn btn-secondary"
						 onclick="processPaste()">Feldolgoz</button>
					</div>
					<?php endif; ?>
					
				</div>
				<div class="d-none d-md-inline col-md-4">
					<?php if ($kep != '') : ?>
						<img src="<?php echo $kep; ?>" class="receptKep" class="receptKep" />				
					<?php endif; ?>			
				</div>
			</div><!-- .row -->

			<div class="row">
				<div class="form-outline col-mb-10">
					<h2>Hozzávalók</h2>
					<strong>Hozzávaló neve / mennyiség / mértékegység</strong>
				</div>				
				<?php for ($i=0; $i < count($hozzavalok);  $i++) : ?>
				<div class="form-outline col-mb-10">
					<input type="text" <?php echo $disable; ?>
						value="<?php echo $hozzavalok[$i]->nev; ?>" 
						name="hozzavalo<?php echo $i; ?>" 
						list="alapanyagok"
						id="hozzavalo<?php echo $i; ?>"
						style="width:49%" /> 
					<datalist id="alapanyagok">
						<?php foreach ($nevek as $nev) : ?> 
					  	<option><?php echo $nev->nev; ?></option>
					  	<?php endforeach; ?>
					</datalist>
						
					<input type="number" min="0" max="100" step="0.5" 
					   <?php echo $disable; ?>
						value="<?php echo $hozzavalok[$i]->mennyiseg; ?>"
						name="mennyiseg<?php echo $i; ?>" style="width:23%"
						id="mennyiseg<?php echo $i; ?>" />			
					<select style="width:24%; height:30px"
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
				cols="40" rows="15"><?php echo $recept->leiras; ?></textarea>
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
		
		var s = document.getElementById('paste').value;
		document.location='index.php?task=recept&id=0&url='+encodeURI(s);		
		
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
	if (isset($_GET['url'])) {
		atvesz($_GET['url']);	
	}
}

/**
* $_GET['id'] alapján recept nyomtatás 
*/
function receptprint() {
	global $hozzavalok;
	$recept = JSON_decode('{"id":0, "leiras":"", "nev":""}');	
	$hozzavalok = [];	
	$receptId = $_GET['id'];
	$db = new Query('receptek');
	if ($receptId > '0') {
		$db->where('id','=',$db->sqlValue($receptId));
		$recept = $db->first();	
	}
	$db = new Query('hozzavalok');
	if ($receptId > '0') {
		$hozzavalok = $db->where('recept_id','=',$db->sqlValue($receptId))->all();	
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
	$db = new Query('receptek');
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
	$db = new Query('receptek');
	$db->orderBy('nev');
	$list = $db->all();
	?>
	<div id="receptList">
		<div class="row">
			<h2>Receptek</h2>
		</div>	
		<div class="row">	
			<div id="receptListTable" class="col-md-8">
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
			<div class="d-none d-md-inline col-md-4">
				<img src="images/dekor1.jpg" class="dekorImg" />
			</div>	
		</div>
		<p>Kattints a recept nevére a megtekintéséhez, modosításhoz, törléséhez!</p>
		<?php if ($_SESSION['loged'] >= 0) : ?>
		<div style="text-align:center">
			<a href="?task=recept&id=0" class="btn btn-primary">
				<em class="fas fa-plus"></em>&nbsp;Új recept</a>
		</div>
		<?php else : ?>
		<div>Recept felviteléhez be kell jelentkezni.</div>
		<?php endif; ?>
	</div>	
	<?php
}

?>