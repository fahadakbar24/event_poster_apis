<?php
session_start();
include("functions.php");

validatePageIds();

$pageInfo = array_filter($_SESSION['fb_page_details']['data'], function ($curPageInfo) {
    return $curPageInfo['name'] == $_GET['name'];
})[0];

fetchPagePostIds($pageInfo['id'], $pageInfo['access_token']);

