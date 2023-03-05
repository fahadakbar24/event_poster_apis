<?php

session_start();
include("functions.php");

validatePageIds();
$pageInfo = getPageInfo($_GET['name']);

if(!empty($_GET['refresh'])){
    $pagePostIds = fetchPagePostIds($pageInfo['id'], $pageInfo['access_token']);

    foreach ($pagePostIds as $postId){
        deletePagePosts($postId, $pageInfo['access_token']);
    }
}else{
    validatePageParams();
    deletePagePosts($_GET['post_id'], $pageInfo['access_token']);
}

