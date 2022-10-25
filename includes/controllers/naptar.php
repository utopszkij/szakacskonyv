<?php
use \RATWEB\DB\Query;

include_once 'includes/models/blogmodel.php';
include_once 'includes/urlprocess.php';
class Naptar {

	function __construct() {
		// a napi menü modul számára szükséges dolgok
		$time = time();
		if (isset($_SESSION['numDay'])) {
			$numDay = $_SESSION['numDay'];
			$numMonth = $_SESSION['numMonth'];
			$numYear = $_SESSION['numYear'];
		} else { 
			$_SESSION['numDay'] = date('d', $time);
			$_SESSION['numMonth'] = date('m', $time);
			$_SESSION['numYear'] = date('Y', $time);
		}
	}

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
				
		$frissHir = '';
		$blogModel = new BlogModel();
		$recs = $blogModel->getBy('title','Friss hír');
		if (count($recs) > 0) {
			$frissHir = urlprocess($recs[0]->body);
			$frissHir = str_replace('<img','<img style="width:80%"',$frissHir);
		} else {
			$frissHir = 'nincs friss hír';
		}

		echo '

		<center>
			<p style="background-color:silver; border:balck; border-width:1px; border-style:solid; fint-size:16px;">
				<script type="text/javascript" src="vendor/bootstrap/js/name-day.js"></script>
			</p>
		</center>

		<div class="row">	
			<div class="col-md-8 text-center">
					<var style="display:inline-block; width:auto">'.$numYear.' '.$strMonth.'</var>&nbsp;
					<a href="?task=elozo"><em class="fas fa-arrow-up" 
						style="color:black"
						title="elöző hónap"></em></a>&nbsp;
					<a href="?task=kovetkezo"><em class="fas fa-arrow-down" 
						style="color:black"
						title="következő hónap"></em></a>
			</div>
		</div>		
		<div class="row">	
			<div class="col-md-6 text-center">
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
				<div class="col-md-6">
						<div class="frisshir">
						'.$frissHir.'
						</div>
					 	<img src="images/dekor1.jpg" class="dekorImg" />
				</div>
			</div><!-- .row -->';
				if ($_SESSION['loged'] >= 0) {
					$t .= '<p>Kattints a napra a napi menü felviteléhez/módosításához/törléséhez!</p>';
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
                                        <!--
					<h3>Tulajdonságok</h3>
					<ul>
						<li>Recepthez hozzávalók, elkészítési leírás és kép vihető fel,</li>
						<li>egy recepthez max 30 hozzávaló adható meg,</li>
						<li>a program támogatja a mindmegette.hu -ról és a receptneked.hu -ról 
							történő adatátvételt,</li>
						<li>a receptek módosíthatóak, törölhetőek,</li>
						<li>ha a recepthez képet nem adunk meg akkor a program a recept neve 
						alapján megpróbál a net-en képet keresni,</li>
						<li>a receptek kinyomtathatóak,</li>		
						<li>napi menübe naponta max. 4 fogás vihető fel, megadható hány főre főzünk aznap,</li>
						<li>a napi menük módosíthatóak, törölhetőek,</li>			
						<li>a számított hozzávaló összesítés (bevásárló lista), nyomtatás előtt módosítható
						(pl. törölhető amiből "van a spájzban").</li>
						<li>A receptekhez hozzászólást lehet írni (pl: megfőztem, jó ), 
							a hozzászóláshoz max 3 db kép is csatolható (pl a saját "alkotásom" fényképei).
						    A hozzászólások és csatolt képek minden látogató számára láthatóak. 
							Törölni, modosítani csak a feltöltő, a moderátorok és a rendszer adminisztrátorok tudják őket.
						</li>			
						<li>Az össesítés optimális müködése érdekében a program egy "szinonima szótárat" és 
							"átváltási adatbázist" kezel. Ezek tartalmát csak a rendszer adminisztrátorok módosíthatják.
						</li>
					</ul>
					<p>A program konfigurálható egyfelhasználós vagy többfelhasználós módba.</p>
					<p>Több felhasználós módban mindenki csak a sajátmaga által felvitt napi menüket 
					látja és ezeket kezelheti, az összesítés is ezek alapján készül. A recepteknél 
					látja, használhatja a mások által felvitteket is, de modosítani, törölni csak a 
					sajátmaga által felvitteket tudja. Illetve a rendszer adminisztrátorok és moderátorok 
					módosíthatják, törölhetik az összes receptet. A hozzászólások mindenki számára láthatóak</p>
                                        -->
					<p></p>
					<p><strong>A felhasználók által felvitt receptek, hozzászólások és képek tartalmáért, a kizárólag
					az azokat felvivő felhasználó a felelős, a program szerzője és üzemeltetője
					ezekkel kapcsolatban semmilyen felelősséget nem vállal.</strong></p>
					<p></p>
                                        <!--
					<h4>A programot mindenki csak saját felelősségére használhatja.</h4>
                                        -->
			</div>
			<?php
	}
} // class

?>

