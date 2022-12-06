<?php
use \RATWEB\DB\Query;

// include_once 'includes/models/blogmodel.php';
// include_once 'includes/urlprocess.php';
// include_once __DIR__.'/../models/receptmodel.php';
// include_once __DIR__.'/../models/likemodel.php';

class Naptar  {

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
		$this->naptar();
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
		$this->naptar();
	}

	public function naptar() {
		if (!isset($_GET['task'])) {
			$_GET['task'] = 'home';
		}
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

		<center>
			<p style="background-color:silver; border:balck; border-width:1px; border-style:solid; fint-size:16px;">
				<script type="text/javascript" src="vendor/bootstrap/js/name-day.js"></script>
			</p>
		</center>

		<div class="row">	
			<div class="col-12 text-center">
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
			<div class="col-2">&nbsp;</div>
			<div class="col-8">
			';
			$t	= '
			<table class="table">
				<thead>
				<tr>
				<th __abbr="Monday" __scope="col" title="Hétfő">H</th>
				<th __abbr="Tuesday" __scope="col" title="Kedd">K</th>
				<th __abbr="Wednesday" __scope="col" title="Szerda">Sze</th>
				<th __abbr="Thursday" __scope="col" title="Csütörtök">Cs</th>
				<th __abbr="Friday" __scope="col" title="Péntek">P</th>
				<th __abbr="Saturday" __scope="col" title="Szombat">Szo</th>
				<th __abbr="Sunday" __scope="col" title="Vasárnap">V</th>
				</tr>
				</thead>
				<tbody>
				<tr style="height:40px">';
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
						$t .= "</tr><tr style=\"height:40px\">";
					}
				}
				$t .= '</tr>
				</tbody>
				</table>
				</div><!-- col-8 -->
				<div class="col-2">&nbsp;</div>
			</div><!-- .row -->
			<div class="row text-center">';
			if ($_SESSION['loged'] >= 0) {
				$t .= '<p>Kattints a napra a napi menü felviteléhez/módosításához/törléséhez!</p>';
			} else {
				$t .= '<p>Napi menü kezeléshez be kell jelentkezni.</p>';
			}			
			$t .= '</div>';
			echo $t;
	}
	
	public function home($style = '') {
		$likeModel = new LikeModel();
		$winers = $likeModel->getWinners('recept',10);
		$q = new Query('receptek');
		if ((STYLE == 'delicious') | (STYLE == 'modern')) {
			$limit = 16;
			$q->setSql('
			SELECT r.id, r.nev, count(l.id) likes
			FROM receptek r
			INNER JOIN likes l ON l.target_id = r.id AND l.target_type = "recept"
			INNER JOIN users u ON u.id = l.user_id
			GROUP BY r.id,r.nev
			UNION ALL
			SELECT r.id, r.nev, count(l.id) likes
			FROM receptek r
			LEFT OUTER JOIN likes l ON l.target_id = r.id AND l.target_type = "recept"
			WHERE l.id is NULL
			GROUP BY r.id,r.nev
			ORDER BY 1 DESC
			LIMIT 8
			');
			$news = $q->all();
		} else {
			$limit = 8;
			$news = $q->select(['id','nev'])
			->orderBy('id')
			->orderDir('DESC')
			->limit($limit)
			->all();
		}

		$frissHir = '';
		$blogModel = new BlogModel();
		$recs = $blogModel->getBy('title','Friss hír');
		if (count($recs) > 0) {
			$frissHir = urlprocess($recs[0]->body);
			$frissHir = str_replace('<img','<img style="width:80%"',$frissHir);
		} else if ($_GET['task'] == 'home') {
			$frissHir = 'nincs friss hír';
		}
		view('home',["news" => $news, 
		"frissHir" => $frissHir, 
		"winers" => $winers, 
		"LIKESIZE" => LIKESIZE]);
		if ($style == 'defauult') {
			echo '
			<!-- Initialize Swiper -->
			<script src="vendor/swiper/swiper-bundle.js"></script>
			<script>
			var sliderCount = 3;
			if (window.innerWidth > 1000) {
				sliderCount = 3;
			} else if (window.innerWidth > 700) {
				sliderCount = 2;
			} else {
				sliderCount = 1;
			}
			var swiper = new Swiper(".mySwiper", {
				slidesPerView: sliderCount,
				spaceBetween: 30,
				navigation: {
				nextEl: ".swiper-button-next",
				prevEl: ".swiper-button-prev",
				},
			});
			</script>
			';
			}	
	}

} // class

?>

