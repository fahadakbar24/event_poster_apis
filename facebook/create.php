<?php
session_start();
include("functions.php");

validatePageIds();

$pageInfo = getPageInfo($_GET['name']);;

$imgData = [
    "access_token" => $pageInfo['access_token'],
    "source" => curl_file_create("../uploads/1.png"),
    'published' => 'false'
];
$imgUploadInfo = storePagePhoto($pageInfo['id'], $imgData);

$postData = [
    "access_token" => $pageInfo['access_token'],
    "message" => "Hello 
World

Check out this link! 
https://developers.facebook.com/docs/graph-api/reference/v16.0/page/feed
    ",
    "link" => "https://developers.facebook.com/docs/graph-api/reference/v16.0/page/feed",
//    "picture" => "https://graph.facebook.com/{$imgUploadInfo['id']}/picture",
//    "thumbnail" => curl_file_create("../uploads/2.jpg"),
];
printVars($postData);

if(isset($imgUploadInfo['id'])){
    $postData['attached_media'] = [
        ["media_fbid" => $imgUploadInfo['id']]
    ];
}

createPost( $pageInfo['id'], $postData);
