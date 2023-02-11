<?php
session_start();
include("functions.php");

validatePageIds();

$pageInfo = array_filter($_SESSION['fb_page_details']['data'], function ($curPageInfo){
    return $curPageInfo['name'] == $_GET['name'];
})[0];


$photosData = array(
    "access_token" => $pageInfo['access_token'],
    "message" => "Description of the image",
    "source" => curl_file_create("../uploads/2.jpg"),
);

$postData = array(
    "message" => "Hello World",
//    "url" => , // image_url
    "link" => "https://developers.facebook.com/docs/graph-api/reference/v16.0/page/feed",
    "access_token" => $pageInfo['access_token']
);

//$uploadedPhotoInfo = storePagePhoto($pageInfo['id'], $photosData);
createPost( $pageInfo['id'], $postData);
