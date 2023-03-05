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
printVars($imgUploadInfo, "Image uploaded to Facebook Page");

$newPostData = array(
    "access_token" => $pageInfo['access_token'],
    "message" => "New description for the post"
);

if(isset($imgUploadInfo['id'])){
    $newPostData['attached_media'] = [
        ["media_fbid" => $imgUploadInfo['id']]
    ];
}

$postInfo = editPost($_GET['post_id'], $newPostData);
printVars($postInfo, "Message updated to Facebook Page");