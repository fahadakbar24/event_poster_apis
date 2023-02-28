<?php
session_start();
$configs = include_once "../config.php";
include_once "functions.php";

$dateFormat = "Y-m-d\TH:i:s";
$today = date($dateFormat);

if(empty($_GET['eventId'])){
    echo "Missing Required Parameters";
    return false;
}

    $eventData = [
        'eventId' => $_GET['eventId'],
        'title' => 'New event name',
        'description' => 'New event description',
        'startDateTime' => date($dateFormat, strtotime($today. ' + 2 days')),
        'duration' => "P2D",
        'publishStatus' => 'PUBLISHED',
        'featuredPhotoId' => 0,
    ];

updateEvent($eventData);
