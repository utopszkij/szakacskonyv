<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once __DIR__.'/controller.php';
include_once __DIR__.'/../models/mertekegysegmodel.php';
class Mertekegyseg extends Controller {

	function __construct() {
        parent::__construct();
        $this->name = "mertekegyseg";
        $this->model = new MertekegysegModel();
        $this->browserURL = 'index.php?task=mertekegysegek';
        $this->addURL = 'index.php?task=meadd';
        $this->editURL = 'index.php?task=meedit';
        $this->browserTask = 'mertekegysegek';
	}
	
    /**
     * rekord ellenörzés
     * @param Record $record
     * @return string üres vagy hibaüzenet
     */
    protected function validator($record):string {
        // mit, mire nem lehet üres
        $result = '';
        if (trim($record->nev) == '') {
            $result .= 'Nem lehet üres!<br />';
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
     * mértékegység browser GET -ben: page, order, filter
     */
    public function mertekegysegek() {
        $this->items('mit');
    }
    
    /**
     * Új mértékegység felvivő képernyő
     */
    public function meadd() {
        $this->new();
    }
    
    /**
     * mértékegység editor képernyő GET -ben id
     */
    public function meedit() {
        $this->edit();
    }     

    /**
     * mértékegység tárolása POST -ban: id, nev
     */
    public function mesave() {
        $record = new Record();
        $record->id = $this->request->input('id');
        $record->nev = trim($this->request->input('nev'));
        $this->save($record); 
    }
  
    /**
     * mértékegység törlése GET-ben: id
     */
    public function medelete() {
        $this->delete();
    }    
}
?>