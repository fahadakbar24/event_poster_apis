<?php
session_start();
include('functions.php');

$configs = include('../config.php');

if(!empty($_GET['refresh'])){
    deleteAllEvents();
} else if(!empty($_GET['eventId'])){
    $deleteResponse = deleteEvent($_GET['eventId']);
    printVars($deleteResponse);
} else{
    echo "Missing Required Parameters";
}

