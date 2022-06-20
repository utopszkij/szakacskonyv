<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    class SzinonimaModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable('szinonimak');
            $this->errorMsg = ''; 
        }

        public function emptyRecord(): Record {
            $result = new Record();
            $result->id = 0;
            $result->mit = '';
            $result->mire = '';
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
            $db = new Query('szinonimak');
            $result = $db->offset((($page - 1) * $limit))
                    ->limit($limit)
                    ->orderBy('mit')
                    ->all();
            return $result;        
        }

        /**
         * Összes rekord száma
         * @return int
         */
        public function getTotal($filter): int {
            $db = new Query('szinonimak');
            $recs = $db->all();
            return count($recs);
        }

        /**
         * az $i -edig szinonima definiciót végrehajtja a recept adatbázis összes 
         * érintett elemén (hozzavalok és atvaltasok)
         * @param int $i
         * @return bool true ha végrehajtotta, false ha $i >= total
         */
        public function processAll(int $i): bool {
            $recs = $this->getItems(1,20000,'','mit');
            if ($i < count($recs)) {
                $mit = $recs[$i]->mit;
                $mire = $recs[$i]->mire;
                $q = new Query('receptek');
  
                $q->exec('update hozzavalok
                set nev = "'.$mire.'"
                where nev = "'.$mit.'"');
                if ($q->error != '') {
                    echo $q->error; exit();
                }
                $q->exec('update hozzavalok
                set me = "'.$mire.'"
                where me = "'.$mit.'"');
                if ($q->error != '') {
                    echo $q->error; exit();
                }
                $q->exec('update hozzavalok
                set szme = "'.$mire.'"
                where szme = "'.$mit.'"');
                if ($q->error != '') {
                    echo $q->error; exit();
                }
  
                $q->exec('update atvaltasok
                set me = "'.$mire.'"
                where me = "'.$mit.'"');
                if ($q->error != '') {
                    echo $q->error; exit();
                }
                $q->exec('update atvaltasok
                set szme = "'.$mire.'"
                where szme = "'.$mit.'"');
                if ($q->error != '') {
                    echo $q->error; exit();
                }
                
                $result = true;
            } else {
                $result = false;
            }
            return $result;
        }
    }    
?>