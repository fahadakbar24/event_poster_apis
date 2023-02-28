<?php
session_start();
include('functions.php');

$configs = include('../config.php');

if(!isset($_GET['refresh']) && isset($_SESSION['mu_access_token_details'])){
    printVars($_SESSION, "session: ");
}
else if (isset($_GET['code'])) {
    getAccessToken($_GET["code"]);
    getNetworkNGroupInfos();
} else {
    redirectToMULogin();
}
