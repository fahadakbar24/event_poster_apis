<?php
$configs = include('../config.php');
$redirect_uri = "http://localhost/Ehtasham/event_apis/facebook/login.php";

function printVars($var){
    echo "<pre>";
    print_r($var);
    echo "<pre/><br/><br/>";
}

function redirectToFBLogin(){
    global $configs, $redirect_uri;

    session_unset();
    header("Location: https://www.facebook.com/dialog/oauth?client_id={$configs['fb_app_id']}&redirect_uri={$redirect_uri}&scope={$configs['fb_permissions']}");
}
function validateSession(){
    if(empty($_SESSION)){
        echo "Missing required Facebook Session <br />";
        die();
    }
}
function validatePageIds(){
    if(empty($_SESSION['fb_page_details'])){
        echo "Missing required Page Details <br />";
        die();
    }
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
function storePagePhoto($page_id, $photoData){
    $response = makeApiReq(
        "post",
        "{$page_id}/photos",
        http_build_query($photoData)
    );

    if (!isset($response["id"])) {
        echo "Error uploading image to Facebook Page";
    } else {
        echo "Image uploaded to Facebook Page";
    }

    printVars($response);
}
function createPost($page_id, $data){
    $response = makeApiReq(
        "post",
        "https://graph.facebook.com/{$page_id}/feed",
        http_build_query($data)
    );

    // Check for errors
    if (!isset($response['id'])) {
        echo "Error posting message to Facebook Page";
    } else {
        echo "Message posted to Facebook Page";

    }

    printVars($response);
}

function fetchPagePostIds($page_id, $access_token){
    $response = makeApiReq(
        "get",
        "{$page_id}/posts?access_token={$access_token}",
        ""
    );

    if (!isset($response["data"])) {
        echo "Error retrieving post IDs from Facebook Page";
        printVars($response);

    } else {
        $post_ids = array();
        foreach ($response["data"] as $post) {
            $post_ids[] = $post["id"];
        }
        echo "Retrieved post IDs from Facebook Page: ";
        printVars($post_ids);
    }
}

function deletePagePosts($post_id, $access_token){
    $response = makeApiReq(
        "get",
        "{$post_id}?method=delete&access_token={$access_token}",
        ""
    );

    if (!isset($response["success"])) {
        echo "Error deleting post from Facebook Page";
        printVars($response);
    } else {
        echo "Deleted post from Facebook Page";
    }
}