<?php
session_start();
include('functions.php');

$configs = include('../config.php');

if(!isset($_GET['refresh']) && isset($_SESSION['eb_access_token_details'])){
    printVars($_SESSION);
}
else if (isset($_GET['code'])) {// Check if the user is coming back from the authentication process
    $appAccessData = array(
        "grant_type" => "authorization_code",
        "client_id" => $configs['eb_key'],
        "client_secret" => $configs['eb_secret'],
        "code" => $_GET['code'],
        "redirect_uri" => $configs['eb_redirect_uri'],
    );

    printVars($_GET['code'], "Auth token:");
    getPrivateToken($appAccessData);
    setOrgIds();
} else { redirectToEBLogin(); }



