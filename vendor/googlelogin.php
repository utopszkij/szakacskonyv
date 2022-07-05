<?php

/*
* google konfigurálás: 
*   https://console.cloud.google.com/apis/dashboard?pli=1
*
* sikeres login után a SITEURL -t hivja meg, usercode url paraméterben
* küldve a user adatokat
* $userCode = base64_encode($guser->name).'-'.
*				$guser->id.'-'.md5($guser->id.FB_SECRET).'-'. !!! FIGYELEM FB_SECRET !!!
*				base64_encode($guser->email).'-'.
*				base64_encode($guser->picture);
*
* a google -on az app configba beirandó callback url: ennek a fájlnak az url-je
*    általában: {yourDomain}/vendor/googlelogin.php
*
* szükség esetén akár másik domainen is lehet telepitve (és ehez konfigurálva)
* a state paraméter segitségével tud máshol lévő programba is beléptetni.
*
* google login kezdeményezése:
*    <a href="vendor/googlelogin.php?state=<?php url_encode(SITEURL; ?>">
*			Belépés facebook -al
*    </a>
*
* szükséges DEFINE -k: SITEURL, FB_SECRET, 
*           GOOGLE_APPID, GOOGLE_SECRET, GOOGLE_REDIRECT
*           ha van laravel .env file; akkor abból olvassa ki ezeket 
*/
if (file_exists(__DIR__.'/../config.php')) {
	include_once(__DIR__.'/../config.php');
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

/**
* token lekérdezése a google -röl code alapján
* @param string $code
* @param string $state
* @return object $token
   	      'https://oauth2.googleapis.com/token',
*/
function getToken(string $code, string $state) {  	 
	if ($code == 'test') {
		$token = JSON_decode('{"access_token":"test"}');	
	} else {
		$token = apiRequest(
				'https://accounts.google.com/o/oauth2/token',
   		   ['client_id' => GOOGLE_APPID,
             'client_secret' => GOOGLE_SECRET,
   		    'grant_type' => 'authorization_code',
             'redirect_uri' => GOOGLE_REDIRECT,
             'state' => $state,
             'code' => $code
   		   ]
	    	);		
	}
	return $token;	
}	

if (isset($_GET['code'])) {
	// google login callback
	$token = false;
	$code = $_GET['code'];
  	if (isset($_GET['state'])) {
 	 	 $state = urldecode($_GET['state']);
  		 if ($state == 0) {
			$state = SITEURL;  		 
  		 }	
  	} else {
		 $state = SITEURL;	  	
  	}
  	$token = getToken($code,$state);	
	if (isset($token->access_token)) {
		$guser = JSON_decode('{"id":"12345678", "name":"guser"}');
		if ($token->access_token == 'test') {
			$guser = JSON_decode('{"id":"12345678", "name":"fbuser"}');
		} else {
			$url="https://www.googleapis.com/oauth2/v1/userinfo?alt=json";
	   	$guser = apiRequest($url,
					   ['access_token' => $token->access_token]);
			// bizonyos esetekben a user nevét nem küldi :(	
			if (!isset($guser->name)) {
				if (isset($guser->email)) {		   
					$w = explode('@',$guser->email);
					$guser->name = $w[0];
				} else {
					$guser->name = 'g_'.$guser->id;
				}	
			}	
		}	
		$userCode = base64_encode($guser->name).'-'.
			$guser->id.'-'.md5($guser->id.FB_SECRET).'-'.
			base64_encode($guser->email).'-'.
			base64_encode($guser->picture);
		?>
		<script>
			document.location = '<?php echo $state; ?>'+
			'?usercode=<?php echo $userCode; ?>';						
		</script>
		<?php
	} else {
		   echo 'Fatal error google invalid call not get access_token'.
		   JSON_encode($token) ; exit();
	}
} else {
	// google login képernyő
	if (isset($_GET['state'])) {
		$state = urldecode($_GET['state']);	
	} else {
		$state = SITEURL;	
	}
	?>
	<script>
	document.location = 'https://accounts.google.com/o/oauth2/v2/auth'+
   '?client_id=<?php echo GOOGLE_APPID; ?>'+
   '&response_type=code'+
   '&scope=openid%20email'+
   '&redirect_uri=<?php echo urlencode(GOOGLE_REDIRECT); ?>'+
   '&state=<?php echo $state; ?>';	
	</script>
	<?php
}
?>

