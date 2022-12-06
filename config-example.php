<?php 
declare(strict_types=1);
DEFINE('SITEURL','https://szakacskonyv.great-site.net');
DEFINE('REWRITE',true);
DEFINE('HOST','localhost');
DEFINE('USER','mysql-user');
DEFINE('PSW','mysql-user-password');
DEFINE('DBNAME','szakacskonyv');
DEFINE('STYLE','default');
DEFINE('LIKESIZE',1); // ennyi like jelent egy "csillagot"

DEFINE('MULTIUSER',true); // true: több felhasználós mód, false: egy felhasználós mód
DEFINE('ADMIN','admin'); // system admin nick név

DEFINE('FB_APPID','123456');
DEFINE('FB_SECRET','123456');
DEFINE('FB_REDIRECT',SITEURL.'/vendor/fblogin.php');

DEFINE('GOOGLE_APPID','123456');
DEFINE('GOOGLE_SECRET','123456');
DEFINE('GOOGLE_REDIRECT',SITEURL.'/vendor/fblogin.php');

?>
