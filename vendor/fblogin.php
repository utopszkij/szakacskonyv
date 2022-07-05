<?php

// lásd: https://www.cloudways.com/blog/add-facebook-login-in-php/


if (file_exists(__DIR__.'/config.php')) {
	include_once (__DIR__.'/config.php');
}
/**
* távoli URL hívás   
* @param string $url
* @param array $post ["név" => "érték", ...]
* @param array $headers
* @return string
*/
function callCurl(string $url, array $post=array(), array $headers=array()):string {
        $return = '';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if(count($post)>0) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $return = curl_exec($ch);
        return $return;
}

/**
* távol API hívás     
* @param string $url
* @param array $post
* @param array $headers
* @return mixed
*/
function apiRequest(string $url, array $post=array(), array $headers=array()) {
	        $headers[] = 'Accept: application/json';
	        if (isset($post['access_token'])) {
	            $headers[] = 'Authorization: Bearer ' . $post['access_token'];
               $post = [];
	        }
	        $response = callCurl($url, $post, $headers);
	        return JSON_decode($response);
}    

if (isset($_GET['code'])) {
	// facebook login callback
	$code = $_GET['code'];
  	if (isset($_GET['state'])) {
  		 $state = urldecode($_GET['state']);
  	} else {
		 $state = SITEURL;	  	
  	}	 
	if ($code == 'test') {
		$token = JSON_decode('{"access_token":"test"}');	
	} else {
		// kell ilyen paraméter is? 'redirect_uri' => \URL::to('/').'/auth/facebook/callback',
  		$token = apiRequest(
   	      'https://graph.facebook.com/oauth/access_token',
   		   ['client_id' => FB_APPID,
             'client_secret' => FB_SECRET,
   		    'grant_type' => 'authorization_code',
   		    'redirect_uri' => 'https://netpolgar.hu/fblogin.php',
             'state' => $state,
             'code' => $code
   		   ]
		);
		echo JSON_encode($token).'<br />';
	}	
	if (isset($token->access_token)) {
		if ($token->access_token == 'test') {
			$fbuser = JSON_decode('{"id":"12345678", "name":"fbuser"}');
		} else {
         $url="https://graph.facebook.com/v2.3/me?fields=id,name,picture";
   		$fbuser = apiRequest($url,
					['access_token' => $token->access_token]
			);
			echo JSON_encode($fbuser);
		}		
		if (!isset($fbuser->error)) {
			// sikeres fb login fbuser:{id, name, picture, email}
			$userCode = base64_encode($fbuser->name).'-'.
				$fbuser->id.'-'.md5($fbuser->id.FB_SECRET);
			?>
			<script>
				document.location = '<?php echo $state; ?>'+
				'?usercode=<?php echo $userCode; ?>';						
			</script>
			<?php
		} else {
			echo 'Fatal error facebook login '.JSON_encode($fbuser->error); exit();
		}	
	} else {
		   echo 'Fatal error facebook login invalid call not get access_token'; exit();
	}
} else {
	// facebook login képernyő
	if (!isset($_GET['state'])) $_GET['state'] = SITEURL;
	?>
	<script>
	document.location='https://www.facebook.com/v12.0/dialog/oauth'+
			'?client_id=<?php echo FB_APPID; ?>'+
			'&redirect_uri='+encodeURI('https://netpolgar.hu/fblogin.php')+
        	'&state=<?php echo $_GET["state"]; ?>';
	</script>
	<?php
}
?>

