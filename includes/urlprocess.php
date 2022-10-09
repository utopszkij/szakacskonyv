<?php

function getUrls($string)
{
$regex = '/[^\"\>]https?\:\/\/[^\" \n]+/i';
preg_match_all($regex, $string, $matches);
return $matches[0];
}

/**
 * a $đtring ben lévő url -eket átalakítja <a...>...</a>
 * vagy youtube iframe --re
 * @param string $string
 * @return string
 */ 
function urlprocess(string $string):string {
	// ckeditor által kreált régi tipusú youtube hivás eltávolítása
	$string = str_replace('<figure class="media"><oembed url="',' ',$string);
	$string = str_replace('"></oembed></figure>',' ',$string);
	// string-ben lévő url -ek kigyüjtése
	$urls = getUrls($string);
	// url-ek modositása <a -re illetve youtube iframe -r
	foreach ($urls as $url) {
		if (strpos($url,'youtu.be') > 0) {
			$code = trim(str_replace('https://youtu.be/','',$url));
			$string = str_replace($url,
			'<div style="text-align:center">
			<iframe width="400" height="240" 
			src="https://www.youtube.com/embed/'.$code.'" 
			title="YouTube video player" frameborder="0" 
			allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
			allowfullscreen>
			</iframe>
			</div>',$string);
		} else if (strpos($url,'vimeo.com') > 0) {
			$code = trim(str_replace('https://vimeo.com/','',$url));
			$string = str_replace($url,'
			<div style="text-align:center">
			<iframe src="https://player.vimeo.com/video/'.$code.'" 
				width="400" height="240" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" 
				allowfullscreen>
			</iframe>
			</div>',$string);
		} else if ((strpos($url,'facebook.com') > 0) & (strpos($url,'/videos/') > 0)) {
			$string = str_replace($url,'
			<div style="text-align:center">
				<a target="_new" href="'.$url.'" 
					style="display:inline-block; background-color:black; color:white; padding:30px 80px; font-size:22px; border-style:solid; border-width:1px;">
				<em class="fas fa-play"></em>
				</a>
			</div>',$string);
		} else if (strpos($url,'tiktok.com') > 0) {
			$string = str_replace($url,'
			<div style="text-align:center">
				<a class="tiktok" target="_new" href="'.$url.'" 
					style="display:inline-block; background-color:black; color:white; padding:30px 80px; font-size:22px; border-style:solid; border-width:1px;">
					<em class="fas fa-play"></em>
				</a>
			</div>',$string);
		} else {
			$string = str_replace($url, '<a href="'.$url.'">'.$url.'</a>',$string);
		}
	}
	
	return $string;
}

?>
