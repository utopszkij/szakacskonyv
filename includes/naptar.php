<?php
use \RATWEB\DB\Query;

class Naptar {
	

	public function elozo() {
		$numDay = $_SESSION['numDay'];
		$numMonth = $_SESSION['numMonth'];
		$numYear = $_SESSION['numYear'];
		if ($numMonth > 1) {
			$numMonth = $numMonth - 1;	
		} else {
			$numYear = $numYear - 1;
			$numMonth = 12;	
		}
		$_SESSION['numDay'] = $numDay;
		$_SESSION['numMonth'] = $numMonth;
		$_SESSION['numYear'] = $numYear;
		$this->home();
	}
	
	public function kovetkezo() {
		$numDay = $_SESSION['numDay'];
		$numMonth = $_SESSION['numMonth'];
		$numYear = $_SESSION['numYear'];
		if ($numMonth < 12) {
			$numMonth = $numMonth + 1;	
		} else {
			$numYear = $numYear + 1;
			$numMonth = 1;	
		}
		$_SESSION['numDay'] = $numDay;
		$_SESSION['numMonth'] = $numMonth;
		$_SESSION['numYear'] = $numYear;
		$this->home();
	}
	
	public function home() {
		$numDay = $_SESSION['numDay'];
		$numMonth = $_SESSION['numMonth'];
		$numYear = $_SESSION['numYear'];
		$honapok=['Január','Február','Március','Április','Május','Junius',
		'Julius','Augusztus','Szeptember','Október','November','December'];
		$strMonth = $honapok[$numMonth-1];
		$firstDay = mktime(0,0,0,$numMonth,1,$numYear);
		$daysInMonth = cal_days_in_month(0, $numMonth, $numYear);
		$dayOfWeek = date('w', $firstDay);
		
		// melyik napokhoz van ebben a hónapban menü az adatbázisban?
		//$db = new \RATWEB\DB\Query('napimenuk');
		$db = new Query('napimenuk');
		$db->select(['nap'])
		->where('ev','=', $db->sqlValue($numYear))
		->where('ho','=', $db->sqlValue($numMonth))
		->where('created_by','=',$db->sqlValue($_SESSION['loged']));
		$menuk = $db->all();
		
		
		echo '
		<div class="row">	
			<div class="col-md-8 text-center">
					<a href="?task=elozo" class="btn btn-primary">&lt;</a>&nbsp;&nbsp;
					<var style="display:inline-block; width:auto">'.$numYear.' '.$strMonth.'</var>
					<a href="?task=kovetkezo" class="btn btn-primary">&gt;</a>
			</div>
		</div>		
		<div class="row">	
			<div class="col-md-8 text-center">
			';
			$t	= '
			<table style="display:inline-block">
				<thead style="width:100%">
				<tr>
				<th abbr="Monday" scope="col" title="Hétfő">H</th>
				<th abbr="Tuesday" scope="col" title="Kedd">K</th>
				<th abbr="Wednesday" scope="col" title="Szerda">Sze</th>
				<th abbr="Thursday" scope="col" title="Csütörtök">Cs</th>
				<th abbr="Friday" scope="col" title="Péntek">P</th>
				<th abbr="Saturday" scope="col" title="Szombat">Szo</th>
				<th abbr="Sunday" scope="col" title="Vasárnap">V</th>
				</tr>
				</thead>
				<tbody>
				<tr>';
				if ($dayOfWeek == 0) 
					$t .= '<td colspan="6"> </td>';
				else if (1 != $dayOfWeek) 
				   $t .= '<td colspan="'.($dayOfWeek - 1).'"> </td>';
				for($i=1; $i<=$daysInMonth; $i++) {
					$tdClass = 'nincs';
					$title = '';
					for ($j =0; $j < count($menuk); $j++) {
						if ($i == $menuk[$j]->nap) {
							$tdClass = 'van';
						}			
					} 
					if (($i == date('d')) & 
					    ($numMonth == date('m')) &
					    ($numYear == date('Y'))) {
						$tdClass .= ' mainap';			
					}
					if ($_SESSION['loged'] >= 0) {
						$t .= '<td class="'.$tdClass.'">'.
					      '<a href="?task=napimenu&nap='.$i.'">'.$i.'</a></td>';
					} else {
						$t .= '<td class="'.$tdClass.'">'.$i.'</td>';
					}      
					if(date('w', mktime(0,0,0,$numMonth, $i, $numYear)) == 0) {
						$t .= "</tr><tr>";
					}
				}
				$t .= '</tr>
				    </tbody>
				    </table>
				</div>
				<div class="col-md-4">
					 	<img src="images/dekor1.jpg" class="dekorImg" />
				</div>
			</div><!-- .row -->';
				if ($_SESSION['loged'] >= 0) {
					$t .= '<p>Kattints a napra a napi menü felviteléhez/mődsításához/törléséhez!</p>';
				} else {
					$t .= '<p>Napi menü kezeléshez be kell jelentkezni.</p>';
				}			
				echo $t;
				?>
				<div class="leiras">
					<h3>Leírás</h3>
					<p>A programba étel recepteket és napi menüket lehet kezelni.</p>
					<p>Ezek alapján a program adott időszak összesített anyagszükségleteit tudja meghatározni. 
					Ebből bevásárló listát lehat a program segitségével készíteni.</p>
					<p></p>
					<h3>Tulajdonságok</h3>
					<ul>
						<li>Recepthez hozzávalók, elkészítési leírás és kép vihető fel,</li>
						<li>egy recepthez max 15 hozzávaló adható meg,</li>
						<li>a program támogatja a mindmegette.hu -ról történő adatátvételt,</li>
						<li>a receptek módosíthatóak, törölhetőek,</li>
						<li>ha a recepthez képet nem adunk meg akkor a program a recept neve 
						alapján megpróbál a net-en képet keresni,</li>
						<li>a receptek kinyomtathatóak,</li>			
						<li>napi menübe naponta max. 4 fogás vihető fel, megadható hány főre főzünk aznap,</li>
						<li>a napi menük módosíthatóak, törölhetőek,</li>			
						<li>a számított hozzávaló összesítés (bevásárló lista), nyomtatás előtt módosítható
						(pl. törölhető amiből "van a spájzban").</li>			
					</ul>
					<p>A program konfigurálható egyfelhasználós vagy többfelhasználós módba.</p>
					<p>Több felhasználós módban mindenki csak a sajátmaga által felvitt napi menüket 
					látja és ezeket kezelheti, az összesítés is ezek alapján készül. A recepteknél 
					látja, használhatja a mások által felvitteket is, de modosítani, törölni csak a 
					sajátmaga által felvitteket tudja.</p>
					<p></p>
					<p><strong>A felhasználók által felvitt receptek és képek tartalmáért, a kizárólag
					az azokat felvivő felhasználó a felelős, a program szerzője és üzemeltetője
					ezekkel kapcsolatban semmilyen felelősséget nem vállal.</strong></p>
					<p></p>
					<h4>A programot mindenki csak saját felelősségére használhatja.</h4>
			</div>
			<?php
	}
} // class

?>

