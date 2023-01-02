<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    class CimkeModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable('cimkek');
            $this->errorMsg = ''; 
        }

        public function emptyRecord(): Record {
            $result = new Record();
            $result->id = 0;
            $result->cimke = '';
            $result->tulaj = 0;
            return $result;
        }

        /**
		 * teljes fa szerkezet beolvasása
		 * @param int $page
		 * @param inr $limit
		 * @param mixed $filter 
		 * @param string $order
		 * @return [{id, tulaj, szint, cimke}, ...]
		 */ 
		public function getItems(int $page,int $limit,$filter,string $order): array {
			$result = [];
			$this->getItems1(0,0,$result);
			return $result;
		}
		
		/**
		 * rekurziv eljárás adott tulajdonos alrekordjait olvassa
		 * a $result tömbbe, kiegészítve a $level adattal
		 * @param int $owner
		 * @param int $level
		 * @param array &$result [{id, tulaj, szint, cimke}, ...]
		 * @return void
		 */ 
		public function getItems1(int $owner, int $level, array &$result) {
			$q = new \RATWEB\DB\Query($this->table);
			$recs = $q->where('tulaj','=',$owner)->orderBy('cimke')->all();
			foreach($recs as $rec) {
				$rec->szint = $level;
				$result[] = $rec;
				$this->getItems1($rec->id, $level+1, $result);
			} 
		}


        /**
         * Összes rekord száma
         * @return int
         */
        public function getTotal($filter): int {
            $db = new Query('cimkek');
            $recs = $db->all();
            return count($recs);
        }

  }    
?>