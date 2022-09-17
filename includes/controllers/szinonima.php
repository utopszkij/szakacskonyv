<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once __DIR__.'/../models/szinonimamodel.php';
class Szinonima extends Controller {

	function __construct() {
        parent::__construct();
        $this->name = "szinonima";
        $this->model = new SzinonimaModel();
        $this->browserURL = 'index.php?task=szinonimak';
        $this->addURL = 'index.php?task=szinonimaadd';
        $this->editURL = 'index.php?task=szinonimaedit';
        $this->browserTask = 'szinonimak';
	}
	
    /**
     * rekord ellenörzés
     * @param Record $record
     * @return string üres vagy hibaüzenet
     */
    protected function validator($record):string {
        // mit, mire nem lehet üres és nem lehet azonos
        $result = '';
        if ((trim($record->mit) == '') | (trim($record->mire) == '')) {
            $result .= 'Nem lehet üres!<br />';
        }
        if ((trim($record->mit) == trim($record->mire))) {
            $result .= 'Nem lehet a kettő azonos<br />';
        }
        $rec = $this->model->getBy('mit',$record->mire);
        if (count($rec) > 0) {
            $result .= 'Hivatkozási hurok alakulna ki!<br />';
        }     
        return $result;
    }
    
    /**
     * bejelentkezett user jogosult erre?
     * @param string $action new|edit|delete
     * @return bool
     */
    protected function  accessRight(string $action, $record):bool {
        return $this->logedAdmin;
    }

    /**
     * szinonima browser GET -ben: page, order, filter
     */
    public function szinonimak() {
        $this->items('mit');
    }
    
    /**
     * Új szinonima felvivő képernyő
     */
    public function szinonimaadd() {
        $this->new();
    }
    
    /**
     * szinonima editor képernyő GET -ben id
     */
    public function szinonimaedit() {
        $this->edit();
    }     

    /**
     * szinonima tárolása POST -ban: id, mit, mire
     */
    public function szinonimasave() {
        $record = new Record();
        $record->id = $this->request->input('id');
        $record->mit = trim($this->request->input('mit'));
        $record->mire = trim($this->request->input('mire'));
        $this->save($record); 
    }
  
    /**
     * szinonima törlése GET-ben: id
     */
    public function szinonimadelete() {
        $this->delete();
    }    

    /**
     * szinonimák alkalmazása a teljes adatbázisra
     */
    public function szinonimaprocess() {
        $i = $this->request->input('i',0);
        $total = $this->model->getTotal('');
        $szazalek = (($i+1) / $total) * 100;
        if ($szazalek <= 100) {
            echo '<div class="alert alert-info">
            Szinonimál alkalmazása a teljes adatbázisra. Türelmet kérek ....'.($i+1).'/'.$total.'
            </div>
            <br /><br />
            <div style="width:90%; border-style:solid; border-width:2px; height:auto;">
                <div style="background-color:blue; height:26px; width:'.$szazalek.'%">&nbsp;</div>
            </div>
            ';
        }
    if ($this->model->processAll($i)) {
            // nem eof
            $i++;
            echo '
            <script>
            setTimeout("location=\"index.php?task=szinonimaprocess&i='.$i.'\"",2000);
            </script>
            ';
        } else {
            // eof
            $this->session->set('successMsg','Szinonimák alkalmazva a teljes recept adatbázisra.');
            echo '
            <script>
            location="'.$this->browserURL.'";
            </script>
            ';
        }
    }

}
?>