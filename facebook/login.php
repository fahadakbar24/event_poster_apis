<?php
session_start();
include('functions.php');

$configs = include('../config.php');

if(!isset($_GET['refresh']) && isset($_SESSION['fb_access_token_details'])){
    print_r($_SESSION['fb_access_token_details']);
}
else if (isset($_GET['code'])) {// Check if the user is coming back from the authentication process
    $shortAccessTokenResp = getShortAccessToken($_GET['code']); // the authorization code

    if(isset($shortAccessTokenResp['error'])){ redirectToFBLogin(); }
    else{
        $longAccessTokenResp = getLongAccessToken($shortAccessTokenResp['access_token']);

        print_r($longAccessTokenResp);
        $_SESSION['fb_access_token_details'] = $longAccessTokenResp;
    }
} else { redirectToFBLogin(); }