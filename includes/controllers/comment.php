<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;
include_once __DIR__.'/controller.php';
include_once __DIR__.'/../models/receptmodel.php';
include_once __DIR__.'/../models/commentmodel.php';

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
        $comment = $this->model->getById(intval($_GET['id']));
        if (isset($comment->recept_id)) {
            $receptModel = new ReceptModel();
            $recept = $receptModel->getById($comment->recept_id);
            $disabled = (($this->session->input('loged') <= 0) |
                         (($this->session->input('loged') != $comment->created_by) & 
                          ($this->logedGroup != 'admin') &
                          ($this->logedGroup != 'moderator')));
            view('commentkep',[
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

    /**
     * képfile upolad feldolgozása
     * @param string upload file control name --> img0 | img1 | img2
     * @param Record $comment
     * @return string '' ha ok, egyébként hibaüzenet, $comment->$name -t is kitölti
     */
    protected function doUpload(string $name, &$comment): string {            
        $result = '';
        if (file_exists($_FILES[$name]['tmp_name'])) { 
            $target_dir = DOCROOT.'/images/comments';
            if (!is_dir($target_dir)) {
                mkdir($target_dir,0777);
            }
            $target_dir .= '/';
            $target_file = $target_dir.$comment->id.'-'.basename($_FILES[$name]["name"]);
            $check = getimagesize($_FILES[$name]["tmp_name"]);
            if($check == false) {
                $result = 'nem kép fájl';
            }
            if ($_FILES[$name]['size'] > (UPLOADLIMIT * 1024 * 1024)) {
                $result = 'túl nagy kép fájl';
            }
            if (file_exists($target_file) & ($result == '')) {
                unlink($target_file);
            }
            if ($result == '') {
                if (!move_uploaded_file($_FILES[$name]["tmp_name"], $target_file)) {
                    $uploadOk = "Hiba a kép fájl feltöltés közben ".$name; 
                }
                $comment->$name = $comment->id.'-'.basename($_FILES[$name]["name"]);
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
            $comment->msg = $this->request->input('msg');
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

