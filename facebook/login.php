<?php
session_start();
include('functions.php');

$configs = include('../config.php');

if(!isset($_GET['refresh']) && isset($_SESSION['fb_access_token_details'])){
    printVars($_SESSION['fb_access_token_details']);
}
else if (isset($_GET['code'])) {// Check if the user is coming back from the authentication process
    $shortAccessTokenResp = getShortAccessToken($_GET['code']); // the authorization code

    if(isset($shortAccessTokenResp['error'])){
        printVars($shortAccessTokenResp);
    }
    else{
        $longAccessTokenResp = getLongAccessToken($shortAccessTokenResp['access_token']);

        $_SESSION['fb_access_token_details'] = $longAccessTokenResp;
        printVars($_SESSION['fb_access_token_details']);

        if(!empty($longAccessTokenResp['access_token'])){
            $pageAccessTokens = getPageAccessTokens();
            printVars($pageAccessTokens);
        }
    }
} else { redirectToFBLogin(); }