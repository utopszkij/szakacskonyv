<?php
/*
NINS KÉSZ - TERV

A profil oldalon és a regist oldalon lehetővé kell tenni a feliratkozást, leiratkozást.
A fiók törlésnél auto leiratkozás is történjen

adatbázis:
    hirlevelek
        id
        title
        body
        status  draft|working|finished
        sended  ennyi usernél lett már réküldve
        cretaed_ba
        created_at
        start_at
    hileveletker
        id
        user_id

define: HIRLVELSTEP egyszerre ennyi hirlevelet küld ki
        HIRLEVELCRON tue|false
        HIRLEVELWAIT várakozási idő
        
public function start() {
    GET: id
    csak admin használhatja
    hillevelek rekordban status='working', sended=0, star_at = date_time
    ha !HIRLEVELCRON akkor hivja a step metodust ?cron=n -el
}        
public function step() {
    GET: cron=i|n
    beolvassa az első status=working hirlevelek rekordot
    ha van ilyen akkor
        beolvassa a hirleveletker táblából a hirlevelek.sended utáni HIRLEVELSTEP db rekordot
        levelet küld (aki nincs letilttva)
        modositja hirlevelek.sended adatot
        ha a hirleveltker tábla végére ért modositja a hirlevelek.status adatot
        ha még nem ért a végére és cron=n akkor HIRLEVELWAIT várakozás után visszahívja önmagát.

    a hirlevél szövegben használható változók:
    {USERNAME}, {USERID}, {USERCODE}    
}

public function leiratkozas() {
    GET: usercode
}

*/
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once __DIR__.'/../models/hirlevelmodel.php';
class Hirlevel extends Controller {

	function __construct() {
        parent::__construct();
        $this->name = "hirlevel";
        $this->model = new HirlevelModel();
        $this->browserURL = 'index.php?task=hirlevelek';
        $this->addURL = 'index.php?task=hirleveladd';
        $this->editURL = 'index.php?task=hirleveledit';
        $this->browserTask = 'hirlevelek';
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