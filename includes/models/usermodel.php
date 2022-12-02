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
                    ->where('u.username','not like','törölt%')
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
            $recs = $db->where('username','not like','törölt%')->all();
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

            // echo 'getById '.JSON_encode($p).JSON_encode($result); exit();

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

        protected function remove_accent($str) {
            $a=array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','Ā','ā','Ă','ă','Ą','ą','Ć','ć','Ĉ','ĉ','Ċ','ċ','Č','č','Ď','ď','Đ','đ','Ē','ē','Ĕ','ĕ','Ė','ė','Ę','ę','Ě','ě','Ĝ','ĝ','Ğ','ğ','Ġ','ġ','Ģ','ģ','Ĥ','ĥ','Ħ','ħ','Ĩ','ĩ','Ī','ī','Ĭ','ĭ','Į','į','İ','ı','Ĳ','ĳ','Ĵ','ĵ','Ķ','ķ','Ĺ','ĺ','Ļ','ļ','Ľ','ľ','Ŀ','ŀ','Ł','ł','Ń','ń','Ņ','ņ','Ň','ň','ŉ','Ō','ō','Ŏ','ŏ','Ő','ő','Œ','œ','Ŕ','ŕ','Ŗ','ŗ','Ř','ř','Ś','ś','Ŝ','ŝ','Ş','ş','Š','š','Ţ','ţ','Ť','ť','Ŧ','ŧ','Ũ','ũ','Ū','ū','Ŭ','ŭ','Ů','ů','Ű','ű','Ų','ų','Ŵ','ŵ','Ŷ','ŷ','Ÿ','Ź','ź','Ż','ż','Ž','ž','ſ','ƒ','Ơ','ơ','Ư','ư','Ǎ','ǎ','Ǐ','ǐ','Ǒ','ǒ','Ǔ','ǔ','Ǖ','ǖ','Ǘ','ǘ','Ǚ','ǚ','Ǜ','ǜ','Ǻ','ǻ','Ǽ','ǽ','Ǿ','ǿ');
            $b=array('A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','U','U','U','U','Y','s','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o');
            return str_replace($a,$b,$str);
        }
        protected function clearFileName($s) {
            return preg_replace("/[^a-z0-9._-]/", '', strtolower($this->remove_accent($s)));
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

            // $record->avatar = '';
            // avatr kép feltöltés
            $error = '';
            if (isset($_FILES['newavatar'])) {
                if (file_exists($_FILES['newavatar']['tmp_name'])) { 
                    $target_dir = DOCROOT.'/images/users';
                    if (!is_dir($target_dir.'/')) {
                        mkdir($target_dir,0777);
                    }
                    $target_dir .= '/';
                    $target_file = $target_dir.$id.'-'.$this->clearFileName($_FILES['newavatar']["name"]);

                    $uploadFileExt = pathinfo($target_file,PATHINFO_EXTENSION);
                    if (!in_array($uploadFileExt, Array('jpg','jpeg','png','gif'))) {
                        $error = 'upload not enabled';
                    }

                    $check = getimagesize($_FILES['newavatar']["tmp_name"]);
                    if($check == false) {
                        $error = 'nem kép fájl';
                    }
                    if ($_FILES['newavatar']['size'] > (UPLOADLIMIT * 1024 * 1024)) {
                        $error = 'túl nagy kép fájl';
                    }
                    if (file_exists($target_file) & ($error == '')) {
                        unlink($target_file);
                    }
                    if ($error == '') {
                        if (!move_uploaded_file($_FILES['newavatar']["tmp_name"], $target_file)) {
                            $error = "Hiba a kép fájl feltöltés közben "; 
                        }
                        $record->avatar = $record->id.'-'.$this->clearFileName($_FILES['newavatar']["name"]);
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
