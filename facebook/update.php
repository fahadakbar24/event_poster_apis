<?php

session_start();
include("functions.php");

validatePageIds();
$pageInfo = getPageInfo($_GET['name']);

$newPostData = array(
    "access_token" => $pageInfo['access_token'],
    "message" => "New description for the post"
);

editPost($_GET['post_id'], $newPostData);