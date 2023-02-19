<?php
$configs = include("../config.php");
include("../globalfunctions.php");

function verifyUpdateParams(){
    if(!isset($_GET["event_id"]) || empty($_GET["event_id"])){
        die("No Event Id specified.");
    }
}

function redirectToEBLogin(){
    global $configs;

    session_unset();
    header("Location: https://www.eventbrite.com/oauth/authorize?response_type=code&client_id={$configs['eb_key']}&redirect_uri={$configs['eb_redirect_uri']}");
}

function makeEBApiReq($type="get",$uri, $fields="", $headers=[]){
    global $configs;
    $headers[] = "Authorization: Bearer {$_SESSION['eb_access_token_details']['access_token']}";
    if(isset($fields)){
        $headers[] = "Content-Type: application/json";
    }
    return makeApiReq($type,"https://www.eventbriteapi.com/{$configs['eb_api_ver']}/{$uri}", $fields, $headers);
}

function getPrivateToken($data){
    $response = makeApiReq(
        "post",
        "https://www.eventbrite.com/oauth/token",
        http_build_query($data),
        []
    );

    if (!isset($response["access_token"])) {
        echo "Error obtaining access token";
    } else {
        $_SESSION['eb_access_token_details'] = $response;
        echo "Access token: ";
    }

    printVars($response);
}

function setOrgIds(){
    $response = makeEBApiReq("get","users/me/organizations/","",[]);

    if (!isset($response["organizations"])) {
        echo "Error obtaining organization ID";
    } else {
        $_SESSION['eb_org_details'] = $response["organizations"];
        echo "User ID: ";
    }

    printVars($response);
}

function createEvent($evtData){
    $response = makeEBApiReq(
        "post",
        "organizations/{$_SESSION['eb_org_details'][0]['id']}/events/",
        json_encode($evtData),
        []
    );

    if (!isset($response["id"])) {
        echo "Error creating event on Eventbrite";
    } else {
        echo "Created event on Eventbrite: ";
    }

    printVars($response);
    return $response;
}

function createTickets($evtId, $ticketData){
    $response = makeEBApiReq(
        "post",
        "/events/$evtId/ticket_classes/",
        json_encode($ticketData)
    );

    if(!isset($response['id'])){
        echo "Error creating ticket";
    } else {
        echo "Ticket added successfully. \n";
    }

    printVars($response);
    return $response;
}

function fetchTickets($evtId){
    $response = makeEBApiReq(
        "get",
        "events/{$evtId}/ticket_classes/",

    );

//    if(!$response['ticket_classes']){
//        echo "error fetching tickets";
//    } else {
//        echo "Tickets fetched successfully";
//    }

//    printVars($response);
    return $response;
}

function updateTickets($evtId, $ticketData){
    $allTickets = fetchTickets($evtId);

    $response = makeEBApiReq(
        "post",
        "/events/$evtId/ticket_classes/{$allTickets['ticket_classes'][0]['id']}/",
        json_encode($ticketData),
        []
    );

    if(!isset($response['id'])){
        echo "Error updating ticket";
    } else {
        echo "Ticket updated successfully. \n";
    }

    printVars($response);
    return $response;
}

function updateEvent($evtId, $evtData){
    $response = makeEBApiReq(
        "post",
        "events/{$evtId}/",
        json_encode($evtData),
        []
    );

    if (!$response["id"]) {

    } else {
        echo "Event updated successfully";
    }

    printVars($response);
    return $response;
}

function publishEvent($evtId, $allowPublish = true){
    $response = makeEBApiReq(
        "post",
        "events/{$evtId}/". ($allowPublish?'':'un') ."publish/",
        "",
        []
    );

    printVars($response);
}

function createEventSchedule($evtId, $schedule){}

function fetchAllOrgEvents(){
    $response = makeEBApiReq(
        "get",
        "/organizations/{$_SESSION['eb_org_details'][0]['id']}/events/",
        "",
        []
    );

    if (!isset($response["events"])) {
        echo "Error retrieving events";
    } else {
        echo "Events are: ";

    }

    printVars($response);
    return isset($response["events"]) ? $response["events"] : [];
}

function deleteEvent($event_id){
    $response  = makeEBApiReq(
        "delete",
        "events/$event_id/",
        "",
        ["Content-Type: application/json"]
    );

    if (isset($response["error_description"])) {
        echo "Error deleting event: ";
    } else {
        echo "Event ($event_id) deleted successfully";
    }

    printVars($response);
}