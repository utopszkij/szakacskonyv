<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    class UserModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable('users');
            $this->errorMsg = ''; 
        }

        /**
         * logikai user rekord (users+profilok)
         */
        public function emptyRecord(): Record {
            $result = new Record();
            $result->id = 0;
            $result->username = '';
            $result->password = '';
            $result->avatar = '';
            $result->email = '';
            $result->realname = '';
            $result->group = '';
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
            $db = new Query('users','u');
            $result = $db->select(['u.id','u.username','p.avatar','p.group'])
                    ->join('LEFT OUTER','profilok','p','p.id','=','u.id')
                    ->offset((($page - 1) * $limit))
                    ->limit($limit)
                    ->orderBy('username')
                    ->all();
            return $result;        
        }

        /**
         * Összes rekord száma
         * @return int
         */
        public function getTotal($filter): int {
            $db = new Query('users');
            $recs = $db->all();
            return count($recs);
        }

        /**
         * logikai user rekord (users+profilok) olvasása id szerint
         * @param int $id
         * @return Record
         */

        public function getById(int $id): Record {
            $result = parent::getById($id);
            if (isset($result->id)) {
                $q = new Query('profilok');
                $p = $q->where('id','=',$result->id)->first();
                if (isset($p->avatar)) {
                    $result->avatar = $p->avatar;
                    $result->phone = $p->phone;
                    $result->realname = $p->realname;
                    $result->email = $p->email;
                    $result->group = $p->group;
                } else {
                    $result->avatar = '';
                    $result->realname = '';
                    $result->email = '';
                    $result->phone = '';
                    $result->group = '';
                }
            }
            return $result;
        }

        /**
         * logikai user rekord (users+profilok) törlése id szerint
         * @param int $id
         */
        public function delById(int $id): bool {
            $old = $this->getById($id);
            if (isset($old->id)) {
                $result = parent::delById($id);
                $q = new Query('profilok');
                $p = $q->where('id','=',$id)->delete();
                if (file_exists('images/users/'.$old->avatar)) {
                    unlink('images/users/'.$old->avatar);
                }
            }
            return $result;
        }

        /**
         * logikai user rekord tárolása (insert vagy update)
         * @param Record $record
         */
        public function save(Record $record): int {
            $u = new Record();
            $u->id = $record->id;
            $u->username = $record->username;
            $u->password = $record->password;
            $id = parent::save($u);

            $record->avatar = '';
            // avatr kép feltöltés
            $error = '';
            if (isset($_FILES['avatar'])) {
                if (file_exists($_FILES['avatar']['tmp_name'])) { 
                    $target_dir = DOCROOT.'/images/users';
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir,0777);
                    }
                    $target_dir .= '/';
                    $target_file = $target_dir.$id.'-'.basename($_FILES['avatar']["name"]);
                    $check = getimagesize($_FILES['avatar']["tmp_name"]);
                    if($check == false) {
                        $error = 'nem kép fájl';
                    }
                    if ($_FILES['avatar']['size'] > (UPLOADLIMIT * 1024 * 1024)) {
                        $error = 'túl nagy kép fájl';
                    }
                    if (file_exists($target_file) & ($error == '')) {
                        unlink($target_file);
                    }
                    if ($error == '') {
                        if (!move_uploaded_file($_FILES['avatar']["tmp_name"], $target_file)) {
                            $error = "Hiba a kép fájl feltöltés közben "; 
                        }
                        $record->avatar = $record->id.'-'.basename($_FILES['avatar']["name"]);
                    } else {
                        echo $error; exit();
                    }
                } 
            }
              
            $p = new Record();
            $p->id = $id;
            $p->avatar = $record->avatar;
            $p->realname = $record->realname;
            $p->email = $record->email;
            $p->phone = $record->phone;
            $p->group = $record->group;
            $q = new Query('profilok');
            $old = $q->where('id','=',$id)->first();
            if (isset($old->id)) {
                $q->update($p);
            } else {
                $q->insert($p);
            }
            return $id;
        }
  }    
?>