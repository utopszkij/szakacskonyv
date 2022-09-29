<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    class BlogModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable('blogs');
            $this->errorMsg = ''; 
        }

        /**
         * logikai user rekord (users+profilok)
         */
        public function emptyRecord(): Record {
            $result = new Record();
            $result->id = 0;
            $result->title = '';
            $result->body = '';
            $result->created_by = '';
            $result->created_at = '';
            return $result;
        }

        /**
         * a $filter alapján Query -t alakit ki
         * @param Query $q
         * @param object $filter {titleStr, bodyStr, creatorName, createdAt}
         */
        protected function processBlogFilter(&$q, $filter) {
            if ($filter->titleStr != '') {
                $q->where('b.title','like','%'.$filter->titleStr.'%');
            }
            if ($filter->bodyStr != '') {
                $q->where('b.body','like','%'.$filter->bodyStr.'%');
            }
            if ($filter->createdAt != '') {
                $q->where('b.created_at','>=',$filter->createdAt);
            }
            if ($filter->creatorName != '') {
                $q2 = new Query('users');
                $user = $q2->where('username','=',$filter->creatorName)->first();
                if (isset($user->id)) {
                    $q->where('b.created_by','=',$user->id);
                } else {
                    $q->where('b.created_by','=',0);
                }
            }
        }

        /**
         * user name lapján avatar kép url -t képez
         * @param string $name
         * @return string
         */
        public function userAvatar(int $user_id): string {
            $result = 'images/users/noavatar.png';
            $q = new Query('profilok');
            $p = $q->where('id','=',$user_id)->first();
            if ($p->avatar != '') {
                $result = 'images/users/'.$p->avatar;
            }
            return $result;
        }

        /**
         * blogg böngésző számára rekord set
         * @param int $page
         * @param object $filter {titleStr, bodyStr, creatorName, createdAt}
         * @param int $limit
         * @param string $oder
         * @param string $orderDir 'ASC'|'DESC'
         * @return array
         */
        public function getBlogs(int $page, $filter,
                 int $limit, string $order, string $orderDir):array {
            $q = new Query('blogs','b');
            $this->processBlogFilter($q, $filter); 
            $result = $q->select(['b.id','b.title','b.body',
                               'b.created_by','b.created_at createdAt'])
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
                    $res->creator->avatar = $this->userAvatar($user->id);
                } else {
                    $res->creator->id = 0;
                    $res->creator->name = '';
                    $res->creator->avatar = 'images/users/noavatar.png';
                }
                $q2 = new Query('blogcomments');
                $res->commentCount = count(
                    $q2->select(['id'])
                    ->where('blog_id','=',$res->id)
                    ->all()
                );
                $q2 = new Query('likes');
                $res->likeCount = count(
                    $q2->select(['id'])
                    ->where('target_id','=',$res->id)
                    ->where('target_type','=','blog')
                    ->all()
                );
            }
            return $result;        
        }

        /**
         * filter object alapján összes rekord számot ad vissza
         * @param object $filter {titleStr, bodyStr, creatorName, createdAt}
         * @return int
         */
        public function getBlogsTotal($filter):int {
            $q = new Query('blogs','b');
            $this->processBlogFilter($q,$filter); 
            return count($q->select(['id'])->all());
        }

        /**
         * id alapján blog rekord és néhány kapcsolodó infó olvasása
         * @param int $id
         * @return object
        */
        public function getById(int $id):Record {
            $q = new Query('blogs');
            $result = $q->where('id','=',$id)->first();
            if (isset($result->id)) {
                $q2 = new Query('users');
                $user = $q2->where('id','=',$result->created_by)->first();
                $result->creator = new \stdClass();
                if (isset($user->id)) {
                    $result->creator->id = $user->id;
                    $result->creator->name = $user->username;
                    $result->creator->avatar = $this->userAvatar($user->id);
                } else {
                    $result->creator->id = 0;
                    $result->creator->name = '';
                    $result->creator->avatar = 'images/users/noavatar.png';
                }
                $q2 = new Query('blogcomments');
                $result->commentCount = count(
                    $q2->select(['id'])
                    ->where('blog_id','=',$result->id)
                    ->all()
                );
                $q2 = new Query('likes');
                $result->likeCount = count(
                    $q2->select(['id'])
                    ->where('target_id','=',$result->id)
                    ->where('target_type','=','blog')
                    ->all()
                );
                $q2 = new Query('likes');
                $result->userLike = (
                    count(
                        $q2->select(['id'])
                        ->where('target_id','=',$result->id)
                        ->where('target_type','=','blog')
                        ->where('user_id','=',$_SESSION['loged'])
                        ->all()
                    ) > 0
                );
            }
            return $result;
        }

        /**
         * id alapján blog rekord és alrekordjainak törlése
         * @param int $id
         * @return bool
        */
        public function delById(int $id):bool {
            $result = true;
            $q = new Query('blogs');
            $q->where('id','=',$id)->delete();
            $q2 = new Query('blogcomments');
            $q2->where('blog_id','=',$id)->delete();
            $q2 = new Query('likes');
            $q2->where('target_id','=',$id)
            ->where('target_type','=','blog')
            ->delete();
            return ($q->error == '');
        }

}    
?>
