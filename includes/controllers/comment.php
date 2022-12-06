<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;
include_once __DIR__.'/../models/receptmodel.php';
include_once __DIR__.'/../models/commentmodel.php';
include_once __DIR__.'/../urlprocess.php';
include_once __DIR__.'/../uploader.php';

class Comment extends Controller {
	function __construct() {
        parent::__construct();
        $this->model = new CommentModel();
	}

    /**
     * comment képernyő GET -ben 'id' = commentId
     * a comment felvivője és az ADMIN modosithat, törölhet, mások csak nézhetik
     * a képek mellet adminnak és a crátornak törlő link is van.
     */
    public function comment() {
        $comment = $this->model->getById(intval($this->request->input('id')));
        if (isset($comment->recept_id)) {
            $receptModel = new ReceptModel();
            $recept = $receptModel->getById($comment->recept_id);
            $disabled = (($this->session->input('loged') <= 0) |
                         (($this->session->input('loged') != $comment->created_by) & 
                          ($this->logedGroup != 'admin') &
                          ($this->logedGroup != 'moderator')));
            view('commentkep',[
                "flowKey" => $this->newFlowKey(),
                "recept" => $recept,
                "comment" => $comment,
                "UPLOADLIMIT" => UPLOADLIMIT,
                "disabled" => $disabled,
                "loged" => $this->session->input('loged'),
                "admin" => $this->logedAdmin,
                "group" => $this->logedGroup
            ]);

        }
    }

    /**
     * Újkomment felviő képernyő (csak bejelentkezetteknek)
     * GET receptid = receptId
     */
    public function commentadd() {
        if ($this->session->input('loged') > 0) {
            $receptId = $this->request->input('receptid',0,INTEGER);
            $receptModel = new ReceptModel();
            $recept = $receptModel->getById($receptId);
            $comment = new Record();
            $comment->id = 0;
            $comment->recept_id = $receptId;
            $comment->created_by = $this->session->input('loged');
            $comment->created_at = date('Y-m-d');
            $comment->msg = '';
            $comment->img0 = "";
            $comment->img1 = "";
            $comment->img2 = "";
            view('commentkep',[
                "flowKey" => $this->newFlowKey(),
                "recept" => $recept,
                "comment" => $comment,
                "UPLOADLIMIT" => UPLOADLIMIT,
                "disabled" => false,
                "loged" => $this->session->input('loged'),
                "admin" => $this->logedAdmin,
                "group" => $this->logedGroup
            ]);
        }    
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
     * képfile upolad feldolgozása
     * @param string upload file control name --> img0 | img1 | img2
     * @param Record $comment
     * @return string '' ha ok, egyébként hibaüzenet, $comment->$name -t is kitölti
     */
    protected function doUpload(string $name, &$comment): string {            
        $result = '';
        if (file_exists($_FILES[$name]['tmp_name'])) { 
            $uploadRes = Uploader::doImgUpload($name, 
                                                DOCROOT.'/images/comments',
                                                'comment_'.$comment->id.'.*');
            if ($uploadRes->error == '') {
                $comment->$name = $uploadRes->name;
            } else {
                $result = $uploadRes->error;
            }                                   
        } 
        return $result;
    }            
/**
     * POST -ban érkező comment adatok tárolása 
     * (lehet edit vagy add, képek is lehetnek)
     * jogosultság ellenörzéssel, után redirect a recep képernyőre
     */
    public function commentsave() {
        if ($this->session->input('loged') > 0) {
            $comment = new Record();
            $comment->id = $this->request->input('id',0,INTEGER);
            if ($comment->id > 0) {
                $comment = $this->model->getById($comment->id);
            }
            $comment->recept_id = $this->request->input('recept_id',0,INTEGER);
            if (!$this->checkFlowKey('index.php?task=recept&id='.$comment->recept_id)) {
                echo 'flowKey error. Lehet, hogy túl hosszú várakozás miatt lejárt a munkamenet.'; 
                exit();
            }
            $comment->msg = urlprocess($this->request->input('msg','',HTML));
            $comment->created_by = $this->request->input('created_by',0,INTEGER);
            $comment->created_at = $this->request->input('created_at');
            if ($comment->id == 0) {
                $comment->created_at = date('Y-m-d');
                $comment->created_by = $this->session->input('loged');
            }  
            if ($this->logedAdmin | 
                ($this->logedGroup == 'moderator') | 
                ($this->session->input('loged') == $comment->created_by)) {
                $comment->id = $this->model->save($comment);
                if ($this->model->errorMsg != '') {
                    echo $this->model->errorMsg; exit();
                }
                // feltöltött kép fájlok tárolása: images/comments/id-mév.kit 
                $uploadOk = '';
                if ($uploadOk == '') {
                    $uploadOk = $this->doUpload('img0', $comment);
                }
                if ($uploadOk == '') {
                    $uploadOk = $this->doUpload('img1', $comment);
                }
                if ($uploadOk == '') {
                    $uploadOk = $this->doUpload('img2', $comment);
                }
                if ($uploadOk == '') {
                    $this->model->save($comment);
                } else {
                    echo 'Fatális hiba '.$uploadOk; exit();
                }
            }
        }
        echo '<script>location="index.php?task=recept&id='.$comment->recept_id.'";</script>';
    }

    /**
     * kép törlése GET -ben "img" img1 | img1 | img2 és comment "id"
     * jogosultság ellenörzéssel, utána vissza a komment képernyőre
     */
    public function commentimgdel() {
        $comment = $this->model->getById($this->request->input('id',0,INTEGER));
        if ($this->logedAdmin | 
            ($this->logedGroup == 'moderator') |
            ($this->loged == $comment->created_by)) {
            if (isset($comment->img0)) {
                $imgName = $this->request->input('img');
                $imgFileName = $comment->$imgName;
                if (file_exists('images/comments/'.$imgFileName)) {
                    unlink('images/comments/'.$imgFileName);
                }
                $comment->$imgName = '';
                $this->model->save($comment);
                if ($this->model->errorMsg != '') {
                    echo $this->model->errorMsg; exit();
                }
            }
        }
        echo '<script>location="index.php?task=comment&id='.$comment->id.'";</script>';
    }

    /**
     * GET -ben érkező id -ü komment törlése a hozzá tartozó képekkel együtt
     * jogosultság ellenörzéssel, utána redirekt a recept képernyőre
     */
    public function commentdel() {
        $comment = $this->model->getById($this->request->input('id',0,INTEGER));
        if ($this->logedAdmin | 
            ($this->logedGroup == 'moderator') |
            ($this->loged == $comment->created_by)) {
            if (isset($comment->id)) {
                $imgFileName = $comment->img0;
                if (is_file('images/comments/'.$imgFileName)) {
                    unlink('images/comments/'.$imgFileName);
                }
                $imgFileName = $comment->img1;
                if (is_file('images/comments/'.$imgFileName)) {
                    unlink('images/comments/'.$imgFileName);
                }
                $imgFileName = $comment->img2;
                if (is_file('images/comments/'.$imgFileName)) {
                    unlink('images/comments/'.$imgFileName);
                }
                $this->model->delById($comment->id);
                if ($this->model->errorMsg != '') {
                    echo $this->model->errorMsg; exit();
                }
            }
        }
        echo '<script>window.location="index.php?task=recept&id='.$comment->recept_id.'";</script>';
    }
} // class

