<?php

function osszeg() {
	?>
	<form action="index.php" style="margin:20px 20px 20px 200px">
		<input type="hidden" name="task" value="szamol" />
		<h2>Összegzés</h2>
		<p>Időszak: éééé-hh-nn - éééé-hh-nn</p>
		<p>
			<input type="date" name="datum1" value="<?php echo date('Y-m-d'); ?>" />
			&nbsp;-&nbsp;
			<input type="date" name="datum2" value="<?php echo date('Y-m-d'); ?>" />
			<p>
				<br />
				<button type="submit" class="btn btn-primary">
				<em class="fas fa-check"></em>&nbsp;start</button>			
			</p> 		
		</p>	
	</form>
	<?php
}

function szamol() {
	$datum1 = $_GET['datum1'];	
	$datum2 = $_GET['datum2'];
	$loged = $_SESSION['loged'];	
	$sql = "
select kell.nev, sum(kell.mennyiseg) mennyiseg, kell.me
from
(SELECT m.adag, h.nev, 
       (m.adag / 4 * h.mennyiseg) mennyiseg, h.me 
from napimenuk m
LEFT OUTER JOIN hozzavalok h ON h.recept_id = m.recept1
WHERE h.nev <> '' and m.datum >= '$datum1' and m.datum <= '$datum2'
		and m.created_by = $loged
UNION ALL
SELECT m.adag, h.nev, 
       (m.adag / 4 * h.mennyiseg) mennyiseg, h.me 
from napimenuk m
LEFT OUTER JOIN hozzavalok h ON h.recept_id = m.recept2
WHERE h.nev <> '' and m.datum >= '$datum1' and m.datum <= '$datum2'
		and m.created_by = $loged
UNION ALL
SELECT m.adag, h.nev, 
       (m.adag / 4 * h.mennyiseg) mennyiseg, h.me 
from napimenuk m
LEFT OUTER JOIN hozzavalok h ON h.recept_id = m.recept3
WHERE h.nev <> ''and m.datum >= '$datum1' and m.datum <= '$datum2'
		and m.created_by = $loged
UNION ALL
SELECT m.adag, h.nev, 
       (m.adag / 4 * h.mennyiseg) mennyiseg, h.me 
from napimenuk m
LEFT OUTER JOIN hozzavalok h ON h.recept_id = m.recept4
WHERE h.nev <> '' and m.datum >= '$datum1' and m.datum <= '$datum2'
		and m.created_by = $loged
) kell
group by kell.nev, kell.me
order by kell.nev
";
$db = new \RATWEB\DB\Query('napimenuk');
$db->setSql($sql);
$items = $db->all();

/* időszak menüje */
$sql = "
select m.datum, m.adag, r1.nev nev1,  r2.nev nev2, r3.nev nev3, r4.nev nev4 
from napimenuk m 
left outer join receptek r1 on r1.id = m.recept1 
left outer join receptek r2 on r2.id = m.recept2 
left outer join receptek r3 on r3.id = m.recept3 
left outer join receptek r4 on r4.id = m.recept4 
left outer join receptek r on r.id = m.recept4 
WHERE m.datum >= '$datum1' and m.datum <= '$datum2'
      and m.created_by = $loged
order by m.datum";
$db = new \RATWEB\DB\Query('napimenuk');
$db->setSql($sql);
$napiMenuk = $db->all();

?>
<div id="osszegzes"> 
	<div class="help">
		<img src="https://cdn.pixabay.com/photo/2017/08/10/07/20/grocery-store-2619380_960_720.jpg"
		style="height:200px"; id="osszesImg" />
	</div>
	<h2><?php echo $datum1.' - '.$datum2; ?></h2>
	<div class="help">
	<?php foreach ($napiMenuk as $napiMenu) : ?>
		<div><?php 
			echo $napiMenu->datum.' '.
			$napiMenu->nev1.' '.
		   $napiMenu->nev2.' '.
		   $napiMenu->nev3.' '.
		   $napiMenu->nev4; 
		   ?>
		</div>
	<?php endforeach; ?>
	</div>
	<table id="hozzavalok">
		<thead style="background-color:silver">
			<tr><th colspan=3>Hozzávalók összesen</th></tr>
		</thead>
		<tbody>
			<?php foreach ($items as $item) : ?>
				<tr>
					<td><?php echo $item->nev; ?></td>
					<td style="width:40px"><?php echo $item->mennyiseg; ?></td>
					<td><?php echo $item->me; ?></td>
				</tr>	
			<?php endforeach ?>
		</tbody>		
	</table>
	<div id="bevListBtn" style="text-align:center">
		<button type="button" onclick="bevListClick()" class="btn btn-secondary">
		<em class="fas fa-shopping-basket"></em>&nbsp;Bevásárló lista
		</button>
	</div>
</div>

<div id="bevasarloLista" style="display:none">
	<h3>Bevásárló lista</h3>
	<div class="help">
		Javithatsz, sorokat törölhetsz, új sorokat vehetsz fel.
	</div>
	<textarea cols="60" rows="20" id="bevLista">
		<?php foreach ($items as $item) : ?>
<?php echo $item->nev; ?>&nbsp;<?php echo $item->mennyiseg; ?>&nbsp;<?php echo $item->me."\n"; ?>
		<?php endforeach ?>
	</textarea>
	<div class="help">
		<button type="button" onclick="printClick()" class="btn btn-secondary">
		<em class="fas fa-print"></em>&nbsp;Nyomtat
		</button>
	</div>
</div>

<script>
	function bevListClick() {
		document.getElementById('hozzavalok').style.display= 'none';
		document.getElementById('bevListBtn').style.display= 'none';
		document.getElementById('osszesImg').style.display= 'none';
		document.getElementById('bevasarloLista').style.display= 'block';
	}
	function printClick() {
		var sorok = document.getElementById('bevLista').value;
		var sorokSzama = sorok.split("\n").length;
		console.log('sorok száma',sorokSzama); 	
		document.getElementById('bevLista').rows = sorokSzama;
		window.print();
	}
</script>
<?php
}
