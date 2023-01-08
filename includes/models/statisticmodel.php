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

        protected function _bot_detected() {
            return (
              isset($_SERVER['HTTP_USER_AGENT'])
              && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'])
            );
        }

        protected function crawlerDetect() {
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
            } else {
                $USER_AGENT = 'Google';
            }
            $crawlers = array(
            'Google' => 'Google',
            'bot' => 'bot',
            'MSN' => 'msnbot',
                'Rambler' => 'Rambler',
                'Yahoo' => 'Yahoo',
                'AbachoBOT' => 'AbachoBOT',
                'accoona' => 'Accoona',
                'AcoiRobot' => 'AcoiRobot',
                'ASPSeek' => 'ASPSeek',
                'CrocCrawler' => 'CrocCrawler',
                'Dumbot' => 'Dumbot',
                'FAST-WebCrawler' => 'FAST-WebCrawler',
                'GeonaBot' => 'GeonaBot',
                'Gigabot' => 'Gigabot',
                'Lycos spider' => 'Lycos',
                'MSRBOT' => 'MSRBOT',
                'Altavista robot' => 'Scooter',
                'AltaVista robot' => 'Altavista',
                'ID-Search Bot' => 'IDBot',
                'eStyle Bot' => 'eStyle',
                'Scrubby robot' => 'Scrubby',
                'Facebook' => 'facebookexternalhit',
            );
            // to get crawlers string used in function uncomment it
            // it is better to save it in string than use implode every time
            // global $crawlers
            $crawlers_agents = implode('|',$crawlers);
            if (strpos($crawlers_agents, $USER_AGENT) === false) {
                return false;
            } else {
                return TRUE;
            }
        }

        /**
         * statisztikai adatok tárolása
         * ugyanannak a user-nek a loginj naponta csak egyszer számit
         * ugyanannak a Sid-nek a látogatása naponta csak egyszer számit
         */
        public function saveStatistic() {
            $d = date('Y-m-d');
            // robotok szürése
            if ($this->crawlerDetect()) {
                return;
            }

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

        /**
        * ha az adott receptet ez a user még nem látta akkor új rekordot visz fel
        * az event táblába és növeli a recept rekorban a számlálót.
        * user azonositás: ha loged akkor a "logedName" ha nem akkor az  "si"
        */
        public function setShow(&$recept, string $loged) {
            // robotok szürése
            if ($this->crawlerDetect()) {
                return;
            }
            // van már "show" event rekord?
            $q = new Query('events');
            $q->where('event','=','show')
                        ->where('data','=',$loged.'_recept_'.$recept->id)
                        ->orWhere('data','=',session_id().'_recept_'.$recept->id)
                        ->where('event','=','show'); 
            $rec = $q->first();
            if (!isset($rec->created_at)) {
                // még nincs; új megtekintés
                if ($loged == 'guest') {
                    $loged = session_id();
                }
                $rec = new Record();
                $rec->created_at = date('Y-m-d');
                $rec->event = 'show';
                $rec->data = $loged.'_recept_'.$recept->id;
                $q->insert($rec);
                // most növeli a recept rekordban a megtekintés számlálót
                $q = new Query('receptek');
                $rec = new Record();
                $rec->lattak = $recept->lattak + 1; 
                $q->where('id','=',$recept->id)->update($rec);
                $recept->lattak = $recept->lattak + 1;
            }        
        }

  }		


?>