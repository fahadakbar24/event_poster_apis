<?php
session_start();
$configs = include_once "../config.php";
include_once "functions.php";

$dateFormat = "Y-m-d\TH:i:s";
$today = date($dateFormat);

$eventData = [
    'groupUrlname' => $_SESSION['mu_groups_details'][0]['node']['urlname'],
    'title' => 'Test Event',
    'description' => 'This is a test event',
    'startDateTime' => date($dateFormat, strtotime($today. ' + 2 days')),
    'duration' => "P2D",
    'publishStatus' => 'PUBLISHED',
    'featuredPhotoId' => 0
];

createEvent($eventData);
