<?php

//config.php

//Include Google Client Library for PHP autoload file
require_once 'vendor/autoload.php';

$clientID = '250591320543-9gai3j05ju7ha10tlfi2cfmta8rr5ibm.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/GoogleOAuth/index_enzo.php';

//Make object of Google API Client for call Google API
$google_client = new Google_Client();
//Set the OAuth 2.0 Client ID
$google_client->setClientId($clientID);
//Set the OAuth 2.0 Client Secret key
$google_client->setClientSecret($clientSecret);
//Set the OAuth 2.0 Redirect URI
$google_client->setRedirectUri($redirectUri);

$google_client->addScope('email');
$google_client->addScope('profile');
$google_client->addScope('https://www.googleapis.com/auth/calendar');

//start session on web page
session_start();
?>