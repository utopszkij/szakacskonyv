<?php
/**
 * ennek a fájlnak a document root -ban kell lennie (ahol az index.php)
 * célszerüen cron ütemezővel kell futtatni például naponta egyszer
*/

include_once 'config.php';
include_once 'vendor/database/db.php';

$urls = [SITEURL, SITEURL.'/', SITEURL.'/index.php',SITEURL.'/task/receptek/page/1',SITEURL.'/task/blogs/page/1'];

$q = new \RATWEB\DB\Query('receptek');
$recs = $q->all();
foreach ($recs as $rec) {
	$urls[] = SITEURL.'/task/recept/id/'.$rec->id.'/title/'.urlencode($rec->nev);
}

$urls[] = SITEURL.'/task/blogs/page/1';

$q = new \RATWEB\DB\Query('blogs');
$recs = $q->all();
foreach ($recs as $rec) {
	$urls[] = SITEURL.'/task/blog/blog_id/'.$rec->id;
}

Header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
?>

<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
      http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php foreach ($urls as $url) : ?>
<url>
  <loc><?php echo $url; ?></loc>
  <lastmod><?php echo date('Y-m-d'); ?></lastmod>
  <priority>1.00</priority>
</url>
<?php endforeach; ?>

</urlset>
