<?php
include("../globalfunctions.php");
$configs = include('../config.php');

function redirectToFBLogin(){
    global $configs;

    session_unset();
    header("Location: https://www.facebook.com/dialog/oauth?client_id={$configs['fb_app_id']}&redirect_uri={$configs['fb_redirect_uri']}&scope={$configs['fb_permissions']}");
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
function validatePostId(){
    if(!isset($_GET['post_id'])){
        die("No post Id given");
    }
}

function makeFBApiReq($type="get",$uri, $fields="", $headers=[]){
    global $configs;
    return makeApiReq($type,"https://graph.facebook.com/{$configs['fb_api_ver']}/{$uri}", $fields, $headers);
}

function getShortAccessToken($code) {
    global $configs;

    return makeFBApiReq(
        "post",
        "oauth/access_token",
        http_build_query([
            "client_id" => $configs['fb_app_id'],
            "redirect_uri" => $configs['fb_redirect_uri'],
            "client_secret" => $configs['fb_app_secret'],
            "code" => $code
        ])
    );
}
function getLongAccessToken($shortAccessToken){
    global $configs;

    return makeFBApiReq(
        "get",
        "oauth/access_token?client_id={$configs['fb_app_id']}&client_secret={$configs['fb_app_secret']}&grant_type=fb_exchange_token&fb_exchange_token={$shortAccessToken}",
        ""
    );
}
function getPageAccessTokens(){

    $_SESSION['fb_page_details'] = makeFBApiReq(
        "get",
        "me/accounts?access_token={$_SESSION['fb_access_token_details']['access_token']}",
        ""
    );

    return $_SESSION['fb_page_details'];
}
function getPageInfo($pageName){
    return array_filter($_SESSION['fb_page_details']['data'], function ($curPageInfo) {
        return $curPageInfo['name'] == $_GET['name'];
    })[0];

}
function storePagePhoto($page_id, $photoData){
    $response = makeFBApiReq( "post", "{$page_id}/photos", $photoData);

    if (!isset($response["id"])) {
        echo "Error uploading image to Facebook Page";
    } else {
        echo "Image uploaded to Facebook Page";
    }

    printVars($response);
    return $response;
}
function createPost($page_id, $data){
    $response = makeFBApiReq(
        "post",
        "{$page_id}/feed",
        http_build_query($data),
//        ["Content-Type: multipart/form-data"]
    );

    // Check for errors
    if (!isset($response['id'])) {
        echo "Error posting message to Facebook Page";
    } else {
        echo "Message posted to Facebook Page";
    }

    printVars($response);
}
function editPost($post_id, $newData){
    $response = makeFBApiReq("post", "{$post_id}", http_build_query($newData));

    if (!isset($response["id"])) {
        echo "Error editing post on Facebook Page";
    } else {
        echo "Edited post on Facebook Page";
    }

    printVars($response);
}
function fetchPagePostIds($page_id, $access_token){
    $response = makeFBApiReq(
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
    return $post_ids;
}
function deletePagePosts($post_id, $access_token){
    $response = makeFBApiReq(
        "get",
        "{$post_id}?method=delete&access_token={$access_token}",
        ""
    );

    if (!isset($response["success"])) {
        echo "Error deleting post from Facebook Page\n";
        printVars($response);
    } else {
        echo "Deleted post from Facebook Page\n";
    }
}