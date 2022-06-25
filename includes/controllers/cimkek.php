<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once __DIR__.'/controller.php';
include_once __DIR__.'/../models/cimkemodel.php';
class Cimkek extends Controller {

	function __construct() {
        parent::__construct();
        $this->name = "cimke";
        $this->model = new CimkeModel();
        $this->browserURL = 'index.php?task=cimkek';
        $this->addURL = 'index.php?task=cimkeadd';
        $this->editURL = 'index.php?task=cimkeedit';
        $this->browserTask = 'cimkek';
	}
	
    /**
     * rekord ellenörzés
     * @param Record $record
     * @return string üres vagy hibaüzenet
     */
    protected function validator($record):string {
        // cimke nem lehet üres
        $result = '';
        if (trim($record->cimke) == '') {
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
     * cimke browser GET -ben: page, order, filter
     */
    public function cimkek() {
        $this->items('cimke');
    }
    
    /**
     * Új cimke felvivő képernyő
     */
    public function cimkeadd() {
        $this->new();
    }
    
    /**
     * cimke editor képernyő GET -ben id
     */
    public function cimkeedit() {
        $this->edit();
    }     

    /**
     * cimke tárolása POST -ban: id, nev
     */
    public function cimkesave() {
        $record = new Record();
        $record->id = $this->request->input('id');
        $record->cimke = trim($this->request->input('cimke',''));
        $this->save($record); 
    }
  
    /**
     * cimke törlése GET-ben: id
     */
    public function cimkedelete() {
        $cimke = $this->model->getById($this->request->input('id',0));
        if (isset($cimke->id)) {
            $this->delete();
            // törlés a recept_cimke táblából
            $db = new Query('recet_cimke');
            $db->where('cimke','=',$cimke->cimke)->delete();
        }    
    }    
}
?>