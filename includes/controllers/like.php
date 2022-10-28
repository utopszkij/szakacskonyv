<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once __DIR__.'/../models/blogmodel.php';
include_once __DIR__.'/../models/blogcommentmodel.php';
include_once __DIR__.'/../models/likemodel.php';

class Like extends Controller {

	function __construct() {
		parent::__construct();
		$this->model = new LikeModel();
        $this->name = "like";
        $this->browserURL = 'index.php?task=likes';
        $this->addURL = '';
        $this->editURL = '';
        $this->browserTask = 'likes';
	}

   /*
    * likes lapozható lista ABC sorrend
    * session/GET: page, type, id
    * paraméterek a viewernek: likes, target_type, target, page, total
    *    likes: [{id, user_id, username, useravatar},..] 
    * akciók: back 
    */
	public function likes() {
        $page = 1;
        $target_type = $this->request->input('type','');
        $target_id = $this->request->input('id',0);
        $name = 'likes_'.$target_type.'_'.$target_id;
        $limit = 8;

        // $page és olvasása get-ből vagy session-ből
        $page = $this->request->input('page', $this->session->input($name.'page',1));

        // $page tárolása sessionba
        $this->session->set($name.'page',$page);

        $likes = $this->model->getLikes($page,$target_type, $target_id,$limit,'u.username','ASC');
        $total = $this->model->getLikesTotal($target_type, $target_id);
        $target = JSON_decode('{"id":0,"title":""}');
        if ($target_type == 'blog') {
            $model = new BlogModel();
            $target = $model->getById($target_id);
        }
        if ($target_type == 'recept') {
            $model = new ReceptModel();
            $target = $model->getById($target_id);
        }
        $pages = [];
        $p = 0;
        $w = 0;
        while ($w < $total) {
            $p++;
            $pages[] = $p;
            $w = $w + $limit;
        }


        view('likes',[
            "loged" => $this->session->input('loged',0),
            "logedGroup" => $this->session->input('logedGroup',0),
            "likes" => $likes,
            "total" => $total,
            "page" => $page,
            "pages" => $pages,
            "task" => 'likes&target_type='.$target_type.'&target_id='.$target_id,
            "target_type" => $target_type,
            "target" => $target,
            "errorMsg" => $this->session->input('errorMsg',''),
            "successMsg" => $this->session->input('successMsg','')
        ]);
        $this->session->set('errorMsg','');
        $this->session->set('successMsg','');
	}
	
	/**
	 * blog like click feldolgozása (like vagy dislike)
	 * GET: id, type 
	 * SESSION-ban loged, logedGroup
	 * Jogosultság ellenörzés!
     * tárolás után --> target oldal
	 */ 
	public function likesave() {
        if ($this->session->input('loged') > 0) {
            $id = $this->request->input('id');
            $type = $this->request->input('type');
            $model = new LikeModel();
            $model->doLike($type, $id, $this->session->input('loged'));
            if ($type == 'blog') {
                echo '
                <script>location="index.php?task=blog&blog_id='.$id.'";</script>
                ';
            }
            if ($type == 'recept') {
                echo '
                <script>location="index.php?task=recept&id='.$id.'";</script>
                ';
            }
        } else {
            echo '
            <script>location="index.php";</script>
            ';
        }
    }

    public function likewinners() {
        $type = $this->request->input('type');
        $items = $this->model->getWinners($type,9);
        view('likewinners',[
            'datum' => date('Y.m.d H:i:s'),
            'type' => $type,
            'items' => $items
        ]);
    }
}


?>