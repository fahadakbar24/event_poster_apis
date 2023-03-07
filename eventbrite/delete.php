<?php
session_start();
require_once "functions.php";

if($_GET['refresh']){
    deleteAllEvents();
} else{
    deleteEvent($_GET['eventId']);
}
