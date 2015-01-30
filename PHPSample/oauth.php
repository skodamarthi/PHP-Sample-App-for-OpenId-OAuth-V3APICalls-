<?php 
require_once("./config.php");
 
define('OAUTH_REQUEST_URL', 'https://oauth.intuit.com/oauth/v1/get_request_token');
define('OAUTH_ACCESS_URL', 'https://oauth.intuit.com/oauth/v1/get_access_token');
define('OAUTH_AUTHORISE_URL', 'https://appcenter.intuit.com/Connect/Begin');

// The url to this page. it needs to be dynamic to handle runnable's dynamic urls
$var = $_SERVER['HTTP_HOST'];

define('CALLBACK_URL','http://'.$_SERVER['HTTP_HOST'].'/PHPSample/oauth.php');
echo $var = CALLBACK_URL;
echo "Callback url is $var";
// cleans out the token variable if comming from
// connect to QuickBooks button
if ( isset($_GET['start'] ) ) {
  echo "<br /> Session token cleared! <br />";
  unset($_SESSION['token']);
}
 
try {
echo ("About to start oAuth \n");
  $oauth = new OAuth( OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
  echo ("Created oAuth object \n");
  $oauth->enableDebug();
  $oauth->disableSSLChecks(); //To avoid the error: (Peer certificate cannot be authenticated with given CA certificates)
  if (!isset( $_GET['oauth_token'] ) && !isset($_SESSION['token']) ){
	// step 1: get request token from Intuit
	  echo ("About to call getRequestToken \n");
    $request_token = $oauth->getRequestToken( OAUTH_REQUEST_URL, CALLBACK_URL );
	 echo ("Got RequestToken \n");
		$_SESSION['secret'] = $request_token['oauth_token_secret'];
		// step 2: send user to intuit to authorize 
		header('Location: '. OAUTH_AUTHORISE_URL .'?oauth_token='.$request_token['oauth_token']);
		$oauthToken = $request_token['oauth_token'];
		$oauthSecret = $request_token['oauth_token_secret'];;
		echo "Successfully obtained oauth token and secret. Token is $oauthToken and secret is $oauthSecret";
	}
	
	if ( isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) ){
		// step 3: request a access token from Intuit
    $oauth->setToken($_GET['oauth_token'], $_SESSION['secret']);
		$access_token = $oauth->getAccessToken( OAUTH_ACCESS_URL );
		
		$_SESSION['token'] = serialize( $access_token );
    $_SESSION['realmId'] = $_REQUEST['realmId'];  // realmId is legacy for customerId
    $_SESSION['dataSource'] = $_REQUEST['dataSource'];
	
	 $token = $_SESSION['token'] ;
	 $realmId = $_SESSION['realmId'];
	 $dataSource = $_SESSION['dataSource'];
	 $secret = $_SESSION['secret'] ;
	 
    // write JS to pup up to refresh parent and close popup
    echo '<script type="text/javascript">
            window.opener.location.href = window.opener.location.href;
            window.close();
          </script>';
  }
 
} catch(OAuthException $e) {
	echo "Got auth exception";
	echo '<pre>';
	print_r($e);
}

?>