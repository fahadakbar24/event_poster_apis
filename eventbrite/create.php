<?php
session_start();
include("functions.php");

$dateFormat = "Y-m-d\TH:i:s\Z";
$today = date($dateFormat);
$stardDate = date($dateFormat, strtotime($today. ' + 0 days'));
$endDate = date($dateFormat, strtotime($today. ' + 12 hours'));
//uploadImage();
//die();


$eventData = [
    "event" => [
        "name" => [ "html" => "Example Event" ],
        "description" => [ "html" => "Some text" ],
        "start" => [
            "timezone" => "UTC",
            "utc" => $stardDate
        ],
        "end" => [
            "timezone" => "UTC",
            "utc" => $endDate
        ],
        "currency" => "USD",
        "capacity" => 100,
        "is_series"=>true,
        "currency" => "USD",
        "online_event" => false,
        "organizer_id" => "",
        "listed" => true,
        "shareable" => true,
        "invite_only" => false,
        "show_remaining" => true,
//        "password" => "12345",
        "is_reserved_seating" => false,
        "show_pick_a_seat" => true,
        "show_seatmap_thumbnail" => true,
        "show_colors_in_seatmap_thumbnail" => true,
        "locale" => "en_US",
//        "logo" => new CURLFile("../uploads/2.jpg"),
        "logo_id" => '',
    ]
];

$ticketData = [
    "ticket_class" => [
        "name" => "Ticket Type 1",
        "quantity_total" => 50,
        "cost" => "USD:25",
        "sales_start" => !$eventData['event']['is_series'] ? date($dateFormat, strtotime($today. ' - 1 days')): "",
        "sales_end" =>  !$eventData['event']['is_series'] ? date($dateFormat, strtotime($today. ' + 10 hours')): "",
    ]
];

$scheduleData = [
    "schedule" => [
        "occurrence_duration" => 3600,
        "recurrence_rule" => "DTSTART:20230305T023000Z
RRULE:FREQ=MONTHLY;BYDAY=1WE;COUNT=5"
    ]
];

$details['event'] = createEvent($eventData);
if(!empty($eventData["event"]["is_series"])){
    $details['schedule'] = scheduleEvent($details['event']["series_id"], $scheduleData);
}

$details['ticket'] = createTickets($details['event']["id"], $ticketData);
$details['publishStatus'] = publishEvent($details['event']["id"]);

printVars($details);
