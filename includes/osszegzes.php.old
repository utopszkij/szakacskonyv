<?php
use \RATWEB\DB\Query;

class Osszegzes {
	function __construct() {
	}

	/**
	* összegzés indító képernyő 
	*/
	public function osszeg() {
		?>
		<center>
			<h1 class="ribbon-banner"><span>Összegzés</span></h1>
		</center>  
		<div class="row">
			<div class="col-md-12 text-center">
			<form action="index.php" class d-inline text-left>
				<input type="hidden" name="task" value="szamol" />
				<p>Időszak: éééé-hh-nn -tól éééé-hh-nn -ig</p>
				<p>
					<input type="date" name="datum1" 
					style="width:170px" value="<?php echo date('Y-m-d'); ?>" />
					&nbsp;-&nbsp;tól
				</p>
				<p>
					<input type="date" name="datum2" 
					style="width:170px" value="<?php echo date('Y-m-d'); ?>" />
					&nbsp;-&nbsp;ig&nbsp;
				</p>	
				<p>
					<button type="submit" class="btn btn-primary">
					<em class="fas fa-check"></em>&nbsp;start</button>			
				</p> 		
			</form>
			</div>
		</div>
		<?php
	}
	
	/**
	* számolás, eredmény megjelenítő képernyő
	* $_GET['datum1'],$_GET['datum2'] 
	*/
	public function szamol() {
		$datum1 = $_GET['datum1'];	
		$datum2 = $_GET['datum2'];
		$loged = $_SESSION['loged'];	
	
	$union2 = new Query('napimenuk','m');
	$union2->select(['m.adag','h.nev',['(m.adag / r.adag * h.szmennyiseg)','mennyiseg'],'h.szme'])
			->join('LEFT OUTER','hozzavalok','h','h.recept_id','=','m.recept2')
			->join('LEFT OUTER','receptek','r','r.id','=','m.recept2')
			->where('h.nev','<>',Query::sqlValue(''))
			->where('h.mennyiseg','>',0)
			->where('m.datum','>=',Query::sqlValue($datum1))
			->where('m.datum','<=',Query::sqlValue($datum2))
			->where('m.created_by','=',Query::sqlValue($loged));
	
	$union3 = new Query('napimenuk','m');
	$union3->select(['m.adag','h.nev',['(m.adag / r.adag * h.szmennyiseg)','mennyiseg'],'h.szme'])
			->join('LEFT OUTER','hozzavalok','h','h.recept_id','=','m.recept3')
			->join('LEFT OUTER','receptek','r','r.id','=','m.recept3')
			->where('h.nev','<>',Query::sqlValue(''))
			->where('h.mennyiseg','>',0)
			->where('m.datum','>=',Query::sqlValue($datum1))
			->where('m.datum','<=',Query::sqlValue($datum2))
			->where('m.created_by','=',Query::sqlValue($loged));
	
	$union4 = new Query('napimenuk','m');
	$union4->select(['m.adag','h.nev',['(m.adag / r.adag * h.szmennyiseg)','mennyiseg'],'h.szme'])
			->join('LEFT OUTER','hozzavalok','h','h.recept_id','=','m.recept4')
			->join('LEFT OUTER','receptek','r','r.id','=','m.recept4')
			->where('h.nev','<>',Query::sqlValue(''))
			->where('h.mennyiseg','>',0)
			->where('m.datum','>=',Query::sqlValue($datum1))
			->where('m.datum','<=',Query::sqlValue($datum2))
			->where('m.created_by','=',Query::sqlValue($loged));
	
	$subSelect = new Query('napimenuk','m');
	$subSelect->select(['m.adag','h.nev',['(m.adag / r.adag * h.szmennyiseg)','mennyiseg'],'h.szme'])
				->join('LEFT OUTER','hozzavalok','h','h.recept_id','=','m.recept1')
				->join('LEFT OUTER','receptek','r','r.id','=','m.recept1')
				->where('h.nev','<>',Query::sqlValue(''))
				->where('h.mennyiseg','>',0)
				->where('m.datum','>=',Query::sqlValue($datum1))
				->where('m.datum','<=',Query::sqlValue($datum2))
				->where('m.created_by','=',Query::sqlValue($loged))
				->addUnion($union2)
				->addUnion($union3)
				->addUnion($union4);
	 
	$db = new Query($subSelect,'s');
	$db->select(['s.nev',['sum(s.mennyiseg)','mennyiseg'],'s.szme'])
	->groupBy(['s.nev','szme'])
	->orderBy('s.nev');
	$items = $db->all();
	
	/* időszak napimenük */
	$db = new Query('napimenuk','m');
	$db->select(['m.datum','m.adag',
					['r1.nev','nev1'],
					['r2.nev','nev2'],
					['r3.nev','nev3'],
					['r4.nev','nev4']])
		->join('LEFT OUTER','receptek','r1','r1.id','=','m.recept1')
		->join('LEFT OUTER','receptek','r2','r2.id','=','m.recept2')
		->join('LEFT OUTER','receptek','r3','r3.id','=','m.recept3')
		->join('LEFT OUTER','receptek','r4','r4.id','=','m.recept4')
		->where('m.datum','>=',Query::sqlValue($datum1))
		->where('m.datum','<=',Query::sqlValue($datum2))
		->where('m.created_by','=',Query::sqlValue($loged))
		->orderBy('m.datum');

	$napiMenuk = $db->all();	
	
	?>
	<div id="osszegzes">
		<h2><?php echo $datum1.' - '.$datum2; ?></h2>
		<div class="row">
			<div class="col-md-8">
				<div class="napimenu">
				<?php foreach ($napiMenuk as $napiMenu) : ?>
					<div><?php 
						echo $napiMenu->datum.' '.$napiMenu->adag.' adag '.
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
								<td><?php echo $item->szme; ?></td>
							</tr>	
						<?php endforeach ?>
					</tbody>		
				</table>
			</div>
			<div class="d-none d-lg-inline col-md-4 help">
				<img src="images/dekor2.jpg" class="dekorImg" />
			</div>
		</div> 
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
<?php echo $item->nev; ?>&nbsp;<?php echo $item->mennyiseg; ?>&nbsp;<?php echo $item->szme."\n"; ?>
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
			document.getElementById('bevasarloLista').style.display= 'block';
		}
		function printClick() {
			var sorok = document.getElementById('bevLista').value;
			var sorokSzama = sorok.split("\n").length;
			document.getElementById('bevLista').rows = sorokSzama + 10;
			window.print();
		}
	</script>
	<?php
	}
} // class

