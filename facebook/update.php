<?php

session_start();
include("functions.php");

validatePageIds();
validatePageParams();

$pageInfo = getPageInfo($_GET['name']);
$imgPath = "../uploads/2.jpg";

$imgData = [
    "access_token" => $pageInfo['access_token'],
    "source" => curl_file_create($imgPath),
    'published' => 'false'
];
$imgUploadInfo = storePagePhoto($pageInfo['id'], $imgData);

$newPostData = array(
    "access_token" => $pageInfo['access_token'],
    "message" => "New description for the post
    
Check out this UPDATED link! 
https://developers.facebook.com/docs/graph-api/reference/v16.0/page/feed
"
);

if(isset($imgUploadInfo['id'])){
    $newPostData['attached_media'] = [
        ["media_fbid" => $imgUploadInfo['id']]
    ];
}

$postInfo = editPost($_GET['post_id'], $newPostData);
$postInfo['imgUploadInfo'] = $imgUploadInfo;

printVars($postInfo, "Message updated to Facebook Page");