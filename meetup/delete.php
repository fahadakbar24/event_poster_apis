<?php
session_start();
include('functions.php');

$configs = include('../config.php');

if(!empty($_GET['refresh'])){
    deleteAllEvents();
} else if(!empty($_GET['eventId'])){
    deleteEvent($_GET['eventId']);
} else{
    echo "Missing Required Parameters";
}

