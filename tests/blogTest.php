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
		// $db = new Query('blogs');
		// $db->delete();
		$this->assertEquals(1,1);
    }
   
	public function test_blogs() {
		$_GET['redirect'] = '';
		$this->controller->blogs();
       	$this->assertEquals(checkView()['name'],'blogs');
	}

	public function test_blog() {
		$_GET['redirect'] = '';
		$this->controller->blog();
       	$this->assertEquals(checkView()['name'],'blog');
	}

	public function test_bloglikes() {
		$_GET['redirect'] = '';
		$this->controller->bloglikes();
       	$this->assertEquals(checkView()['name'],'bloglikes');
	}

	public function test_addblog() {
		$_GET['redirect'] = '';
		$this->controller->addblog();
       	$this->assertEquals(checkView()['name'],'blogform');
	}

	public function test_editblog() {
		$_GET['redirect'] = '';
		$this->controller->addblog();
       	$this->assertEquals(checkView()['name'],'blogform');
	}

	public function test_editblogcomment() {
		$_GET['redirect'] = '';
		$this->controller->editblogcomment();
       	$this->assertEquals(checkView()['name'],'blogcommentform');
	}
	
	public function test_blogsave() {
		$_GET['redirect'] = '';
		$this->controller->blogsave();
       	$this->assertEquals(checkView()['name'],'blogs');
	}

	public function test_blogcommentsave() {
		$_GET['redirect'] = '';
		$this->controller->blogcommentsave();
       	$this->assertEquals(checkView()['name'],'blogcomments');
	}
	
	public function test_likesave() {
		$_GET['redirect'] = '';
		$this->controller->likesave();
       	$this->assertEquals(checkView()['name'],'blog');
	}
	
	public function test_delblog() {
		$_GET['redirect'] = '';
		$this->controller->delblog();
       	$this->assertEquals(checkView()['name'],'blogs');
	}

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
