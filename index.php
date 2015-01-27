<?php
  require_once("./config.php");
  require_once("./CSS Styles/StyleElements.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<h3>IPP PHP Sample App</h3>
<title>IPP PHP sample</title>
<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
<script>
    // Runnable uses dynamic URLs so we need to detect our current //
    // URL to set the grantUrl value   ########################### //
    /*######*/ var parser = document.createElement('a');/*#########*/
    /*######*/parser.href = document.url;/*########################*/
    // end runnable specific code snipit ##########################//
    intuit.ipp.anywhere.setup({
        menuProxy: '',
        grantUrl: 'http://'+parser.hostname+'/PHPSample/oauth.php?start=t' 
        // outside runnable you can point directly to the oauth.php page
    });
  </script>
  
  
</head>
<body>

<?php
    # This sample uses the LightOpenID library located here:  https://gitorious.org/lightopenid
    echo '<div> Please refer to the <a target="_blank" href="http://localhost/PHPSample/ReadMe.htm">Read Me</a> page for detailed instructions and information regarding this sample </div><br />';
            
    require 'lightopenid-lightopenid/openid.php';
    try {
        # Change 'localhost' to your domain name.
        $openid = new LightOpenID('localhost');
        if(!$openid->mode) {
        	echo '<div> This sample uses PHP 5.6.3 and Intuit PHP SDK version v3-php-sdk-2.0.5
			</div><br />';
			
        	echo '<div> To be listed on QuickBooks Apps.com, any app must implement OpenID for user authentication. This sample uses LightOpenID library located at
			<a target="_blank" href="https://gitorious.org/lightopenid"> https://gitorious.org/lightopenid </a><br />
			</div><br />';
			
			# The connectWithIntuitOpenId parameter is passed when the user clicks the login button below
            # The subscribeFromAppsDotCom parameter is an argument in the OpenID URL of a sample app on developer.intuit.com
            # Example of OpenID URL:  http://localhost/ippPhpOpenId/IPP-PHP-OpenID-Login.php?subscribeFromAppsDotCom
            if(isset($_GET['connectWithIntuitOpenId']) || isset($_GET['subscribeFromAppsDotCom'])) {
                $openid->identity = "https://openid.intuit.com/Identity-me";
                # The following two lines request email and full name
                # from the Intuit OpenID provider
                $openid->required = array('contact/email');
                $openid->optional = array('namePerson', 'namePerson/friendly');
                header('Location: ' . $openid->authUrl());
            }else{
                # Show the login button.  The user is not in the process of loggin in
                echo '<div><ipp:login href="index.php?connectWithIntuitOpenId" type="vertical"></div>';
            }
        } elseif($openid->mode == 'cancel') {
            echo 'User has canceled authentication!';
        } else {
            # Print the OpenID attributes that we requested above, email and full name
            //print_r($openid->getAttributes());
            # Add a link to allow the user to logout. The link makes a JavaScript call to intuit.ipp.anywhere.logout()
            echo '<br /><a href="javascript:void(0)" onclick="return intuit.ipp.anywhere.logout(function () { window.location.href = \'http://localhost/PHPSample/index.php\'; });">Sign Out</a>';
			
			//oAuth code
			//Susmitha: Adding OAuth Code
			
					require_once('../v3-php-sdk-2.0.5/config.php');  // Default V3 PHP SDK (v2.0.1) from IPP
					require_once(PATH_SDK_ROOT . 'Core/ServiceContext.php');
					require_once(PATH_SDK_ROOT . 'DataService/DataService.php');
					require_once(PATH_SDK_ROOT . 'PlatformService/PlatformService.php');
					require_once(PATH_SDK_ROOT . 'Utility/Configuration/ConfigurationManager.php');
					error_reporting(E_ERROR | E_PARSE);

				

					// After the oauth process the oauth token and secret 
					// are storred in session variables.
					$tk = $_SESSION['token'];
				    if(!isset($_SESSION['token'])){
					  echo "<h3>You are not currently authenticated!</h3>";
					  echo '<div> This sample uses the Pecl Oauth library for OAuth. </div> <br />
					  		<div> If not done already, please download the Oauth package from
							<a target="_blank" href="http://pecl.php.net/package/oauth"> http://pecl.php.net/package/oauth </a> and follow the instructions given 
							<a target="_blank" href="http://pecl.php.net/package/oauth"> here </a> for installing the Oauth module.
							</div><br />
							<div> Add the OAuth Consumer Key and OAuth Consumer Secret of your application to config.php file </div> </br>
							<div> Click on the button below to connect this app to QuickBooks
							</div>';
					  // print connect to QuickBooks button to the page
					  echo "<br /> <ipp:connectToIntuit></ipp:connectToIntuit><br />";
					} else {
					   echo "<h3>You are currently authenticated!</h3>";
					   $token = unserialize($_SESSION['token']);
					   echo "If not already done, please make sure that you set the below variables in the app.config file, before proceeding further! <br />";
					   echo "<br />";
					   echo "realm ID: ". $_SESSION['realmId'] . "<br />";
					   echo "oauth token: ". $token['oauth_token'] . "<br />";
					   echo "oauth secret: ". $token['oauth_token_secret'] . "<br />";
					   echo "<br />";
					   echo "<button class='myButton' title='App Home Page' onclick='myFunction($value)'>Go to the app</button>";
					   echo '&nbsp;&nbsp;&nbsp;';
					   echo "<button class='myButton' title='Disconnect your app from QBO' onclick='Disconnect($value)'>Disconnect the app</button>";
					   echo '&nbsp;&nbsp;&nbsp;';
					   echo "<button class='myButton' title='Regenerate the tokens within 30 days prior to token expiration' onclick='Reconnect($value)'>Reconnect the app</button>";
					   echo "<br />";
					   echo "<br />";
					   echo "<br />";
					   echo '<div> <small> <u> Note:</u> Configuring the Oauth tokens manually in app.config file is only for demonstartion purpose in this sample app. In real time production app, save the oath_token, oath_token_secret, and realmId in a persistent storage, associating them with the user who is currently authorizing access. Your app needs these values for subsequent requests to Quickbooks Data Services. Be sure to encrypt the access token and access token secret before saving them in persistent storage.<br />
					   		 Please refer to this <a target="_blank" href="https://developer.intuit.com/docs/0050_quickbooks_api/0020_authentication_and_authorization/connect_from_within_your_app"> link </a>for implementing oauth in your app. </small></div> <br />'; 
					  }
        }
    } catch(ErrorException $e) {
        echo $e->getMessage();
    }
?>
<script>
function myFunction(parameter){
window.location.href = "http://localhost/PHPSample/SampleAppHomePage.php";
}

function Disconnect(parameter){
window.location.href = "http://localhost/PHPSample/Disconnect.php";
}

function Reconnect(parameter){
window.location.href = "http://localhost/PHPSample/Reconnect.php";
}
</script>
</body>
</html>
