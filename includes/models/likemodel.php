<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    include_once __DIR__.'/blogmodel.php';

    class LikeModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable('likes');
            $this->errorMsg = ''; 
        }

        /**
         * logikai user rekord (users+profilok)
         */
        public function emptyRecord(): Record {
            $result = new Record();
            $result->id = 0;
            $result->target_id = 0;
            $result->target_type = '';
            $result->user_id = 0;
            $created_at = '';
            return $result;
        }

        /**
         * like click feldolgozása
         * - ha még nincs ilyen rekord akkor insert
         * - ha már van akkor delete
         */
        public function doLike($type, $id, $user_id) {
            $q = new Query($this->table);
            $rec = $q->where('target_type','=',$type)
                     ->where('target_id','=',$id)
                     ->where('user_id','=',$user_id)
                     ->first();
            if (isset($rec->id)) {
                $q->where('target_type','=',$type)
                ->where('target_id','=',$id)
                ->where('user_id','=',$user_id)
                ->delete();
            } else {
                $record = $this->emptyRecord();
                $record->target_type = $type;
                $record->target_id = $id;
                $record->user_id = $user_id;
                $record->created_at = date('Y-m-d');
                $q->insert($record);
            }        
        }

        /**
         * like rekord sorozat böngésző megjelenítéshez
         * @param int $page
         * @param string $target_type
         * @param int $target_id
         * @param int $limit
         * @param string $order
         * @param string $orderDir 'ASC'|'DESC'
         * @return array 
         */
        public function getLikes(int $page,string $target_type, string $target_id,
            int $limit, string $order, string $orderDir):array {
            $blogModel = new BlogModel();
            $q = new Query($this->table,'l');
            $q->select(['l.id', 'u.username', 'u.id user_id'])
                        ->join('INNER','users','u','u.id','=','l.user_id')
                        ->where('l.target_type','=',$target_type)
                        ->where('l.target_id','=',$target_id)
                        ->orderBy($order)
                        ->orderDir($orderDir)
                        ->offset(($page-1)*$limit)
                        ->limit($limit);
            $result = $q->all();
            foreach ($result as $res) {
                $res->avatar = $blogModel->userAvatar($res->user_id); 
            }
            return $result;            
        }

        /**
         * összes like szám 
         * @param string $target_type
         * @param int $target_id
         * @return int
        */        
        public function getLikesTotal($target_type, $target_id):int {
            $q = new Query($this->table,'l');
            return count(
                $q->select(['l.id'])
                    ->join('INNER','users','u','u.id','=','l.user_id')
                    ->where('target_type','=',$target_type)
                    ->where('target_id','=',$target_id)
                    ->all()
            );
        }

        /**
         * user like-olta ezt?
         * @param string $target_type
         * @param int $user_id
         * @return bool
         */
        public function userLiked(string $target_type, int $target_id, int $user_id): bool {
            $q2 = new Query('likes');
            return (
                count(
                    $q2->select(['id'])
                    ->where('target_id','=',$target_id)
                    ->where('target_type','=',$target_type)
                    ->where('user_id','=',$user_id)
                    ->all()
                ) > 0
            );
        }

        /**
         * Összes like rekord törlése
         * @param string $target_type
         * @param int $target_id
         */
        public function deleteLikes(string $target_type, int $target_id) {
            $q2 = new Query('likes');
            $q2->select(['id'])
            ->where('target_id','=',$target_id)
            ->where('target_type','=',$target_type)
            ->delete();
        }

        /**
         * like bajnokság első helyezetjei
         * @param string $type
         * @param int $limit
         */
        public function getWinners(string $type,int $limit=10) {
            $result = [];

            $db = new Query('likes','l');
            if ($type == 'recept') {
                $items = $db->select([['count(l.id)','cc'],'r.id','r.nev'])
                ->join('LEFT OUTER','receptek','r','r.id','=','l.target_id')
                ->groupBy(['r.id'])
                ->where('l.target_type','=','recept')
                ->where('r.nev','<>','')
                ->orderBy('cc')
                ->orderDir('DESC')
                ->limit($limit)
                ->all();
                // $result = array_merge($items,$items0);
                $result = $items;
            }
            return $result;
        }
}    
?>
