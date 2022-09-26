<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    class BlogcommentModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable('blogcomments');
            $this->errorMsg = ''; 
        }

        /**
         * logikai user rekord (users+profilok)
         */
        public function emptyRecord(): Record {
            $result = new Record();
            $result->id = 0;
            $result->blog_id = 0;
            $result->body = '';
            $result->created_by = '';
            $result->created_at = '';
            return $result;
        }

        /**
         * $blog_id alapján Query -t alakit ki
         * @param Query $q
         * @param object $blog_id
         */
        protected function processFilter(&$q, int $blog_id) {
            $q->where('blog_id','=',$blog_id);
            return $q;
        }

        /**
         * user name lapján avatar kép url -t képez
         * @param string $name
         * @return string
         */
        protected function userAvatar(string $name): string {
            $result = 'images/users/noavatar.png';
            if (file_exists('images/users/'.$name.'.png')) {
                $result = 'images/users/'.$name.'.png';
            }
            if (file_exists('images/users/'.$name.'.jpg')) {
                $result = 'images/users/'.$name.'.jpg';
            }
            if (file_exists('images/users/'.$name.'.gif')) {
                $result = 'images/users/'.$name.'.gif';
            }
            return $result;
        }

        /**
         * blog comment böngésző számára rekord set
         * @param int $page
         * @param int $blog_id
         * @param int $limit
         * @param string $oder
         * @param string $orderDir 'ASC'|'DESC'
         * @return array
         */
        public function getComments(int $page, int $blog_id,
                 int $limit, string $order, string $orderDir):array {
            $q = new Query('blogcomments');
            $this->processFilter($q, $filter); 
            $result = $q->select(['id','body',
                               'created_by','created_at createdAt'])
                    ->offset(($page-1)*$limit)
                    ->limit($limit)
                    ->orderBy($order)
                    ->orderDir($orderDir)
                    ->all();

            foreach ($result as $res) {
                $q2 = new Query('users');
                $user = $q2->where('id','=',$res->created_by)->first();
                $res->creator = new \stdClass();
                if (isset($user->id)) {
                    $res->creator->id = $user->id;
                    $res->creator->name = $user->username;
                    $res->creator->avatar = $this->userAvatar($user->username);
                } else {
                    $res->creator->id = 0;
                    $res->creator->name = '';
                    $res->creator->avatar = 'images/users/noavatar.png';
                }
            }
            return $result;        
        }

        /**
         * $blog_id alapján összes rekord számot ad vissza
         * @param object $blog_id
         * @return int
         */
        public function getTotal(int $blog_id):int {
            $q = new Query('blogcomments');
            $this->processBlogFilter($q,$blog_id); 
            return count($q->select(['id'])->all());
        }

        /**
         * id alapján blogcomment rekord és néhány kapcsolodó infó olvasása
         * @param int $id
         * @return object
        */
        public function getById(int $id):Record {
            $q = new Query('blogcomments');
            $result = $q->where('id','=',$id)->first();
            if (isset($result->id)) {
                $q2 = new Query('users');
                $user = $q2->where('id','=',$result->created_by)->first();
                $result->creator = new \stdClass();
                if (isset($user->id)) {
                    $result->creator->id = $user->id;
                    $result->creator->name = $user->username;
                    $result->creator->avatar = $this->userAvatar($user->username);
                } else {
                    $result->creator->id = 0;
                    $result->creator->name = '';
                    $result->creator->avatar = 'images/users/noavatar.png';
                }
            }
            return $result;
        }

}    
?>
