<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    class MertekegysegModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable('mertekegysegek');
            $this->errorMsg = ''; 
        }

        public function emptyRecord(): Record {
            $result = new Record();
            $result->id = 0;
            $result->nev = '';
            return $result;
        }

        /**
         * rekordok lapozható listája
         * @param int $page
         * @param int $limit
         * @param string $filter - nincs használva
         * @param string $order - nincs használva
         * @return array
         */
        public function getItems(int $page, int $limit, string $filter, string $order): array {
            $db = new Query('mertekegysegek');
            $result = $db->offset((($page - 1) * $limit))
                    ->limit($limit)
                    ->orderBy('nev')
                    ->all();
            return $result;        
        }

        /**
         * Összes rekord száma
         * @return int
         */
        public function getTotal($filter): int {
            $db = new Query('mertekegysegek');
            $recs = $db->all();
            return count($recs);
        }

  }    
?>