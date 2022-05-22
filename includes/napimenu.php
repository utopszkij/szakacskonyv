<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

class NapiMenu {
	
	function __construct() {
		// hiánya esetén az object konstruálás behivná a "napimenu" funciót!
		// (classnévvel megegyegyező nevü method)
	}
	
	// napi menü törlése (csak a saját maga által felvittet törölheti)
	// $_GET['id'] menu rekord id
	public function menuDelete() {
		$db = new Query('napimenuk');
		$db->where('id','=',$db->sqlValue($_GET['id']))
		   ->where('created_by','=', $db->sqlValue($_SESSION['loged'])) 
		   ->delete();
		$comp = new Naptar();
		$comp->home();	
	}
	
	// napimenü képernyő adatainak tárolása
	// $_GET['ev', 'ho', 'nap', .... képernyő mezők .... ] 
	public function menusave() {
		$ev = intval($_GET['ev']);
		$ho = intval($_GET['ho']);
		$nap = intval($_GET['nap']);
		if ($ho < 10) $ho = '0'.$ho;
		if ($nap < 10) $nap = '0'.$nap;
		if ($_SESSION['loged'] < 0) {
			echo '<div class="alert alert-danger">Napi menü felviteléhez be kell jelentkezni!</div>';
			return;	
		}
		
		$db = new Query('napimenuk');
		$db->where('ev','=',$db->sqlValue($ev))
		   ->where('ho','=',$db->sqlValue($ho))
		   ->where('nap','=',$db->sqlValue($nap))
		   ->where('created_by','=',$db->sqlValue($_SESSION['loged']));
		$db->delete();
		
		$r = new Record();
		$r->ev = $ev;	
		$r->ho = $ho;	
		$r->nap = $nap;	
		$r->datum = $ev.'-'.$ho.'-'.$nap;
		$r->recept1 = $_GET['recept1'];	
		$r->recept2 = $_GET['recept2'];	
		$r->recept3 = $_GET['recept3'];	
		$r->recept4 = $_GET['recept4'];
		$r->created_by = $_SESSION['loged'];	
		$r->adag = $_GET['adag'];
		if (!is_numeric($r->adag)) $r->adag = 4;	
		$db->insert($r);
		$comp = new Naptar();
		$comp->home();	
	}
	
	private function receptSelect($v, $a) {
			if ($v == $a) echo ' selected="selected"';
	}

	// napi menü felvivő/modsító képernyő
	// $_GET['nap'], $_SESSION['numYear'], $_SESSION['numMonth'],
	public function napimenu() {
		// get nap
		// sessionban a numYear és numMonth
		$db = new Query('napimenuk');
		$db->exec('CREATE TABLE IF NOT EXISTS napimenuk (
			    id int AUTO_INCREMENT,
			    ev int,
			    ho int,
			    nap int,
			    datum varchar(12),
			    adag int,
			    recept1 int,
			    recept2 int,
			    recept3 int,
			    recept4 int,
			    created_by int,
			    PRIMARY KEY (id)
		)');	
		$nap = $_GET['nap'];
		$ho = $_SESSION['numMonth'];
		$ev = $_SESSION['numYear'];
	
		// aktuális menu
		$rec = JSON_decode('{"id":0, "recept1":0, "recept2":0, "recept3":0, "recept4":0, "adag":4}');	
		$db->where('ev','=',$db->sqlValue($ev))
		   ->where('ho','=',$db->sqlValue($ho))
		   ->where('nap','=',$db->sqlValue($nap))
		   ->where('created_by','=',$db->sqlValue($_SESSION['loged']));
		$rec = $db->first();
		if ($db->error != '') {
			$rec = JSON_decode('{"id":0, "recept1":0, "recept2":0, "recept3":0, "recept4":0, "adag":4}');	
		}
	
		// összes meglévő recept
		$receptek = [];
		$db = new Query('receptek');
		$receptek = $db->orderBy('nev')->all();
		
		if ($_SESSION['loged'] < 0) {
			echo '<div class="alert alert-danger">Napi menü felviteléhez be kell jelentkezni!</div>';	
		}
		?>
		<div class="row">
			<div class="col-md-6">
			<form id="menuForm" action="index.php">
				<input type="hidden" value="menusave" name="task" />			
				<input type="hidden" value="<?php echo  $nap; ?>" name="nap" />
				<input type="hidden" value="<?php echo $ho; ?>" name="ho" />
				<input type="hidden" value="<?php echo $ev; ?>" name="ev" />
				<h2><?php echo $ev.'.'.$ho.'.'.$nap; ?> napi menü</h2>
				<div class="form-outline mb-4">
					<input type="number" name="adag" 
						value="<?php echo $rec->adag; ?>" style="width:60px" /> adag
					<br /><br />	
				</div>	
				<div class="form-outline mb-4">
					<select name="recept1" style="width:90%">
						<option value="0"></option>
						<?php foreach ($receptek as $recept) : ?>
						<option value="<?php echo $recept->id; ?>"<?php $this->receptSelect($recept->id, $rec->recept1); ?> ><?php echo $recept->nev; ?></option>
						<?php endforeach; ?> 				
					</select>
				</div>
				<div class="form-outline mb-4">
					<select name="recept2" style="width:90%">
						<option value="0"></option>
						<?php foreach ($receptek as $recept) : ?>
						<option value="<?php echo $recept->id; ?>"<?php $this->receptSelect($recept->id, $rec->recept2); ?>><?php echo $recept->nev; ?></option>
						<?php endforeach; ?> 				
					</select>
				</div>
				<div class="form-outline mb-4">
					<select name="recept3" style="width:90%">
						<option value="0"></option>
						<?php foreach ($receptek as $recept) : ?>
						<option value="<?php echo $recept->id; ?>"<?php $this->receptSelect($recept->id, $rec->recept3); ?>><?php echo $recept->nev; ?></option>
						<?php endforeach; ?> 				
					</select>
				</div>
				<div class="form-outline mb-4">
					<select name="recept4" style="width:90%">
						<option value="0"></option>
						<?php foreach ($receptek as $recept) : ?>
						<option value="<?php echo $recept->id; ?>"<?php $this->receptSelect($recept->id, $rec->recept4); ?>><?php echo $recept->nev; ?></option>
						<?php endforeach; ?> 				
					</select>
				</div>
				<div class="form-outline mb-4">
					A legtöbb főétel receptje nem tartalmazza a köretet. Szükség esetén azt
					külön sorban válaszd ki!
				</div>
						
				<?php if ($_SESSION['loged'] >= 0) : ?>						
				<div class="form-outline mb-4">
					<button type="submit" class="btn btn-primary">
					<em class="fas fa-check"></em>&nbsp;Tárolás</button>
					&nbsp;
					<?php if ($rec->id > 0) : ?>
					<button type="button" onclick="delClick()" class="btn btn-danger">
					<em class="fas fa-ban"></em>&nbsp;Napi menü törlése</button>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</form>
			</div>
			<div class="d-none d-lg-inline col-md-6">
				<img src="images/dekor1.jpg" class="dekorImg" />
			</div>
		</div>	
		<script>
			function delClick() {
				if (window.confirm('Biztos, hogy törölni akarod ezt a napi menüt?')) {
					document.location = 'index.php?task=menudelete&id=<?php echo $rec->id; ?>';
				}			
			}	
		</script>	
		<?php
	}
} // class

?>
