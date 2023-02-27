<?php
$configs = include("../config.php");
include("../globalfunctions.php");

function makeMUApiReq($type="get",$uri, $fields="", $headers=[]){
    global $configs;

    $headers[] = "Authorization: Bearer {$_SESSION['eb_access_token_details']['access_token']}";
    if(isset($fields)){
        $headers[] = "Content-Type: application/json";
    }

    return makeApiReq($type,"https://api.meetup.com/graphql/{$uri}", $fields, $headers);
}

function redirectToMULogin(){
    global $configs;
    session_unset();

    $authorization_url = "https://secure.meetup.com/oauth2/authorize?client_id={$configs['mu_api_key']}&response_type=code&redirect_uri={$configs['mu_redirect_uri']}";


    header("Location: " . $authorization_url);
}

function getAccessToken($AuthCode){
    global $configs;
    $token_request_params = [
        "client_id" => $configs['mu_api_key'],
        "client_secret" => $configs['mu_api_secret'],
        "grant_type" => "authorization_code",
        "redirect_uri" => $configs['mu_redirect_uri'],
        "code" => $AuthCode
    ];

    $response = makeApiReq(
        "post",
        "https://secure.meetup.com/oauth2/access",
        http_build_query($token_request_params)
    );

    if (!isset($response["access_token"])) {
        echo "Error obtaining access token";
    } else {
        $_SESSION['mu_access_token_details'] = $response;
        echo "Access token: ";
    }

    printVars($response);
    return $response;
}