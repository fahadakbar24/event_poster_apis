<?php
$configs = include("../config.php");

function redirectToEBLogin(){
    global $configs;

    session_unset();
    header("Location: https://www.eventbrite.com/oauth/authorize?response_type=code&client_id={$configs['eb_key']}&redirect_uri={$configs['eb_redirect_uri']}");
}

function makeEBApiReq($type="get",$uri, $fields="", $headers=[]){
    return makeApiReq($type,"https://www.eventbrite.com/{$uri}", $fields, $headers);
}

function getPrivateToken($data){
    $response = makeEBApiReq(
        "post",
        "oauth/token",
        http_build_query($data),
        array( "content-type: application/x-www-form-urlencoded" )
    );

    if (!isset($response["access_token"])) {
        echo "Error obtaining access token";
    } else {
         $_SESSION['eb_access_token_details'] = $response;
        echo "Access token: ";
    }

    printVars($response);
}