<?php
/*
konfig: developer.facebook.com

  create new app: Type: nincs, facebook login, www
  						site url "quicstart" js kodokat javasol.
  							
  Basic settings
	Display name      	megadva 
	App domains       	valami.hu
	Namespace         	üres
	Contact email     	megadva 
	Privacy Policy URL   megadva
	Terms of Service URL megadva
	User Data Deletion URL megadva
	Category  Lifestyle
	App purpose Clients
	Site url             megadva
  Advanced
   Nativ or Desctop app
   SocialDiscovery
  App Review
   Permission and Faitures
   	public_profile  Advanced Access
   	email				 Advanced Access
   	
   	beállításához a Requests menüben az "Edit" ikon majd 
   	"+ Add aditional..." linken keresztül	lehet eljutni. A jobb szélen
   	lévő szürke téglalapra kell kattintani.	
  Facebook Login
  	setting
  		első 5 "yes"
  Képernyő felső részén App mode: "live"		 	
   	 
​
User Data Deletion
https://netpolgar.hu/policy
App Icon (1024 x 1024)


*/
include_once __DIR__.'/../config.php';

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
  		 $state = $_GET['state'];
  	} else {
		 $state = '0';	  	
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
   		    'redirect_uri' => FB_REDIRECT,
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
				document.location = '<?php echo SITEURL; ?>'+
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
	?>
	<script>
	document.location='https://www.facebook.com/v12.0/dialog/oauth'+
			'?client_id=<?php echo FB_APPID; ?>'+
			'&redirect_uri='+encodeURI(FB_REDIRECT)+
        	'&state=0';
	</script>
	<?php
}
?>

