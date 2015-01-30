<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
</head>
<body>

<?php

require_once('../v3-php-sdk-2.0.5/config.php');

require_once(PATH_SDK_ROOT . 'Core/ServiceContext.php');
require_once(PATH_SDK_ROOT . 'DataService/DataService.php');
require_once(PATH_SDK_ROOT . 'PlatformService/PlatformService.php');
require_once(PATH_SDK_ROOT . 'Utility/Configuration/ConfigurationManager.php');

//Specify QBO or QBD
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

// Prep Data Services
$dataService = new DataService($serviceContext);
if (!$dataService)
	exit("Problem while initializing DataService.\n");
echo '<table width="500" cellspacing="5" cellpadding="5" border="1">';
echo '<tr> <th>Name</th> <th> Number </th> <th>Type</th> <th> Subtype </th> </tr>';
// Iterate through all Accounts, even if it takes multiple pages
$startPosition = 1;
$maxResult =1000;
$count = 0;
while (1) {
	
	$allAccounts = $dataService->FindAll('Account', $startPosition, $maxResult );
	if (!$allAccounts || (0==count($allAccounts)))
		break;
	
	foreach($allAccounts as $oneAccount)
	{
		$startPosition++;
		$count++;
		echo "<tr>";
		echo "<td>{$oneAccount->Name}</td>";
		echo "<td>{$oneAccount->Id}</td>";
		echo "<td>{$oneAccount->AccountType}</td>";
		echo "<td>{$oneAccount->AccountSubType}</td>";
		echo'</tr>';
	}
}

echo '<a href="javascript:void(0)" onclick="goHome()">Home</a>';
echo '&nbsp;&nbsp;&nbsp;';
echo '<a href="javascript:void(0)" onclick="return intuit.ipp.anywhere.logout(function () { window.location.href = \'http://localhost/PHPSample/index.php\'; });">Sign Out</a>';
echo '&nbsp;&nbsp;&nbsp;';
echo '<a target="_blank" href="http://localhost/PHPSample/ReadMe.htm">Read Me</a><br />';
echo "<br />Total no. of accounts is $count \n \n";
?>
<script>
function goHome(){
window.location.href = "http://localhost/PHPSample/SampleAppHomePage.php";
}
</script>
</body>
</html>