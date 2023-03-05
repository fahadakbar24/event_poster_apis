<?php

function printVars($var, $message=""){
    echo "<br/>$message<br/>";
    echo "<pre>";
        print_r($var);
    echo "<pre/><br/>";
}

function printError($msg, $errorInfoObj=[]){
    printVars($errorInfoObj, $msg);
    die();
}
function makeApiReq($method = "get",$uri, $payload = "", $headers = []){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $uri);

    if($method == "delete"){
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    } else {
        curl_setopt($ch, CURLOPT_POST, $method == "post");
    }

    if($payload){
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        echo "cURL Error: $error";
    }

    curl_close($ch);

    return json_decode($response, true);
}