<?php
session_start();
include('functions.php');

$configs = include('../config.php');
$events = fetchEvents($_SESSION['mu_groups_details'][0]['node']['id']);

printVars($events);