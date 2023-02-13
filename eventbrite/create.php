<?php

$event_name = "Example Event";
$event_start = "2023-03-01T10:00:00Z";
$event_end = "2023-03-01T12:00:00Z";
$event_currency = "USD";
$event_capacity = 100;

$data = array(
    "event.name.html" => $event_name,
    "event.start.utc" => $event_start,
    "event.end.utc" => $event_end,
    "event.currency" => $event_currency,
    "event.capacity" => $event_capacity
);

$headers = array(
    "Authorization: Bearer {OAuth token}",
    "Content-Type: application/json"
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.eventbriteapi.com/v3/events/");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

// Check for errors
$data = json_decode($response, true);
if (!isset($data["id"])) {
    echo "Error creating event on Eventbrite";
} else {
    echo "Created event on Eventbrite";
}

?>
