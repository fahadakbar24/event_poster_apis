<?php
session_start();

include("functions.php");

validateSession();
printVars($_SESSION);

$pageAccessTokens = getPageAccessTokens();

printVars($pageAccessTokens);
