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
require_once(PATH_SDK_ROOT . 'PlatformService/PlatformService.php');
require_once(PATH_SDK_ROOT . 'Utility/Configuration/ConfigurationManager.php');

require_once(PATH_SDK_ROOT . 'Core/CoreHelper.php');
require_once(PATH_SDK_ROOT . 'DataService/Batch.php');
require_once(PATH_SDK_ROOT . 'DataService/IntuitCDCResponse.php');
require_once(PATH_SDK_ROOT . 'Data/IntuitRestServiceDef/IPPAttachableResponse.php');
require_once(PATH_SDK_ROOT . 'Data/IntuitRestServiceDef/IPPFault.php');
require_once(PATH_SDK_ROOT . 'Data/IntuitRestServiceDef/IPPError.php');
require_once('RestServiceHandler.php');


	
// Echo some formatted output
echo '<a href="javascript:void(0)" onclick="goHome()">Home</a>';
echo '&nbsp;&nbsp;&nbsp;';
echo '<a href="javascript:void(0)" onclick="return intuit.ipp.anywhere.logout(function () { window.location.href = \'http://localhost/PHPSample/index.php\'; });">Sign Out</a>';
echo '&nbsp;&nbsp;&nbsp;';
echo '<a target="_blank" href="http://localhost/PHPSample/ReadMe.htm">Read Me</a><br />';
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

$serviceContext->IppConfiguration->Logger->RequestLog->Log(TraceLevel::Info, "Going to fetch report data.");

		//$query = "entities=" . $entityString . "&changedSince=" . $formattedChangedSince;
		$queryParamValue="This Fiscal Year-to-date";
		
		$query = "date_macro='".$queryParamValue."'";
		$query=urlencode($query);
		//$uri = "company/{1}/cdc?{2}";
		$uri = "company/{1}/reports/TrialBalance?{2}";
		//$uri = str_replace("{0}", CoreConstants::VERSION, $uri);
		$uri = str_replace("{1}", $serviceContext->realmId, $uri);
		$uri = str_replace("{2}", $query, $uri);

        // Creates request parameters
		$requestParameters = new RequestParameters($uri, 'GET', CoreConstants::CONTENTTYPE_APPLICATIONJSON, NULL);
		//var_dump($requestParameters);
		$restRequestHandler = new RestServiceHandler($serviceContext);
		try
		{
		    // gets response
			list($responseCode,$responseBody) = $restRequestHandler->GetReportsResponse($requestParameters, NULL, NULL);
			//CoreHelper::CheckNullResponseAndThrowException($responseBody);
			
			/*echo "response code is:".$responseCode."<br />";
			echo "response body is:".$responseBody."<br />";
			var_dump($responseCode);
			var_dump($responseBody);*/
			
			$responseArray = json_decode($responseBody, true);
			
			ConstructReportUI($responseArray);
			
		}
		catch (Exception $e)
		{
			echo"There is an exception";
		}		


function ConstructReportUI($responseArray)
{
	echo "<h3>Trial Balance as of ".$responseArray['Header']['EndPeriod']."</h3>";
	echo "From Date:".$responseArray['Header']['StartPeriod']."&nbsp;&nbsp;&nbsp;To Date:".$responseArray['Header']['EndPeriod']."<br />";
	echo '<table width="1000" cellspacing="5" cellpadding="5">';
	echo '<tr> <th></th> <th> Debit </th> <th>Credit</th> </tr>';
	if (!$responseArray || (0==count($responseArray)))
		return;
	
	$count = count($responseArray['Rows']['Row']);
	$i = 1;
	
	foreach($responseArray['Rows']['Row'] as $oneArray)
	{
		
		if ($i < $count)
		{
			echo "<tr>";
			echo "<td align='center'>{$oneArray['ColData'][0]['value']}</td>";
			echo "<td align='center'>{$oneArray['ColData'][1]['value']}</td>";
			echo "<td align='center'>{$oneArray['ColData'][2]['value']}</td>";
			
		}
		else
		{
			echo "<td align='center'><b>{$oneArray['Summary']['ColData'][0]['value']}</b></td>";
			echo "<td align='center'><b>{$oneArray['Summary']['ColData'][1]['value']}</b></td>";
			echo "<td align='center'><b>{$oneArray['Summary']['ColData'][2]['value']}</b></td>";
		}
		echo'</tr>';
		$i++;
	}
}

?>

<script type="text/javascript">
function goHome(){
window.location.href = "http://localhost/PHPSample/SampleAppHomePage.php";
}
</script>
</body>
</html>
