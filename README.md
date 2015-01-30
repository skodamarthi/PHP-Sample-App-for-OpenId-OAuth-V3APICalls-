PHP Sample App for OpenId / OAuth and Data Exchange Calls
===

Welcome to the Intuit Developer's PHP Sample App.

This sample app is meant to provide working examples of how to integrate your app with the Intuit Small Business ecosystem. Specifically, this sample application demonstrates the following:


1. Implementing OpenID to enable single sign on (SSO) between your app and QuickBooks Apps.com.
2. Implementing OAuth to connect your application to a customer's QuickBooks Online company. 
3. Managing the OAuth tokens
4. Creating an Employee in QuickBooks Online company
5. Querying the list of accounts in QuickBooks Online company
6. Creating a General Journal Entry in QuickBooks Online company
7. Using the Reports API to obtain data from Trial Balance report in QuickBooks Online company

Please note that while these examples work, features not called out above are not intended to be taken and used in production business applications. In other words, this is not a seed project to be taken cart blanche and deployed to your production environment.  

For example, certain concerns are not addressed at all in our samples (e.g. security, privacy, scalability). In our sample apps, we strive to strike a balance between clarity, maintainability, and performance where we can. However, clarity is ultimately the most important quality in a sample app.

Therefore there are certain instances where we might forgo a more complicated implementation (e.g. caching a frequently used value, robust error handling, more generic domain model structure) in favor of code that is easier to read. In that light, we welcome any feedback that makes our samples apps easier to learn from.

## Table of Contents

* [Requirements](#requirements)
* [First Use Instructions](#first-use-instructions)
* [Running the code](#running-the-code)
* [High Level Workflow](#high-level-workflow)
* [Functional Details](#functional-details)
* [Project Structure](#project-structure)
* [How To Guides](#how-to-guides)


## Requirements

In order to successfully run this sample app you need a few things:

1. Latest version of PHP on your machine. This sample uses PHP 5.6.3.
2. Install Apache Server and configure PHP 5 to run with Apache Server
3. Download Intuit’s latest PHP devkit from https://developer.intuit.com/docs/0100_accounting/0500_developer_kits/0210_ipp_php_sdk_for_quickbooks_v3– This sample uses v3-php-sdk-2.0.5. (v3-php-sdk-2.0.5 is also included in this repository for your convenience!) 
4. This sample needs LightOpenID library for OpenID. You can download  the LightOpenID library located [here](https://gitorious.org/lightopenid). Make sure that this is placed inside our PHPSample’s root folder.
5. For Oauth implementation, this sample uses the Pecl Oauth library. Please download the Oauth 
package from this [page](http://pecl.php.net/package/oauth)
<ul>
  <li>Instructions for Windows:
      <ul>
      <li>
      Download the php_oauth.dll and copy it to the ext folder of your PHP installation.
      </li>
      <li>
      Add the entry “extension=php_oauth.dll” in your php.ini file.
      </li>
      </ul>
  </li>
  <li>Instructions for MAC OSX:
      <ul>
      Follow this link: http://lupomontero.com/installing-phps-oauth-pecl-extension-on-mac-os-x-snow-leopard/
      </ul>
  </li>
</ul>
6. A [developer.intuit.com](http://developer.intuit.com) account
7. An app on [developer.intuit.com](http://developer.intuit.com) and the associated app token, consumer key, and consumer secret.

## First Use Instructions

1. Clone the GitHub repo to your computer
2. Place our PHPSample folder and the downloaded v3-php-sdk-2.0.5 folder inside the web folder of the Apache web server.
3. This sample is using the sandbox environment by default. So, you need to use the development tokens of your app for running this sample. If you want to switch to production, please make sure that you change the baseUrl in app.config file inside PHPSample folder to quickbooks.api.intuit.com from sandbox-quickbooks.api.intuit.com. Also, make sure that you configure the sample app to use prod tokens instead of development tokens.
4. **Configuring the app tokens**: Go to your app on developer.intuit.com and copy the OAuth Consumer Key and OAuth Consumer Token from the keys tab. Add these values to the config.php file in our PHPSample folder.

## Running the code

Once the sample app code is on your computer, you can do the following steps to run the app:

1. Index.php is the starting page for our sample. Open the index.php file in the web browser and follow the instructions.
2. Sign up/ Sign in with your credentials to your Intuit account.
3. Connect your app to Quickbooks, by clicking on “Connect to QuickBooks” button and follow the steps.
4. After successfully connecting the app to QuickBooks, you will see the realmID, Oauth token and Oauth secret on the webpage. Add these values to the app.config file inside the PHPSample folder before proceeding.
<ul>
<li>
**Note**: Configuring the Oauth tokens manually in app.config file is only for demonstartion purpose in this sample app. In real time production app, save the oath_token, oath_token_secret, and realmId and creation date in a persistent storage, associating them with the user who is currently authorizing access. Your app needs these values for subsequent requests to Quickbooks Data Services. Be sure to encrypt the access token and access token secret before saving them in persistent storage.
</li>
<li>
Please refer to this [link](https://developer.intuit.com/docs/0050_quickbooks_api/0020_authentication_and_authorization/connect_from_within_your_app) for implementing oauth in your app.
</li>
</ul>

### High Level Workflow

1. Click the **Sign In with Intuit** button and log-in.
2. Connect to a QuickBooks Online company.
3. Click on one of the three buttons to proceed further!


### Functional Details
Buttons and their functionalities:

1. **Go to the app**: Takes you to the home page of the app where you can perform various actions in QuickBooks. 
2. **Disconnect the app**: Allows the user to disconnect the app from QuickBooks, by deleting the oauth token and secret of the app associated with that user.  If you need to connect to Quickbooks later again, you have to go through the “Connect to QuickBooks” process to generate the new oauth tokens. (Check implementation in disconnect.php) 
3. **Reconnect the app**: Before the token expires, your app can obtain a new token to provide uniterrupted service by calling the Reconnect API. (Check implementation in reconnect.php)
   
    The following conditions must be met in order to renew the OAuth access token:
    <ul>
        <li>The renewal must be made within 30 days of token expiry. Note that when your app received the token during the OAuth grant, the expiry date was calculated (180 days).Only production approved apps can make this call for unlimited connections. Developer and non approved prod instances can test in playground and are limited to 10 connections. The current token must still be active.</li>
        <li>Note: For Production app, it is advised to run a scheduled daily job to regenerate the tokens, if the current date is more than 150 days and less than 180 days from the Creation date of OAuth tokens (obtained from the persistent storage)</li>
    </ul>

### Project Structure
1.	**Create Employee**: Please check the implementation in EmployeeCreate.php file. Employee information has to be set in IPPEmployee object before calling the Create API.
2.	**Get QBO Accounts**: Please check the implementation in AccountsFindAll.php file. To paginate through all of the objects of a specific type in a given company, call the FindAll() method. Increment the startPosition parameter with each successive call.  The maxResult parameter is the number of objects to fetch in each call.
3.	**Create Journal Entry**: Please check the implementation in CreateGJE.php file. Journal entry information has to be set in IPPJournalEntry object. JournalEntryLineDetails have to be set in IPPJournalEntryLineDetail and the line object has to be added to the main IPPJournalEntry object.
4.	**Trial Balance report**: Reports SDK is not supported by the Intuit PHP SDK as of now. Please note that in this sample we are directly sending a HTTP request to the Reports API and not using the PHP devkit. 
<ul>
<li>Implementation is in TrialBalanceReport.php file. We are decoding the json response from the server and displaying the required data in a html table.</li>
<li>Various filters can be set in the form of query params to obtain the required information from the Quickbooks reports.</li>
<li>Please refer to various reports that are currently supported by the reports API: https://developer.intuit.com/docs/0100_accounting/0400_references/reports </li>
</ul>
5. All the styles are located in StyleElements.php file present inside CSS Styles folder.

### How To Guides

The following How-To guides related to implementation tasks necessary to produce a production-ready Intuit Partner Platform app (e.g. OAuth, OpenId, etc) are available:
* <a href="https://developer.intuit.com/docs/0100_accounting/0060_authentication_and_authorization/connect_from_quickbooks_apps.com" target="_blank">OpenID How To Guide </a>
* <a href="https://developer.intuit.com/docs/0100_accounting/0060_authentication_and_authorization/connect_from_within_your_app" target="_blank">OAuth How To Guide </a>



