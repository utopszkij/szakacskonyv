<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    class CommentModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable('comments');
        }

        /**
         * commentek olvasása
         * @param int $receptId
         * @param int $page
         * @return array [{id, created_at, msg, userName, images},..] max 20 elem
         *    images:['url','url',...]
        */    
        public function getComments(int $receptId, int $page = 1): array {
            $result = [];
            $q = new Query('comments');
            $result = $q->select(['comments.id','comments.msg','comments.created_at',
            'comments.created_by','comments.img0','comments.img1','comments.img2',
            'u.username'])
            ->join('left','users','u','u.id','=','comments.created_by')
            ->where('recept_id','=',$receptId)
            ->orderBy('created_at','DESC')
            ->offset(($page-1)*20)
            ->limit(20)
            ->all();
            return $result;
        }

        /**
         * összes komment száma
         * @param int $receptId
         * @return int
         */
        public function getCommentsTotal(int $receptId): int {
            $result = 0;
            $q = new Query('comments');
            $recs = $q->where('recept_id','=',$receptId)
            ->all();
            $result = $q->count();
            return $result;
        }
    }

?>