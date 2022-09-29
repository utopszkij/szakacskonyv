<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once __DIR__.'/../models/blogmodel.php';
include_once __DIR__.'/../models/blogcommentmodel.php';
include_once __DIR__.'/../models/likemodel.php';

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
        if ($record->title == '') {
            $result = 'TITLE_REQUESTED<br>';
        }
        if ($record->body == '') {
            $result .= 'BODY_REQUESTED<br>';
        }
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
                $blogs[$fn]->body = mb_substr(strip_tags($fv->body),0,255).'...<strong>Olvass tovább: kattints!</strong>...';
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
            "filter" => $filter,
            "errorMsg" => $this->session->input('errorMsg',''),
            "successMsg" => $this->session->input('successMsg','')
        ]);
        $this->session->set('errorMsg','');
        $this->session->set('successMsg','');
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
            $comments = $commentModel->getComments($page, $blog->id, $limit, 'created_at','DESC');
            $total = $commentModel->getTotal($blog_id);
        } else {
            $comments = [];
            $total = 0;
        }    

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
            "task" => 'blog',
            "errorMsg" => $this->session->input('errorMsg',''),
            "successMsg" => $this->session->input('successMsg','')
        ]);
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
        if ($this->session->input('loged') <= 0) {
            $this->session->set('errorMsg', 'ACCESS_DENIED');
            view('blogs',[]);
        }
        $q = new Query('users');
        $user = $q->where('id','=',$this->session->input('loged'))->first();
        if (!isset($user->id)) {
            $user->id = 0;
            $user->username = '';
        }
        $blog = $this->model->emptyRecord();
        $blog->commentCount = 0;
        $blog->likeCount = 0;
        $blog->createdAt = date('Y.m.d H:i');
        $blog->creator = JSON_decode('{"name":"'.$user->username.'", "avatar":"'.$this->model->userAvatar($user->id).'"}');        
        $blog->userLike = 0;
        $this->session->set('errorMsg', '');
        $this->session->set('successMsg', '');
        view('blogform',['blog' => $blog,
            "errorMsg" => $this->session->input('errorMsg',''),
            "successMsg" => $this->session->input('successMsg','')
        ]);
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
        if ($this->session->input('loged') <= 0) {
            $this->session->set('errorMsg', 'ACCESS_DENIED');
            view('blogs',[]);
        }
        $q = new Query('users');
        $user = $q->where('id','=',$this->session->input('loged'))->first();
        if (!isset($user->id)) {
            $user->id = 0;
            $user->username = '';
        }
        $id = $this->request->input('blog_id',0);
        $blog = $this->model->getById($id);
        if (isset($blog->id)) {
            $this->session->set('errorMsg', '');
            $this->session->set('successMsg', '');
            view('blogform',['blog' => $blog,
                "errorMsg" => $this->session->input('errorMsg',''),
                "successMsg" => $this->session->input('successMsg','')
            ]);
        } else {
            $this->session->set('errorMsg', 'NOT_FOUND');
            $this->session->set('successMsg', '');
            $this->blogs();
            $this->session->set('errorMsg', '');
        }    
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
        if (($this->session->input('logedGroup') == 'admin') |
            ($this->session->input('logedGroup') == 'moderator')) {
            $id = $this->request->input('id');
            $commentModel = new BlogcommentModel();
            $comment = $commentModel->getById($id);
            if (isset($comment->id)) {
                $blog_id = $comment->blog_id;
                $blog = $this->model->getById($blog_id);
                if (isset($blog->id)) {
                    if (strlen($blog->body) > 128) {
                        $blog->body = substr(strip_tags($blog->body),0,128).'....';
                    }
                    $blog->body =
                    view('blogcommentform',['blog' => $blog, 
                                            'comment' => $comment,
                                            'loged' => $this->session->input('loged'),
                                            'logedGroup' => $this->session->input('logedGroup')]);
                }
            }
            $this->session->set('errorMsg','NOT_FOUND');
            view('blogs',[]);
            $this->session->set('errorMsg', '');
        } else {
            $this->session->set('errorMsg', 'ACCESS_DENIED');
            view('blogs',[]);
            $this->session->set('errorMsg', '');
        }    
	}
	
	/**
	 * blog add/edit képernyő tárolása
	 * session[loged] és [logedGroup] jogosultság ellenörzéssel 
	 * POSTban: blog_id, blog rekord adatai
     * tárolás után --> blogs
	 */ 
	public function blogsave() {
        if ($this->session->input('loged') >= 0) {
            $id = $this->request->input('id',0);
            $record = $this->model->emptyRecord();
            if ($id > 0) {
                $oldRecord = $this->model->getById($id);
                $record->id = $id;
                $record->created_by = $oldRecord->created_by;
                $record->created_at = $oldRecord->created_at;
            } else {
                $q = new Query('users');
                $record->created_at = date('Y.m.d H:i');
                $record->created_by = $this->session->input('loged');        
            }    
            $record->title = $this->request->input('title','');
            $record->body = $this->request->input('body','',HTML);
            $errorMsg = ($this->validator($record));
            if ($errorMsg == '') {    
                $this->model->save($record);
                $errorMsg = $this->model->errorMsg;
            }    
            if ($errorMsg == '') {    
                $this->session->set('successMsg', 'SAVED');
                $this->session->set('errorMsg', '');
                $this->blogs();
            } else {
                $this->session->set('successMsg', '');
                $this->session->set('errorMsg', $errorMsg);
                $this->blogs();
            }
        } else {
            $this->session->set('errorMsg', 'ACCESS_DENIED');
            $this->session->set('successMsg', '');
            $this->blogs();
            $this->session->set('successMsg', '');
            $this->session->set('errorMsg', '');
        }
	}

	/**
	 * blog comment add/edit képernyő tárolása
	 * session[loged] és [logedGroup] jogosultság ellenörzéssel 
	 * POSTban: blog_id, comment rekord adatai
	 * SESSIONBAN logged, logedGroup
	 * tárolás után --> blogcomments
	 */ 
	public function blogcommentsave() {
        if ($this->session->input('loged') > 0) {
            $record = new \RATWEB\DB\Record();
            $record->blog_id = $this->request->input('blog_id',0);
            $record->id = $this->request->input('id',0);
            $record->body = $this->request->input('commentbody',0);
            if ($record->id == 0) {
                $record->created_at = date('Y.m.d');
                $record->created_by = $this->session->input('loged',0);
            }
            $commentModel = new BlogcommentModel();
            $commentModel->save($record);

            $this->session->set('errorMsg', '');
            $this->session->set('successMsg', 'SAVED');
            $this->blog();
            $this->session->set('successMsg', '');
            $this->session->set('errorMsg', '');
        } else {
            $this->session->set('errorMsg', 'ACCESS_DENIED');
            $this->session->set('successMsg', '');
            $this->blog();
            $this->session->set('successMsg', '');
            $this->session->set('errorMsg', '');
        }    
	}
	

	/**
	 * blog bejegyzés és kommentjei, like -jai törlése
	 * GET param: blog_id 
     * jogosultság ellenörzés!
	 * SESSIONban loged, logedGroup
	 * Jogosultság ellenörzés!
     * törlések után --> blogs
	 */ 
	public function delblog() {
        if (($this->session->input('logedGroup') == 'admin') |
            ($this->session->input('logedGroup') == 'moderator')) {
            $id = $this->request->input('blog_id',0);
            $this->model->delById($id);
            $this->session->set('successMsg','DELETED');
            $this->session->set('errorMsg','');
            $this->blogs();    
            $this->session->set('successMsg','');
            $this->session->set('errorMsg','');
        } else {
            $this->session->set('successMsg','');
            $this->session->set('errorMsg','ACCESS_DENIED');
            $this->blogs();    
            $this->session->set('successMsg','');
            $this->session->set('errorMsg','');
        }
	}

	/**
	 * blog komment törlése
	 * GET param: comment_id, blog_id jogosultság ellenörzés!
	 * SESSIONban loged, logedGroup
	 * Jogosultság ellenörzés!
     * törlés után --> blog
	 */ 
	public function blogcommentdelete() {
        if (($this->session->input('logedGroup') == 'admin') |
        ($this->session->input('logedGroup') == 'moderator')) {
            $blog_id = $this->request->input('blog_id',0);
            $comment_id = $this->request->input('comment_id',0);
            $commentModel = new BlogcommentModel();
            $commentModel->delById($comment_id);
            $this->session->set('successMsg','DELETED');
            $this->session->set('errorMsg','');
            $this->blog();    
            $this->session->set('successMsg','');
            $this->session->set('errorMsg','');
        } else {
            $this->session->set('successMsg','');
            $this->session->set('errorMsg','ACCESS_DENIED');
            $this->blog();    
            $this->session->set('successMsg','');
            $this->session->set('errorMsg','');
        }
  	}


}


?>