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
            return $result;
        }

        /**
         * rekordok lapozható listája
         * @param int $page
         * @param int $limit
         * @param string $filter - nincs használva
         * @param string $order
         * @return array
         */
        public function getItems(int $page, int $limit, string $filter, string $order): array {
            $db = new Query('cimkek');
            $result = $db->offset((($page - 1) * $limit))
                    ->limit($limit)
                    ->orderBy($order)
                    ->all();
            if ($db->error != '') {        
                echo $db->error.' '.$db->getSql(). ' '.JSON_encode($result);        
            }    
            return $result;        
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