<?php

use PHPUnit\Framework\TestCase;
use RATWEB\DB\Query;
use RATWEB\DB\Record;

include_once __DIR__.'/mock.php';

include_once('./includes/controllers/blog.php');
class BlogTest extends TestCase {
	protected $controller;
	
	function __construct() {
		parent::__construct();
		$this->controller = new Blog();
	}
    
	public function test_start()  {
		$db = new Query('blogs');
		$db->delete();
		$this->assertEquals(1,1);
    }
   
   /*
    * Blog bejegyzés lapozható list legfrisebb elöl
    * paraméterek a viewernek: blogs, loged, logedGroup, page, total
    *    blogs: [{id,title, descripton, commentCount, creator, created_at, like_count},..] 
    * gombok (jogosultság függően) addblog
    * itemclick: show
    */
	public function test_list() {
		$_GET['redirect'] = '';
		$this->controller->blogs();
       	$this->assertEquals(checkView()['name'],'blogs');
	}

   /*
    * Blog bejegyzés megjelenitése
    * paraméterek a viewernek: blog, loged, logedGroup, page, total
    *    blog: {id,title, descripton, commentCount, creator, created_at, like_count} 
    * gombok (jogosultság függően) editblog, delblog, comments
    */
	public function test_blogshow() {
		$_GET['redirect'] = '';
		$this->controller->blogshow();
       	$this->assertEquals(checkView()['name'],'blogshow');
	}

   /*
    * Blog bejegyzés kommentek lapozható list legfrisebb elöl
    * paraméterek a viewernek: blog, comments, loged, logedGroup, page, total
    *    blog: {id,title, descripton, commentCount, creator, created_at, like_count} 
    *    comments: [{id, body, creator, created_at, like_count},..] 
    * gombok (jogosultság függően) editcomment, delcomment, 
    * addcomment form is van rajta
    */
	public function test_commentlist() {
		$_GET['redirect'] = '';
		$this->controller->blogcomments();
       	$this->assertEquals(checkView()['name'],'blogcomments');
	}

   /*
    * Blog bejegyzés komment like lista
    * paraméterek a viewernek: blog, comment, loged, logedGroup, page, total
    *    blog: {id,title, descripton, commentCount, creator, created_at, like_count} 
    * gombok (jogosultság függően) vissza a blog komment listára 
    */
	public function test_bloglikelist() {
		$_GET['redirect'] = '';
		$this->controller->bloglikelist();
       	$this->assertEquals(checkView()['name'],'bloglikelist');
	}

   /*
    * Blog bejegyzés edit/add form
    * paraméterek a viewernek: blog, loged, logedGroup
    *    blog: {id,title, descripton, commentCount, creator, created_at, like_count} 
    * gombok (jogosultság függően) save, cancel
    */
	public function test_blogform() {
		$_GET['redirect'] = '';
		$this->controller->edit();
       	$this->assertEquals(checkView()['name'],'blogform');
	}

   /*
    * Blog comment edit form
    * paraméterek a viewernek: blog, comment, loged, logedGroup
    *    blog: {id,title, descripton, commentCount, creator, created_at, like_count} 
    *    comment: {id, body, creator, created_at, like_count} 
    * gombok (jogosultság függően) save, cancel, delete
    */
	public function test_commentform() {
		$_GET['redirect'] = '';
		$this->controller->edit();
       	$this->assertEquals(checkView()['name'],'blogcommentform');
	}
	
	/**
	 * blog add/edit képernyő tárolása
	 * POSTban: blog_id, blog rekord adatai
	 */ 
	public function test_blogsave() {
		$_GET['redirect'] = '';
		$this->controller->blogsavet();
       	$this->assertEquals(checkView()['name'],'blogs');
	}

	/**
	 * blog comment add/edit képernyő tárolása
	 * POSTban: blog_id, comment rekord adatai
	 * SESSIONBAN logged
	 * Jogosultság ellenörzés!
	 */ 
	public function test_blogsave() {
		$_GET['redirect'] = '';
		$this->controller->blogcommentsavet();
       	$this->assertEquals(checkView()['name'],'blogcomments');
	}
	
	/**
	 * blog like click feldolgozása (like vagy dislike)
	 * POSTban: blog_id, 
	 * SESSIONban loged
	 * Jogosultság ellenörzés!
	 */ 
	public function test_blogcommentsave() {
		$_GET['redirect'] = '';
		$this->controller->bloglike();
       	$this->assertEquals(checkView()['name'],'blog');
	}
	
	/**
	 * blog bejegyzés és kommentjei, like -jai törlése
	 * GET param: blog_id jogosultság ellenörzés!
	 */ 
	public function test_blogdelete() {
		$_GET['redirect'] = '';
		$this->controller->blogdelete();
       	$this->assertEquals(checkView()['name'],'blogs');
	}

	/**
	 * blog bejegyzés és kommentjei, like -jai törlése
	 * GET param: comment_id jogosultság ellenörzés!
	 */ 
	public function test_blogcommentdelete() {
		$_GET['redirect'] = '';
		$this->controller->blogcommentdelete();
       	$this->assertEquals(checkView()['name'],'blogcomments');
	}
   
   
/* hibaüzenet tesztelés példa:  
    public function test_errorMsg() {
		$this->controller->errorMsgOkozoRutin();
		$this->expectOutputRegex('/hibaüzenet/'); 
	}
*/	

}
