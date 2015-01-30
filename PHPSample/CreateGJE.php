<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
</head>
<body>
<?php

//error_reporting( E_ALL & ~( E_STRICT | E_DEPRECATED | E_WARNING ) );

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

$linedet = new IPPJournalEntryLineDetail();

$linedet->PostingType = 'Debit';

$linedet->AccountRef  = 9;

$line = new IPPLine();

$line->Id = 0;

$line->Description = 'test journal';

$line->Amount = 2.00;

$line->DetailType= 'JournalEntryLineDetail ';

$line->JournalEntryLineDetail = $linedet;

$linedet2 = new IPPJournalEntryLineDetail();

$linedet2->PostingType = 'Credit';

$linedet2->AccountRef  = 8;

$line2 = new IPPLine();

$line2->Id = 1;

$line2->Description = 'test journal';

$line2->Amount = 2.00;

$line2->DetailType= 'JournalEntryLineDetail ';

$line2->JournalEntryLineDetail = $linedet2;



// Add a journal

$journalObj = new IPPJournalEntry();

$journalObj->SyncToken = '1';

$journalObj->DocNumber = '1';

$journalObj->TxnDate = '2014-12-30';

$journalObj->RefNumber = 't123';

$journalObj->PrivateNote = 'Just testing';

$journalObj->Line = array($line, $line2);

$journalObj->Adjustment  = TRUE;

$journalObj->IsAdjustment  = TRUE;


$resultingjournalObj = $dataService->Add($journalObj);

// Echo some formatted output

echo '<a href="javascript:void(0)" onclick="goHome()">Home</a>';
echo '&nbsp;&nbsp;&nbsp;';
echo '<a href="javascript:void(0)" onclick="return intuit.ipp.anywhere.logout(function () { window.location.href = \'http://localhost/PHPSample/index.php\'; });">Sign Out</a>';
echo '&nbsp;&nbsp;&nbsp;';
echo '<a target="_blank" href="http://localhost/PHPSample/ReadMe.htm">Read Me</a><br />';
//print_r($resultingjournalObj);

echo "<br />Created Journal Id={$resultingjournalObj->Id}. <br /> <br />  Reconstructed response body:<br />";

$xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingjournalObj, $urlResource);

echo $xmlBody . "\n";

?>
<script type="text/javascript">
function goHome(){
window.location.href = "http://localhost/PHPSample/SampleAppHomePage.php";
}
</script>
</body>
</html>