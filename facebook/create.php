<?php
session_start();
include("functions.php");

validatePageIds();

$pageInfo = getPageInfo($_GET['name']);
$imgPath = "../uploads/1.png";

$imgData = [
    "access_token" => $pageInfo['access_token'],
    "source" => curl_file_create($imgPath),
    'published' => 'false'
];
$imgUploadInfo = storePagePhoto($pageInfo['id'], $imgData);
printVars($imgUploadInfo, "Image uploaded to Facebook Page");

$postData = [
    "access_token" => $pageInfo['access_token'],
    "message" => "Hello 
World

Check out this link! 
https://developers.facebook.com/docs/graph-api/reference/v16.0/page/feed
    ",
//    "link" => "https://developers.facebook.com/docs/graph-api/reference/v16.0/page/feed",
//    "picture" => "https://graph.facebook.com/{$imgUploadInfo['id']}/picture",
//    "thumbnail" => curl_file_create("../uploads/2.jpg"),
];

if(isset($imgUploadInfo['id'])){
    $postData['attached_media'] = [
        ["media_fbid" => $imgUploadInfo['id']]
    ];
}
$postInfo = createPost( $pageInfo['id'], $postData);
printVars($postInfo, "Message posted to Facebook Page");
