<?php
session_start();
require_once "functions.php";

verifyUpdateParams();

$event_id = $_GET['event_id'];

$dateFormat = "Y-m-d\TH:i:s\Z";
$today = date($dateFormat);
$startDate = date($dateFormat, strtotime($today. ' + 6 days'));
$endDate = date($dateFormat, strtotime($today. ' + 8 days'));

$newEventData = [
    "event" => [
        'name' => ['html' => 'Updated Event Name'],
        'description' => ['html' => 'Updated Event Description'],
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

$scheduleData = [
    "schedule" => [
        "occurrence_duration" => 3600,
        "recurrence_rule" => "DTSTART:20230305T023000Z
RRULE:FREQ=MONTHLY;BYDAY=1WE;COUNT=7"
    ]
];

$details["old"] = fetchEvent($event_id);

if(empty($details["old"]["is_series"])){
    $eventData["start"] = [ "timezone" => "UTC", "utc" => $startDate ];
    $eventData["end"] = [ "timezone" => "UTC", "utc" => $endDate];
} else{
    $details['schedule'] = scheduleEvent($event_id, $scheduleData);
}

$details['event'] = updateEvent($event_id, $newEventData);
$details['ticket'] = updateTickets($event_id, $newTicketData);

printVars($details);