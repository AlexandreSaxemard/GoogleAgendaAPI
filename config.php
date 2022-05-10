<?php

//config.php

//Include Google Client Library for PHP autoload file
require_once 'vendor/autoload.php';

// Database configuration    
define('DB_HOST', 'localhost'); 
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', ''); 
define('DB_NAME', 'api_google_agenda'); 

//Make object of Google API Client for call Google API
$google_client = new Google_Client();

//Set the OAuth 2.0 Client ID
$google_client->setClientId('205163721970-0ncl7bn7eb5qh3den6oreenkd2lvno8j.apps.googleusercontent.com');

//Set the OAuth 2.0 Client Secret key
$google_client->setClientSecret('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');

//Set the OAuth 2.0 Redirect URI
$google_client->setRedirectUri('http://localhost/GoogleOAuth/index.php');

//
$google_client->addScope('email');

$google_client->addScope('profile');

$google_client -> addScope('https://www.googleapis.com/auth/calendar');


//start session on web page
session_start();

?>