<?php
session_start();
include("functions.php");

fetchAllOrgEvents($_SESSION['eb_org_details'][0]['id']);