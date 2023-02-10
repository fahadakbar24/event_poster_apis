<?php
session_start();
include("functions.php");

printVars($_SESSION);

$pageAccessTokens = getPageAccessTokens();

printVars($pageAccessTokens);
