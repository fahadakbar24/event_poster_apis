<?php
session_start();
include('functions.php');

$configs = include('../config.php');

if(
    !isset($_GET['refresh'])
    && isset($_SESSION['fb_access_token_details']['access_token'])
    && isset($_SESSION['fb_page_details']['data'])
){
    printVars($_SESSION);
}
else if (isset($_GET['code'])) {// Check if the user is coming back from the authentication process
    $shortAccessTokenResp = getShortAccessToken($_GET['code']); // the authorization code

    //check if code is expired
    if(isset($shortAccessTokenResp['error']) && $shortAccessTokenResp['error']['error_subcode'] == 36009 ){
        redirectToFBLogin();
    }
    else{
        $longAccessTokenResp = getLongAccessToken($shortAccessTokenResp['access_token']);

        if(!empty($longAccessTokenResp['access_token'])){
            $_SESSION['fb_access_token_details'] = $longAccessTokenResp;
            $_SESSION['fb_page_details'] = getPageAccessTokens($_SESSION['fb_access_token_details']['access_token']);
        }

        printVars($_SESSION);
    }
} else { redirectToFBLogin(); }