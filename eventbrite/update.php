<?php
session_start();
require_once "functions.php";

verifyUpdateParams();

$event_id = $_GET['event_id'];

$dateFormat = "Y-m-d\TH:i:s\Z";
$today = date($dateFormat);

$newEventData = [
    "event" => [
        'name' => ['html' => 'Updated Event Name'],
        'description' => ['html' => 'Updated Event Description'],
        "start" => [
            "timezone" => "UTC",
            "utc" => date($dateFormat, strtotime($today. ' + 6 days'))
        ],
        "end" => [
            "timezone" => "UTC",
            "utc" => date($dateFormat, strtotime($today. ' + 8 days'))
        ],
        'currency' => 'USD',
        'online_event' => false,
        'listed' => true,
        'shareable' => true,
        'capacity' => 500,
//        'logo_id' => 'YOUR_EVENT_LOGO_ID',
    ]
];

$newTicketData = [
    "ticket_class" => [
        "name" => "General Admission",
        "quantity_total" => 500,
        "cost" => "USD:250"
    ]
];

$eventDetails = updateEvent($event_id, $newEventData);
updateTickets($event_id, $newTicketData);


