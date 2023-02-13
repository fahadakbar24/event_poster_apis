<?php

session_start();
include("functions.php");

validatePageIds();
validatePostId();

$pageInfo = getPageInfo($_GET['name']);
deletePagePosts($_GET['post_id'], $pageInfo['access_token']);