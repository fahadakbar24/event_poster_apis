<?php
session_start();
$configs = include_once "../config.php";
include_once "functions.php";

$today = new DateTime();
$today->add(new DateInterval("P1D"));


$eventData = [
    'groupUrlname' => $_SESSION['mu_groups_details'][0]['node']['urlname'],
    'title' => 'Test Event',
    'description' => 'This is a test event',
    'startDateTime' => $today->format("Y-m-d\TH:i:s"),
    'duration' => "P2D",
    'publishStatus' => 'PUBLISHED',
    'featuredPhotoId' => 0,
    'recurring' => [
        'weeklyRecurrence' => [
            'weeklyInterval' => 1,
            //should be same day as of start date
            'weeklyDaysOfWeek' => [strtoupper($today->format('l'))],
        ],
        "endDate" => $today->add(new DateInterval("P15D"))->format("Y-m-d")
    ]
];
$response = createEvent($eventData);
printVars($response, "Event Successfully updated");