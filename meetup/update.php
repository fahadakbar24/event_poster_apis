<?php
session_start();
$configs = include_once "../config.php";
include_once "functions.php";

$today = new DateTime();
$today->add(new DateInterval("P1D"));

if(empty($_GET['eventId'])){
    echo "Missing Required Parameters";
    return false;
}

    $eventData = [
        'eventId' => $_GET['eventId'],
        'title' => 'New event name',
        'description' => 'New event description',
        'startDateTime' => $today->format("Y-m-d\TH:i:s"),
        'duration' => "P2D",
        'publishStatus' => 'PUBLISHED',
        'featuredPhotoId' => 0,
        'recurring' => [
            'enabled' => true,
            'settings' => [
                'weeklyRecurrence' => [
                    'weeklyInterval' => 1,
                    //should be same day as of start date
                    'weeklyDaysOfWeek' => [strtoupper($today->format('l'))],
                ],
                "endDate" => $today->add(new DateInterval("P30D"))->format("Y-m-d")
            ]
        ]
    ];

$response = updateEvent($eventData);
printVars($response, "Event Successfully updated");
