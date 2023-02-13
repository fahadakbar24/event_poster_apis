<?php
session_start();
include("functions.php");

validatePageIds();

$pageInfo = getPageInfo($_GET['name']);
fetchPagePostIds($pageInfo['id'], $pageInfo['access_token']);

