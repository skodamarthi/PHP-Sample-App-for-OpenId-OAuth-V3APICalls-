<?php

require_once('../v3-php-sdk-2.0.5/config.php');
require_once('../v3-php-sdk-2.0.5/Core/RestCalls/RestHandler.php');
require_once('../v3-php-sdk-2.0.5/Core/RestCalls/FaultHandler.php');
require_once('../v3-php-sdk-2.0.5/Utility/IntuitErrorHandler.php');

/**
 * RestServiceHandler contains the logic for preparing the REST request, calls REST services and returns the response.
 */
class RestServiceHandler extends RestHandler
{

	/**
	 * The context
	 * @var ServiceContext 
	 */	     
	private $serviceContext;

	/**
	 * Initializes a new instance of the SyncRestHandler class.
	 *
	 * @param ServiceContext $context The context
	 */	
	public function RestServiceHandler($context)
	{
		parent::__construct($context);
		$this->context = $context;
		
		return $this;
	}
	
	public function GetReportsResponse($requestParameters, $requestBody, $oauthRequestUri)
	{
		$this->context->IppConfiguration->Logger->CustomLogger->Log(TraceLevel::Info, "Called PrepareRequest method");

		// This step is required since the configuration settings might have been changed.
		$this->RequestCompressor = CoreHelper::GetCompressor($this->context, true);
		$this->ResponseCompressor = CoreHelper::GetCompressor($this->context, false);
		$this->RequestSerializer = CoreHelper::GetSerializer($this->context, true);
		$this->ResponseSerializer = CoreHelper::GetSerializer($this->context, false);

		// Determine dest URI
		$requestUri='';	
		if ($requestParameters->ApiName)
		{
			// Example: "https://appcenter.intuit.com/api/v1/Account/AppMenu"	
			$requestUri = $this->context->baseserviceURL . $requestParameters->ApiName;
		}
		else if ($oauthRequestUri)
		{
			// Prepare the request Uri from base Uri and resource Uri.
			$requestUri = $oauthRequestUri;
		}
		else if ($requestParameters->ResourceUri)
		{
			$requestUri = $this->context->baseserviceURL . $requestParameters->ResourceUri;
		}
		else {

		}

		$oauth = new OAuth($this->context->requestValidator->ConsumerKey, $this->context->requestValidator->ConsumerSecret);
		$oauth->setToken($this->context->requestValidator->AccessToken, $this->context->requestValidator->AccessTokenSecret);
		$oauth->enableDebug();
		$oauth->setAuthType(OAUTH_AUTH_TYPE_AUTHORIZATION);
		$oauth->disableSSLChecks();
		
		$httpHeaders = array();
		if ('QBO'==$this->context->serviceType ||
			'QBD'==$this->context->serviceType)
		{
			// IDS call
			$httpHeaders = array(
				'accept'        => 'application/json');

			// Log Request Body to a file
			$this->RequestLogging->LogPlatformRequests($requestBody, $requestUri, $httpHeaders, TRUE);
			
			
			if ($this->ResponseCompressor)
				$this->ResponseCompressor->PrepareDecompress($httpHeaders);
		}
		else
		{
			// IPP call
			$httpHeaders = array('accept'        => 'application/json');
		}

		try
		{
			
			$OauthMethod = OAUTH_HTTP_METHOD_GET;
			
			$oauth->fetch($requestUri, $requestBody, $OauthMethod, $httpHeaders);		
		}
		catch ( OAuthException $e )
		{
			//echo "ERROR:\n";
			//print_r($e->getMessage()) . "\n";
			
			list($response_code, $response_xml, $response_headers) = $this->GetOAuthResponseHeaders($oauth);
			$this->RequestLogging->LogPlatformRequests($response_xml, $requestUri, $response_headers, FALSE);
			return FALSE;
		}

		list($response_code, $response_xml, $response_headers) = $this->GetOAuthResponseHeaders($oauth);
		
		// Log Request Body to a file
		$this->RequestLogging->LogPlatformRequests($response_xml, $requestUri, $response_headers, FALSE);
		
		return array($response_code,$response_xml);    
	}


	/**
	 * Returns the response headers and response code from a called OAuth object
	 *
	 * @param OAuth $oauth A called OAuth object
	 * @return array elements are 0: HTTP response code; 1: response content, 2: HTTP response headers
	 */	
	private function GetOAuthResponseHeaders($oauth)
	{
		$response_code = NULL;
		$response_xml = NULL;
		$response_headers = array();
		
		try {
			$response_xml = $oauth->getLastResponse();
			
			$response_headers = array();
			$response_headers_raw = $oauth->getLastResponseHeaders();
			$response_headers_rows = explode("\r\n",$response_headers_raw);
			foreach($response_headers_rows as $header) {
				$keyval = explode(":",$header);
				if (2==count($keyval))
					$response_headers[$keyval[0]] = trim($keyval[1]);
				
				if (FALSE !== strpos($header, 'HTTP'))
					list(,$response_code,) = explode(' ', $header);
				
			}
			
			// Decompress, if applicable
			if ('QBO'==$this->context->serviceType ||
				'QBD'==$this->context->serviceType)
			{
				// Even if accept-encoding is set to deflate, server never (as far as we know) actually chooses
				// to respond with Content-Encoding: deflate.  Thus, the inspection of 'Content-Encoding' response
				// header rather than assuming that server will respond with encoding specified by accept-encoding
				if ($this->ResponseCompressor &&
					$response_headers &&
					array_key_exists('Content-Encoding', $response_headers))			
				{
					$response_xml = $this->ResponseCompressor->Decompress($response_xml, $response_headers);
				}
			}		
		}
		catch(Exception $e)
		{
			
		}
		
		return array($response_code, $response_xml, $response_headers);	
	}
	
}

?>
