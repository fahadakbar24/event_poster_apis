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

function uploadImage(){
//    $imgPath = '../uploads/2.jpg';
//    $response = makeEBApiReq(
//        "post",
//        "media/upload/",
//        array(
//            'upload_token' => $_SESSION['eb_access_token_details']['access_token'],
//            'image_file' => new CURLFILE($imgPath, mime_content_type($imgPath), basename($imgPath)),
//        ),
//        [
//            'Content-Type: application/octet-stream',
//            'Content-Disposition: form-data; name="upload"; filename="' . basename($imgPath) . '"'
//        ]
//    );
//
//    printVars($response);

    // Image details
    $imagePath = "../uploads/2.jpg";
    $imageType = mime_content_type($imagePath);

    // Get the upload URL for the image
    $ch = curl_init("https://www.eventbriteapi.com/v3/media/upload/?type=image-event-logo&token={$_SESSION['eb_access_token_details']['access_token']}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer {$_SESSION['eb_access_token_details']['access_token']}"));
    $result = curl_exec($ch);
    $uploadReqs = json_decode($result, true);
    curl_close($ch);

    printVars($uploadReqs);

    if (!isset($uploadReqs['upload_url'])) {
        die("Failed to get upload URL.");
    }

    // Upload the image
    $ch = curl_init($uploadReqs['upload_url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//        "Authorization: Bearer {$_SESSION['eb_access_token_details']['access_token']}",
//        "Authorization: Bearer {$uploadReqs['upload_token']}",
        "Content-Type: multipart/form-data",
//        "Content-Type: $imageType",
//        "Content-Length: " . filesize($imagePath)
    ));

//    $uploadReqs['upload_data']['upload_token'] = $uploadReqs['upload_token'];
//    $uploadReqs['upload_data']['file'] = curl_file_create($imagePath);

//    printVars($uploadReqs['upload_data']);
    curl_setopt($ch, CURLOPT_POSTFIELDS,array_merge(
        $uploadReqs['upload_data'],
        [
            'file' => curl_file_create($imagePath),
            'upload_token' => $uploadReqs['upload_token']
        ]
    ));
    $result = curl_exec($ch);
    $response = json_decode($result, true);


    printVars($result);
    if (!isset($response['id'])) {
        die("Failed to upload image.".curl_error($ch));
    }
    curl_close($ch);
    $imageId = $response['id'];

printVars($response);
}

function createEvent($evtData){
    $response = makeEBApiReq(
        "post",
        "organizations/{$_SESSION['eb_org_details'][0]['id']}/events/",
        json_encode($evtData),
    );

    if (!isset($response["id"])) {
        printError("Error creating event on Eventbrite", $response);
    }

    return $response;
}
function scheduleEvent($eventId, $scheduleData){
    $response = makeEBApiReq("post", "events/{$eventId}/schedules/", json_encode($scheduleData));

    if(!isset($response['id'])){
        printError("Error Scheduling Event dates", $response);
    }

    return $response;
}

function createTickets($evtId, $ticketData){
    $response = makeEBApiReq(
        "post",
        "/events/$evtId/ticket_classes/",
        json_encode($ticketData)
    );

    if(!isset($response['id'])){
        printError("Error creating ticket", $response);
    }

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

    if(!isset($response['id'])) {
        printError("Error updating ticket", $response);
    }

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
        printError("Error updating Event", $response);
    }

    return $response;
}

function publishEvent($evtId, $allowPublish = true){
    return makeEBApiReq(
        "post",
        "events/{$evtId}/". ($allowPublish?'':'un') ."publish/",
        "",
        []
    );
}
function fetchEvent($eventId){
    $response = makeEBApiReq( "get", "/events/$eventId/","", []);

    if (!isset($response["id"])) {
        printError("Error retrieving events", $response);
    }

    return $response;
}

function fetchAllOrgEvents($orgId){
    $response = makeEBApiReq(
        "get",
        "/organizations/$orgId/events/?status=all",
        "",
        []
    );

    if (!isset($response["events"])) {
        printError("Error retrieving events", $response);
    }

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