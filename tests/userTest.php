<?php

use PHPUnit\Framework\TestCase;
use RATWEB\DB\Query;
use RATWEB\DB\Record;

include_once __DIR__.'/mock.php';

include_once('./includes/controllers/user.php');
class UserTest extends TestCase {
	protected $controller;
	
	function __construct() {
		parent::__construct();
		$this->controller = new User();
	}
    
	public function test_start()  {
		$db = new Query('users');
		$db->delete();
		$this->assertEquals(1,1);
    }
   
    public function test_logout() {
		$this->controller->logout();
       	$this->assertEquals($_SESSION['loged'],-1);
		$this->expectOutputRegex('/index.php/'); // ha van php output akkor kell expectOutputRegex !
	}
	
	public function test_login() {
		$_GET['redirect'] = '';
		$this->controller->login();
       	$this->assertEquals(checkView()['name'],'login');
	}

	public function test_regist() {
		$_GET['redirect'] = '';
		$this->controller->regist();
       	$this->assertEquals(checkView()['name'],'regist');
	}

	public function test_doregist_ok() {
		$_POST['redirect'] = '';
		$_POST['username'] = 'test';
		$_POST['password'] = '123456';
		$_POST['password2'] = '123456';
		$this->controller->doregist();
		$this->expectOutputRegex('/document.location="http:\/\/localhost\/szakacskonyv\/";/');
		$_POST['redirect'] = '';
		$_POST['username'] = ADMIN;
		$_POST['password'] = '123456';
		$_POST['password2'] = '123456';
		$this->controller->doregist();
		$this->expectOutputRegex('/document.location="http:\/\/localhost\/szakacskonyv\/";/');
		
	}

	public function test_doregist_emptydata() {
		$_POST['redirect'] = '';
		$_POST['username'] = '';
		$_POST['password'] = '';
		$_POST['password2'] = '';
		$this->controller->doregist();
		$this->expectOutputRegex('/Névet és jelszót meg kell adni!/');
	}

	public function test_doregist_notequals() {
		$_POST['redirect'] = '';
		$_POST['username'] = 'test3';
		$_POST['password'] = '123456';
		$_POST['password2'] = '12345678';
		$this->controller->doregist();
		$this->expectOutputRegex('/nem azonos/');
	}

	public function test_doregist_exists() {
		$_POST['redirect'] = '';
		$_POST['username'] = 'test';
		$_POST['password'] = '123456789';
		$_POST['password2'] = '123456789';
		$this->controller->doregist();
		$this->expectOutputRegex('/van ilyen/');
	}

	public function test_dologin_notfound() {
		$_POST['redirect'] = '';
		$_POST['username'] = 'test2';
		$_POST['password'] = '12345678';
		$this->controller->dologin();
		$this->expectOutputRegex('/Nincs ilyen/');
	}

	public function test_dologin_wrongpsw() {
		$_POST['redirect'] = '';
		$_POST['username'] = 'test';
		$_POST['password'] = 'wrong';
		$this->controller->dologin();
		$this->expectOutputRegex('/Nem jó jelszó/');
	}

	public function test_dologin_ok() {
		global $viewData;
		$_POST['redirect'] = '';
		$_POST['username'] = 'test';
		$_POST['password'] = '123456';
		$this->controller->dologin();
		$this->expectOutputRegex('/document.location="http:\/\/localhost\/szakacskonyv\/";/');
	}

}
