
<?php
session_start();
require_once('../v3-php-sdk-2.0.5/config.php');

require_once(PATH_SDK_ROOT . 'Core/ServiceContext.php');
require_once(PATH_SDK_ROOT . 'PlatformService/PlatformService.php');
require_once(PATH_SDK_ROOT . 'Utility/Configuration/ConfigurationManager.php');

// Tell us whether to use your QBO vs QBD settings, from App.config
$serviceType = IntuitServicesType::QBO;

// Get App Config
$realmId = ConfigurationManager::AppSettings('RealmID');
if (!$realmId)
	exit("Please add realm to App.Config before running this sample.\n");

// Prep Service Context
$requestValidator = new OAuthRequestValidator(ConfigurationManager::AppSettings('AccessToken'),
                                              ConfigurationManager::AppSettings('AccessTokenSecret'),
                                              ConfigurationManager::AppSettings('ConsumerKey'),
                                              ConfigurationManager::AppSettings('ConsumerSecret'));
$serviceContext = new ServiceContext($realmId, $serviceType, $requestValidator);
if (!$serviceContext)
	exit("Problem while initializing ServiceContext.\n");

// Prep Platform Services
$platformService = new PlatformService($serviceContext);

// Get App Menu HTML
$Respxml = $platformService->Reconnect();

if ($Respxml->ErrorCode != '0')
{

	echo "Error! Reconnection failed..";
	
	if ($Respxml->ErrorCode  == '270')
	{
		echo "OAuth Token Rejected! <br />";
	}
	else if($Respxml->ErrorCode  == '212')
	{
		echo "Token Refresh Window Out of Bounds! <br />";
	}
	else if($Respxml->ErrorCode  == '24')
	{
		echo "Invalid App Token! <br />";
	}
	
}
echo "ResponseXML: ";
var_dump( $Respxml);

echo "<br /> <br /><a href=\"javascript:history.go(-1)\">Go Back</a>";
echo '&nbsp;&nbsp;&nbsp;';
echo '<a target="_blank" href="http://localhost/PHPSample/ReadMe.htm">Read Me</a><br />';


?>
