<?php
session_start();
include("functions.php");

$eventsInfo = fetchAllOrgEvents($_SESSION['eb_org_details'][0]['id']);
printVars($eventsInfo);