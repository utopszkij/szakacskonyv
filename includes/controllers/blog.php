<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once __DIR__.'/../models/blogmodel.php';
include_once __DIR__.'/../models/blogcommentmodel.php';
// include_once __DIR__.'/../models/likemodel.php';

class Blog extends Controller {

	function __construct() {
		parent::__construct();
		$this->model = new BlogModel();
        $this->name = "blog";
        $this->browserURL = 'index.php?task=blogs';
        $this->addURL = 'index.php?task=addblog';
        $this->editURL = 'index.php?task=editblog';
        $this->browserTask = 'blogs';
	}

	/**
     * rekord ellenörzés felvitelnél, modosításnál van hivva
     * @param Record $record
     * @return string üres vagy hibaüzenet
     */
    protected function validator($record):string {
        $result = '';
        return $result;
    }


    /**
     * bejelentkezett user jogosult erre?
	 * a forman vannak szükség esetén letiltva a modositó mezők 
     * @param string $action new|edit|delete
     * @return bool
     */
    protected function  accessRight(string $action, $record):bool {
		$result = true;
        return $result;
    }

   /*
    * Blog bejegyzés lapozható lista legfrisebb elöl
	* session/GET: page, filter
    * paraméterek a viewernek: blogs, loged, logedGroup, page, total
	*    filter: {titleStr, bodyStr, creatorName, ctreatedAt}
    *    blogs: [{id,title, descripton, commentCount, creator, createdAt, likeCount},..] 
	*    creator:{id,name, avatar}
    *    pages, task
    *akciok (session[loged] jogosultság függően) 
    *    addBlog
    *    blog (itemClick)
    */
	public function blogs() {
        $page = 1;
        $filter = new \stdClass();
        $filter->titleStr = '';
        $filter->bodyeStr = '';
        $filter->creatorName = '';
        $filter->createdAt = '';
        $name = 'blogs_';
        $limit = 8;

        // $page és $filter olvasása get-ből vagy session-ből
        $page = $this->request->input('page', $this->session->input($name.'page',1));
        $filter->titleStr = $this->request->input('titlestr', $this->session->input($name.'titleStr',''));
        $filter->bodyStr = $this->request->input('bodystr', $this->session->input($name.'bodyStr',''));
        $filter->creatorName = $this->request->input('creatorname', $this->session->input($name.'creatorName',''));
        $filter->createdAt = $this->request->input('createdat', $this->session->input($name.'createdAt',''));

        // $page és $filter tárolása sessionba
        $this->session->set($name.'page',$page);
        $this->session->set($name.'titleStr',$filter->titleStr);
        $this->session->set($name.'bodyStr',$filter->bodyStr);
        $this->session->set($name.'creatorName',$filter->creatorName);
        $this->session->set($name.'createdAt',$filter->createdAt);

        $blogs = $this->model->getBlogs($page,$filter,$limit,'b.created_at','DESC');
        $total = $this->model->getBlogsTotal($filter);

// TEST
        $blog = new \stdClass();
        $blog->id = 1;
        $blog->title = 'blog bejegyzés';
        $blog->body = 'Ez egy elég hosszú blog bejegyzés szöveg ami html elemeket is tartalmaz <strong>kiemelt szöveg</strong> 1234 5678 91011 ....';
        $blog->commentCount = 0;
        $blog->likeCount = 0;
        $blog->createdAt = "2022.07.07";
        $blog->creator = JSON_decode('{"name":"user1", "avatar":"nincs"}');        
        $blogs = [$blog, $blog, $blog];
        $total = 28;

        $pages = [];
        $p = 0;
        $w = 0;
        while ($w < $total) {
            $p++;
            $pages[] = $p;
            $w = $w + $limit;
        }

        foreach ($blogs as $fn => $fv) {
            if (strlen($fv->body) > 255) {
                $blogs[$fn]->body = mb_substr(strip_tags($fv->body),0,255).'...';
            }    
        }
        

        view('blogs',[
            "loged" => $this->session->input('loged',0),
            "logedGroup" => $this->session->input('logedGroup',0),
            "blogs" => $blogs,
            "total" => $total,
            "page" => $page,
            "pages" => $pages,
            "task" => 'blogs',
            "filter" => $filter
        ]);
	}

   /*
    * Blog bejegyzés és comments lista megjelenitése
	* GET: blog_id, page
    */
	public function blog() {
        $name = 'blogcomments_';
        $limit = 8;
        $blog_id = $this->request->input('blog_id',0);
        $blog = $this->model->getById($blog_id);
        $page = $this->request->input('page', $this->session->input($name.'page',1));
        $this->session->set($name.'page',$page);
    
        $commentModel = new BlogcommentModel();
        if (isset($blog->id)) {
            $comments = $commentModel->getComments($blog->id, $page, $limit, 'created_at','DESC');
            $total = $commentModel->getTotal($blog_id);
        } else {
            $comments = [];
            $total = 0;
        }    

// TEST
$blog = new \stdClass();
$blog->id = 1;
$blog->title = 'blog bejegyzés';
$blog->body = 'Ez egy elég hosszú blog bejegyzés szöveg ami html elemeket is tartalmaz <strong>kiemelt szöveg</strong> 1234 5678 91011 ....';
$blog->commentCount = 0;
$blog->likeCount = 0;
$blog->createdAt = "2022.07.07";
$blog->creator = JSON_decode('{"name":"user1", "avatar":"nincs"}');        
$blog->userLike = 0;
$comment = new \stdClass();
$comment->id = 1;
$comment->body = 'proba comment';
$comment->createdAt = "2022.06.07";
$comment->creator = JSON_decode('{"name":"Gipszjakab", "avatar":"nincs"}');
$comments = [$comment, $comment];
$total = 30;

        $pages = [];
        $p = 0;
        $w = 0;
        while ($w < $total) {
            $p++;
            $pages[] = $p;
            $w = $w + $limit;
        }

        view('blog',[
            "loged" => $this->session->input('loged',0),
            "logedGroup" => $this->session->input('logedGroup',0),
            "blog" => $blog,
            "comments" => $comments,
            "total" => $total,
            "page" => $page,
            "pages" => $pages,
            "task" => 'blog'
        ]);
	}


   /*
    * Blog bejegyzés komment like lista
	* GET: blog_id
	* session/GET: page
    * paraméterek a viewernek: blog, comment, loged, logedGroup, likes, page, total
    *    blog: {id,title, descripton, commentCount, creator, created_at, like_count} 
	*        creator:{id,name, avatar}
    *    likes:[{id, name, avatar},...]
	* session/GET page
    * akciok
    *    blogcomments 
    */
	public function bloglikes() {
        view('nincskesz',[]);
	}

   /*
    * Blog bejegyzés add form
	* session[loged] és [logedGroup] jogosultság ellenörzéssel 
    * paraméterek a viewernek: blog, loged, logedGroup
    *    blog: {id,title, descripton, commentCount, creator, created_at, like_count} 
	*    creator:{id,name, avatar}
    * akciok (session[logedGroup] jogosultság függően) 
    *    save --> saveblog
    *    cancel --> blogs
    */
	public function addblog() {
        if ($_SESSION['loged'] <= 0) {
            echo 'not enabled'; exit();
        }
        $q = new Query('users');
        $user = $q->where('id','=',$_SESSION['loged'])->first();
        $blog = $this->model->emptyRecord();
        $blog->commentCount = 0;
        $blog->likeCount = 0;
        $blog->createdAt = date('Y.m.d H:i');
        $blog->creator = JSON_decode('{"name":"'.$user->username.'", "avatar":"'.$this->model->userAvatar($user->username).'"}');        
        $blog->userLike = 0;
        view('blogform',['blog' => $blog]);
	}

	/*
    * Blog bejegyzés edit form
	* GET: blog_id
	* session[loged] és [logedGroup] jogosultság ellenörzéssel 
    * paraméterek a viewernek: blog, loged, logedGroup
    *    blog: {id,title, descripton, commentCount, creator, created_at, like_count} 
	*    creator:{id,name, avatar}
    * akciok (session[logedGroup] jogosultság függően) 
    *    save  --> saveblog 
    *    cancel --> blogs
    */
	public function editblog() {
        view('nincskesz',[]);
	}

   /*
    * Blog comment edit form
	* GET: comment_id, blog_id
	* session[logedGroup] és [loged] jogosultság ellenörzéssel 
    * paraméterek a viewernek: blog, comment, loged, logedGroup
    *    blog: {id,title, descripton, commentCount, creator, created_at, like_count} 
	*        creator:{id,name, avatar}
    *    comment: {id, body, creator, created_at, like_count} 
	*        creator:{id,name, avatar}
    * akciok (session[logedGroup] jogosultság függően) 
    *    save --> saveblogcomment, 
    *    cancel  --> blog, 
    *    delete  --> deleteblogcomment
    */
	public function editblogcomment() {
        view('nincskesz',[]);
	}
	
	/**
	 * blog add/edit képernyő tárolása
	 * session[loged] és [logedGroup] jogosultság ellenörzéssel 
	 * POSTban: blog_id, blog rekord adatai
     * tárolás után --> blogs
	 */ 
	public function blogsave() {
        view('nincskesz',[]);
	}

	/**
	 * blog comment add/edit képernyő tárolása
	 * session[loged] és [logedGroup] jogosultság ellenörzéssel 
	 * POSTban: blog_id, comment rekord adatai
	 * SESSIONBAN logged, logedGroup
	 * tárolás után --> blogcomments
	 */ 
	public function blogcommentsave() {
        view('nincskesz',[]);
	}
	
	/**
	 * blog like click feldolgozása (like vagy dislike)
	 * GET: blog_id, like_type 
	 * SESSIONban loged, logedGroup
	 * Jogosultság ellenörzés!
     * tárolás után --> blog
	 */ 
	public function likesave() {
        view('nincskesz',[]);
	}
	
	/**
	 * blog bejegyzés és kommentjei, like -jai törlése
	 * GET param: blog_id 
     * jogosultság ellenörzés!
	 * SESSIONban loged, logedGroup
	 * Jogosultság ellenörzés!
     * törlések után --> blogs
	 */ 
	public function blogdelete() {
        view('nincskesz',[]);
	}

	/**
	 * blog bejegyzés és kommentjei, like -jai törlése
	 * GET param: comment_id jogosultság ellenörzés!
	 * SESSIONban loged, logedGroup
	 * Jogosultság ellenörzés!
     * törlés után --> blogcomments
	 */ 
	public function blogcommentdelete() {
        view('nincskesz',[]);
	}


}


?>