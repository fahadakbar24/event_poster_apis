<?php
session_start();
include("functions.php");

$dateFormat = "Y-m-d\TH:i:s\Z";
$today = date($dateFormat);

$eventData = [
    "event" => [
        "name" => [ "html" => "Example Event" ],
        "description" => [ "html" => "Some text" ],
        "start" => [
            "timezone" => "UTC",
            "utc" => date($dateFormat, strtotime($today. ' + 2 days'))
        ],
        "end" => [
            "timezone" => "UTC",
            "utc" => date($dateFormat, strtotime($today. ' + 5 days'))
        ],
        "currency" => "USD",
        "capacity" => 100,
        "is_series"=>false,
        "currency" => "USD",
        "online_event" => false,
        "organizer_id" => "",
        "listed" => true,
        "shareable" => false,
        "invite_only" => false,
        "show_remaining" => true,
//        "password" => "12345",
        "capacity" => 100,
        "is_reserved_seating" => false,
        "show_pick_a_seat" => true,
        "show_seatmap_thumbnail" => true,
        "show_colors_in_seatmap_thumbnail" => true,
        "locale" => "en_US",
    ]
];

$ticketData = [
    "ticket_class" => [
        "name" => "Ticket Type 1",
        "quantity_total" => 50,
        "cost" => "USD:25",
        "sales_start" => date($dateFormat, strtotime($today)),
        "sales_end" =>  date($dateFormat, strtotime($today. ' + 5 days')),
    ]
];

$eventDetails = createEvent($eventData);
createTickets($eventDetails["id"], $ticketData);