<?php
session_start();

$configs = include('../config.php');
$redirect_uri = "http://localhost/Ehtasham/event_apis/facebook/login.php";

function getShortAccessToken($code) {
    global $configs, $redirect_uri;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/{$configs['fb_api_ver']}/oauth/access_token");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id={$configs['fb_app_id']}&redirect_uri={$redirect_uri}&client_secret={$configs['fb_app_secret']}&code={$code}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}
function getLongAccessToken($shortAccessToken){
    global $configs;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/{$configs['fb_api_ver']}/oauth/access_token");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id={$configs['fb_app_id']}&client_secret={$configs['fb_app_secret']}&grant_type=fb_exchange_token&fb_exchange_token={$shortAccessToken}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}
function redirectToLogin(){
    global $configs, $redirect_uri;

    header("Location: https://www.facebook.com/dialog/oauth?client_id={$configs['fb_app_id']}&redirect_uri={$redirect_uri}&scope={$configs['fb_permissions']}");
}

if(!isset($_GET['refresh']) && isset($_SESSION['fb_access_token_details'])){
    print_r($_SESSION['fb_access_token_details']);
}
else if (isset($_GET['code'])) {// Check if the user is coming back from the authentication process
    $shortAccessTokenResp = getShortAccessToken($_GET['code']); // the authorization code

    if(isset($shortAccessTokenResp['error'])){ redirectToLogin(); }
    else{
        $longAccessTokenResp = getLongAccessToken($shortAccessTokenResp['access_token']);

        print_r($longAccessTokenResp);
        $_SESSION['fb_access_token_details'] = $longAccessTokenResp;
    }
} else { redirectToLogin(); }