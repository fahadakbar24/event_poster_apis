<?php
$configs = include('../config.php');
$redirect_uri = "http://localhost/Ehtasham/event_apis/facebook/login.php";

function printVars($var){
    echo "<pre>";
    print_r($var);
    echo "<pre/><br/><br/>";
}

function redirectToLogin(){
    global $configs, $redirect_uri;

    session_unset();
    header("Location: https://www.facebook.com/dialog/oauth?client_id={$configs['fb_app_id']}&redirect_uri={$redirect_uri}&scope={$configs['fb_permissions']}");
}
function makeApiReq($type="get",$uri, $fields=""){
    global $configs;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/{$configs['fb_api_ver']}/{$uri}");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_POST, $type == "post");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}
function getShortAccessToken($code) {
    global $configs, $redirect_uri;

    return makeApiReq(
        "post",
        "oauth/access_token",
        "client_id={$configs['fb_app_id']}&redirect_uri={$redirect_uri}&client_secret={$configs['fb_app_secret']}&code={$code}"
    );
}
function getLongAccessToken($shortAccessToken){
    global $configs, $redirect_uri;

    return makeApiReq(
        "get",
        "oauth/access_token?client_id={$configs['fb_app_id']}&client_secret={$configs['fb_app_secret']}&grant_type=fb_exchange_token&fb_exchange_token={$shortAccessToken}",
        ""
    );
}
function getPageAccessTokens(){

    $_SESSION['fb_page_details'] = makeApiReq(
        "get",
        "me/accounts?access_token={$_SESSION['fb_access_token_details']['access_token']}",
        ""
    );

    return $_SESSION['fb_page_details'];
}
function createPost($page_id, $data){
    $response = makeApiReq(
        "post",
        "https://graph.facebook.com/{$page_id}/feed",
        $data
    );

    // Check for errors
    if ($response === false) {
        echo "Error posting message to Facebook Page";
    } else {
        echo "Message posted to Facebook Page";
        var_dump($response);
    }

    return $response;
}