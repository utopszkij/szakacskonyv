<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    class AdminModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable = 'events';
            $this->errorMsg = ''; 
        }

        public function getTotals() {
            $result = new \stdClass();
            $result->recept = 0;
            $result->blog = 0;
            $result->menu = 0;
            $result->user = 0;
            
            $q =  new Query('receptek');
            $result->recept = $q->where('nev','<>','')->count();

            $q =  new Query('napimenuk');
            $result->menu = $q->count();

            $q =  new Query('blogs');
            $result->blog = $q->count();

            $q =  new Query('users');
            $q->where('username','not like','törölt%');
            $result->user = $q->count();

            return $result;
        }

        protected function buildDates(string $interval):array {
            $dates = [];
            if ($interval == 'week') {
                for ($i = 7; $i >= 0; $i--) {
                    $dates[] = date('Y-m-d', time() - $i*24*60*60);
                }    
            }    
            if ($interval == 'month') {
                for ($i = 30; $i >= 0; $i--) {
                    $dates[] = date('Y-m-d', time() - $i*24*60*60);
                }    
            }    
            if ($interval == 'year') {
                for ($i = 365; $i >= 0; $i--) {
                    $dates[] = date('Y-m-d', time() - $i*24*60*60);
                }    
            }    
            return $dates;
        }

        public function getReceptDatas(string $interval): array {
            $dates = $this->buildDates($interval);
            $dmin = $dates[0];
            $dmax = $dates[count($dates) - 1];
            $q = new Query('receptek');
            $recs = $q->select(['created_at',['count(id)','cc']])
                    ->where('created_at','>=',$dmin)
                    ->where('created_at','<=',$dmax)
                    ->groupBy(['created_at'])
                    ->all(); 
            $res = [];
            foreach ($recs as $rec) {
                $res[$rec->created_at] = $rec->cc;
            }
            $result = [];
            foreach ($dates as $date) {
                if (isset($res[$date])) {
                     $result[] = $res[$date];   
                } else {
                    $result[] = 0;
                }
            }
            return $result;
        }

        public function getBlogDatas(string $interval): array {
            $dates = $this->buildDates($interval);
            $dmin = $dates[0];
            $dmax = $dates[count($dates) - 1];
            $q = new Query('blogs');
            $recs = $q->select(['created_at',['count(id)','cc']])
                    ->where('created_at','>=',$dmin)
                    ->where('created_at','<=',$dmax)
                    ->groupBy(['created_at'])
                    ->all(); 
            $res = [];
            foreach ($recs as $rec) {
                $res[$rec->created_at] = $rec->cc;
            }
            $result = [];
            foreach ($dates as $date) {
                if (isset($res[$date])) {
                     $result[] = $res[$date];   
                } else {
                    $result[] = 0;
                }
            }
            return $result;
        }

        public function getRegistDatas(string $interval): array {
            $dates = $this->buildDates($interval);
            $dmin = $dates[0];
            $dmax = $dates[count($dates) - 1];
            $q = new Query('users');
            $recs = $q->select(['created_at',['count(id)','cc']])
                    ->where('created_at','>=',$dmin)
                    ->where('created_at','<=',$dmax)
                    ->groupBy(['created_at'])
                    ->all(); 
            $res = [];
            foreach ($recs as $rec) {
                $res[$rec->created_at] = $rec->cc;
            }
            $result = [];
            foreach ($dates as $date) {
                if (isset($res[$date])) {
                     $result[] = $res[$date];   
                } else {
                    $result[] = 0;
                }
            }
            return $result;
        }

        public function getVisitDatas(string $interval): array {
            $dates = $this->buildDates($interval);
            $dmin = $dates[0];
            $dmax = $dates[count($dates) - 1];
            $q = new Query('events');
            $recs = $q->select(['created_at',['count(*)','cc']])
                    ->where('created_at','>=',$dmin)
                    ->where('created_at','<=',$dmax)
                    ->where('event','=','visit')
                    ->groupBy(['created_at'])
                    ->all(); 
            $res = [];
            foreach ($recs as $rec) {
                $res[$rec->created_at] = $rec->cc;
            }
            $result = [];
            foreach ($dates as $date) {
                if (isset($res[$date])) {
                     $result[] = $res[$date];   
                } else {
                    $result[] = 0;
                }
            }
            return $result;
        }

        public function getLoginDatas(string $interval): array {
            $dates = $this->buildDates($interval);
            $dmin = $dates[0];
            $dmax = $dates[count($dates) - 1];
            $q = new Query('events');
            $recs = $q->select(['created_at',['count(*)','cc']])
                    ->where('created_at','>=',$dmin)
                    ->where('created_at','<=',$dmax)
                    ->where('event','=','login')
                    ->groupBy(['created_at'])
                    ->all(); 
            $res = [];
            foreach ($recs as $rec) {
                $res[$rec->created_at] = $rec->cc;
            }
            $result = [];
            foreach ($dates as $date) {
                if (isset($res[$date])) {
                     $result[] = $res[$date];   
                } else {
                    $result[] = 0;
                }
            }
            return $result;
        }

        public function getMenuDatas(string $interval): array {
            $dates = $this->buildDates($interval);
            $dmin = $dates[0];
            $dmax = $dates[count($dates) - 1];
            $q = new Query('napimenuk');
            $recs = $q->select(['datum',['count(id)','cc']])
                    ->where('datum','>=',$dmin)
                    ->where('datum','<=',$dmax)
                    ->groupBy(['datum'])
                    ->all(); 
            $res = [];
            foreach ($recs as $rec) {
                $res[$rec->datum] = $rec->cc;
            }
            $result = [];
            foreach ($dates as $date) {
                if (isset($res[$date])) {
                     $result[] = $res[$date];   
                } else {
                    $result[] = 0;
                }
            }
            return $result;
        }

  }		


?>