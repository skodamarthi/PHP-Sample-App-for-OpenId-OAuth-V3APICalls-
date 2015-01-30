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

// Add an employee
$employeeObj = new IPPEmployee();
$employeeObj->Name = "Employee";
$employeeObj->FamilyName = "Intuit";
$employeeObj->GivenName = "Employee";
$employeeObj->DisplayName = "Emp".rand();

$resultingEmployeeObjObj = $dataService->Add($employeeObj);

// Echo some formatted output
echo '<a href="javascript:void(0)" onclick="goHome()">Home</a>';
echo '&nbsp;&nbsp;&nbsp;';
echo '<a href="javascript:void(0)" onclick="return intuit.ipp.anywhere.logout(function () { window.location.href = \'http://localhost/PHPSample/index.php\'; });">Sign Out</a> ';
echo '&nbsp;&nbsp;&nbsp;';
echo '<a target="_blank" href="http://localhost/PHPSample/ReadMe.htm">Read Me</a><br />';

echo "<br />Created Employee in QuickBooks with Id={$resultingEmployeeObjObj->Id} and Name={$resultingEmployeeObjObj->DisplayName}<br /> <br /> Reconstructed response body:<br />";
$xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingEmployeeObjObj, $urlResource);
echo $xmlBody . "\n";
?>
<script type="text/javascript">
function goHome(){
window.location.href = "http://localhost/PHPSample/SampleAppHomePage.php";
}
</script>
</body>
</html>
