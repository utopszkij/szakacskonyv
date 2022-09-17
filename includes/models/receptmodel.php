<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    include_once (__DIR__.'/atvaltasmodel.php');
    include_once (__DIR__.'/usermodel.php');
    
    class ReceptModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable('receptek');
        }

        /**
         * törli az adott recept összes hozzávalóját
         * @param int $id
         */
        public function deleteHozzavalok(int $id) {		
			$db = new Query('hozzavalok');
			$db->where('recept_id','=',$id)->delete();
            $this->errorMsg = $db->error;
    	}		

        /**
         * törli az adott recept összes cimkéjét
         * @param int $id
         */
        public function deleteCimkek(int $id) {		
			$db = new Query('recept_cimke');
			$db->where('recept_id','=',$id)->delete();
            $this->errorMsg = $db->error;
    	}		

        /**
         * felvisz az adatbázisba egy hozzavalok rekordot
         */
        public function insertHozzavalok(Record $record) {
            $atvaltasModel = new AtvaltasModel();
            // számítási (alap) mértékegység kiolvasása az adazbázisból
            $record->szme = $atvaltasModel->getSzme($record);
            $record->szmennyiseg = $record->mennyiseg; // ideiglenesen, lejebb át lesz számolva

			$db = new Query('hozzavalok');
            $db->insert($record);
            $this->errorMsg = $db->error;
            if ($this->errorMsg == '') {
                // átszámolás az alapmértékegységre
                $rec = new Record();
                $atvaltasModel->receptAtszamito($record);
                $this->errorMsg = $atvaltasModel->errorMsg;
            }
        }    

        /**
         * Beolvassa az adott recept összes hozzávalóját
         * @param int $id
         * @return array of Record   [HozzavaloRecord,...]
         */
        public function getHozzavalok(int $id): array {		
			$db = new Query('hozzavalok');
			$result = $db->where('recept_id','=',$id)->all();
			$this->errorMsg = $db->error;
			return $result;
        }   
        
        /**
         * Beolvassa az adott recept létrehozóját
         * @param Record $recept
         * @return Record UserRecord
         */
        public function getCreator(Record $recept):Record {		
			$userModel= new UserModel();
			$creator = $userModel->getById($recept->created_by);
			if (!isset($creator->username)) {
				$creator->username = 'guest';
                $creator->avatar = '';
                $creator->group = '';
			}
			return $creator;
    	}		
        
        /**
         * Beolvassa az össes létező recept nevét
         * @return [{id,nev},...]
         */
		public function getReceptNevek(): array {	
            $db = new Query('receptek');
            return $db->select(['id','nev'])->all();
        }	
    
        /**
         * Beolvassa az össes létező hozzávaló nevet
         * @return [{nev},...]
         */

        public function getHozzavaloNevek(): array {		
            $db = new Query('hozzavalok');
            $db->select(['distinct nev'])
                ->orderBy('nev');
            return $db->all();
        }		

        /**
         * Egy darab üres Hozzavalok rekordot hoz létre
         * @return Record
         */
        public function emptyHozzavalo(): Record {			
            $result = new Record();
            $result->mennyiseg = "";
            $result->me = "";
            $result->nev = "";
            return $result;
        }
        
        /**
         * Beolvassa az adott recepthez tartozó cimkéket
         * @param int $id
         * @return array of recept_cimkek rekord
         */
        public function getReceptCimkek(int $id):array {
            $db = new Query('recept_cimke');
            return $db->where('recept_id','=',$id)->all();
        } 

        /**
         * recept_cimke rekord tárolása (isert ha szükséges)
         * @param int $receptId
         * @param string $cimke 
         */    
        public function saveReceptCimke(int $receptId, string $cimke) {
            $db = new Query('recept_cimke');
            $rec = $db->where('recept_id','=',$receptId)
                        ->where('cimke','=',$cimke)
                        ->first();
            if (!isset($rec->cimke)) {
                $rec = new Record();
                $rec->recept_id = $receptId;
                $rec->cimke = $cimke;
                $db->insert($rec);
            }          

        }

        /**
         * recept_cimke rekord törlése
         * @param int $receptId
         * @param string $cimke 
         */    
        public function delReceptCimke(int $receptId, string $cimke) {
            $db = new Query('recept_cimke');
            $db->where('recept_id','=',$receptId)
                ->where('cimke','=',$cimke)
                ->delete();
        }

        /**
         * az "id" recpet szerepel a kedvencek között?
         * @param int $user_id
         * @param int $recept_id
         * @param bool
         */
        public function isFavorit(int $user_id, int $recept_id) {
            $q = new Query('kedvencek');
            $rec = $q->where('user_id','=',$user_id)
                    ->where('recept_id','=',$recept_id)
                    ->first();
            return isset($rec->id);        
        }

        /**
         * recept hozzá adása a kedvencekhez
         * @param int $user_id
         * @param int $recept_id
         */
        public function addToFavorit(int $user_id, int $recept_id) {
            $record = new Record();
            $record->id = 0;
            $record->user_id = $user_id;
            $record->recept_id = $recept_id;
            $record->pozicio = 0;
            $q = new Query('kedvencek');
            $q->insert($record);
            if ($q->error != '') {
                echo $q->error(); exit();
            }
        }
  
        /**
         * recept törlése a kedvencek közül
         * @param int $user_id
         * @param int $recept_id
         */
        public function delFromFavorit(int $user_id, int $recept_id) {
            $q = new Query('kedvencek');
            $q->where('user_id','=',$user_id)->where('recept_id','=',$recept_id)->delete();
            if ($q->error != '') {
                echo $q->error(); exit();
            }
        }
    
    }		


?>