<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    class NapimenuModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable('napimenuk');
            $db = new Query('napimenuk');
            $db->exec('CREATE TABLE IF NOT EXISTS napimenuk (
                    id int AUTO_INCREMENT,
                    ev int,
                    ho int,
                    nap int,
                    datum varchar(12),
                    adag int,
                    sorszam int,
                    recept int,
                    created_by int,
                    PRIMARY KEY (id)
            )');	
        }

        /**
         * napimenu rekordok olvasása dátum (és loged) alapján
         */
        public function getByDate(int $ev, int $ho, int $nap): array {
            $db = new Query('napimenuk');
            return $db->where('ev','=',$ev)
               ->where('ho','=',$ho)
               ->where('nap','=',$nap)
               ->where('created_by','=',$db->sqlValue($_SESSION['loged']))
               ->orderBy('sorszam')
               ->all();
        }

        /**
         * összes tárolt recept rekord olvasása
         */
        public function getAllRecept() {	
            $db = new Query('receptek');
            return  $db->orderBy('nev')->all();
        }	
      
    }		


?>