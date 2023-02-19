<?php
session_start();
require_once "functions.php";

if($_GET['refresh'] == true){
    $events = fetchAllOrgEvents();

    foreach ($events as $event){
        deleteEvent($event["id"]);
    }
}
else{
    deleteEvent($_GET['event_id']);
}
