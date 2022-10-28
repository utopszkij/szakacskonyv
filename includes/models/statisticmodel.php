<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    class StatisticModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable = 'events';
            $this->errorMsg = ''; 
        }

        /**
         * statisztikai adatok tárolása
         * ugyanannak a user-nek a loginj naponta csak egyszer számit
         * ugyanannak a Sid-nek a látogatása naponta csak egyszer számit
         */
        public function saveStatistic() {
            $d = date('Y-m-d');

            // látogatás
            $q = new Query('events');
            $rec = $q->where('created_at','=',$d)
                    ->where('event','=','visit')
                    ->where('data','=',session_id())
                    ->first();
            if (!isset($rec->created_at)) {
                $q = new Query('events');
                $q->exec('INSERT INTO `events` VALUES ("'.$d.'","visit","'.session_id().'")');
            }
            
            if (isset($_SESSION['loged'])) {
                $u = $_SESSION['loged'];
            } else {
                $u = 0;
            }    
            if ($u > 0) {
                $q = new Query('events');
                $rec = $q->where('created_at','=',$d)
                        ->where('event','=','login')
                        ->where('data','=',$u)
                        ->first();
                if (!isset($rec->created_at)) {
                    $q = new Query('events');
                    $q->exec('INSERT INTO `events` VALUES ("'.$d.'","login","'.$u.'")');
                }
            }
        }
  }		


?>